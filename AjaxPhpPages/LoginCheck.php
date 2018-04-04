<?php
/**
 * Created by PhpStorm.
 * User: Doug Porter
 * Date: 4/1/2018
 * Time: 4:58 PM
 */

/*if(!isset($_SESSION["dbHost"])){
    include_once "../setup.php";
}*/
//session_regenerate_id();
session_start();
if(!isset($_SESSION["dbHost"])) {
    include_once "../setup.php";
}

include("../Helpers/SQLHelper.php");
$oSqlHelper = new SQLHelper();
$username = $_POST["username"];
$password = $_POST["pword"];

if(!empty($username) && !empty($password)){
    $query = "SELECT password FROM user WHERE uname = \"$username\"";
    $result = $oSqlHelper->queryToDatabase($query);
    //print_r($result);
    $array = $result[0];
    //echo $password;
    //print_r($array);
    if(in_array($password, $array)){
        //$id = $_SESSION["user_id"];
        //if($id != $username){
        //$_SESSION["user_id"] = $username;
        //$id = $_SESSION["user_id"];
        //echo $id;
        //}
        //setcookie("user", $username, time() + (86400 * 30), "/");
        session_destroy();
        //session_start();
        //echo $username;
        //Set the session id to their username
        session_id($username);
        session_start();
        //$_SESSION["user_id"] = $username;
        $url = "http://home.localhost/GenericNew/GenericPlatform/main.php?display=home";
        header("Location: " . $url);
        die();

        //echo "success"; //Successful login
    }
    else{
        //echo "Login failed: incorrect username or password"; //Login failure; incorrect password
        //echo "<input type='hidden' name='error' value='Error: incorrect username or password.'>";
        $_POST["login_error"] = "error";
        $url = "http://home.localhost/GenericNew/GenericPlatform/login.php";
        header("Location: " . $url);
    }
    $url = "http://home.localhost/GenericNew/GenericPlatform/main.php?display=home";
    //header("Location: " . $url);
    //die();
}
