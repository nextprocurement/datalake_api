<?php

/**
 * Description of app
 * Main Application for REST APIs
 * @author gelpi
 */
// Error codes
define('NOERROR', 0);
define('NOSTORE', 1);
define('NODEFOP', 2);
define('NOTIMPLEMENTED', 3);
define('NOOP', 4);
define('UNAVAILMETHOD', 5);
define('IDNOTFOUND', 6);
define('NOCLUSTER',7);
define('NOTFOUND',8);

class App {

    public $URI;
    public $baseURL;
    public $pathList = [];
    public $currentPath = [];
    public $params;
    public $headerSet = ['Content-type:text/plain'];
    public $dataStore;
    public $output;
    private $errorData = [
        NOSTORE =>        ['httpCode' => 404, 'msg' => 'Unknown Data Store ##cols## allowed)'],
        NODEFOP =>        ['httpCode' => 404, 'msg' => 'No default operation is defined'],
        NOTIMPLEMENTED => ['httpCode' => 501, 'msg' => 'Operation not (yet) implemented'],
        NOOP =>           ['httpCode' => 400, 'msg' => 'No request defined'],
        UNAVAILMETHOD =>  ['httpCode' => 404, 'msg' => 'Resource not available'],
        IDNOTFOUND =>     ['httpCode' => 404, 'msg' => 'Requested id was not found in the selected Data Store'],
        NOCLUSTER =>      ['httpCode' => 404, 'msg' => 'Requested cluster informaction is unavailable'],
        NOTFOUND =>       ['httpCode' => 404, 'msg' => 'Requested resource is unavailable'],
    ];

    function __construct() {
        $this->errorData[NOSTORE]['msg'] = str_replace("##cols##", join(", ",array_keys($GLOBALS['cols'])),$this->errorData[NOSTORE]['msg']);
        $this->URI = preg_replace('/\?.*/','',$_SERVER['REQUEST_URI']);        
        $this->baseURL = pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME);
        // Format from HTTP
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            switch ($_SERVER['HTTP_ACCEPT']) {
                case 'application/json' :
                    $fmtHTTP = 'json';
                    break;
                case 'text/xml': 
                    $fmtHTTP = 'xml';
                    break;
                case 'text/html':
                    $fmtHTTP = 'html';
                    break;
                case 'text/plain':
                    $fmtHTTP = 'tsv';
            };
        }
        $ext = pathinfo($this->URI, PATHINFO_EXTENSION);
        if (!preg_match('/\/files\//', $this->URI) and preg_match('/(gz|json|xml|html|htm|tsv)/', $ext)) { // Hack to avoid get dotted ids (Enzyme) as extensions
            $this->URI = str_replace('.'.$ext,'',$this->URI);
        } else {
	    $ext = '';
        }
        // Escape colon after prefixes on ids that look like ports :NN
        $this->URI = str_replace(":","__", $this->URI);
	if ($this->baseURL == '/') {	
	        $this->pathList = explode('/', parse_url($this->URI, PHP_URL_PATH));
        } else {
        	$this->pathList = explode('/', str_replace($this->baseURL, '', parse_url($this->URI, PHP_URL_PATH)));
	}
        array_shift($this->pathList);
        if (!$this->pathList[0]) {
            redirect($GLOBALS['baseURL']."/home.html");
            exit;
        }
        if (preg_match('/(home|about)/', $this->pathList[0])) {
            $html = parseTemplate(['baseURL' => $GLOBALS['baseURL'],'title'=>$GLOBALS['baseTitle']], getTemplate($GLOBALS['htmlHeader']));
            $html .= parseTemplate(['baseURL' => $GLOBALS['baseURL']], getTemplate($this->pathList[0].".templ.htm"));
            $html .= parseTemplate(['baseURL' => $GLOBALS['baseURL']], getTemplate($GLOBALS['htmlFooter']));
            print $html;
//            redirect($GLOBALS['baseURL']."/".$this->pathList[0].".htm");
            exit;
        }
        if ($this->pathList[0] == 'help') {
            return $this->help();
            exit;
        }

        // Capture QUERY_STRING parameters
        $this->params = new Parameters($_REQUEST);
        // recover formats and options from URI extensions
        // case {fmt}.gz treated first to allow double extension
        if ($ext == 'gz') {
            $this->params->gzip=1;
            $URInogz = str_replace('.gz','',$this->URI);
            $ext = pathinfo($URInogz, PATHINFO_EXTENSION); // no effect if no fmt ext
            $this->pathList[1] = str_replace('.'.$ext,'',$this->pathList[1]);
        }
        if ($ext) {
            $this->params->fmt=$ext;
        } elseif (isset($fmtHTTP)) {
            $this->params->fmt=$fmtHTTP;
        }
        //
        $this->currentPath = $this->pathList;
        // honor .fasta extension as /sequence
