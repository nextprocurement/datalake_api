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

    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        if (isset($params->extended)and $params->extended) {
            foreach (
            ['Tool' => 'tool_id',
                'Dataset' => 'input_dataset_id',
                'BenchmarkingEvent' => 'benchmarking_event_id'] as $col => $field) {
                $data[$col] = getDataGeneric($col, $data[$field]);
            }
        }

        return $data;
    }

}
