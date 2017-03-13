<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class BenchmarkingEvent extends DataStore {
    
    const StoreDescription = 'Benchmarking Events';

    public $baseXMLTag = 'BenchmarkingEvent';
    public $defaultOp = 'entry';
    public $addDefault = true;
    public $storeData = '';   
    
    public $templateFieldDefaults = [
        'search' => [
            '_id' => 'Community:Id',
            'name' => 'Event',
            'is_automated' => 'Automated?',
            'dates.benchmark_start' => 'Start',
            'dates.benchmark_stop' => 'End',
            'url' => 'URL',
            'referencesList' => 'References'
        ],
    ];
    public $templateAllFields = [
            '_id' => 'Community:Id',
            'name' => 'Event',
            'is_automated' => 'Automated?',
            'dates.benchmark_start' => 'Start',
            'dates.benchmark_stop' => 'End',
            'url' => 'URL',
            'referencesList' => 'References'
    ];
    
    public $templateLinks = [
        '_id' => "<a href=\"##baseURL##/BenchmarkingEvent/##_id##.html\">##_id##</a>",
    ];
    public $templateArrayLinks = [        
        'bench_contact_id' => "<a href=\"##baseURL##/Contact/##item##.html\">##item##</a>",
        'references' => "<a href=\"https://dx.doi.org/##item##\">##item##</a>",
        'target-list' => "<a href=\"##baseURL##/Dataset/##item##.html\">##item##</a>",
    ];
    
    public $classTemplate = 'file';
    
    public $textQueryOn=['_id'=>1, 'name'=> 1];
   
}
