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
        //$data['lastUpdate'] = getUpdateDate($GLOBALS['cols'][$dataStore]);
    } else {
        $data = ['Total' => 0];
    }
    return $data;
}

function searchGeneric($dataStore, $params, $toArray=True, $sortA=[], $projection=[], $noEmptyQuery=False) {
    $cond = [];
    if (!$params['queryOn'] or isset($params['text']) ) {
        return textSearch($dataStore, $params, $toArray=$toArray, $sortA=$sortA, $projection=$projection, $noEmptyQuery=$noEmptyQuery);
    } else {
        return keyWordSearch($dataStore, $params, $toArray=$toArray, $sortA=$sortA, $projection=$projection, $noEmptyQuery=$noEmptyQuery);
    }
}

function keyWordSearch($dataStore, $params, $toArray=True, $sortA=[], $projection=[], $noEmptyQuery=False) {
    $cond=[];
    if (isset($params['query'])) {
        foreach (array_keys($params['queryOn']) as $fld) {
            if ($params['searchType'][$fld] == 'N') {
                $orCond = [];
                foreach(explode(',', $params['query']) as $q) {
                    $orCond[]=[$fld => (int)$q];
                }
                $cond[] = ['$or' => $orCond];
                unset($params['queryOn'][$fld]);
            }
        }
        if ($params['queryOn']) {
            foreach (explode(' ', $params['query']) as $wd) {
                $orCond = [];
                foreach (array_keys($params['queryOn']) as $fld) {
                    $rex = new MongoDB\BSON\Regex($wd, "i");
                    $orCond[] = [$fld => $rex];
                }
                if (count($orCond) > 1) {
                    $cond[] = ['$or' => $orCond];
                } else {
                    $cond[] = $orCond[0];
                }
            }
        }
    } elseif ($noEmptyQuery) {
	return False;
    }

    if (count($cond)) {
        $fcond = ['$and' => $cond];
    } else {
        $fcond = [];
    }
    //print "<pre>";
    //print json_encode($fcond);
    //print "</pre>";
    // exit;
    $resultsCursor = $GLOBALS['cols'][$dataStore]->find(
        $fcond,
        [
            'projection' => $projection,
            'sort' => $sortA,
                    'allowDiskUse' => True,
                    'maxTimeMS' => 0,
                    'allowPartialResults' => True,
                    'noCursorTimeout' => True
        ]
    );
    if ($toArray) {
        return $resultsCursor->toArray();
    }
    return $resultsCursor;
}

function textSearch($dataStore, $params, $toArray=True, $sortA=[], $projection=[], $noEmptyQuery=False) {
	// search against text index
	if (!isset($params['language'])) {
		$params['language'] = 'spanish';
	}
    if (!$params['query'] and $noEmptyQuery) {
        return false;
    }
    $resultsCursor = $GLOBALS['cols'][$dataStore]->find(
		['$text'=>
			[
				'$search'=>$params['query'],
				'$language' => $params['language']
			]
		],
		[
            'projection'=> $projection,
			'sort'=>['score'=>['$meta'=>'textScore']],
			'allowDiskUse' => True,
			'maxTimeMS' => 0,
			'allowPartialResults' => True,
			'noCursorTimeout' => True
		]
        );
        if ($toArray) {
            return $resultsCursor->toArray();
        }
	return $resultsCursor;
}
// MongoDB wrappers

//MongoDB findOne wrapper
function getOneDocument($dataStore, $id) {
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

function getProjectionArray($fieldsStr, $keep_id=True) {
    $projection = [];
    foreach (explode(',',$fieldsStr) as $field) {
        $projection[$field] = 1;
    }
    if (!$keep_id) {
        $projection['_id'] = 0;
    }
    return $projection;
}

// Build json tree from flat array (json levels coded on labels)
function makeJsonTree($data, $sep='/') {
    foreach ($data as $k => $v) {
        if (str_contains($k, $sep)) {
            $levels = explode($sep, $k);
            eval ('$data["'.join('"]["', $levels).'"] = $v;');
            unset($data[$k]);
        }
    }
    return $data;
}