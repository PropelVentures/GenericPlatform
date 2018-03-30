<?php
/**
 * Created by PhpStorm.
 * User: Ryan Linehan
 * Date: 3/30/2018
 * Time: 1:10 PM
 */
header("content-type=application/json");
if(!isset($_SESSION["dbHost"])){
    include_once "setup.php";
}
include("../Helpers/SQLHelper.php");

if($_POST["action"] == "test"){
    echo "ok";
}