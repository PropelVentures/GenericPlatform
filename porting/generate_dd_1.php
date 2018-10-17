<?php

/**
 * Created by Susmit.
 * User: work
 * Date: 10/12/14
 * Time: 3:25 AM
 */



echo "<h3>GENERATE DATA DICTIONARY<h3>";
echo "<h3><a  href='?action=newDD'> CLICK HERE TO GENERATE New DD DEFINITION</a></h3>";

echo "<h3><a  href='?action=appendDD'> CLICK HERE TO UPDATE/Append  DD DEFINITION</a></h3>";

function newDD() {

    require('dictionaryConfig.php');

    /*
     * 
     * emptying the DD table
     */
    
   // $con = connect();
    
    
    $con->query("truncate table $DDtbl");


    $rs = $con->query("select * from $DDT");

   
    while ($row = $rs->fetch_array(MYSQLI_ASSOC)) {

        //print_r($row);die;
        unset($row['dict_id']);

        $ddKeys = array('database_table_name', 'table_alias', 'table_type', 'tab_name', 'keyfield', 'parent_table', 'list_filter', 'list_sort');


        foreach ($ddKeys as $k) {

            if (isset($row[$k])) {

                $dd_key = (array_keys($DEFAULT['DD']));

                foreach ($dd_key as $key) {

                    $row[$k] = str_replace($key, $DEFAULT['DD'][$key], $row[$k]);
                }
            }///inner foreach
        }///outer foreach


        insert($DDtbl, $row);
    }
}

///end of newDD generation

function appendDD() {

    require('dictionaryConfig.php');


    $rs = $con->query("select * from $DDT");

    while ($row = $rs->fetch_array(MYSQLI_ASSOC)) {

//exit("select * from $DDtbl where table_alias='$row[table_alias]' and display_page=$row['display_page']");
        $r = $con->query("select * from $DDtbl where table_alias='$row[table_alias]' and display_page='$row[display_page]'");

        $ddCheck = $r->fetch_array(MYSQLI_ASSOC);

//print_r($ddCheck);die;
        if (isset($ddCheck)) {
            
            $id = $ddCheck['dict_id'];
            
            unset($row['dict_id']);

            $ddKeys = array('database_table_name', 'table_alias', 'table_type', 'tab_name', 'keyfield', 'parent_table', 'list_filter', 'list_sort');


            foreach ($ddKeys as $k) {

                if (isset($row[$k])) {

                    $dd_key = (array_keys($DEFAULT['DD']));

                    foreach ($dd_key as $key) {

                        $row[$k] = str_replace($key, $DEFAULT['DD'][$key], $row[$k]);
                    }
                }///inner foreach
            }///outer foreach


            update($DDtbl,$row,"dict_id=$id");
           
            
        } else {
            /// append
            unset($row['dict_id']);

            $ddKeys = array('database_table_name', 'table_alias', 'table_type', 'tab_name', 'keyfield', 'parent_table', 'list_filter', 'list_sort');


            foreach ($ddKeys as $k) {

                if (isset($row[$k])) {

                    $dd_key = (array_keys($DEFAULT['DD']));

                    foreach ($dd_key as $key) {

                        $row[$k] = str_replace($key, $DEFAULT['DD'][$key], $row[$k]);
                    }
                }///inner foreach
            }///outer foreach


            insert($DDtbl, $row);
        }//// appended new record
    }
}

//echo "<pre> Data Dictionary Generated Successfully";
//$con->close();



if (isset($_GET['action'])) {

    $_GET['action']();
} 