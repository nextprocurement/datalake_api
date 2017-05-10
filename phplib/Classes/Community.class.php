<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Community extends DataStore {
    
    const StoreDescription = 'Benchmarking communities';

    public $baseXMLTag = 'Commmunity';
    public $defaultOp = 'entry';
    public $addDefault = true;
    public $storeData = '';   
    
    public $templateFieldDefaults = [
        'search' => [
            '_id' => 'Acronym',
            'name' => 'Name',
            'status_id' => 'Status',
            'description' => 'Description',
            'linkMain' => 'URL'
        ],
    ];
    public $templateAllFields = [
        'search' => [
            '_id' => 'Acronym',
            'name' => 'Name',
            'status_id' => 'Status',
            'description' => 'Description',
            'linkMain' => 'URL'
        ]
    ];
    
    public $templateLinks = [
        '_id' => "<a href=\"##baseURL##/Community/##_id##.html\">##_id##</a>",
    ];
    
    public $templateArrayLinks = [
        'community_contacts' =>     "API:Contact",
        'BenchmarkingEventsList' => "API:BenchmarkingEvent",
        'DatasetList' =>            "API:Dataset",        
        'DatasetInputList' =>       "API:Dataset",        
        'DatasetOutputList' =>      "API:Dataset",        
        'DatasetOtherList' =>       "API:Dataset",        
        'toolsList' =>              "API:Tool",        
        'metricsList' =>            "API:Metrics",        
    ];
    
    public $classTemplate = 'file';
    
    public $textQueryOn = ["name" => 1, "_id" => 1, "description" => 1, "community_contacts" => 1];

    
    function getData($params, $checkId=true) {
        if (!isset($params->extended)) {
            $params->extended=0;
        }
        if (!isset($params->fmt)) {
            $params->fmt='json';
        }
        return $this->checkData(getCommunityData($params->id,$params->fmt,$params->extended), $params->id);
    }
    
}