<?php

/**
 * Abstract for DataStore (PDB, Uniprot, PDBMonomers, ...)
 *
 * @author gelpi
 */
abstract class DataStore {

    public $id;
    public $output = [];
    public $defaultOp = '';
    public $joinFullPath = false;
    public $currentPath;
    public $baseXMLTag;
    public $storeData;
    public $error = NOERROR;
    
    public $templateFieldDefaults = [];
    public $templateAllFields = [];
    public $templateLinks = [];
    public $templateArrayLinks = [];
    public $classTemplate = '';
    
    public $textQueryOn=['_id'=>1];
    
    function __construct() {
        $this->id = get_class($this);
        $this->readConfig();
        return $this;
    }

    function readConfig() {
        $cfgjson = file_get_contents($GLOBALS['config']."/".$this->id.".cfg.json");
        $cfg = json_decode($cfgjson, true);
        foreach (array_keys($cfg) as $ff) {
            $this->$ff = $cfg[$ff];
        }        
        return $this;
    }
    

    function processId($params) {
        //placeholder for getting specific data from $id;
        if ($this->joinFullPath) {
            if ($this->currentPath) { 
                $params->id .= "/".join("/",$this->currentPath);
                $this->currentPath=[''];
            }
        }
        return $params;
    }

    function setStoreData($dir) {
        $this->storeData = $dir;
    }
    
    function getData($params, $checkId=true) {
          if ($checkId) {
            return $this->checkData(getOneDocument($this->id, $params->id),$params->id);
        } else {
            return getOneDocument($this->id, $params->id);
        }
    }

    function prepDataOutput($data) {
        //placeholder for getting formatting data if necessary;
        return $data;
    }

    static function info($store='',$params='') {
        if (!isset($params->fmt)) {
            $params->fmt="json";
         //   $params->compact= false;
        }
        $data['Description'] = $store::StoreDescription;        
        $data['Data'] = getGenericInfo('BenchmarkingEvent');
        return [STRUCT, $data];
    }

    function checkData($data, $id) {
        if (!isset($data['_id']) or (strtolower($data['_id']) != strtolower($id))) {
            $this->setError($id, IDNOTFOUND);
            return;
        } else {
            return $data;
        }
    }

    function setError($str, $errorId) {
        $this->error = [$str, $errorId];
    }

    function processData($params) {
//recover colon for prefixing escaped in App
        $params->id = str_replace('__',':',array_shift($this->currentPath));
        // Accept Boolean on extended
        if (isset($params->extended) and ($params->extended=='false')) {
            $params->extended=0;
        }
        switch ($params->id) {
            case 'info':
                $this->output = $this->info($this->id,$params);
                break;
            case 'files':
                if (!$this->storeData) {
                    $this->setError('files', UNAVAILMETHOD);
                    return $this;
                }
                $this->output = $this->files($params);
                break;
            case 'search': // Equivalent to empty id
                $params->id='';
            default:
                $params = $this->processId($params); // any processing required once id is known 
                if (!$params->id) { //api/{store}/ Search
                    $this->output = $this->search($params);
                } else { //api/{store}/{id}/op/
                    if (count($this->currentPath) and method_exists($this, $this->currentPath[0])) { //next keyword is a valid method, go ahead
                        $op = array_shift($this->currentPath);
                    } else { //default methods
                        if (!$this->defaultOp) {
                            $this->setError($this->id, NODEFOP);
                            return $this;
                        }
                        if (!method_exists($this, $this->defaultOp)) { // this should never happen
                            $this->setError($this->defaultOp, UNAVAILMETHOD);
                            return $this;
                        }
                        $op = $this->defaultOp;
                        if (count($this->currentPath) and $this->currentPath[0] == $op) { //remove defaultOp from URI, should never happen
                            array_shift($this->currentPath);
                        }
                    }
                    $this->output = $this->$op($params);
                }
        }
        return $this;
    }
// Functions

