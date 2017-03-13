<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ToolStatus extends DataStore {
    
    const StoreDescription = 'Benchmarking Tool Status';

    public $baseXMLTag = 'ToolStatus';
    public $defaultOp = 'entry';
    public $addDefault = true;
    public $storeData = '';   
    
    public $templateFieldDefaults = [
        'search' => [
            '_id' => 'Id',
            'description' =>'Description'
        ],
    ];
    public $templateAllFields = [
            '_id' => 'Id',
            'description' =>'Description'
    ];
    
    public $templateLinks = [
        '_id' => "<a href=\"##baseURL##/ToolStatus/##_id##.html\">##_id##</a>",
    ];
    public $templateArrayLinks = [
        'CommunityList' => "<a href=\"##baseURL##/Community/##item##.html\">##item##</a>"
    ];
    
    public $textQueryOn = ['_id'=>1,'description'=>1];
    
    function getData($params) {
        $data = parent::getData($params);
        $data['Tools']= iterator_to_array(findInDataStore('Tool', ['status_id' => $data['_id']], ['projection'=>['_id']]));
        return $data;
    }
 
}
