<?php

/*
 * Several utility funcions
 * xml_encode: Produces XML from php array structure
 * setLinks: replace URL like expressions by HTML links
 * is_assoc: Check if array is associative
 */
define('STRUCT', 0); //PHP array structure
define('TEXT',   1);
define('XML',    2);
define('JSON',   3);
define('HTML',   4);
define('GZIP',   5);
define('CURSOR', 6); // MongoDB Cursor
define('TARRAY', 7);
define('RAW',    8);

function xml_encode($mixed, $domElement = null, $DOMDocument = null) {
    if (is_null($DOMDocument)) {
        $DOMDocument = new DOMDocument;
        $DOMDocument->formatOutput = true;
        xml_encode($mixed, $DOMDocument, $DOMDocument);
        return $DOMDocument;
    } else {
        if (is_array($mixed)) {
            foreach ($mixed as $index => $mixedElement) {
                if (is_int($index)) {
                    if ($index === 0) {
                        $node = $domElement;
                        foreach (['_id','id','dbId','varId','entity_id'] as $idStr) {
                                if (isset($mixedElement[$idStr])) {
                                    $node->setAttribute('id', $mixedElement[$idStr]);
                                    unset($mixedElement[$idStr]);
                                }
                        }
                    } else {
                        $node = $DOMDocument->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                        foreach (['_id','id','dbId','varId','entity_id'] as $idStr) {
                            if (isset($mixedElement[$idStr])) {
                                    $node->setAttribute('id', $mixedElement[$idStr]);
                                    unset($mixedElement[$idStr]);
                                }
                        }
                    }
                } else {
                    if (preg_match('/^[0-9]/', $index))
                        $index = "_" . $index;
                    $index = preg_replace("/([\[\]])/", '_', $index);
                    $plural = $DOMDocument->createElement(preg_replace('/_$/','',$index));
                    $domElement->appendChild($plural);
                    $node = $plural;
                    if (!(rtrim($index, 's') === $index)) {
                        $ii = preg_replace("/ie$/","y",rtrim($index, 's'));
                        $singular = $DOMDocument->createElement($ii);
                        $plural->appendChild($singular);
                        $node = $singular;
                    } 
                }

                xml_encode($mixedElement, $node, $DOMDocument);
            }
        } else {
            $mixed = is_bool($mixed) ? ($mixed ? 'true' : 'false') : $mixed;
            $domElement->appendChild($DOMDocument->createTextNode($mixed));
        }
    }
}

function setLinks ($a) {
    $d = preg_replace('/(htt[p|ps]:\/\/[^ |<]*)/',"<a href=\"\\1\">\\1</a>", $a);
    return $d;
}

function isAssoc(array $arr)
{
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}