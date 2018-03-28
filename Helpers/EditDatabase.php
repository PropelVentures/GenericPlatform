<?php
/**
 * Created by PhpStorm.
 * User: Doug Porter
 * Date: 3/26/2018
 * Time: 12:50 AM
 */

class EditDatabase
{
    function EditDatabaseValues(Factory $oFactory, $post)
    {
        $queryEnd = "";
        $queryToFindTable = "";
        foreach($post as $key=>$value){
            if(strpos($key, 'old_') === false){
                $queryToFindTable = $queryToFindTable . "generic_field_name = `" . $key . "` AND ";
            }
        }
        $queryToFindTable = substr($queryToFindTable, 0, -6);
        $queryToFindTable = "SELECT `table_alias` FROM  `field_dictionary` WHERE " . $queryToFindTable;
        echo $queryToFindTable;
        $tableName = $oFactory->SQLHelper()->queryToDatabase($queryToFindTable);
        print_r($tableName);
        foreach ($post as $key => $value) {
            $oldKey = "old_" . $key;
            if (array_key_exists($oldKey, $post)) {
                $queryEnd = $queryEnd . $key . " = " . $post[$oldKey] . " AND ";
            }
        }
        $queryEnd = substr($queryEnd, 0, -5);
        //$queryEnd = "affiliation_id=1 AND affiliation_name='Yahoo' AND isActive=1";
        foreach ($post as $key => $value) {
            //$update = "";
            $update = "UPDATE $tableName SET $key=$value WHERE $queryEnd";
            //$update = "UPDATE $tableName SET affiliation_name = 'not Yahoo'";
            //$result = $_SESSION['oFactory']->SQLHelper()->UpdateDatabase($update);
            $result = $oFactory->SQLHelper()->UpdateDatabase($update);
            //$NumSuccess += $result;
            echo "<br>Updated $result<br>";
            //print_r($result);
        }
        //echo "$NumSuccess out of $length updates successful!";
    }
}
