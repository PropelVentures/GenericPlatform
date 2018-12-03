<?php
/* * *SITE INFORMATION********* */
// require_once '../system/config.php';

define("SESSION_AUTO_TIMEOUT","9200");


// =======================================================================
// DATABASE CONFIG (Location, CREDENTIALS)
// =======================================================================

$config['db_host'] = "localhost";

#$config['db_name'] = "genericplatform";
#$config['db_user'] = "genericinternal";
#$config['db_password'] = "Upwork0814!!";


/*
$config['db_name'] = "genericsandbox2";
$config['db_user'] = "genericsandbox2";
$config['db_password'] = "Upwork!081461!";
*/


$config['db_name'] = "genericplatform";
$config['db_user'] = "genericinternal";
$config['db_password'] = "Upwork0814!!";


$GLOBALS['db-host'] =  $config['db_host'];
$GLOBALS['db-username'] = $config['db_name'] ;
$GLOBALS['db-password'] =  $config['db_password'] ;
$GLOBALS['db-database'] = $config['db_name'] ;


if(!empty($config['db_name'])){

    $_SESSION['config'] = $config;
}else{

    unset($_SESSION['config']);
}

if ($_SERVER['HTTP_HOST'] === 'localhost') {
    $GLOBALS['db-username'] = "root";
    $GLOBALS['db-host'] = "localhost";
    $GLOBALS['db-password'] = "";
    $GLOBALS['db-database'] = "generic";
} else {

    $GLOBALS['db-host'] = $dbconfig->db_host;
    $GLOBALS['db-username'] = $dbconfig->username;
    $GLOBALS['db-password'] = $dbconfig->password;
    $GLOBALS['db-database'] = $dbconfig->database;
}

require_once("system-defines.php");
?>
