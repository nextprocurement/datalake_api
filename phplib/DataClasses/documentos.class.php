<?php

/*
 * Skeleton for new data type store classes
 */

class documentos extends DataStore {

    const StoreDescription = 'DOCUMENTOS';

    function getData($params, $checkId=true) {
         $data = getRawDocuments((array)$params);

        if ($this->error) {
            return '';
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
        return [RAW, $data];
    }

    function info($store='place',$params='') {
         return [STRUCT, getSummaryData($store, $params)];
    }

}

