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
        'TestEvent' => "<a href=\"##baseURL##/TestEvent/##item##.html\">##item##</a>",
        'tools' => "<a href=\"##baseURL##/Tool/##item##.html\">##item##</a>",
    ];
    
    public $classTemplate = 'file';
    
    public $textQueryOn=['_id'=>1, 'name'=> 1];
   
    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        foreach (iterator_to_array(findInDataStore('TestEvent', ['benchmarking_event_id' => $data['_id']],[])) as $te) {
            $data['TestEvent'][]=$te['_id'];
            $data['tools'][] = $te['tool_id'];
        }
        print_r($data['tools']);
        $data['tools']= array_values(array_unique($data['tools']));
        if (isset($params->extended) and $params->extended) {
            $data['bench_contacts']=[];
            foreach ($data['bench_contact_id'] as $c) {
                $data['bench_contacts'][] = getOneDocument('Contact', $c);
            }
            unset($data['bench_contact_id']);
            $data['referencesList'] = [];
            foreach ($data['references'] as $r) {
                $data['referencesList'][] = getOneDocument('Reference',$r);
            }
            unset ($data['references']);
        }
        return $data;
    }
}