//        if (($this->params->fmt == 'fasta') and (!$this->currentPath[2])) {
//            $this->currentPath[2] = 'sequence';
//        }
        // Set Store from URI
        $this->setDataStore(array_shift($this->currentPath)); 
        return $this;
    }
    
    function help() {
//        $html = parseTemplate(['baseURL'=> $GLOBALS['baseURL']], file_get_contents($GLOBALS['htmlib']. "/API.inc.htm"));
//        $html .= parseTemplate(['baseURL'=> $GLOBALS['baseURL']], file_get_contents($GLOBALS['htmlib']. "/templates/footer.templ.htm"));
//        print $html;
        header("Location: ".$GLOBALS['APIDocsURL']);
        exit;
    }

    function setDataStore($dataStore) {
        $this->dataStoreId = $dataStore;
        if (!class_exists($dataStore)) {
            $this->sendError([$dataStore, NOSTORE]);
        }
        $this->dataStore = new $this->dataStoreId();
        if ($this->dataStore->classTemplate == "file") {
            $this->dataStore->classTemplate = file_get_contents($GLOBALS['htmlib']."/templates/".$dataStore.".templ.html");
        }
        $this->dataStore->currentPath = $this->currentPath;
        return $this;
    }

    function run($send = True) {
        // Process query through DataStore
        $result = $this->dataStore->processData($this->params);
        // Error state
        if ($result->error) {
            $this->sendError($result->error,$this->params->fmt);
        } 
        // Redirect state
        if (isset($result->redirect)) {
            $this->redirectCall($result->redirect);
        }
        // Normal output
        // $dataType defined shape for $data 
        list ($dataType, $data) = $result->output;
        switch ($dataType) {
            case STRUCT: //structured array data, default json
                if (!isset($this->params->fmt)) {
                    $this->params->fmt='';
                }
                switch ($this->params->fmt) {
                    case "xml":
                        $outputDataType = XML;
                        $this->headerSet = ['Content-type:text/xml'];
                        break;
                    case "json":
                    default:
                       $this->headerSet = ['Content-type:application/json'];
                       $outputDataType = JSON;
                }
                // TODO Gzip
                break;
            case TEXT: // Simple text (like PDB file)
                if (isset($this->params->gzip)) {
                    $outputDataType = GZIP;
                    $this->headerSet = ['Content-type: application/x-gzip',
                        'Content-Disposition: attachment; filename="' . $this->dataStore->dataFn . '"'];
                } else {
                    $outputDataType = TEXT;
                }
                
                break;
            case HTML: // Simple text (like PDB file)
                $this->headerSet = ['Content-type: text/html'];
                $outputDataType=HTML;
                break;
            case CURSOR:
                $outputDataType=CURSOR;
                break;
            case RAW:
                $outputDataType = RAW;
                $fn = pathinfo($data['file'], PATHINFO_BASENAME);
                $this->headerSet = ['Content-Disposition: attachment; filename="' . $fn . '"'];
                break;
            default:
                print "ERROR $dataType";
        }
        //
        switch ($outputDataType) {
            case CURSOR: // Mongo Cursor for long outputs like in searches, to revise
                if (!$this->params->fmt) {
                    $this->params->fmt == 'tab';
                }
                if (!isset($this->params->noheaders)) {
                    $dataTab = parseTemplate(['query' => urlencode($_SERVER['QUERY_STRING'])], $result->template->headerTempl);
                }
                if ($data->count()) {
                    while ($d = $data->getNext()) {
                        $dataTab .= parseTemplate($result->prepDataOutput($d), $result->template->dataTempl);
                    }
                }
                $dataTab .= parseTemplate([], $result->template->footerTempl);
                $this->output = $dataTab;
                if ($this->params->fmt == 'xml') {
                    $this->headerSet = ['Content-type:text/xml'];
                }
                break;
            case JSON:
                if (isset($this->params->compact)) {
                    $dataout = json_encode($data);
                } else {
                    $dataout = json_encode($data, JSON_PRETTY_PRINT);
                }
                $this->output = str_replace('_"','"', $dataout); // _ char to avoid expand lists for tags ending 's' (xml encode issue)
                break;
            case XML:
                if (is_array($data)) {
                    $xml = xml_encode([$this->dataStore->baseXMLTag => $data]);
                    if (isset($data['_id'])) {
                        $idatr = $xml->createAttribute('id');
                        $idatr->value = $data['_id'];
                        $rootelem = $xml->getElementsByTagName($this->dataStore->baseXMLTag)->item(0);
                        $rootelem->appendChild($idatr);
                    }
                    $this->output = $xml->saveXML();
                } else {
                    $this->output = "<$this->dataStore->baseXmlTag>\n<![CDATA[" . $data . "]]>\n</$this->dataStore->baseXmlTag>";
                }
                break;
            case GZIP:
                $this->output = gzip($data);
                break;
            case RAW;
                break;
            default:
                $this->output = $data;
        }
        if (($outputDataType == RAW) and $send) {
            $this->sendThroughData($result->storeData."/".$data['file']);
        } elseif ($send) {
            $this->sendData();
        } else {
            return $this;
        }
    }
    
    function redirectCall($newUri) {
        http_response_code(303);
        header ('Location: '. $GLOBALS['baseURL']."/".$this->dataStore->id."/".$this->params->id.$newUri);
    }
    
    function sendError($error, $fmt='json') {
        list ($str, $errorId) = $error;
        http_response_code($this->errorData[$errorId]['httpCode']);
        $error = [
            'errorId' => $errorId,
            'httpCode' => $this->errorData[$errorId]['httpCode'],
            'msg' => $this->errorData[$errorId]['msg']. " (" . $str . ")"
        ];        
        switch ($fmt) {
            case 'html':
            case 'htm':
                header ("Content-type: text/html");
                $error['baseURL'] = $GLOBALS['baseURL'];
                $html = parseTemplate(['baseURL'=>$GLOBALS['baseURL'], 'title'=>'Error '.$error['httpCode']],  getTemplate($GLOBALS['htmlHeader']));
                $html .= parseTemplate($error,  getTemplate($GLOBALS['htmlError']));
                $html .=  parseTemplate(['baseURL'=>$GLOBALS['baseURL']],  getTemplate($GLOBALS['htmlFooter']));
                print $html;
                break;
            case 'json':
            default:
                header ("Content-type: application/json");
                print json_encode($error, JSON_PRETTY_PRINT);
        }
        exit(1);
    }

    function sendData() {
	header ('Access-Control-Allow-Origin: *');
        foreach ($this->headerSet as $h) {
            header($h);
        }
        print $this->output;
    }
    
    function sendThroughData($file) {
	header ('Access-Control-Allow-Origin: *');
        foreach ($this->headerSet as $h) {
            header($h);
        }
        passthru("/bin/cat $file");
    }
}
