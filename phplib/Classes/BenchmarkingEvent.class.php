<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class BenchmarkingEvent extends DataStore {
    
    const StoreDescription = 'Benchmarking Events';
   
    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        if ($this->error) 
            {return '';};
      foreach (iterator_to_array(findInDataStore('TestEvent', ['benchmarking_event_id' => $data['_id']],[])) as $te) {
            $data['TestEvent'][]=$te['_id'];
            $data['tools'][] = $te['tool_id'];
            $data['InputDatasets'][]= $te['input_dataset_id'];
        }
        $data['tools']= array_values(array_unique($data['tools']));
        $data['InputDatasets']= array_values(array_unique($data['InputDatasets']));
        if (isset($params->extended) and $params->extended) {
            $data['bench_contacts']=[];
            foreach ($data['bench_contact_id'] as $c) {
                $data['bench_contacts'][] = getOneDocument('Contact', $c);
            }
            unset($data['bench_contact_id']);
            $data['referencesList'] = [];
            foreach ($data['references'] as $r) {
                $data['referencesList'][] = getOneDocument('Reference',$r);
            }
            unset ($data['references']);
        }
        return $data;
    }
}
