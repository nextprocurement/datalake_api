<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Metrics extends DataStore {
    
    const StoreDescription = 'Benchmarking metrics';

    public $baseXMLTag = 'Metrics';
    public $defaultOp = 'entry';
    public $addDefault = true;
    public $storeData = '';   
    
    public $templateFieldDefaults = [
        'search' => [
            '_id' => 'Id',
            'title' => 'Title',
            'description' => 'Description'
        ],
    ];
    public $templateAllFields = [
            '_id' => 'Id',
            'title' => 'Title',
            'description' => 'Description'
    ];
    
    public $templateLinks = [
        '_id' => "<a href=\"##baseURL##/Metrics/##_id##.html\">##_id##</a>",
    ];
    public $templateArrayLinks = [
        'metrics_contact_id' => "<a href=\"##baseURL##/Contact/##item##.html\">##item##</a>",
        'references' => "<a href=\"##baseURL##/Reference/##item##.html\">##item##</a>"
    ];
    
    public $classTemplate = 'file';

    
    function getData($params) {
        if (!isset($params->extended)) {
            $params->extended=0;
        }
        return $this->checkData(getMetricsData($params->id,$params->extended), $params->id);
    }
    
    static function info($params) {
        if (!isset($params->fmt)) {
            $params->fmt="json";
            $params->compact= false;
        }
        $data['Description'] = Metrics::StoreDescription;        
        $data['Data'] = getMetricsInfo();
        return [STRUCT, $data];
    }
}
