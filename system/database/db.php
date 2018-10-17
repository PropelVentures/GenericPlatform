<?php

if ($_SERVER['HTTP_HOST'] === 'localhost') {
    $GLOBALS['db-username'] = "root";
    $GLOBALS['db-host'] = "localhost";
    $GLOBALS['db-password'] = "";
    $GLOBALS['db-database'] = "generic";
} else {
    /*
     * Here make changes if you want to change the DB Credentials
     */
    $GLOBALS['db-host'] = "localhost";
    $GLOBALS['db-username'] = "genericinternal";
    $GLOBALS['db-password'] = "Upwork2016!";
    $GLOBALS['db-database'] = "genericplatform";
}

//print_r($config);die;
//if (!empty($config['db_name'])) {


function connect($config = 'false') {

    $config = $_SESSION['config'];

    return mysqli_connect($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);
}

function connect_generic() {

    return mysqli_connect($GLOBALS['db-host'], $GLOBALS['db-username'], $GLOBALS['db-password'], $GLOBALS['db-database']);
}

/* } else {

  function connect($config = 'false') {


  if (!empty($_SESSION['config'])) {

  $config = $_SESSION['config'];

  return mysqli_connect($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);
  } else {

  return mysqli_connect($GLOBALS['db-host'], $GLOBALS['db-username'], $GLOBALS['db-password'], $GLOBALS['db-database']);
  }
  }

  } */

/////////
//////////////////////insert/////


function query($qry) {
//echo "SELECT * FROM $table WHERE $ws ";die;
    $result = mysqli_query(connect(), "$qry");

    //$row = mysqli_fetch_array($result);
    // return $row;
}

function insert($table, $data, $config = 'false') {

    $con = connect($config);
    $is = insertString($data);
    //echo "INSERT INTO $table $is";die;
    mysqli_query($con, "INSERT INTO $table $is");
    return mysqli_insert_id($con);
}

function insertString($data) {
    $f = implode(", ", array_keys($data));
    $v = array();
    foreach ($data as $d) {
        $v[] = "'$d'";
    }
    $v = implode(", ", $v);
    return "($f) VALUES ($v)";
}

function update($table, $data, $where, $config = 'false') {       
    $ws = whereString($where);
    $us = updateString($data);
//    echo ("UPDATE $table SET $us WHERE $ws");echo "<br>";
    
    $con = connect($config); 
    if(!$status = mysqli_query($con, "UPDATE $table SET $us WHERE $ws") )
    {
        $errorMessage = "Error description: database_table_name -> $table, error details - " . mysqli_error($con);
        if($_SESSION['user_privilege'] == 9 || $_SESSION['user_privilege'] == 3)
            $status = $errorMessage;
    } 
    return $status;
}

function delete($table, $where) {
    $ws = whereString($where);

    //exit("DELETE FROM $table WHERE $ws");

    mysqli_query(connect(), "DELETE FROM $table WHERE $ws");
}

function updateString($data) {
    $i = array();
    foreach ($data as $key => $value) {
        $i[] = "$key = '$value'";
    }
    return implode(", ", $i);
}

function whereString($data) {
    $w = array();
    foreach ($data as $key => $value) {
        $w[] = "$key = '$value'";
    }
    return implode(" AND ", $w);
}

function getWhere($table, $where = "false", $order = "") {

    if ($where != 'false') {
        $ws = whereString($where);

        //exit("SELECT * FROM $table WHERE $ws $order");

        $result = mysqli_query(connect(), "SELECT * FROM $table WHERE $ws $order");
    } else {

        $result = mysqli_query(connect(), "SELECT * FROM $table $order");
    }
    $r = array();
    while ($row = mysqli_fetch_array($result)) {
        $r[] = $row;
    }
    return $r;
}

function get($table, $ws) {
//echo "SELECT * FROM $table WHERE $ws ";die;
    $result = mysqli_query(connect(), "SELECT * FROM $table WHERE $ws ");

    //$r = array();
    $row = mysqli_fetch_array($result);

    return $row;
}

function getMulti($table, $ws, $field='false') {
//echo "SELECT * FROM $table WHERE $ws ";die;
    
    if($field == 'false')
        $field = '*';
    $result = mysqli_query(connect(), "SELECT $field FROM $table WHERE $ws ");

    //$r = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $r[] = $row;
    }
    
    return $r;
}

function numOfRows($table, $where) {

    $ws = whereString($where);

    //exit("SELECT * FROM $table WHERE $ws ");

    $result = mysqli_query(connect(), "SELECT * FROM $table WHERE $ws ");


    return mysqli_num_rows($result);
}

function sumValues($table, $where = 'false') {


    if ($where != 'false') {

        $ws = whereString($where);

        $result = mysqli_query(connect(), "SELECT SUM(value) as total_value FROM $table WHERE $ws");
    } else {

        $result = mysqli_query(connect(), "SELECT SUM(value) as total_value FROM $table");
    }


    $row = mysqli_fetch_assoc($result);

    return $row['total_value'];
}

/*
 * ****
 * ************************
 * ************************
 * 
 * @function firstFieldName 
 * 
 * ********
 * *************
 * ******************************************
 */

function firstFieldName($tableName) {


    $con = connect();

    $res = $con->query("SHOW COLUMNS FROM $tableName");


    $row = mysqli_fetch_assoc($res);

    return $row['Field'];
}

function nextKey($tblName, $pkey, $current_id, $clause) {


    $con = connect();


    if (!empty($clause))
        $clause = 'and ' . $clause;

    //exit("select $pkey from $tblName where $pkey = (select min($pkey) from $tblName where $pkey > $current_id $clause)");

    $res = $con->query("select $pkey from $tblName where $pkey = (select min($pkey) from $tblName where $pkey > $current_id $clause)");


    $row = mysqli_fetch_assoc($res);

    return $row[$pkey];
}

function prevKey($tblName, $pkey, $current_id, $clause) {


    $con = connect();


    if (!empty($clause))
        $clause = 'and ' . $clause;
    //exit("select $pkey from $tblName where $pkey = (select min($pkey) from $tblName where $pkey > $current_id)");

    $res = $con->query("select $pkey from $tblName where $pkey = (select max($pkey) from $tblName where $pkey < $current_id $clause)");


    $row = mysqli_fetch_assoc($res);

    return $row[$pkey];
}

function firstKey($tblName, $pkey, $clause) {


    $con = connect();

    if (!empty($clause))
        $clause = 'where ' . $clause;

    $res = $con->query("select $pkey from $tblName $clause limit 1");


    $row = mysqli_fetch_assoc($res);

    return $row[$pkey];
}

function lastKey($tblName, $pkey, $clause) {


    $con = connect();

    if (!empty($clause))
        $clause = 'where ' . $clause;

    $res = $con->query("select $pkey from $tblName $clause ORDER BY $pkey DESC limit 1");


    $row = mysqli_fetch_assoc($res);

    return $row[$pkey];
}

$con = connect();

//print_r($con->query("select * from users"));die;
  
//$_GET["checkUserName"] = 'testuser';
//
//$uname = getWhere('users', array('uname' => $_GET["checkUserName"]));
//
//
//print_r($uname);


