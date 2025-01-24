<?php
require_once "../phplib/globals.inc.php";
require_once "../phplib/eTranslate.inc.php";

$json = json_encode($_REQUEST);

error_log($json);

processCallBack(json_encode($json));

