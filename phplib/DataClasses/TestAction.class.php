<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TestEvent extends DataStore {

    const StoreDescription = 'Benchmarking Test Events';

    function getData($params, $checkId = true) {
        $data = parent::getData($params);
        if ($this->error) {
            return '';
        };
        $prefix = explode(":", $data['_id']);
        $data['community_id'] = $prefix[0];
        if (preg_match("/htm/", $params->fmt)) {
            if (isset($data['result_report'])) {
                foreach ($data['result_report'] as $r) {
                    $data['reports'][] = "Status: " . $r['status'] . ", Date:" . $r['status_date'] . ", Report: " . $r['report'];
                }
            }
        }
        if (isset($params->extended)and $params->extended) {
            foreach (
            [ 'Tool' => 'tool_id',
                'Dataset' => 'input_dataset_id',
                'BenchmarkingEvent' => 'benchmarking_event_id'] as $col => $field) {
                $data[$col] = getOneDocument($col, $data[$field]);
            }
        }
        return $data;
    }

}
