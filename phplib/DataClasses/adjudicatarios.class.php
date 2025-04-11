<?php

/*
 * Skeleton for new data type store classes
 */

class adjudicatarios extends DataStore {

    const StoreDescription = 'Adjudicatarios';

    function getData($params, $checkId=true) {
	$params->id = strtoupper($params->id);
        $data = parent::getData($params);
        $data = extendAdjData($data, array($params));
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
}
