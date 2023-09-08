<?php
// functions for place and place_menores store


function getDataFromPlaceId($store, $params) {
    global $PLACEIdPrefixes;
    $data = findInDataStore(
        $store,
        ['$or' =>
            [
                ['id' => $PLACEIdPrefixes['agregados'].$params['id']],
                ['id' => $PLACEIdPrefixes['perfiles'].$params['id']],
                ['id' => $PLACEIdPrefixes['menores'].$params['id']]
            ]
        ],
        ['sort' => ['updated' => -1]]
    )->toArray()[0];
    return $data;
}

function extendPlaceData($data, $store, $params) {
    $dateFields = [
        'updated',
        'Presentacion_Oferta'
    ];
    $data['versions'] = getPlaceVersions($data, $store);
    if ($params['final']) {
        $data['original_requested_id'] = $data['_id'];
        $data = getFinalAtom($data, $store);
    }
    $data = fixDateFields($data, $dateFields);
    foreach ($data['Clasificacion_CPV'] as $cpv) {
        $cpv_text = getOneDocument('cpv', "".$cpv);
        $data['Definicion_CPV'][] = $cpv_text;
    }
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
