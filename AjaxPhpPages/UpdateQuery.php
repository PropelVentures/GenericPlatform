<?php
/**
 * Created by PhpStorm.
 * User: Ryan Linehan
 * Date: 3/30/2018
 * Time: 1:10 PM
 */
//header("content-type=application/json");
if(!isset($_SESSION["dbHost"])){
    include_once "../setup.php";
}
include("../Helpers/SQLHelper.php");
$oSqlHelper = new SQLHelper();
$updatedValues = $_POST["UpdatedValues"];
$oldKey = $_POST["OldKey"];
$oldValue = $_POST["OldValue"];
$database_table_name = $_POST["DatabaseTableName"];
$count = 0;
if(!empty($updatedValues) && !empty($oldKey) && !empty($oldValue) && !empty($database_table_name)){
    $query = "UPDATE `$database_table_name` SET ";
    foreach($updatedValues as $key=>$value){
        if($count == sizeof($updatedValues) - 1){
            $query .= "`$key` = '$value' ";
        }
        else{
            $query .= "`$key` = '$value', ";
        }
        $count++;
    }
    $query .= "WHERE `$oldKey` = '$oldValue'";
    $result = $oSqlHelper->queryToDatabase($query);
    if(is_array($result)){
        echo "success";
    }
}