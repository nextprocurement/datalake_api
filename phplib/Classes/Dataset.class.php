<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Dataset extends DataStore {
    
    const StoreDescription = 'Benchmarking Datasets';

    public $baseXMLTag = 'Dataset';
    public $defaultOp = 'entry';
    public $addDefault = true;
    public $storeData = '';   
    
    public $templateFieldDefaults = [
        'search' => [
            "_id"=> "Community:Id",
            "name" => "Name",
            "type" => "type",
            "description"=> "Description",
        ]
    ];
    public $templateAllFields = [
            "_id"=> "Community:Id",
            "name" => "Name",
            "type" => "type",
            "description"=> "Description",
    ];
    
    public $templateLinks = [
        '_id' => "<a href=\"##baseURL##/Dataset/##_id##.html\">##_id##</a>",
    ];
    public $templateArrayLinks = [
        'dataset_contact_id' => "<a href=\"##baseURL##/Contact/##item##.html\">##item##</a>",
        'event' => "<a href=\"##baseURL##/BenchmarkingEvent/##item##.html\">##item##</a>",
    ];
    
    public $classTemplate = 'file';
    public $textQueryOn = ['_id'=>1,'name'=>1,'description'=>1];
    
    function getData($params) {
        $data = parent::getData($params);
        if (isset($params->extended) and $params->extended) {
            $data['contacts']=  findArrayInDataStore('Contact', $data['dataset_contact_id']);
            $data['references'] = findArrayInDataStore('Reference', $data['references']);
        }
        return $data;
    }
}
