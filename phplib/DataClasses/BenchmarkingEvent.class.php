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
        $data['InputDatasets']=[];
        $data['OutputDatasets']=[];
        foreach (iterator_to_array(findInDataStore('TestEvent', ['benchmarking_event_id' => $data['_id']], [])) as $te) {
            $data['TestEvent'][] = $te['_id'];
            $data['tools'][] = $te['tool_id'];
            $data['InputDatasets'][] = $te['input_dataset_id'];
            if (isset($te['output_dataset_id'])) {
                $data['OutputDatasets'][] = $te['output_dataset_id'];
            }
            if (isset($te['output_dataset_id'])) {
                $data['OutputDatasets'][] = $te['output_dataset_id'];
            }
        }
        $data['tools'] = array_values(array_unique($data['tools']));
        $data['InputDatasets'] = array_values(array_unique($data['InputDatasets']));
        $data['OutputDatasets'] = array_values(array_unique($data['OutputDatasets']));
        $data['dataLinks']=[];
        foreach (array_merge($data['InputDatasets'],$data['OutputDatasets']) as $od) {
            $dts = getOneDocument('Dataset', $od);
            $data['dataLinks'][]=['id'=>$od,'link' => $dts['datalink']];
        }
        $data['metricsSummary'] = sumMetrics($data);
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
            $data['dataLinksHTML'] = '';
            foreach ($data['dataLinks'] as $dk) {
                $data['dataLinksHTML'] .= setLinks(parseTemplate($dk,$this->otherTemplates['datalink']));
            }
            $data['metricsSummaryHTML'] = prepMetricsHTML($data, $this->otherTemplates['MS']);
        }
        return $data;
    }

}
