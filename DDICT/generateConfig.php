<?php

/**
 * Created by Susmit.
 * User: Admin
 * Date: 10/13/14
 * Time: 9:37 AM
 */
session_start();

function setDefinitionArray($GLOBALS1)
{



  $host = $GLOBALS1['db-host'];
  $user = $GLOBALS1['db-username'];
  $pass = $GLOBALS1['db-password'];
  $dbname = $GLOBALS1['db-database'];
  //require('dictionaryConfig.php');

  require('default.php');

  $con = mysqli_connect($host, $user, $pass, $dbname);
  if ($con->connect_errno)
  {
    printf("Connect failed: %s\n", $con->connect_error);
    exit();
  }


//  $rs = $con->query("select `database_table_name` from $datadictTABLE;");
  $rs = $con->query("select `table_alias` from $datadictTABLE;");
  $tblCounter = 0;
  $coding_ArrayView = "";
  $rs3 = $con->query("SELECT `COLUMN_NAME` FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$fieldDefTABLE' AND table_schema = '$dbname'");
  $selectPattern = "";

  while ($rwe = $rs3->fetch_assoc())
  {
    if ($selectPattern == "")
    {
      $selectPattern = $selectPattern . "`" . $rwe['COLUMN_NAME'] . "`";
    }
    else
    {
      $selectPattern = $selectPattern . ",`" . $rwe['COLUMN_NAME'] . "`";
    }
  }


  while ($row = $rs->fetch_assoc())
  { //Loop for table names from dictionary

//d($row);

    $database_table_nameVal = $row['table_alias'];
//    d($database_table_nameVal);

    $rs1 = $con->query("select $selectPattern from `$fieldDefTABLE` where `table_alias`='$database_table_nameVal' order by `generic_field_order` asc");
    $fldCounter = 0;

    while ($row1 = $rs1->fetch_assoc())
    { // Loop for field names from field-definition

      $fldparamCounter = 0;
      $trueParamCounter = 0;
      foreach ($row1 as $k => $v)
      { // Loop for each field's parameters

        if ($fldparamCounter > 1)
        {
          $v = ($v == "" || $v == null) ? (isset($DEFAULT[$k]) ? $DEFAULT[$k] : "") : $v;
          $DATA[$database_table_nameVal][$row1['generic_field_name']]["$k"] = $v; // Creating $DATA array
          $DATA_ARRAY[$database_table_nameVal][$fldCounter]["$k"] = $v;
          //DISPLAY PURPOSE ONLY START
          if ($fldparamCounter == 2)
          {
            $coding_ArrayView = $coding_ArrayView . '<br><br><br>$DATA[' . $row['database_table_name'] . '][' . $row1['generic_field_name'] . '][' . $k . ']  =>  ' . $v;
          }
          else
          {
            $coding_ArrayView = $coding_ArrayView . '<br>$DATA[' . $row['database_table_name'] . '][' . $row1['generic_field_name'] . '][' . $k . ']  =>  ' . $v;
          }
          //DISPLAY PURPOSE ONLY END
          $trueParamCounter++;
        }
        $fldparamCounter++;
      }
      $fldCounter++;
    }
    $tblCounter++;
  }


  // echo "<br>PASSES<br>";
  $_SESSION["datadictionary"] = $DATA;
  $_SESSION["datadictionaryArray"] = $DATA_ARRAY;
  $_SESSION["DD_database_table_name"] = $datadictTABLE;
  $_SESSION["FD_database_table_name"] = $fieldDefTABLE;

//d($DATA, $DATA_ARRAY);
//d($datadictTABLE, $fieldDefTABLE);
//dd($_SESSION);

}

/*echo "<H3><u> CONFIGURATION ARRAY DEVELOPER FRIENDLY VIEW </u></H3>";
    echo "<pre>$coding_ArrayView</pre>";
    echo "<H3><u> CONFIGURATION ARRAY ANATOMY </u></H3><pre>";print_r($DATA);echo "</pre>";*/
