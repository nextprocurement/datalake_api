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
//        if ($fieldList) {
//            foreach ($fieldList as $f => $v) {
//                $this->addField($f,$v);
//            }
//            $this->close();
//        }
        return $this->setListFields($fieldList);
    }
    
//    public function setListFields ($fieldStr) {
//        if ($fieldStr) {
//            foreach (explode(',',str_replace(' ','',$fieldStr)) as $f) {
//                $this->addField($f); // TODO Labels
//            }
//            $this->close();
//        }
//        return $this;
//    }
    
    public function setListFields ($fieldList=[]) {
       if ($fieldList) {
            foreach ($fieldList as $f => $v) {
                $this->addField($f,$v);
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
        $this->headerTempl='<table><tr><th>';
        $this->dataTempl=">##_id## ##header##\n##sequence##\n";        
        return $this;
    }
}

class htmlTabTemplate extends TabTemplate {
    public function __construct($fieldList = []) {
        $this->headerTempl = "<table class=\"table table.striped table.hover\" id='##table_id##'><tr>";
        if ($fieldList) {
            foreach ($fieldList as $f => $v) {
                $this->addField($f,$v);
            }
            $this->close();
        }
        $this->footerTempl="</tr></table>";
        return $this;
    }
    public function addField ($field, $label='') {
        if (!$label) {
            $label = $field;
        }
        $this->headerTempl .= "<th>$label</th>";
        $this->dataTempl .= "<td>##$field##</td>";
        return $this;
    }
    public function close() {
        $this->headerTempl .= "</tr>";
        $this->dataTempl .= "\n";
    }
}