<?php

/* 
 * Id Resolution
 */

class idsolv extends DataStore {
    
    const StoreDescription = 'ID resolution';
    
    function processId($params) {
        parent::processId($params);
        if ($params->id) {
            list($params->id,$params->bdid) = explode(':',$params->id);
        }
        return $params;
    }

    function getData($params, $checkId=true) {
        $data = parent::getData($params);
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
        if (isset($params->extended) and $params->extended) {
            // get denormalized data
        }
        
        return $data;
    }
    
    function redir ($params) {
        $data = $this->getData($params);
        $URL = preg_replace('/{id}/',$params->bdid,$data['URL']);
        redirect($URL);
    }
}
