<?php
header("content-type:application/json");
session_start();
//include("Factory.php");
if(!isset($_SESSION["dbHost"])) {
    include_once "setup.php";
}
include("Helpers/SQLHelper.php");
$oSQLHelper = new SQLHelper();
$results = $oSQLHelper->queryToDatabase("SELECT * FROM `affiliations`");

/**
 * Created by PhpStorm.
 * User: Ryan Linehan
 * Date: 3/13/2018
 * Time: 8:54 PM
 */

if($_POST['action'] == "test") {

    /**
     * we can pass any action like block, follow, unfollow, send PM....
     * if we get a 'follow' action then we could take the user ID and create a SQL command
     * but with no database, we can simply assume the follow action has been completed and return 'ok'
     **/

    echo "ok";
}
elseif ($_POST['action'] == "test2"){
    echo json_encode($results);
}
