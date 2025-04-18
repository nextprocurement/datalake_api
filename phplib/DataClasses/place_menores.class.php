<?php

/*
 * Skeleton for new data type store classes
 */

class place_menores extends DataStore {

    const StoreDescription = 'PLACE (MENORES)';

    function getData($params, $checkId=true) {
        if (preg_match('/^ntp/', $params->id)) {
            $data = parent::getData($params);
        } else {
            $data = getDataFromPlaceId($this->id, (array)$params);
        }
        if (!$data['_id']) {
            $this->setError($this->id, IDNOTFOUND);
            return;
        }
        $data = extendPlaceData($data, $this->id, (array)$params);
        if (!isset($params->fmt)) {
            $params->fmt='json';
        }
        // Specific code
        if (preg_match('/htm/',$params->fmt)) {
            // code specific to HTML output
        }
        if (isset($params->simple) and $params->simple) {
            return $data;
        }
        // Code for additional data, usually FKs
        if (isset($params->extended) and $params->extended) {
            // get denormalized data
	    }
        if (isset($params->tree)) {
            $data = makeJsonTree($data);
        }

        return $data;
    }

    function info($store='place_menores',$params='') {
        return [STRUCT, getSummaryData($store, $params)];
    }
}
