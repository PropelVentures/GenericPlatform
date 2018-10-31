<?php
@session_start();
include("dbConfig.php");
include("defaultConst.php");
include_once("system-constants.php");


$now = time();
if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
    // this session has worn out its welcome; kill it and start a brand new one
    session_unset();
    session_destroy();

   	header("location: /system/login.php");
}

$_SESSION['discard_after'] = $now + SESSION_AUTO_TIMEOUT;
//$_SESSION['discard_after'] = $now + 10;