<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Community extends DataStore {
    
    const StoreDescription = 'Benchmarking communities';

    function getData($params, $checkId=true) {
        if (!isset($params->extended)) {
            $params->extended=0;
        }
        if (!isset($params->fmt)) {
            $params->fmt='json';
        }
        return $this->checkData(getCommunityData($params->id,$params->fmt,$params->extended), $params->id);
    }
    
}