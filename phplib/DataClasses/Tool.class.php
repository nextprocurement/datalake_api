<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Tool extends DataStore {
    
    const StoreDescription = 'Benchmarked Tools';

    function getData ($params, $checkId=true) {
        $data = parent::getData($params);
        if ($this->error) 
            {return '';};
        if (preg_match('/htm/',$params->fmt) ) {
            $data['accesslinkList']=[];
            foreach ($data['tool_access'] as $lk) {
                $data['accesslinkList'][] = $lk['tool_access_type_id'].": ".setLinks($lk['link']);
            }
            $data['description']=  setLinks($data['description']);
        }
        if (isset($params->simple) and $params->simple) {
            return $data;
        }
        if (isset($params->extended) and $params->extended) {
            foreach ([
                'Community' => 'community_id',
                'Contact' =>'tool_contact_id',
                'Reference' => 'references'
            ] as $col => $field) {
                $data[$col] = findArrayInDataStore($col, $data[$field]);
               unset($data[$field]);
            }
            foreach (['ToolStatus'=> 'status_id'] as $col=>$field) {
                $data[$col] = getOneDocument($col, $data[$field]);
                unset($data[$field]);
            }
        }
        return $data;
    }
   
}
