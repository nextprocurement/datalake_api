<?php

class Contact extends DataStore {

    const StoreDescription = 'Benchmarking community contacts';

    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        if ($this->error) 
            {return '';};
        if (!isset($params->fmt)) {
            $params->fmt='json';
        }
        if (isset($params->simple) and $params->simple) {
            return $data;
        }
 
        if (preg_match("/htm/",$params->fmt) or (isset($params->extended) and $params->extended )) {
            foreach (
            [
                'BenchmarkingEvent' => 'bench_contact_id',
                'Community' => 'community_contacts',
                'Dataset' => 'dataset_contact_id',
                'Metrics' => 'metrics_contact_id',
                'Tool' => 'tool_contact_id'
            ] as $col => $field) {
                $data[$col] = getFieldArray($col, $field, $data['_id']);
            }
            $data['linksList'] = [];
                foreach ($data['links'] as $lk) {
                    $data['linksList'][] = $lk['label'] . ": " . setLinks($lk['uri']);
            }
        }
        return $data;
    }

}