    function entry($params) {
        // Full entry from DB as Data Structure, allow nested tags
        $data = $this->getData($params);
        if (!isset($params->fmt)) {
            $params->fmt = 'json';
        }
        if (!count($this->currentPath) or !$this->currentPath[0]) { // Full Entry Fields
            if (isset($params->fields)) {
                $data = selectArrayFields($data, $params->fields);
            }
            switch ($params->fmt) {
                case 'htm':
                case 'html':
                    return [HTML, $this->_formatHTML($params, $data,'object')]; 
                    break;
                default:
                    return [STRUCT, $data];
            }
        } else { //Parse into Entry fields (only json/xml no html)
            $parse_result = parseArrayStruc($data, $this->currentPath, (array) $params);
            $ops = join(".", $parse_result['ops']);
            $this->baseXMLTag .= "_" . join("_", $parse_result['ops']);
            if (count($parse_result['query'])) {
                return [STRUCT, ['_id' => $params->id, 'options' => $parse_result['query'], $ops => $parse_result['data']]];
            } else {
               return [STRUCT, ['_id' => $params->id, $ops => $parse_result['data']]];
            }
        }
    }

   function search($params) {
        if (!isset($params->queryOn)) {
            $params->queryOn = $this->textQueryOn;
        } else {
            $params->expand('queryOn', 'queryOn');
        }
        if (!isset($params->fields)) {
            $params->fields='';
        }
        if (isset($params->fmt) and preg_match('/htm/',$params->fmt)) {
            $this->template = new htmlTabTemplate();
        } else {
            $this->template = new TabTemplate();
        }        
        switch ($params->fields) {
            case 'ids':
                $params->fields = '_id';
                $this->template->setListFields(['_id' => 'Id'],$this->templateLinks);
                break;
            case 'all':
                $params->fields = join (",",array_keys($this->templateAllFields));
                $this->template->setListFields($this->templateAllFields,$this->templateLinks);
                break;
            default:
                $params->fields = join (",",array_keys($this->templateFieldDefaults['search']));
                $this->template->setListFields($this->templateFieldDefaults['search'],$this->templateLinks);
                break;
        }        
        if (isset($params->sort)) {
            $sort=[];
            foreach (explode (",",$params->sort) as $ss) {
                $sort[$ss]=1;
            }
        } else {
            $sort=['_id'=>1];
        }
        $dataOut = searchGeneric($this->id,(array) $params, $sort);        
        if (!isset($params->fmt)) {
            $params->fmt='tsv';
        }
        switch ($params->fmt) {
            case 'tsv':
                return [TEXT, $this->_formatTArray($params, $dataOut)];
                break;
            case 'html':
            case 'htm':
                return [HTML, $this->_formatHTML($params, $dataOut,'tsv')];
                break;
            default:
                return [STRUCT, [$this->baseXMLTag => $dataOut]];
        }
    }

    function files($params) {
        if (!$this->storeData) {
              $this->setError('files', UNAVAILMETHOD);
             return $this;
        }
        $fileName = join("/", $this->currentPath);
        $discFn = $this->storeData . "/$fileName";
        if (!file_exists($discFn)) {
            $this->setError($fileName, NOTFOUND);
        }
        if (!is_dir($discFn)) {
            return [RAW, ['file' => $fileName]];
        } else {
            $dirFiles = [];
            foreach (scanDir($discFn) as $f) {
                if (substr($f, 0, 1) != '.') {
                    $dirFiles[] = $f;
                }
            }
            return [STRUCT, ['dir' => $fileName, 'files' => $dirFiles]];
        }
    }

