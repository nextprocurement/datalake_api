<?php
require_once "../phplib/globals.inc.php";
require_once "../phplib/eTranslate.inc.php";

$json = json_encode($_REQUEST);
$idRequest = $_REQUEST['request-id'];
error_log($json);

processCallBack($idRequest, json_encode($json));

