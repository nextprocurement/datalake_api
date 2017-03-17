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
        return $this->setListFields($fieldList);
    }
    
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
    public function __construct($fieldList = [], $custom = []) {
        $this->headerTempl = "<div class=\"panel\">".
                "<h1>##title##</h1>".
                "<span class=\"label label-default\"><a style=\"color:#fff\" href=\"##baseURL##/##table_id##.tsv?##params##\" target=\"_blank\">TSV</a></span>\n".
                "<span class=\"label label-primary\"><a style=\"color:#fff\" href=\"##baseURL##/##table_id##.json?##params##\" target=\"_blank\">JSON</a></span>\n".
                "<span class=\"label label-warning\"><a style=\"color:#fff\" href=\"##baseURL##/##table_id##.xml?##params##\" target=\"_blank\">XML</a></span>\n".
                "<table class=\"table bgblank\" id=\"##table_id##\">\n<tr>\n";
        $this->footerTempl="</tr>\n</table>\n</div>";
        return $this->setListFields($fieldList, $custom);
    }
    
    public function setListFields ($fieldList=[], $custom=[]) {
       if ($fieldList) {
            foreach ($fieldList as $f => $v) {
                if (!isset($custom[$f])) {
                    $custom[$f]= '';
                }
                $this->addField($f,$v, $custom[$f]);
            }
            $this->close();
        }
        return $this;
    }
    public function addField ($field, $label='', $customTempl='') {
        if (!$label) {
            $label = $field;
        }
        $this->headerTempl .= "<th>$label</th>\n";
        if ($customTempl) {
           $this->dataTempl.= "<td>$customTempl</td>";
        } else {
            $this->dataTempl .= "<td>##$field##</td>\n";
        }
        return $this;
    }
    public function close() {
        $this->headerTempl .= "</tr>\n";
        $this->dataTempl .= "\n";
    }
}