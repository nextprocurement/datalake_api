<?php

class cpv extends DataStore {
    
    const StoreDescription = 'Codigos de Clasificació CPV';
  
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
        if (isset($params->simple) and $params->simple) {
            return $data;
        }
        // Code for additional data, usually FKs
	if (isset($params->extended) and $params->extended) {
	    $data = addCPVTree($data, (array)$params);


        }
        
        return $data;
    }
}
