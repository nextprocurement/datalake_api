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
                ['id' => $PLACEIdPrefixes['menores'].$params['id']],
                ['id' => $PLACEIdPrefixes['outsiders'].$params['id']],
                ['id' => $PLACEIdPrefixes['insiders'].$params['id']],
                ['id' => $PLACEIdPrefixes['minors'].$params['id']]
            ]
        ],
        ['sort' => ['updated' => -1]]
    )->toArray()[0];
    return $data;
}

function extendPlaceData($data, $store, $params) {
    if ($data['date_mode'] == 'v2023') {
        $dateFields = [];
        $CPVFields = [
            'Datos_Generales_del_Expediente/Clasificacion_CPV',
            'Datos_Generales_del_Expediente_del_Lote/Clasificacion_CPV'
        ];
    } else {
        $dateFields = [
            'updated',
            'Presentacion_Oferta'
        ];
        $CPVFields = ['Clasificacion_CPV'];
        $data['versions'] = getPlaceVersions($data, $store);
        if ($params['final']) {
            $data['original_requested_id'] = $data['_id'];
            $data = getFinalAtom($data, $store);
        }
    }
    $data = fixDateFields($data, $dateFields);
    $data['documentos'] = getDownloadedDocuments($params);
    foreach ($CPVFields as $cpvF) {
        foreach ($data[$cpvF] as $cpv) {
            $cpv_text = getOneDocument('cpv', "".$cpv);
            $data['Definicion_CPV'][] = $cpv_text;
        }
    }
    #eTranslation
    if ($params['etranslate']) {
        print_r(strpos(',', strtoupper($params['etranslate'])));
        if (strpos($params['etranslate'], ',') !== false) {
            $targetLangs = explode(',', strtoupper($params['etranslate']));
        }
        else {
            $targetLangs = [strtoupper($params['etranslate'])];
        }

        foreach ($GLOBALS['eTRANSLATE_FIELDS'] as $field) {
            if (!$GLOBALS['eTRANSLATE_LABELS'][$target][$field]) {
                $etranslate_fields = geteTranslation($targetLangs, $field);
                foreach ($targetLangs as $target) {
                    $GLOBALS['eTRANSLATE_LABELS'][$target][$field] = $etranslate_fields[$target];
                }
            }
            $etranslate_data = geteTranslation($targetLangs, $data[$field]);
            foreach ($targetLangs as $target) {
                $data['eTranslation/'.$target.'/'.$GLOBALS['eTRANSLATE_LABELS'][$target][$field]] = $etranslate_data[$target];
            }
        }
    }
    return $data;
}

function geteTranslation($targetLanguages, $text) {
    $idRequest = sendRequest('ES', $targetLanguages, $text);
    $numRequests = count($targetLanguages);
    if ($idRequest > 0) {
        $etranslate_responses = setListenSocket($idRequest, $numRequests);
        foreach ($etranslate_responses as $response) {
            $decoded_response = json_decode($response, true);
            $etranslate_data[$decoded_response['target-language']] = $decoded_response['translated-text'];
        }
        return $etranslate_data;
    } 
    error_log("Error sending eTranslation request: " . $idRequest);
    return false;
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

function getSummaryData($store, $params) {
    $data = getOneDocument($store, 'summary_data');
    return $data;
}

function getDownloadedDocuments($params) {
    $fileList = $GLOBALS['cols'][$GLOBALS['documentsPrefix'].'.files']->find(
        ['filename' => ['$regex' => '^'.$params['id']]]
    )->toArray();
    return $fileList;
}

function getRawDocuments($params) {
    $ids = explode('_', $params['id']);

    $files = getDownloadedDocuments(['id' => $ids[0]]);
    if (count($ids) > 1) {
        $tmpFile = $params['id'];
        $fileContents = getGSFile($GLOBALS['docsGS'], $params['id']);
    } else {
        $filesData = [];
        foreach ($files as $file) {
            $filesData[$file['filename']] = getGSFile($GLOBALS['docsGS'], $file['filename']);
        }
        $tmpFile = '/tmp/'.$params['id'].'_files.tar';
        // Create a new TAR file
        $tar = new PharData($tmpFile);
        // Add each file to the TAR archive
        foreach ($filesData as $filename => $fileContents) {
            $tar->addFromString($filename, $fileContents);
        }
        if ($params->fmt == 'gzip') {
            // Compress the TAR file using GZ compression
            $tar->compress(Phar::GZ);
        }
        $fileContents = file_get_contents($tmpFile);
        //unlink ($tmpFile);
    }
    return ['file' => $tmpFile, 'contents' => $fileContents];
}
