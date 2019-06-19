<?php

function start_app_session(){
  //to set the session saving path .
  $DirectoryPath = "/tmp"."/".$_SERVER['HTTP_HOST'];
  is_dir($DirectoryPath) or mkdir($DirectoryPath, 0777);
  ini_set("session.save_path", $DirectoryPath);
  @session_start();
}

start_app_session();

require_once("../application/system-config.php");
require_once("dbFunctions.php");

$now = time();
if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
    // this session has worn out its welcome; kill it and start a brand new one
    session_unset();
    session_destroy();

   	header("location: /system/login.php");
}

$_SESSION['discard_after'] = $now + SESSION_AUTO_TIMEOUT;
