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

function getGSFile($gfs, $fn) {
    $stream = $gfs->openDownloadStreamByName($fn);
    if (!$stream) {
        return '';
    }
    return stream_get_contents($stream);
}

function getUpdateDate ($col, $stampfield='stamp') {
    $data = iterator_to_array(
            $col->find([],[
                'projection'=>[$stampfield=>1,'_id'=>0],
                'sort'=>[$stampfield=>-1],
                'limit'=>1]));
    if ($data[0]) {
        return date('d M Y', $data[0]['stamp']);
    } else {
        return '';
    }
}

