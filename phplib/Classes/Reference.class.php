<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Reference extends DataStore {
    
    const StoreDescription = 'Benchmarking references';

    public $baseXMLTag = 'Reference';
    public $defaultOp = 'entry';
    public $addDefault = true;
    public $storeData = '';   
    
    public $templateFieldDefaults = [
        'search' => [
            '_id' => 'Id',
            'title' =>'Title',
            'pubmed_id'=> 'PubMed Id'
        ],
    ];
    public $templateAllFields = [
            '_id' => 'Id',
            'title' =>'Title',
            'pubmed_id'=> 'PubMed Id'
    ];
    
    public $templateLinks = [
        '_id' => "<a href=\"##baseURL##/Reference/##_id##.html\">##_id##</a>",
        'pubmed_id' => "<a href=\"https://www.ncbi.nlm.nih.gov/pubmed/##pubmed_id##\">##pubmed_id##</a>",
    ];
    public $templateArrayLinks = [
        'CommunityList' => "<a href=\"##baseURL##/Community/##item##.html\">##item##</a>"
    ];
    
    public $classTemplate = 'file';

    function processId($params) { //fixing doi \/ as part of the id
        $params->id = str_replace('_','/',$params->id);
        return $params;
    }
    
    function getData($params) {
        if (!isset($params->extended)) {
            $params->extended=0;
        }
        return $this->checkData(getReferenceData($params->id,$params->extended), $params->id);
    }
    
    static function info($params) {
        if (!isset($params->fmt)) {
            $params->fmt="json";
            $params->compact= false;
        }
        $data['Description'] = Reference::StoreDescription;        
        $data['Data'] = getReferenceInfo();
        return [STRUCT, $data];
    }
        
}
