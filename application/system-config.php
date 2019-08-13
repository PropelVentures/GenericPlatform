<?php
/* * *SITE INFORMATION********* */
// require_once '../system/config.php';

define("SESSION_AUTO_TIMEOUT","604800");

// =======================================================================
// DATABASE CONFIG (Location, CREDENTIALS)
// =======================================================================
if ($_SERVER['HTTP_HOST'] == 'localhost') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "generic";
	$config['db_user'] = "root";
	$config['db_password'] = "";
} elseif($_SERVER['HTTP_HOST'] == 'genericsandbox1.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericsandbox1";
	$config['db_user'] = "genericsandbox1";
	$config['db_password'] = "Lennin1009##";
} elseif($_SERVER['HTTP_HOST'] == 'genericsandbox2.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericsandbox2";
	$config['db_user'] = "genericsandbox2";
	$config['db_password'] = "Lennin1002##";
} elseif($_SERVER['HTTP_HOST'] == 'genericsandbox3.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericsandbox3";
	$config['db_user'] = "genericsandbox3";
	$config['db_password'] = "Lennin1003##";
} elseif($_SERVER['HTTP_HOST'] == 'genericsandbox4.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericsandbox4";
	$config['db_user'] = "genericsandbox4";
	$config['db_password'] = "Lennin1009##";
} elseif($_SERVER['HTTP_HOST'] == 'genericsandbox5.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericsandbox5";
	$config['db_user'] = "genericsandbox5";
	$config['db_password'] = "Lennin1005##";

} elseif($_SERVER['HTTP_HOST'] == 'genericsandbox6.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericsandbox6";
	$config['db_user'] = "genericsandbox6";
	$config['db_password'] = "Lennin1006##";

} elseif($_SERVER['HTTP_HOST'] == 'genericsandbox7.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericsandbox7";
	$config['db_user'] = "genericsandbox7";
	$config['db_password'] = "Lennin1007##";


} elseif($_SERVER['HTTP_HOST'] == 'genericsandbox8.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericsandbox8";
	$config['db_user'] = "genericsandbox8";
	$config['db_password'] = "Lennin1008##";


} elseif($_SERVER['HTTP_HOST'] == 'genericsandbox9.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericsandbox9";
	$config['db_user'] = "genericsandbox9";
	$config['db_password'] = "Lennin1009##";


} elseif($_SERVER['HTTP_HOST'] == 'generic.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericplatform";
	$config['db_user'] = "genericinternal";
	$config['db_password'] = "Lennin1009##";

} elseif($_SERVER['HTTP_HOST'] == 'cyrano.cjcornell.net') {
	$config['db_host'] = "localhost";
	$config['db_name'] = "CyranoProduction";
	$config['db_user'] = "CyranoProduction";
	$config['db_password'] = "Melinda0315#";

} else {
	$config['db_host'] = "localhost";
	$config['db_name'] = "genericplatform";
	$config['db_user'] = "genericinternal";
	$config['db_password'] = "Lennin1009##";

}

$GLOBALS['db-host'] =  $config['db_host'];
$GLOBALS['db-username'] = $config['db_name'] ;
$GLOBALS['db-password'] =  $config['db_password'] ;
$GLOBALS['db-database'] = $config['db_name'] ;

if(!empty($config['db_name'])){
	$_SESSION['config'] = $config;
}else{
	unset($_SESSION['config']);
}
/* Print function */
function pr($data){
	echo "<pre>";
		print_r($data);
	echo "</pre>";
}
require_once("system-defines.php");
?>
