<?php
// functions for place and place_menores store



function extendPlaceData($data, $store, $params) {
    $dateFields = [
        'updated',
        'Presentacion_Oferta'
    ];
    $data['versions'] = getPlaceVersions($data, $store);
    if ($params['final']) {
        $data['original_equested_id'] = $data['_id'];
        $data = getFinalAtom($data, $store);
    }
    $data = fixDateFields($data, $dateFields);
    return $data;
}

function getPlaceVersions($data, $store) {
    $versions = findInDataStore(
        $store,
        ['id' => $data['id']],
        [
            'projection' => ['_id' => 1, 'updated' => 1],
            'sort' => ['updated' => 1]
        ]
    )->toArray();

    $corrVersions = [];
    foreach ($versions as $v) {
        $v = fixDateFields($v, ['updated']);
        $corrVersions[] = $v;
    }
    return $corrVersions;
}


function getFinalAtom($data, $store) {
    if (!$data['versions']) {
        $data['versions'] = getPlaceVersions($data, $store);
    }
    $last_doc = end($data['versions']);
    $new_data = getOneDocument($store, $last_doc['_id']);
    $new_data['versions'] = $data['versions'];
    $new_data['requested_id'] = $data['_id'];
    return $new_data;
}
