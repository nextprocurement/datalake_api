<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ToolAccessType extends DataStore {
    
    const StoreDescription = 'Benchmarking Tools Access types';

    public $baseXMLTag = 'ToolAccessType';
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
        '_id' => "<a href=\"##baseURL##/ToolsAccessType/##_id##.html\">##_id##</a>",
    ];
    public $templateArrayLinks = [
    ];
    
    public $textQueryOn= ['_id'=>1,'description'=>1];
    
    function getData($params) {
        $data = parent::getData($params);
        $data['Tools']= iterator_to_array(findInDataStore('Tool', ['tool_access.tool_access_type_id' => $data['_id']], ['projection'=>['_id']]));
        return $data;
    }
    
}
