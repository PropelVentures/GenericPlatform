<?php
require('socprox3.0/config/dbConfig.php');
require 'socprox3.0/lib/functions/appFunctions.php';
$id = $mysqli->real_escape_string(strip_tags($_POST['id']));
$tablename = $mysqli->real_escape_string(strip_tags($_POST['tablename']));
$tblidx=getPK($config,$tablename);                                                
$return=false;
$query="select * from $tablename where $tblidx=$id";
$rs=mysqli_query($link, $query) or die(mysqli_errno($link));
$rs_rows=mysqli_fetch_array($rs);
$rows=getColfromTab($config,$tablename);
$i=0;
$html="";
while(count($rows)>$i){
	$type=$rows[$i]['1'];
	$colname=$rows[$i]['0']; 
    $disablefield='';
	if($colname==$tblidx){
		$disablefield='disabled';
	}
	$html=$html."<div style='float:left;width:100%;padding:3px;'>
			<div style='float:left;width:40%;'><label  for='".$colname."'>$colname</label></div>".
			"<div style='float:left;width:60%;'><input $disablefield type='text' name='".$colname."'	id='".$colname."' value='".$rs_rows[$i]."' class='text ui-widget-content ui-corner-all'></div></div>";
	$i++;
}
$html=$html."<input type='hidden' id='tabidx' value='$tblidx'><input type='hidden' id='id' value='$id'>";
echo $html;
$mysqli->close();        
?>
      
