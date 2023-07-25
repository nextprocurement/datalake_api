<?php

/*
 * Skeleton for new data type store classes
 */

class place extends DataStore {

    const StoreDescription = 'PLACE';

    function getData($params, $checkId=true) {
        if (preg_match('/^ntp/', $this->id)) {
	        $data = parent::getData($params);
        } else {
            $data = getDataFromPlaceId($this->id, (array)$params);
        }

        $data = extendPlaceData($data, $this->id, (array)$params);
        if ($this->error) {
            return '';
        }
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

        return $data;
    }
    // function info($store='place',$params='') {
    //     return super::info($store, $params);
    // }
}
