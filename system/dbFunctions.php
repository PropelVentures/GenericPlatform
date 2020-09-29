<?php
/*
function connect($config = 'false')
function connect_generic()
function connect($config = 'false') 
function query($qry)
function insert($table_name, $data, $config = 'false')
function insertString($data)
function update($table_name, $data, $where, $config = 'false')
function delete($table_name, $where)
function updateString($data)
function whereString($data)
function getWhere($table_name, $where = "false", $order = "", $setwhereString = true)
function get($table_name, $ws)
function getMulti($table_name, $ws, $field = 'false')
function numOfRows($table_name, $where)
function sumValues($table_name, $where = 'false')
function firstFieldName($tableName)
function getColumnNames($tableName)
function nextKey($tblName, $pkey, $current_id, $clause)
function prevKey($tblName, $pkey, $current_id, $clause)
function firstKey($tblName, $pkey, $clause)
function lastKey($tblName, $pkey, $clause)
function secure($value, $type = "", $quoted = true)
function is_empty($value)

*/

/**
 * Establish mysqli connection
 * 
 * @param mixed $config Config for the database connection, optional
 * 
 * @author ph
 * 
 * @return false|mysqli
 */
function connect($config = null)
{
    $config = $_SESSION['config'];
    return mysqli_connect($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);
}

function connect_generic()
{
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


/**
 * Execute query with mysqli connection
 * 
 * @param string $qry SQL to execute
 * 
 * @author ph
 * 
 * @return void
 */
function query($qry)
{
    $result = mysqli_query(connect(), "$qry");
}

function insert($table_name, $data, $config = 'false')
{

    $con = connect($config);
    $is = insertString($data);
    //echo "INSERT INTO $table_name $is";die;
    mysqli_query($con, "INSERT INTO $table_name $is");
    return mysqli_insert_id($con);
}

function insertString($data)
{
    $f = implode(", ", array_keys($data));
    $v = array();
    foreach ($data as $d) {
        $v[] = "'$d'";
    }
    $v = implode(", ", $v);
    return "($f) VALUES ($v)";
}

function update($table_name, $data, $where, $config = 'false')
{
    $ws = whereString($where);
    $us = updateString($data);
//    echo ("UPDATE $table_name SET $us WHERE $ws");echo "<br>";

    $con = connect($config);
    if (!$status = mysqli_query($con, "UPDATE $table_name SET $us WHERE $ws")) {
        $errorMessage = "Error description: table_name -> $table_name, error details - " . mysqli_error($con);
        if ($_SESSION['user_privilege'] == 9 || $_SESSION['user_privilege'] == 3)
            $status = $errorMessage;
    }
    return $status;
}

function delete($table_name, $where)
{
    $ws = whereString($where);

    //exit("DELETE FROM $table_name WHERE $ws");

    mysqli_query(connect(), "DELETE FROM $table_name WHERE $ws");
}

function updateString($data)
{
    $i = array();
    foreach ($data as $key => $value) {
        //5.4.202 trimiing spaces
        trimSpacesAroundSepraters($value, ',');
        trimSpacesAroundSepraters($value, ';', ',');
        $i[] = "$key = '$value'";
    }

    return implode(", ", $i);
}

function whereString($data)
{
    $w = array();
    foreach ($data as $key => $value) {
        $w[] = "$key = '$value'";
    }
    return implode(" AND ", $w);
}

function getWhere($table_name, $where = "false", $order = "", $setwhereString = true)
{

    if ($where != 'false') {
        if ($setwhereString) {
            $where = whereString($where);
        }
        $result = mysqli_query(connect(), "SELECT * FROM $table_name WHERE $where $order");
    } else {

        $result = mysqli_query(connect(), "SELECT * FROM $table_name $order");
    }
    $r = array();
    while ($row = mysqli_fetch_array($result)) {
        $r[] = $row;
    }
    return $r;
}

function get($table_name, $ws)
{
//echo "SELECT * FROM $table_name WHERE $ws ";die;
    $result = mysqli_query(connect(), "SELECT * FROM $table_name WHERE $ws ");

    //$r = array();
    $row = mysqli_fetch_array($result);

    return $row;
}

function getMulti($table_name, $ws, $field = 'false')
{
//echo "SELECT * FROM $table_name WHERE $ws ";die;

    if ($field == 'false')
        $field = '*';
    $result = mysqli_query(connect(), "SELECT $field FROM $table_name WHERE $ws ");

    //$r = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $r[] = $row;
    }
    return $r;
}

function numOfRows($table_name, $where)
{

    $ws = whereString($where);

    //exit("SELECT * FROM $table_name WHERE $ws ");

    $result = mysqli_query(connect(), "SELECT * FROM $table_name WHERE $ws ");


    return mysqli_num_rows($result);
}

