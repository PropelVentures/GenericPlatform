<?php
/**
 * Created by PhpStorm.
 * User: Ryan Linehan
 * Date: 3/30/2018
 * Time: 1:10 PM
 */
if(!isset($_SESSION["dbHost"])){
    include_once "../setup.php";
}
include("../Helpers/SQLHelper.php");
$oSqlHelper = new SQLHelper();
$addedValues = $_POST["AddedValues"];
$database_table_name = $_POST["DatabaseTableName"];
$count = 0;
if(!empty($database_table_name) && !empty($addedValues)){
    $query = "INSERT INTO `$database_table_name` VALUES (";
    foreach($addedValues as $key=>$value){
        if($count == sizeof($addedValues) - 1){
            $query .= "'$value' ";
        }
        else{
            $query .= "'$value', ";
        }
        $count++;
    }
    $query .= ")";
    $result = $oSqlHelper->queryToDatabase($query);
    if(is_array($result)){
        echo "success";
    }
}