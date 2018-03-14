<?php
header("content-type:application/json");
session_start();
//include("Factory.php");
if(!isset($_SESSION["dbHost"])) {
    include_once "setup.php";
}
include("Helpers/SQLHelper.php");
$oSQLHelper = new SQLHelper();
//$results = $oSQLHelper->queryToDatabase("SELECT * FROM `states`");

/**
 * Created by PhpStorm.
 * User: Ryan Linehan
 * Date: 3/13/2018
 * Time: 8:54 PM
 */

$resultArray = $_POST["resultArray"];
$currentPage = $_POST["currentPage"];
$amountToDisplay = $_POST["amountToDisplay"];

$lowerBound = $amountToDisplay *$currentPage;
$upperBound = $amountToDisplay;

$splitArray = array_slice($resultArray, $lowerBound, $upperBound);

echo json_encode($splitArray);
