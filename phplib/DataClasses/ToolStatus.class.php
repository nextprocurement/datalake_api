<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ToolStatus extends DataStore {
    
    const StoreDescription = 'Benchmarking Tool Status';

    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        if ($this->error) 
            {return '';};
        $data['Tools']= iterator_to_array(findInDataStore('Tool', ['status_id' => $data['_id']], ['projection'=>['_id']]));
        return $data;
    }
 
}
