<?php

#
# genlib.inc.php
# Utilitats generals 
# Copyright 2007 Josep Ll. Gelpi
#

function pad($t, $n) {
    return str_pad($t, $n, "0", STR_PAD_LEFT);
}

function elimEspais($t) {
    return (trim($t));
}

function elimCRLF($t) {
    return str_replace("\r\n", " ", $t);
}

function translStr($txt, $a, $b) {
    return str_replace($a, $b, $txt);
}

function elimStr($txt, $a) {
    return str_replace($a, "", $txt);
}

function protCom($t) {
    $t1 = str_replace("\"", "&quot;", $t);
    return str_replace("'", "&#146;", $t1);
}

function protNum($t) {
    return str_replace(",", ".", $t);
}

function elimNoChar($t) {
# elimina dels extrems
    $a = "������������������������<>()";
    while (strpos($a, substr($t, 0, 1)) !== false)
        $t = delStr($t, 0, 1);
    while (strpos($a, substr($t, strlen($t) - 1, 1)) !== false)
        $t = delStr($t, strlen($t) - 1, 1);
    return $t;
}

function noAccents($t) {
    return translStr($t, "������������������������", "aaeeiioouucnAAEEIIOOUUCN");
}

function delStr($a, $p1, $l) {
    return substr_replace($a, "", $p1, $l);
}

function isUpper($a, $l) {
    $n = 0.;
    $ua = strtoupper($a);
    for ($i = 0; $i < strlen($a); $i++) {
        if (substr($a, $i, 1) != substr($ua, $i, 1))
            $n++;
    }
    if ($a)
        return ((100. * $n / strlen($a)) < $l);
    else
        return $a;
}

# Dates

function avui() {
    return date("Ymd");
}

function ara() {
    return date("His");
}

function araNos() {
    return date("Hi");
}

function moment() {
    return date("YmdHis");
}

function timestamp() {
    return date("d/m/y : H:i:s", time());
}

function momentNos() {
    return date("YmdHi");
}

function getTimestamp($dat) {
    if (strlen($dat) == 8)
        return mktime(0, 0, 0, substr($dat, 4, 2), substr($dat, 6, 2), substr($dat, 0, 4));
    else
        return mktime(substr($dat, 8, 2), substr($dat, 10, 2), substr($dat, 12, 2), substr($dat, 4, 2), substr($dat, 6, 2), substr($dat, 0, 4));
}

function prdata($idi, $dat) {
    if (strlen($dat) == 8)
        return date("d.m.Y", getTimestamp($dat));
    else
        return date("d.m.Y H:i \h", getTimestamp($dat));
}

function prdataText($idi, $dat) {
    $tst = getTimestamp($dat);
    $txt = $GLOBALS['dayNamesComp'][date('w', $tst)] . " " . date('j', $tst) .
            ' de ' . $GLOBALS['monthNames'][date('n', $tst) - 1];
    if (strlen($dat) > 8) {
        $txt .= " a les " . date('H', $tst) . ":" . date('i', $tst) . " h";
    }
    return $txt;
}

function redirect($url) {
    header("Location:$url");
    exit;
}

## Compression

function gzip($data) {
    if (function_exists('gzencode'))
        return gzencode($data);
    else {
        $fn = tmpDir . "/" . uniqId('gztmp');
        file_put_contents($fn, $data);
        exec("/bin/gzip $fn");
        $data = file_get_contents($fn . '.gz');
        unlink($fn . '.gz');
        return $data;
    }
}

function gunzip($data) {
    if (function_exists('gzdecode'))
        return gzdecode($data);
    else {
        $fn = tmpDir . "/" . uniqId('gztmp');
        file_put_contents($fn . ".gz", $data);
        exec("/bin/gunzip $fn.gz");
        $data = file_get_contents($fn);
        unlink($fn);
        return $data;
    }
}

function is_assoc($arr) {
    return (array_keys($arr) !== range(0, count($arr) - 1));
}

function complexArray_unique($arr) {
    return array_map("unserialize", array_unique(array_map("serialize", $arr)));
}

function selectArrayFields($arr, $flist) {
    if (!preg_match('/,/', $flist)) { // Check for comma sep. fields
        $arr = $arr[$flist];
    } else {
        $dd = [];
        foreach (explode(',', str_replace(' ', '', $flist)) as $f) {
            $dd[$f] = $arr[$f];
        }
        $arr = $dd;
    }
    return $arr;
}

function checkArrayElement($cond, $data) { // $q condition array, $d data
    $dataOk = True;
    $query = [];
    $orig = $cond;
    foreach (array_keys($cond) as $k) {
        if (!preg_match('(/)', $cond[$k])) {
            $cond[$k] = "/^" . $cond[$k] . "$/";
        }
        $cond[$k].="i"; //case insensitive search on values
        if (isset($data[$k])) {
            $query[$k] = $orig[$k]; // Recover unmodified query
            if (preg_match('/^!/', $cond[$k])) {
                $cond[$k] = str_replace('!', '', $cond[$k]);
                if (preg_match($cond[$k], $data[$k])) {
                    $dataOk = False;
                }
            } else {
                if (!preg_match($cond[$k], $data[$k])) {
                    $dataOk = False;
                }
            }
        }
    }
    return [$dataOk, $query];
}

function parseArrayStruc($data, $opfields, $params) {
    $query = [];
    $ops = [];
    foreach ($opfields as $ofi) {
        if ($ofi != '') {
            $ops[] = str_replace(',', '|', $ofi);
            if ($ofi == 'count') { // Count elements, works TODO decide if restrict to non assoc 
                $data = count($data);
            } elseif (is_assoc($data) or is_numeric($ofi)) {  
                $data = selectArrayFields($data, $ofi);
            } else { // Plain array: Extract the given field from all elements
                $field = next($opfields);
                $nqop = [];
                foreach ($data as $q) {
                    $res = checkArrayElement($params, $q);
                    if ($res[0]) {
                        $nqop[] = selectArrayFields($q, $field);
                        }
                    if (count($res[1])) {
                        foreach (array_keys($res[1]) as $k) {
                            $query[$k] = $res[1][$k];
                        }
                    }
                }
                if (isset($params['distinct'])) {
                    $data = [];
                    foreach (complexArray_unique($nqop) as $q) {
                        $data[] = $q;
                    }
                    $query['distinct'] = 1;
                } else {
                    $data = $nqop;
                }
            }
        }
    }
    return ['data' => $data, 'query' => $query, 'ops' => $ops];
}
