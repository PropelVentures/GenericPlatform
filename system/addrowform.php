<?php
require 'socprox3.0/config/dbConfig.php';
require 'socprox3.0/lib/functions/appFunctions.php';
echo "<div style='float:left;width:100%;padding-bottom:10px;'><U><b> Table Name: ".$_REQUEST['tablename']."</b></U></div>";

// CJ NOTE 1-20-15 ... not sure of the correct usage of tableAlias  and tablename here ... we really want to get more specific about using database_table_name and table_name (the key field in DD) ...


$tableAlias=$_REQUEST['tablename'];
$rows=getColfromTab($config,$tableAlias);
$i=0;
$html="";
while(count($rows)>$i){
	$type=$rows[$i]['1'];
	$colname=$rows[$i]['0'];
	$html=$html."<div style='float:left;width:100%;padding:3px;'>
			<div style='float:left;width:40%;'><label  for='".$colname."'>$colname</label></div>".
			"<div style='float:left;width:60%;'><input  type='text' name='".$colname."'	id='".$colname."' class='text ui-widget-content ui-corner-all'></div></div>";
	$i++;
}
echo $html;
?>