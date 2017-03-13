<?php

function getCommunityData($id, $fmt='', $extended = false) {

    $data = $GLOBALS['cols']['Community']->findOne(['_id' => $id]);
    foreach (array_keys($GLOBALS['cols']) as $colname) {
        if ($colname == 'Community') {
            continue;
        }
        $rex = new MongoDB\BSON\Regex($id, "i");
        if ($extended) {
            $proj = [];
        } else
            $proj = ['_id'];
        $data1 = iterator_to_array($GLOBALS['cols'][$colname]->find(['_id' => $rex], ['projection' => $proj]));
        if (count($data1)) {
            $data[$colname] = $data1;
        }
    }
    if (preg_match('/htm/', $fmt)) {
        foreach ($data['BenchmarkingEvent'] as $be) {
            $data['BenchmarkingEventsList'][] = $be['_id'];
        }
        $data['linksList'] = [];
        foreach ($data['links'] as $lk) {
            $data['linksList'][] = $lk['label'] . ": " . setLinks($lk['uri']);
        }
        $data['DatasetList'] = [];
        foreach ($data['Dataset'] as $dts) {
            $data['DatasetList'][] = $dts['_id'];
        }
    }
    if ($extended) {
        $data['community_contacts'] = findArrayInDataStore('Contact', $data['community_contacts']);
        $data['status'] = getDataGeneric('CommunityStatus', $data['status']);
        if ($data['status']) {
            unset($data['status_id']);
        }
    }
    return $data;
}
