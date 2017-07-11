<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CommunityStatus extends DataStore {
    
    const StoreDescription = 'Benchmarking community status';
   
    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        if ($this->error) 
        {return '';};
        $data['communities'] = iterator_to_array(findInDataStore('Community',['status_id' => $data['_id']], ['projection'=>['_id'=>1]]));
        return $data;
    }
        
}
