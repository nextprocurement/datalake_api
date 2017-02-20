<?php

function printGSFile($col, $fn, $mime = '', $sendFn = False) {
    $file = $col->findOne(['filename' => $fn]);
    if (!$file->file['_id']) {
        return 1;
    }
    if ($mime) {
        header('Content-type: ' . $mime);
    }
    if ($sendFn) {
        header('Content-Disposition: attachment; filename="' . $fn . '"');
    }
    print($file->getBytes());
    return 0;
}

function getGSFile($col, $fn) {
    $file = $col->findOne(['filename' => $fn]);
    if (!$file->file['_id']) {
        return '';
    } else {
        return $file->getBytes();
    }
}

function getUpdateDate ($col, $stampfield='stamp') {
    return date('d M Y', iterator_to_array(
            $col->find([],[
                'projection'=>[$stampfield=>1,'_id'=>0],
                'sort'=>[$stampfield=>-1],
                'limit'=>1]))[0]['stamp']);
}

