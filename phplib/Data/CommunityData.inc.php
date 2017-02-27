<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getCommunityInfo() {
    $data['Total'] = $GLOBALS['cols']['Community']->count();
    $data['lastUpdate'] = getUpdateDate($GLOBALS['cols']['Community']);
    return $data;
}

function searchCommunity($params) {
    $cond = [];

    if (isset($params['query'])) {
// Text index pendents versio Mongodb
        foreach (explode(' ', $params['query']) as $wd) {
            $cl2 = [];
            foreach (array_keys($params['queryOn']) as $fld) {
                $rex = new MongoDB\BSON\Regex($wd, "i");
                $cl2[] = [$fld => $rex];
            }
            if (count($cl2) > 1) {
                $cond[] = ['$or' => $cl2];
            } else {
                $cond[] = $cl2[0];
            }
        }
    }
    if (count($cond)) {
        $fcond = ['$and' => $cond];
    } else {
        $fcond = [];
    }
//print "<pre>";
//print json_encode($fcond);
//print "</pre>";

    $sortA = [];
    foreach ($GLOBALS['cols']['Community']->find($fcond, ['sort' => $sortA]) as $rs) {
        $rs['status_id'] = join(",", $rs['status_id']);
        foreach ($rs['links'] as $l) {
            if ($l['label'] == 'MainSite') {
                $rs['linkMain'] = $l['uri'];
            }
        }
        $results[] = $rs;
    }
    return $results;
}

function getCommunityData($id, $extended = false) {

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
            $data[$colname . "s"] = $data1;
        }
    }
    $data['BenchmarkingEventsList'] = [];
    foreach ($data['BenchmarkingEvents'] as $be) {
        $data['BenchmarkingEventsList'][] = $be['_id'];
    }
    $data['linksList'] = [];
    foreach ($data['links'] as $lk) {
        $data['linksList'][] = $lk['label'].": ".$lk['uri'];
    }
    $data['DatasetList'] = [];
    foreach ($data['Datasets'] as $dts) {
        $data['DatasetList'][] = $dts['_id'];
    }
    if ($extended) {
        $contacts = [];
        foreach ($data['community_contacts'] as $contact) {
            $dataContact = $GLOBALS['cols']['Contact']->findOne(['_id' => $contact]);
            if ($dataContact['_id']) {
                $contacts[] = $dataContact;
            }
        }
        $data['community_contacts'] = $contacts;
        $data['status']= [];
        foreach ($data['status_id'] as $s) {
            $dataStatus = $GLOBALS['cols']['CommunityStatus']->findOne(['_id'=>$s]);
            if ($dataStatus['_id']) {
                $data['status'][] = $dataStatus;
            }
        }
        unset($data['status_id']);
    }
    return $data;
}
