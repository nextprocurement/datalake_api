<?php
#
$baseDir = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_DIRNAME);
$baseDir = "/data/DEVEL/NEXTP/basicAPI/nextprocurement";
$baseURL = pathinfo($_SERVER['SCRIPT_NAME'],PATHINFO_DIRNAME);
$baseTitle = "NextProcurement";
$APIDocsURL = "$baseURL/APIDocs.php";
//
require_once "../vendor/autoload.php";
include "../settings.inc.php";
//
$phplib = "$baseDir/../phplib";
#$htmlib = "$baseDir/../htmlib";
$htmlib = "/data/DEVEL/NEXTP/basicAPI/htmlib";
$config = "$baseDir/../config";
$htmlStdPage = "stdpage.templ.html";
$htmlError = "error.templ.html";
// Loading php libraries
require_once "$phplib/libraries.inc.php";
// Loading generic classes
foreach (scandir("$phplib/Classes") as $cl) {
   if (preg_match('/class/',$cl) and !preg_match('/swp/',$cl)) { //avoid -swp files from vi
        require_once ("$phplib/Classes/$cl");
    }
}
// Loading specific store classes
foreach (scandir("$phplib/DataClasses") as $cl) {
   if (preg_match('/class/',$cl) and !preg_match('/swp/',$cl)) { //avoid -swp files from vi
        require_once ("$phplib/DataClasses/$cl");
        $GLOBALS['loadedClasses'][] = str_replace('.class.php','',$cl);
    }
}
// Loading Stores' specific code
foreach (scandir("$phplib/Data") as $cl) {
    if (preg_match('/Data/',$cl)) {
        require_once ("$phplib/Data/$cl");
    }
}
