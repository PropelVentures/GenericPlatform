<?php
require('socprox3.0/config/dbConfig.php');
require 'socprox3.0/lib/functions/appFunctions.php';
// Get all parameters provided by the javascript
$colname = $mysqli->real_escape_string(strip_tags($_POST['colname']));
$pagenum = $mysqli->real_escape_string(strip_tags($_POST['pagenum']));
$id = $mysqli->real_escape_string(strip_tags($_POST['id']));
$tabid=$mysqli->real_escape_string(strip_tags($_POST['tabid']));
$inpid=$mysqli->real_escape_string(strip_tags($_POST['inpid']));
$spanid = $mysqli->real_escape_string(strip_tags($_POST['spanid']));
$oldvalue = $mysqli->real_escape_string(strip_tags($_POST['oldvalue']));
$newvalue = $mysqli->real_escape_string(strip_tags($_POST['newvalue']));
$tablename = $mysqli->real_escape_string(strip_tags($_POST['tablename']));
$tblidx=getPK($config,$tablename);
if($newvalue===""){
	$newvalue=null;
}
// This very generic. So this script can be used to update several tables.
$return=0;
echo $query="UPDATE $tablename SET $colname = '$newvalue' WHERE $tblidx = '$id' limit 1";
$return=mysqli_query($link,$query) or die("Error".mysql_error($link));
$query="SELECT $colname from $tablename WHERE $tblidx = '$id' limit 1";
$rst=mysqli_query($link,$query) or die("Error".mysql_error($link));
$row=mysqli_fetch_array($rst);
if($row[0]=='0'||$row[0]=='0000-00-00 00:00:00'){
echo "inside danger";
	$query="UPDATE $tablename SET $colname = '$oldvalue' WHERE $tblidx = '$id' limit 1";
	mysqli_query($link,$query) or die("Error".mysql_error($link));
	$return=0;
}
$query1="SELECT * from $tablename WHERE $tblidx = '$id' limit 1";
$res=$mysqli->query($query1) or die("Error".mysql_error($link));
$tblBody="";
$count="";
$pagenum=$pagenum;
$tableAlias=$tablename;
$rowCount=$mysqli->field_count;
$dbname=$config['db_name'];
$rs=$mysqli->query("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE,COLUMN_KEY
		FROM INFORMATION_SCHEMA.COLUMNS
		WHERE table_name = '$tableAlias'
		AND table_schema = '$dbname'
		");
		// CJ-NOTE *** 01-20-15  uh oh -- global search and replace may have cause an error above...tablealias might be incorrect

$rows= array();
$columns=array();
$count=0;
while ( $row = $rs->fetch_assoc()) {
$columns[$count] = $row['COLUMN_NAME'];
$count++;
}
$row="";
$count=0;
while ( $row = $res->fetch_array()) {
$tblBody=$tblBody.'';
$tbltd="";
$i=0;
$editHtml="<td ><div style='float: left; width: 20px;'>
<img title='Edit row' alt='Edit'
onclick=\"editRow($row[0],'$tableAlias','$pagenum');\"
src='socprox3.0/resources/images/edit.png' class='icon'
style='float: left;'>
</div></td>";
while ($rowCount>$i){
	$tbltd=$tbltd."<td onclick='updateCell(\"$count\",\"$i\");' class='grid' rel='$row[0]'><span spnrel='spnrel' id='span-$count-$i' >$row[$i]</span><input inprel='inprel' onchange='updateCellVal(\"$row[0]\",\"$tableAlias\",\"$columns[$i]\",\"$row[$i]\",\"span-$count-$i\",this.value,this.id,\"$pagenum\",\"tr-$count\",\"$count-$i\");' type='text' id='$count-$i' style='display:none;' value='$row[$i]'/></td>";
	$i++;
}
$deleteHtml="<td><div style='float: left; width: 20px;'>
<img title='Delete row' alt='delete'
onclick=\"deleteRow($row[0],'$tableAlias','$pagenum');\"
src='socprox3.0/resources/images/delete.png' class='icon'
style='float: left;'>
</div></td>";
$tblBody=$tblBody.$editHtml.$tbltd.$deleteHtml.'';
$count++;
echo $tblBody;
}
mysqli_close($link);
if($return==0){
	echo "~error";
}else{
	echo "~ok";
}
echo "~".$spanid;
echo "~".$inpid;
?>

