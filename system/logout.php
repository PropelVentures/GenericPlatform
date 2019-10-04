<?php
/*
 *
 *  This is Intake array which take DD AND FD data and put in master array
 *
 */
require_once 'functions_loader.php';

/*
 * "log_event" is used to store the event or action performed by the user in the log file of this system 
 *  and Database . Also responsible to send the notification/message.
 *  
 *  "log_event" take 4 paramaters :
 *
 *   display_page => page name from where the action make,
 *   action =>  name of the action,
 *   senderId => default value is false ,
 *   reciverId => default value is false 
 *
 *   Detail definition is in the "functions.php" on the same location. 
 */
	log_event('logout','logout');
	// @session_start();

	/*
	* "start_app_session" function is responsible to make the session and store the details 
	*  in the sepecific directory on the site.
	* 
	* For more details please check the "appConfig.php" on the same location.
	*/
	start_app_session();
 
	// thid function is responsible to remove the session of current user.
	@session_destroy();

	echo "<script>window.location='../index.php';</script>";
