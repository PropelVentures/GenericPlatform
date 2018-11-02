<?php
require 'socprox3.0/config/dbConfig.php';
$data=$_POST['fdata1'];
$table=$_POST['table'];
$tab_ref="";
$val_ref="";
$i=0;
foreach ($data as $key => $val){
	if($i!=count($data)-1){
    $tab_ref=$tab_ref.$key.",";
	$val_ref=$val_ref."'".$val."',";
	}else {
		$tab_ref=$tab_ref.$key."";
		$val_ref=$val_ref."'".$val."'";
	}
$i++;
}
echo $query="insert into $table($tab_ref) values($val_ref)";
mysqli_query($link,$query) or die("Error".mysql_error($link));
mysqli_close($link);
?>