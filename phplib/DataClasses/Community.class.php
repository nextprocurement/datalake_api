<?php

class Community extends DataStore {
    
    const StoreDescription = 'Benchmarking communities';

    function getData($params, $checkId=true) {
        $data = parent::getData($params);
        if (!isset($params->fmt)) {
            $params->fmt='json';
        }
        if ($this->error) {
            return '';
        }
        if (isset($params->simple) and $params->simple) {
            return $data;
        }
        foreach (array_keys($GLOBALS['cols']) as $colname) {
            if ($colname == 'Community') {
                continue;
            }   
            #$rex = new MongoDB\BSON\Regex($params->id, "i");
            if (isset($params->extended) and $params->extended) {
                $proj = [];
            } else {
                $proj = ['_id'];
            }
            $data1 = iterator_to_array($GLOBALS['cols'][$colname]->find(['community_id' => $params->id], ['projection' => $proj]));
            if (count($data1)) {
                $data[$colname] = $data1;
            }

        }
        $datasetList = [];            
        foreach ($data['Dataset'] as $dts) {
            $dtsData=getOneDocument('Dataset',$dts['_id']);
            $datasetList[$dtsData['type']][] = $dts['_id'];
        }
        $data['Dataset']=$datasetList;
        $testActionList = [];            
        foreach ($data['TestAction'] as $dts) {
            $dtsData=getOneDocument('TestAction',$dts['_id']);
            $testActionList[$dtsData['action_type']][] = $dts['_id'];
        }
        $data['TestAction']=$testActionList;
        unset($data['Contact']);
        foreach ($data['links'] as $lk) {
	   if ($lk['label'] == 'MainSite') {
		$data['MainSite'] = $lk['uri'];
	   }
        }
        if (preg_match('/htm/', $params->fmt)) {
            foreach ($data['BenchmarkingEvent'] as $be) {
                $data['BenchmarkingEventsList'][] = $be['_id'];
            }
            $data['linksList'] = [];
            foreach ($data['links'] as $lk) {
                $data['linksList'][] = $lk['label'] . ": " . setLinks($lk['uri']);
            }
            unset($data['links']);
            $data['toolsList'] = [];
            foreach ($data['Tool'] as $dts) {
                $data['toolsList'][] = $dts['_id'];
            }
            $data['metricsList'] = [];
            foreach ($data['Metrics'] as $dts) {
                $data['metricsList'][] = $dts['_id'];
            }
        }
        if (isset ($params->extended) and $params->extended) {
        }
        return $data;
    }
    
}
