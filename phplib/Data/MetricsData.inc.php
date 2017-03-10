<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getMetricsInfo() {
    $data['Total'] = $GLOBALS['cols']['Metrics']->count();
    $data['lastUpdate'] = getUpdateDate($GLOBALS['cols']['Metrics']);
    return $data;
}


function getMetricsData($id, $extended = false) {

    $data = $GLOBALS['cols']['Metrics']->findOne(['_id' => $id]);
    list($data['community_id'], $lb)= explode(":",$data['_id']);
    $data['LinksList']=[];
    foreach ($data['links'] as $l) {
        $data['linksList'][] = "<a href=\"".$l['uri']."\">".$l['label']."</a>";
    }
    return $data;
}
