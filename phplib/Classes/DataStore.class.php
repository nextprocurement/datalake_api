<?php

/**
 * Abstract for DataStore (PDB, Uniprot, PDBMonomers, ...)
 *
 * @author gelpi
 */
abstract class DataStore {

    public $id;
    public $output = [];
    public $defaultOp;
    public $currentPath;
    public $baseXMLTag;
    public $storeData;
    public $error = NOERROR;

    function __construct() {
        $this->id = get_class($this);
        return $this;
    }

    function processId($params) {
        //placeholder for getting specific data from $id;
        return $params;
    }

    function setStoreData($dir) {
        $this->storeData = $dir;
    }

    function getData($params) {
        //placeholder for getting data form the DB;
        $this->setError($this->id, NOTIMPLEMENTED);
        return;
    }

    function prepDataOutput($data) {
        //placeholder for getting formatting data if necessary;
        return $data;
    }

    function checkData($data, $id) {
        if (strtolower($data['_id']) != strtolower($id)) {
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
        $params->id = array_shift($this->currentPath);
        if ($params->id == 'info') {
            $this->output = $this->info($params);
        } elseif ($params->id == 'files') {
            if (!$this->storeData) {
                $this->setError('files', UNAVAILMETHOD);
                return $this;
            }
            $this->output = $this->files($params);
        } else {
            $params = $this->processId($params); // any processing required once id is known 
            if (!$params->id) { //api/{store}/ Search
                $this->output = $this->search($params);
            } else { //api/{store}/{id}/op/
                if (method_exists($this, $this->currentPath[0])) { //next keyword is a valid method, go ahead
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
                    if ($this->currentPath[0] == $op) { //remove defaultOp from URI, should never happen
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
        if (!$this->currentPath[0]) { // Full Entry Fields
            if (isset($params->fields)) {
                $data = selectArrayFields($data, $params->fields);
            }
            return [STRUCT, $data];
        } else { //Parse into Entry fields
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
        $this->setError($this->id . " search", NOTIMPLEMENTED);
        return;
    }

    function files($params) {
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
            return [STRUC, ['dir' => $fileName, 'files' => $dirFiles]];
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

}
