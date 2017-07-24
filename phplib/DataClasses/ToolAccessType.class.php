<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ToolAccessType extends DataStore {

    const StoreDescription = 'Benchmarking Tools Access types';

    function getData($params, $checkId = true) {
        $data = parent::getData($params);
        if ($this->error) {
            return '';
        }
        if (isset($params->simple) and $params->simple) {
            return $data;
        }

        $data['Tools'] = iterator_to_array(findInDataStore('Tool', ['tool_access.tool_access_type_id' => $data['_id']], ['projection' => ['_id']]));
        return $data;
    }

}
