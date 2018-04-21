<?php
/**
 * Created by PhpStorm.
 * User: Doug Porter
 * Date: 4/1/2018
 * Time: 4:58 PM
 */

session_start();
if(!isset($_SESSION["dbHost"])) {
    include_once "../setup.php";
}

include("../Helpers/SQLHelper.php");
$oSqlHelper = new SQLHelper();
$username = $_POST["username"];
$password = $_POST["pword"];

//Check if the password matches what's stored in the database
//Create a cookie and set the session id to their username if the password is correct, then send them to the homepage
//If the password is incorrect, send them back to the login screen with an error
if(!empty($username) && !empty($password)){
    $query = "SELECT password FROM user WHERE uname = \"$username\"";
    $result = $oSqlHelper->queryToDatabase($query);
    $array = $result[0];
    if(in_array($password, $array)){
        setcookie("user", $username, time() + (86400 * 30), "/");
        session_id($username);
        $url = "http://genericnew.cjcornell.net/GenericPlatform/main.php?display=home";
        header("Location: " . $url);
        die();
    }
    else{
        $_POST["login_error"] = "error";
        $url = "http://genericnew.cjcornell.net/GenericPlatform/login.php";
        header("Location: " . $url);
    }
}
