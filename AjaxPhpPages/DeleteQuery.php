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
$ValuesToDelete = $_POST["ValuesToDelete"];
$database_table_name = $_POST["DatabaseTableName"];
$primarykey = $_POST["PrimaryKey"];
$count = 0;
if(!empty($database_table_name) && !empty($ValuesToDelete) && !empty($primarykey)){
    $query = "DELETE FROM `$database_table_name` WHERE `$primarykey` IN (";
    foreach($ValuesToDelete as $key=>$value){
        if($count == sizeof($ValuesToDelete) - 1){
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