<?php 
require_once("config.php");
require_once("appConfig.php");
require_once("functions.php");
require_once("class/Handler.php");
try {
	$con = connect();
	$handler =  new Handler($con);
	$handler->checkLogin();
	$handler->redirectAccordingToUrl();

} catch(Exception $e){
	FlashMessage::add($e->getMessage());
	header("Location:".BASE_URL.'system/login.php');
	exit;
}

?>