    protected function _formatTArray($params, $data) {
        if (isset($data['prolog'])) {
            $dataProlog = $data['prolog'];
            unset($data['prolog']);
        } else {
            $dataProlog = '';
        }
        $dataTab = '';
        if (!isset($params->noheaders)) {
            $dataTab = $dataProlog . parseTemplate([], $this->template->headerTempl);
        }
        foreach ($data as $lin) {
            $dataTab .= parseTemplate($lin, $this->template->dataTempl);
        }
        $dataTab .= parseTemplate([], $this->template->footerTempl);
        return $dataTab;
    }
    

    protected function _formatHTML($params, $data, $type,$removePrefix=true) {
        $html = parseTemplate(
                ['baseURL'=>$GLOBALS['baseURL'],
                    'title'=>$this->id
                ],  getTemplate($GLOBALS['htmlHeader']));
        switch ($type) {
            case 'tsv': 
                $html .= parseTemplate([
                            'baseURL'=>$GLOBALS['baseURL'], 
                            'title'=>$this->id, 
                            'table_id'=>$this->id, 
                            'params'=>$params->toQueryString()
                        ], 
                        $this->template->headerTempl);
                foreach ($data as $lin) {
                    $lin['baseURL'] = $GLOBALS['baseURL'];
                    $html .= "<tr>\n". setLinks(parseTemplate($lin, $this->template->dataTempl))."</tr>\n";
                }
                $html.= parseTemplate([],$this->template->footerTempl);
                break;
            case 'object':
                $data['baseURL']=$GLOBALS['baseURL'];
                // Replace selected fields by the appropriate links on arrays, single fields links should be in the template
                foreach (array_keys($this->templateArrayLinks) as $k) {
                    if (!isset($data[$k])){
                        continue;
                    }
                    if (is_array($data[$k])) {
                        $template = $this->_prepLinkTemplate($k);
                        $newArray=[];
                        for ($i=0;$i<count($data[$k]); $i++) {
                            if (is_array($data[$k][$i])) {
                                $data[$k][$i]['baseURL']=$GLOBALS['baseURL'];
                                $newArray[] = parseTemplate($data[$k][$i], $template);
                            } else {
                            $newArray[] = str_replace('##item##',$data[$k][$i],$template);
                        }
                        }
                        $data[$k] = $newArray;
                    }
                }
                $html .= parseTemplate($data, $this->classTemplate);
                break;
        }
        $html .= parseTemplate(['baseURL'=>$GLOBALS['baseURL']],  getTemplate($GLOBALS['htmlFooter']));
        if ($type == 'tsv') {
            $html .= "\n<script type=\"text/javascript\">\n$(document).ready(function(){\n$('#".$this->id."').DataTable();\n});\n</script>\n";
        }
        if ($removePrefix and isset($data['_id'])) {// Remove Prefixes from shown data, URLs unchanged
            $prefix = explode (":",$data['_id']);
            if ($prefix[0]) {
                $html = str_replace (">$prefix[0]:",">",$html);
            }
        }
        return $html;        
    }
    
    function _prepLinkTemplate($k) {
        $tmpdata = explode (":",$this->templateArrayLinks[$k]);
        if (!isset($tmpdata[2])) {
            $tmpdata[2] = "item";
        }
        if (!isset($tmpdata[3])) {
            $tmpdata[3] = "item";
        }
        switch ($tmpdata[0]) {
            case 'API' : 
                return "<a href=\"##baseURL##/$tmpdata[1]/##$tmpdata[2]##.html\">##$tmpdata[3]##</a>";
                break; 
            case 'APIObj' : 
                return "<a href=\"##baseURL##/$tmpdata[1]/##$tmpdata[2]##.html\">##$tmpdata[3]## (##$tmpdata[2]##)</a>";
                break; 
            case 'DOI' :
                return "<a href=\"##baseURL##/idsolv/DOI:##item##\" target=\"_blank\">##item##</a>";
                break;
            case 'PM' :
                return "<a href=\"##baseURL##/idsolv/PM:##item##\" target=\"_blank\">##item##</a>";
            default:
                return $this->templateArrayLinks[$k];
        }
    }

}
