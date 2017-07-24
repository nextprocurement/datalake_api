<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Metrics extends DataStore {
    
    const StoreDescription = 'Benchmarking metrics';
    
    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        if ($this->error) 
            {return '';};
        list($data['community_id'],$id) = explode(':',$data['_id']);
        if (preg_match('/htm/',$params->fmt)) {
            foreach ($data['references'] as $l) {
                $data['referencesList'][] = $l;
            }
            foreach ($data['links'] as $l) {
                $data['linksList'][] = $l['label'].": ". setLinks($l['uri']);
            }
        }
        if (isset($params->extended) and $params->extended) {
            $data['contacts'] = findArrayInDataStore('Contact', $data['metrics_contact_id']);
            unset($data['metrics_contact_id']);
            $data['references'] = findArrayInDataStore('Reference', $data['references']);
        }
        return $data;
    }
}
