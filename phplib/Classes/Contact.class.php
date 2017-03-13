<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Contact extends DataStore {
    
    const StoreDescription = 'Benchmarking community contacts';

    public $baseXMLTag = 'Contact';
    public $defaultOp = 'entry';
    public $addDefault = true;
    public $storeData = '';   
    
    public $templateFieldDefaults = [
        'search' => [
            '_id' => 'Id',
            'surname' => 'Surname',
            'givenName' => 'Given Name',
            'email' => 'Email',
            'notes' => 'Notes'
        ],
    ];
    public $templateAllFields = [
            '_id' => 'Id',
            'surname' => 'Surname',
            'givenName' => 'Given Name',
            'email' => 'Email',
            'notes' => 'Notes',
    ];
    
    public $templateLinks = [
        '_id' => "<a href=\"##baseURL##/Contact/##_id##.html\">##_id##</a>",
    ];
    public $templateArrayLinks = [
        'CommunityList' => "<a href=\"##baseURL##/Community/##item##.html\">##item##</a>"
    ];
    
    public $classTemplate = 'file';
    
    public $textQueryOn = ["givenName", "_id", "surname", "notes"];
                
}
