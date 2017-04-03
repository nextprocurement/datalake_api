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
    
    public $textQueryOn = ['_id'=>1,'name'=>1,'description'=>1,'status_id'=>1];

    function getData ($params, $checkId=true) {
        $data = parent::getData($params);
        if ($this->error) 
            {return '';};
        if (preg_match('/htm/',$params->fmt) ) {
            $data['accesslinkList']=[];
            foreach ($data['tool_access'] as $lk) {
                $data['accesslinkList'][] = $lk['tool_access_type_id'].": ".setLinks($lk['link']);
            }
        }
        if (isset($params->extended) and $params->extended) {
            foreach ([
                'Community' => 'community_id',
                'Contact' =>'tool_contact_id',
                'Reference' => 'references'
            ] as $col => $field) {
                $data[$col] = findArrayInDataStore($col, $data[$field]);
               unset($data[$field]);
            }
            foreach (['ToolStatus'=> 'status_id'] as $col=>$field) {
                $data[$col] = getOneDocument($col, $data[$field]);
                unset($data[$field]);
            }
        }
        return $data;
    }
   
}
