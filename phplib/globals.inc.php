<?php
#
$baseDir = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_DIRNAME);
$baseURL = pathinfo($_SERVER['SCRIPT_NAME'],PATHINFO_DIRNAME);
//
require_once "../vendor/autoload.php";
include "../settings.inc.php";
//
$phplib = "$baseDir/../phplib";
$htmlib = "$baseDir/../htmlib";
// Loading php libraries
require_once "$phplib/libraries.inc.php";

// Loading classes
require_once "$phplib/Classes/DataStore.class.php";
foreach (scandir("$phplib/Classes") as $cl) {
   if (preg_match('/class/',$cl) and !preg_match('/swp/',$cl)) { //avoid -swp files from vi       
        require_once ("$phplib/Classes/$cl");
        $GLOBALS['loadedClasses'][] = str_replace('.class.php','',$cl);
    }
}
// Loading Stores' specific code
foreach (scandir("$phplib/Data") as $cl) {
    if (preg_match('/Data/',$cl)) {
        require_once ("$phplib/Data/$cl");
    }
}


