<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getToolInfo() {
    $data['Total'] = $GLOBALS['cols']['Tool']->count();
    $data['lastUpdate'] = getUpdateDate($GLOBALS['cols']['Tool']);
    return $data;
}

function getToolData($id, $extended = false) {
    $data = $GLOBALS['cols']['Tool']->findOne(['_id' => $id]);
    $data['access_urls']=[];
    foreach ($data['tool_access'] as $ac){
        $data['access_urls'][] = $ac['link'];
    }
    return $data;
}
