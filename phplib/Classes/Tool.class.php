<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Tool extends DataStore {
    
    const StoreDescription = 'Benchmarked Tools';

    public $baseXMLTag = 'Tool';
    public $defaultOp = 'entry';
    public $addDefault = true;
    public $storeData = '';   
    
    public $templateFieldDefaults = [
        'search' => [
            '_id' => 'Id',
            'bioTools_id' => 'Bio.tools Id',
            'name' => 'Name',
            'description' =>'Description',
            'status_id' => 'Status'
        ],
    ];
    public $templateAllFields = [
            '_id' => 'Id',
            'bioTools_id' => 'Bio.tools Id',
            'name' => 'Name',
            'description' =>'Description',
            'status_id' => 'Status'
    ];
    
    public $templateLinks = [
        '_id' => "<a href=\"##baseURL##/Tool/##_id##.html\">##_id##</a>",
        'bioTools_id' => "<a href=\"https://bio.tools/tool/##bioTools_id##.html\">##bioTools_id##</a>",
    ];
    public $templateArrayLinks = [
        'community_id' => "<a href=\"##baseURL##/Community/##item##.html\">##item##</a>",
        'tool_contact_id' => "<a href=\"##baseURL##/Contact/##item##.html\">##item##</a>",
        'references' => "<a href=\"##baseURL##/Reference/##item##.html\">##item##</a>"
    ];
    
    public $classTemplate = 'file';

    
    function getData($params) {
        if (!isset($params->extended)) {
            $params->extended=0;
        }
        return $this->checkData(getToolData($params->id,$params->extended), $params->id);
    }
    
    static function info($params) {
        if (!isset($params->fmt)) {
            $params->fmt="json";
            $params->compact= false;
        }
        $data['Description'] = Tool::StoreDescription;        
        $data['Data'] = getToolInfo();
        return [STRUCT, $data];
    }
    
   
}
