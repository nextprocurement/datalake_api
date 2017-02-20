<?php

/**
 * Class to hold Text Templates for data output, only text, XML and Json come from STRUCT
 *
 * @author gelpi
 */
class TabTemplate {

    public $headerTempl;
    public $dataTempl;
    public $footerTempl;
    
    public function __construct ($fieldList = []) {
        $this->headerTempl = "#";
        if ($fieldList) {
            foreach ($fieldList as $f => $v) {
                $this->addField($f,$v);
            }
            $this->close();
        }
        return $this;
    }
    
    public function setListFields ($fieldStr) {
        if ($fieldStr) {
            foreach (explode(',',str_replace(' ','',$fieldStr)) as $f) {
                $this->addField($f); // TODO Labels
            }
            $this->close();
        }
        return $this;
    }
        
    public function addField ($field, $label='') {
        if (!$label) {
            $label = $field;
        }
        $this->headerTempl .= "$label\t";
        $this->dataTempl .= "##$field##\t";
        return $this;
    }
    
    public function close() {
        $this->headerTempl .= "\n";
        $this->dataTempl .= "\n";
    }
       
}

class fastaTemplate extends TabTemplate {
    public function __construct() {
        parent::__construct();
        $this->headerTempl='';
        $this->dataTempl=">##_id## ##header##\n##sequence##\n";
        return $this;
    }
}