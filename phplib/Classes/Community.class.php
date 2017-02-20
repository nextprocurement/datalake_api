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
    
    function getData($params) {
        return $this->checkData(getCommunityData($params->id,$params->extended), $params->id);
    }
    
    static function info($params) {
        if (!isset($params->fmt)) {
            $params->fmt="json";
            $params->compact= false;
        }
        $data['Description'] = Community::StoreDescription;        
        $data['Data'] = getCommunityInfo();
        return [STRUCT, $data];
    }
    
    function search($params) {
        if (!isset($params->queryOn)) {
            $params->queryOn = ["name" => 1, "_id" => 1, "description" => 1, "community_contacts" => 1];
        } else {
            $params->expand('queryOn', 'queryOn');
        }
        if (!isset($params->fields)) {
            $params->fields='';
        }
        switch ($params->fields) {
            case 'ids':
                $params->fields = '_id';
                $this->template = new TabTemplate(['_id' => 'Acronym']);
                break;
            case 'all':
                 // TODO definir llista
                break;
            case '':
                $params->fields = "_id,name,status_id,description,linkMain";
                $this->template = new TabTemplate($this->templateFieldDefaults['search']);
                break;
            default:
                $this->template = new TabTemplate();
                $this->template->setListFields($params->fields);
        }        
        $dataOut = searchCommunity((array) $params);        
        if (!isset($params->fmt) or ( $params->fmt == 'tab')) {
            return [TEXT, $this->_formatTArray($params, $dataOut)];
        } else {
            return [STRUCT, ['Communities' => $dataOut]];
        }
    }
}
