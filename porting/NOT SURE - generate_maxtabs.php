<?php
/**
 * Created by Susmit.
 * User: work
 * Date: 10/12/14
 * Time: 3:25 AM
 */
require('dictionaryConfig.php');
function getLabelName($name){
    $new = explode("_",$name);
    $newval="";
    foreach($new as $k => $v){
        $newval=$newval." ".$v;
    }
    return $newval;
}
$con=mysqli_connect($host,$user,$pass,$dbname);

if($con->connect_errno){
    printf("Connect failed: %s\n", $con->connect_error);
    exit();
}
$database_table_names=array();
$rs=$con->query("
      show tables;
    ");
$i=0;
while ( $row = $rs->fetch_array()) {
    $database_table_names[$i]= $row[0];

    $i++;
}
echo "<h3>GENERATE Max TABS<h3>";
//echo "<h3><a target='_blank' href=\"generateConfig.php\"> CLICK HERE TO GENERATE CONFIGURATION VARIABLE</a></h3>";
echo "<pre>";
foreach($database_table_names as $k => $v){
    // MAX_Tabs generation
    $rs56=$con->query("SELECT max(sub_tab_num) FROM `$fieldDefTABLE` WHERE table_alias = '$v'");
    $maxpages=$rs56->fetch_row();
    $con->query("update `$datadictTABLE` set tab_num='$maxpages[0]' where table_alias = '$v'");
}
echo "<pre> Max Tabs Generated Successfully";
$con->close();