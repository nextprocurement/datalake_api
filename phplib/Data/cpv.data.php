<?php
// Functions for CPV data store
function addCPVTree($data, $params) {
	$data['parents'] = getParents($data, $data['_id']);
	// Children
	$data['children'] = [];
	$base = $data['_id'];
	while ($base and substr($base, -2) == '00') {
                $base = substr($base, 0, -2);
	}
	$regex = new MongoDB\BSON\Regex('^'.$base);
	foreach (findInDataStore('cpv', ['_id' => $regex], ['sort' => ['_id'=>1]]) as $child) {
		if ($child['_id'] != $data['_id']) {
			if ($params['tree']) {
				$data = addTreeChild($data, $base, $child);
			} else {
				$data['children'][] = $child;
			}
		}
	}
	return $data;
}

function getParents($data, $base, $getData=True) {
	$parents = [];
        while ($base and substr($base, -2) == '00') {
                $base = substr($base, 0, -2);
        }
        while ($base and $base = substr($base, 0, -2)) {
                $parent_id = str_pad($base, 8, "0");
		if ($getData) {
			$parents[] = getOneDocument('cpv', $parent_id);
		} else {
			$parents[] = $parent_id;
		}
	}        
	return $parents;
}

function addTreeChild($data, $base, $child) {
	return $data;
}
	

