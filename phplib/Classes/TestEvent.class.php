<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TestEvent extends DataStore {
    
    const StoreDescription = 'Benchmarking Test Events';

    public $baseXMLTag = 'TestEvent';
    public $defaultOp = 'entry';
    public $addDefault = true;
    public $storeData = '';   
    
    public $templateFieldDefaults = [
        'search' => [
            '_id' => 'Id',
        ],
    ];
    public $templateAllFields = [
            '_id' => 'Id',
    ];
    
    public $templateLinks = [
        '_id' => "<a href=\"##baseURL##/TestEvent/##_id##.html\">##_id##</a>",
    ];
    public $templateArrayLinks = [
        'CommunityList' => "<a href=\"##baseURL##/Community/##item##.html\">##item##</a>"
    ];
    
    public $classTemplate = 'file';
    
}