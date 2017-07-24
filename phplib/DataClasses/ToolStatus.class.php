<?php

class ToolStatus extends DataStore {
    
    const StoreDescription = 'Benchmarking Tool Status';

    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        if ($this->error) {
            return '';
        }
        if (isset($params->simple) and $params->simple) {
            return $data;
        }
        $data['Tools']= iterator_to_array(findInDataStore('Tool', ['status_id' => $data['_id']], ['projection'=>['_id']]));
        return $data;
    }
 
}
