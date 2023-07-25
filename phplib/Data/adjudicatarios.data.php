<?php

// Functions for adjudicatarios

function extendAdjData($data, $params) {
    $data['licitaciones']['place'] = findInDataStore(
        'place',
        ['Identificador' => $data['_id']],
        ['projection' => [
            'id' => 1,
            '_id' => 0,
            'Objeto_Contrato' => 1,
            'Clasificacion_CPV' => 1
            ]]
    )->toArray();

    $data['licitaciones']['place_menores'] = findInDataStore(
        'place_menores',
        ['Identificador' => $data['_id']],
        ['projection' => [
            'id' => 1,
            '_id' => 0,
            'Objeto_Contrato' => 1,
            'Clasificacion_CPV' => 1
            ]
        ]
    )->toArray();
    return $data;
}