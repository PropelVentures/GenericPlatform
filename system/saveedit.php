<?php
require 'socprox3.0/config/dbConfig.php';
$data=$_POST['fdata1'];
$table=$_POST['table'];
$tabidx=$_POST['tabidx'];
$id=$_POST['id'];

$tab_ref="";
$val_ref="";
$update_ref="";
$i=0;

//Points='50',VerifiedBy='70'
foreach ($data as $key => $val){
	if ($key!=$tabidx) {
		if($i!=count($data)-1){
		$update_ref=$update_ref." ".$key."='".$val."',";
					
	   	}else {
		 $update_ref=$update_ref." ".$key."='".$val."'";
		}
	}
$i++;
}
$subject = $update_ref;
$search = ',';
$replace="";
$update_ref=strrev(implode(strrev($replace), explode($search, strrev($subject), 2))); 
//echo $tab_ref."<br>".$val_ref;
$query="update $table set $update_ref where $tabidx=$id";
$return=mysqli_query($link,$query) or die("Error".mysql_error($link));
mysqli_close($link);
echo $return ? "ok" : "error";
?>