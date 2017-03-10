<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ToolStatus extends DataStore {
    
    const StoreDescription = 'Benchmarking Tool Status';

    public $baseXMLTag = 'ToolStatus';
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
        '_id' => "<a href=\"##baseURL##/ToolStatus/##_id##.html\">##_id##</a>",
    ];
    public $templateArrayLinks = [
        'CommunityList' => "<a href=\"##baseURL##/Community/##item##.html\">##item##</a>"
    ];
    
    public $classTemplate = 'file';

    
    function getData($params) {
        if (!isset($params->extended)) {
            $params->extended=0;
        }
        return $this->checkData(getToolStatusData($params->id,$params->extended), $params->id);
    }
    
    static function info($params) {
        if (!isset($params->fmt)) {
            $params->fmt="json";
            $params->compact= false;
        }
        $data['Description'] = ToolStatus::StoreDescription;        
        $data['Data'] = getToolStatusInfo();
        return [STRUCT, $data];
    }
    
 
}
