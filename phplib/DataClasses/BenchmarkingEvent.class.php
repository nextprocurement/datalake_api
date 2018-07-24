<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class BenchmarkingEvent extends DataStore {

    const StoreDescription = 'Benchmarking Events';

    function getData($params, $checkId = true) {
        $data = parent::getData($params);
        if ($this->error) {
            return '';
        };
        $data['Challenges']= getFieldArray('Challenge', 'benchmarking_event_id', $data['_id']);

        if (isset($params->extended) and $params->extended) {
            $data['bench_contacts'] = [];
            foreach ($data['bench_contact_id'] as $c) {
                $data['bench_contacts'][] = getOneDocument('Contact', $c);
            }
            unset($data['bench_contact_id']);
            $data['referencesList'] = [];
            foreach ($data['references'] as $r) {
                $data['referencesList'][] = getOneDocument('Reference', $r);
            }
            unset($data['references']);
        }
        if (isset($params->fmt) and preg_match("/htm/",$params->fmt)) {
        }
        return $data;
    }

}
