<?php
/*
*  start_app_session function for making the session of the user and same it 
*  in the specific path on the server to cross check the user session or not 
*/
function start_app_session(){
  //to set the session saving path .
  $DirectoryPath = "/tmp"."/".$_SERVER['HTTP_HOST'];
  is_dir($DirectoryPath) or mkdir($DirectoryPath, 0777);
  ini_set("session.save_path", $DirectoryPath);
  @session_start();
}

start_app_session();

require_once("../application/system-config.php");

/* The dbFunctions.php file responsible for making the connecting with db and all 
*the action like get data, update, fetch, delete and other function are defined 
*/
require_once("dbFunctions.php");

$now = time();
if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
    // this session has worn out its welcome; kill it and start a brand new one
    session_unset();
    session_destroy();

   	header("location: /system/login.php");
}

$_SESSION['discard_after'] = $now + SESSION_AUTO_TIMEOUT;
