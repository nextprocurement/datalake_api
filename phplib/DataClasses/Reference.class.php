<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Reference extends DataStore {
    
    const StoreDescription = 'Benchmarking references';

//    function processId($params) { //fixing doi \/ as part of the id
//#        print_r($this->currentPath);
//        if ($this->currentPath) { // Provisional
//            $params->id .= "/".join("/",$this->currentPath);
//            $this->currentPath=[''];
//        }
//        
//        return $params;
//    }
    
    function getData($params, $checkId=true) {//TODO
        $data = parent::getData($params);
        if ($this->error) 
            {return '';};
        if (preg_match('/htm/',$params->fmt)) {
            foreach ($data['links'] as $l) {
                $data['linksList'][] = $l['label'].": ". setLinks($l['uri']);
            }
        }
        return $data;
    }   
}
