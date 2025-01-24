<?php
#
$baseDir = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_DIRNAME);
$baseURL = pathinfo($_SERVER['SCRIPT_NAME'],PATHINFO_DIRNAME);
$baseTitle = "NextProcurement";
$APIDocsURL = "https://nextprocurement.bsc.es/api/APIDocs/index.html";
$APIPrefix = "https://nextprocurement.bsc.es/api/";
//
require_once "../vendor/autoload.php";
include "../settings.inc.php";
//
$phplib = "$baseDir/../phplib";
$htmlib = "$baseDir/../htmlib";
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
	if (preg_match('/data/',$cl) and !preg_match('/swp/',$cl)) {
        require_once ("$phplib/Data/$cl");
    }
}

// PLACE specifics
$PLACEIdPrefixes = [
    "perfiles" => "https://contrataciondelestado.es/sindicacion/licitacionesPerfilContratante/",
    "agregados" =>"https://contrataciondelestado.es/sindicacion/PlataformasAgregadasSinMenores/",
    "menores" => "https://contrataciondelestado.es/sindicacion/datosAbiertosMenores/",
    "insiders" => "https://contrataciondelestado.es/sindicacion/licitacionesPerfilContratante/",
    "outsiders" =>"https://contrataciondelestado.es/sindicacion/PlataformasAgregadasSinMenores/",
    "minors" => "https://contrataciondelestado.es/sindicacion/datosAbiertosMenores/"
];
$documentsPrefix = "downloadedDocuments_backup";
$documentsBCKPrefix = "downloadedDocuments_backup";

// Loading eTranslate details

$eTRANSLATE_API = "https://webgate.ec.europa.eu/etranslation/si/translate";
$eTRANSLATE_USER = "nextprocur_qRyebcWv";
$eTRANSLATE_PASS = "AFQom2kvlvInGwIr";
$eTRANSLATE_CALLBACK = "https://nextprocurement.bsc.es/api/receiveCallback.php";
$eTRANSLATE_TARGET_LANGUAGES = ["EN","FR","DE","IT","PT","NL","PL","RO","BG","CS","DA","ET","FI","EL","HU","GA","HR","LV","LT","MT","SK","SL","SV"];
$eTRANSLATE_SOCK_PATH = "/tmp/eTranslate_".uniqId().".sock";
$eTRANSLATE_SOCK_PORT = 50000;
$eTRANSLATE_SOCK_TIMEOUT = 60;
$eTRANSLATE_LABELS = [
    'EN' => ['Objeto_del_Contrato' => 'Object_of_the_contract'],
    'IT' => ['Objeto_del_Contrato' => 'Oggetto_del_contratto'],
    'FR' => ['Objeto_del_Contrato' => 'Objet_du_marché'],
    'DE' => ['Objeto_del_Contrato' => 'Vertragsgegenstand']
];
