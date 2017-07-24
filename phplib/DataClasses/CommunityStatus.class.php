<?php

class CommunityStatus extends DataStore {
    
    const StoreDescription = 'Benchmarking community status';
   
    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        if ($this->error) 
        {return '';};
       if (isset($params->simple) and $params->simple) {
            return $data;
        }
         $data['communities'] = iterator_to_array(findInDataStore('Community',['status_id' => $data['_id']], ['projection'=>['_id'=>1]]));
        return $data;
    }
        
}
