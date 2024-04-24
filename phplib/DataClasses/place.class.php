<?php

/*
 * Skeleton for new data type store classes
 */

class place extends DataStore {

    const StoreDescription = 'PLACE';

    function getData($params, $checkId=true) {
        if (preg_match('/^ntp/', $params->id)) {
	        $data = parent::getData($params, $checkId);
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
        // move from flat format (with agregated fields) to json tree
        if (isset($params->tree)) {
            $data = makeJsonTree($data, $sep="/");
        }

        return $data;
    }

    function info($store='place',$params='') {
         return [STRUCT, getSummaryData($store, $params)];
    }

    // function documentos($params) {
    //     return [STRUCT, getDownloadedDocuments((array)$params)];
    // }
}
