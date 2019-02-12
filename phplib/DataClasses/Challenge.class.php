<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Challenge extends DataStore {

    const StoreDescription = 'Benchmarking Challenges';

    function getData($params, $checkId = true) {
        $data = parent::getData($params);
        if ($this->error) {
            return '';
        };

        foreach (getFieldArray('TestAction', "challenge_id", $data['_id']) as $taid) {
            $ta = getOneDocument('TestAction', $taid);
            $data['TestActions'][$ta['action_type']][]=$taid;
        }
        foreach (getFieldArray('Dataset', "challenge_id", $data['_id']) as $dsid) {
            $ds = getOneDocument('Dataset', $dsid);
            $data['Datasets'][$ds['type']][]=$dsid;
        }
      
        if (isset($params->extended) and $params->extended) {
            $data['challenge_contacts'] = [];
            foreach ($data['challenge_contact_ids'] as $c) {
                $data['challenge_contacts'][] = getOneDocument('Contact', $c);
            }
            unset($data['challenge_contact_ids']);
            $data['referencesList'] = [];
            foreach ($data['references'] as $r) {
                $data['referencesList'][] = getOneDocument('Reference', $r);
            }
            unset($data['references']);
        }
        if (isset($params->fmt) and preg_match("/htm/",$params->fmt)) {
            foreach ($data['Datasets'] as $ty) {
                $data['Datasets_'.$ty] = $data['Datasets'][$ty];
            }
            foreach ($data['TestActions'] as $ty) {
                $data['TestActions_'.$ty] = $data['TestActions'][$ty];
            }
        }
        return $data;
    }

}