function sumValues($table_name, $where = 'false')
{


    if ($where != 'false') {

        $ws = whereString($where);

        $result = mysqli_query(connect(), "SELECT SUM(value) as total_value FROM $table_name WHERE $ws");
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

function firstFieldName($tableName)
{


    $con = connect();

    $res = $con->query("SHOW COLUMNS FROM $tableName");


    $row = mysqli_fetch_assoc($res);

    return $row['Field'];
}

/*
 * ****
 * ************************
 * ************************
 *
 * @function getColumnNamee
 *
 * ********
 * *************
 * ******************************************
 */

function getColumnNames($tableName)
{
    $con = connect();
    $res = $con->query("SHOW COLUMNS FROM $tableName");
    $data = array();
    if ($res->num_rows) {
        while ($row = $res->fetch_assoc()) {
            $data[$row['Field']] = $row['Field'];
        }
    }
    return $data;
}

function nextKey($tblName, $pkey, $current_id, $clause)
{


    $con = connect();


    if (!empty($clause))
        $clause = 'and ' . $clause;

    //exit("select $pkey from $tblName where $pkey = (select min($pkey) from $tblName where $pkey > $current_id $clause)");

    $res = $con->query("select $pkey from $tblName where $pkey = (select min($pkey) from $tblName where $pkey > $current_id $clause)");


    $row = mysqli_fetch_assoc($res);

    return $row[$pkey];
}

function prevKey($tblName, $pkey, $current_id, $clause)
{


    $con = connect();


    if (!empty($clause))
        $clause = 'and ' . $clause;
    //exit("select $pkey from $tblName where $pkey = (select min($pkey) from $tblName where $pkey > $current_id)");

    $res = $con->query("
    	  select $pkey from $tblName where $pkey =
         (select max($pkey) from $tblName where $pkey < $current_id $clause)
         ");


    $row = mysqli_fetch_assoc($res);

    return $row[$pkey];
}

function firstKey($tblName, $pkey, $clause)
{


    $con = connect();

    if (!empty($clause))
        $clause = 'where ' . $clause;

    $res = $con->query("select $pkey from $tblName $clause limit 1");


    $row = mysqli_fetch_assoc($res);

    return $row[$pkey];
}

function lastKey($tblName, $pkey, $clause)
{


    $con = connect();

    if (!empty($clause))
        $clause = 'where ' . $clause;

    $res = $con->query("select $pkey from $tblName $clause ORDER BY $pkey DESC limit 1");


    $row = mysqli_fetch_assoc($res);

    return $row[$pkey];
}


/**
 * secure
 *
 * @param string $value
 * @param string $type
 * @param boolean $quoted
 * @return string
 */
function secure($value, $type = "", $quoted = true)
{
    global $con;
    if ($value !== 'null') {
        // [1] Sanitize //
        /* Escape all (single-quote, double quote, backslash, NULs) */
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        /* Convert all applicable characters to HTML entities */
        $value = htmlentities($value, ENT_QUOTES, 'utf-8');
        // [2] Safe SQL //
        $value = $con->real_escape_string($value);
        switch ($type) {
            case 'int':
                $value = ($quoted) ? "'" . intval($value) . "'" : intval($value);
                break;
            case 'datetime':
                $value = ($quoted) ? "'" . set_datetime($value) . "'" : set_datetime($value);
                break;
            case 'search':
                if ($quoted) {
                    $value = (!is_empty($value)) ? "'%" . $value . "%'" : "''";
                } else {
                    $value = (!is_empty($value)) ? "'%%" . $value . "%%'" : "''";
                }
                break;

            case 'NULL':
                $value = NULL;
                break;
            default:
                $value = (!is_empty($value)) ? "'" . $value . "'" : "''";
                break;
        }
    }
    return $value;
}

/**
 * is_empty
 *
 * @param string $value
 * @return boolean
 */
function is_empty($value)
{
    if (strlen(trim(preg_replace('/\xc2\xa0/', ' ', $value))) == 0) {
        return true;
    } else {
        return false;
    }
}


/* ------------------------------- */
/* Date */
/* ------------------------------- */

/**
 * set_datetime
 *
 * @param string $date
 * @return string
 */
function set_datetime($date)
{
    return date("Y-m-d H:i:s", strtotime($date));
}


/**
 * get_datetime
 *
 * @param string $date
 * @return string
 */
function get_datetime($date)
{
    return date("m/d/Y g:i A", strtotime($date));
}

$con = connect();

//print_r($con->query("select * from users"));die;

//$_GET["checkUserName"] = 'testuser';
//
//$uname = getWhere('users', array('uname' => $_GET["checkUserName"]));
//
//
//print_r($uname);

/**
 * took a string by reference and a $separator and parse it such that if there are extra spaces around that $separator
 *it trims out those spaces like width=5px ; height=10px   ;  it will become width=5px;height=10px;
 */
function trimSpacesAroundSepraters(&$string, $separator, $unsetIfRaw = false)
{
    if (!empty($string) && is_string($string)) {
        $parts = explode($separator, $string);
        if (count($parts) > 1) {
            $string = '';
            foreach ($parts as $key => $value) {
                if (!empty($value)) {
                    if ($value !== $unsetIfRaw) {
                        $string = $string . trim($value) . $separator;
                    }
                }
            }
        }
    }
}

function unsetExtraRows(&$data)
{
    foreach ($data as $key => $value) {
        if (is_numeric($key)) {
            unset($data[$key]);
        }
    }
}

?>
