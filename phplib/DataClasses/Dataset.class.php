<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Dataset extends DataStore {
    
    const StoreDescription = 'Benchmarking Datasets';

    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        if ($this->error) 
            {return '';}
        if (isset($params->simple) and $params->simple) {
            return $data;
        }
        $data['challenges']=[];
        foreach (getFieldArray('Challenge', "community_id", $data['community_id']) as $chid) {
            $chData = getOneDocument('Challenge', $chid);
            foreach ($chData['dataset_ids'] as $dts) {
                if ($dts['_id'] == $data['_id']) {
                    $data['challenges'][] = $chData['_id'];
                }
            }
        }
        if (isset($params->extended) and $params->extended) {
            $data['contacts']=  findArrayInDataStore('Contact', $data['dataset_contact_id']);
            $data['references'] = findArrayInDataStore('Reference', $data['references']);
        }        
        if (preg_match("/htm/",$params->fmt)) {
            switch ($data['datalink']['attrs']){
                case "curie":
                  $data['datalink']="../idsolv/".$data['datalink']['uri'];
                  break;
                case "inline":
		  $data['data'] = $data['datalink']['uri'];
 	          unset($data['datalink']);
                  break;
                default:
                  $data['datalink']=$data['datalink']['uri'];
            }
            $data['relDatasetList']=[];
            foreach ($data['depends_on']['rel_dataset_ids'] as $rdts) {
                print_r($rdts['dataset_id']);
                $data['relDatasetList'][]=$rdts['dataset_id'];
            }
            if (!isset($data['depends_on'])) {
                $data['depends_on']='';
            }
        }       
        return $data;
    }
}
