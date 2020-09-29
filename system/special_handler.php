<?php
require_once("config.php");
require_once("appConfig.php");
require_once("functions_misc.php");
require_once("handler.php");
require_once("../application/system-config.php");
require_once("dbFunctions.php");

try {
	$con = connect();
	$handler =  new handler($con);
	$handler->checkLogin();
	$handler->redirectAccordingToUrl();

} catch(Exception $e){
	FlashMessage::add($e->getMessage());
	header("Location:".BASE_URL.'system/login.php');
	exit;
}

?>
