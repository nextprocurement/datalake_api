<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CommunityStatus extends DataStore {
    
    const StoreDescription = 'Benchmarking community status';

    public $baseXMLTag = 'CommunityStatus';
    public $defaultOp = 'entry';
    public $addDefault = true;
    public $storeData = '';   
    
    public $templateFieldDefaults = [
        'search' => [
            '_id' => 'Id',
            'description' => 'Description'
        ],
    ];
    public $templateAllFields = [
            '_id' => 'Id',
            'description' => 'Description'
    ];
    
    public $templateLinks = [
        '_id' => "<a href=\"##baseURL##/CommunityStatus/##_id##.html\">##_id##</a>",
    ];
 
    public $templateArrayLinks = [
    ];
    
    function getData($params) {
        $data = parent::getData($params);
        $data['communities'] = iterator_to_array(findInDataStore('Community',['status_id' => $data['_id']], ['projection'=>['_id'=>1]]));
        return $data;
    }
        
}
