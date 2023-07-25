<?php
/*
 * Bunch of function to deal with MongoDB
 * getGenericInfo: Get counts and update time for DataStore
 * getOneDocupemt: Wrapper to Mongodb findOne
 * findInDataStore: Wrapper to Mongodb find
 * findArrayInDataStore: Produces an array of documents from Datastore form an array of ids
 * getFieldArray: Returns an array of "targetField" values on docs that contain $field:$id
 *
 */

function getGenericInfo($dataStore) {
    if ($GLOBALS['cols'][$dataStore]) {
        $data['Total'] = $GLOBALS['cols'][$dataStore]->estimatedDocumentCount();
        $data['lastUpdate'] = getUpdateDate($GLOBALS['cols'][$dataStore]);
    } else {
        $data = ['Total' => 0];
    }
    return $data;
}

function searchGeneric($dataStore, $params, $sortA=[]) {
    $cond = [];

    if (isset($params['query'])) {
        foreach (array_keys($params['queryOn']) as $fld) {
            if ($params['searchType'][$fld] == 'N') {
                $cond[] = [$fld => (int)$params['query']];
                unset($params['queryOn'][$fld]);
            }
        }
// Text index pendents versio Mongodb
        if ($params['queryOn']) {
            foreach (explode(' ', $params['query']) as $wd) {
                $cl2 = [];
                foreach (array_keys($params['queryOn']) as $fld) {
                    $rex = new MongoDB\BSON\Regex($wd, "i");
                    $cl2[] = [$fld => $rex];
                }
                if (count($cl2) > 1) {
                    $cond[] = ['$or' => $cl2];
                } else {
                    $cond[] = $cl2[0];
                }
            }
        }
    }
    if (count($cond)) {
        $fcond = ['$and' => $cond];
    } else {
        $fcond = [];
    }
// print "<pre>";
// print json_encode($fcond);
// print "</pre>";

    foreach ($GLOBALS['cols'][$dataStore]->find($fcond, ['sort' => $sortA]) as $rs) {
        $results[] = $rs;
    }
    return $results;
}
// MongoDB wrappers

//MongoDB findOne wrapper
function getOneDocument($dataStore,$id) {
    $data = $GLOBALS['cols'][$dataStore]->findOne(['_id' => $id]);
    return $data;
}

// MongoDB find wrapper
function findInDataStore($dataStore,$query, $options) {
    return $GLOBALS['cols'][$dataStore]->find($query,$options);
}

// returns an array of MongoDB documents from an array of _id's
function findArrayInDataStore($dataStore,$idsArray) {
   $data=[];
   if (isset($idsArray)) {
    foreach ($idsArray as $id) {
           $data[] = getOneDocument($dataStore,$id);
    }
   }
   return $data;
}

// returns an array of "targetField" values on docs that contain $field:$id
function getFieldArray($dataStore,$field,$id,$targetField='_id') {
    $odata = iterator_to_array(findInDataStore(
        $dataStore, [$field => $id], ['projection' => [$targetField => 1]])
    );
    $data=[];
    foreach ($odata as $v) {
        $data[]=$v[$targetField];
    }
    return $data;
}
// Fix MongoDB Date object to Readable format
function fixDateFields($data, $dateFields) {
    foreach ($dateFields as $f) {
        if (gettype($data[$f]) == 'object') {
            $tmp = $data[$f]->toDateTime()->format(DATE_ATOM);
            $data[$f] = $tmp;
        }
    }
    return $data;
}