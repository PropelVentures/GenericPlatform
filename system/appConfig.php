<?php
@session_start();
require_once("../application/system-config.php");
// require_once("../application/dbConfig.php");
require_once("dbFunctions.php");


$now = time();
if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
    // this session has worn out its welcome; kill it and start a brand new one
    session_unset();
    session_destroy();

   	header("location: /system/login.php");
}

$_SESSION['discard_after'] = $now + SESSION_AUTO_TIMEOUT;
//$_SESSION['discard_after'] = $now + 10;


