<?php
require 'socprox3.0/config/dbConfig.php';
require 'socprox3.0/lib/functions/appFunctions.php';
$id = $mysqli->real_escape_string(strip_tags($_REQUEST['id']));
$tablename = $mysqli->real_escape_string(strip_tags($_REQUEST['tablename']));
$tblidx=getPK($config,$tablename);
$return=false;
if ( $stmt = $mysqli->prepare("DELETE FROM ".$tablename."  WHERE $tblidx = ?")) {
	$stmt->bind_param("i", $id);
    $return = $stmt->execute();
	$stmt->close();
}  
$mysqli->close();        
echo $return ? "ok" : "error";
?>
      

