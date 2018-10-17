<?php

/**
 * Created by Susmit.
 * User: work
 * Date: 10/12/14
 * Time: 3:25 AM
 */
/// $con is set already in db.php file///



echo "<h3>GENERATE Field Dictionary Table<h3>";

echo "<h4><a href='?action=newFD'> CLICK HERE TO GENERATE Field Dictionary Table</a></h4>";

echo "<h4><a href='?action=updateFD'> CLICK HERE TO UPDATE Field Dictionary Table</a></h4>";

///////////
///// FD functions starts here/////////
/////////////////////////////////////////


function newFD() {

    require('dictionaryConfig.php');

    $con = connect($config);

    $con->query("

CREATE TABLE IF NOT EXISTS `field_dictionary` (
  `field_def_id` int(10) NOT NULL AUTO_INCREMENT,
  `table_alias` varchar(50) DEFAULT NULL,
  `generic_field_name` varchar(50) DEFAULT NULL,
  `display_field_order` int(10) DEFAULT NULL,
  `field_identifier` varchar(50) DEFAULT NULL,
  `format_type` varchar(200) NOT NULL,
  `format_length` varchar(200) NOT NULL,
  `field_label_name` varchar(50) DEFAULT NULL,
  `visibility` int(3) DEFAULT NULL,
  `privilege_level` int(3) DEFAULT NULL,
  `editable` varchar(5) DEFAULT 'true',
  `required` varchar(5) DEFAULT NULL,
  `error_message` varchar(100) DEFAULT NULL,
  `help_message` varchar(100) DEFAULT NULL,
  `dropdown_alias` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`field_def_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
    
    
    $con->query("TRUNCATE TABLE $FDtbl");
    
    $con_generic = connect_generic();
    

    $dd = $con->query("SELECT * from $DDtbl");

    while ($row = $dd->fetch_assoc()) {

        $tbName = $row['database_table_name'];

        $checkResult = $con->query("SHOW TABLES LIKE '" . $tbName . "'");

        if ($checkResult->num_rows == 1) {

             $tblAlias = $row['table_alias'];

            echo "Generating FD for table =" . $tbName . " and table_alias = $tblAlias<br>";

           
            /*
              ///checking whether dd->database_table_name exists or not.
              $rs = $con->query("SHOW COLUMNS FROM $tbName");
              echo "<pre>";
              print_r($rs);
              $fdCol = $rs->fetch_assoc();
             */
            $rs = $con->query("SHOW COLUMNS FROM $tbName");

            $fields_used = $row['fields_used'];

            if (!empty($fields_used)) {
                $fields_used = explode('-', $fields_used);
  // print_r($fields_used);die;

                $start = $end = 0;

                if (!empty($fields_used[0]) && !empty($fields_used[1])) {
//// 1-1
                    $start = $fields_used[0];

                    $end = $fields_used[1];
                }else{
                    
                    

                    $end = $fields_used[0];
                    
                }
                
            } else {
                $start = 1;

                $end = $rs->num_rows;
                
                
            }
            //else if (!empty($fields_used[0]) && empty($fields_used[1])) {
//
//        //// 10-  or only one row
//
//        if( isset($fields_used[1]) ){
//            
//            exit('yes');
//        }else{
//            
//            exit('no');
//        }
//        
//        $start = 1;
//
//        $end = $fields_used[0];
//    } else if (empty($fields_used[0]) && !empty($fields_used[1])) {
//
//        //// -10
//
//        $start =  ($rs->num_rows) - $fields_used[1];
//
//        $end = $rs->num_rows;
//    }
            //echo $start . '<br>' . $end;die;
            //*****///*** checking fd_initialization field value and dealing according to value 
            //print_r($row);die;

            if ($row['fd_initialization'] == 'delete' || empty($row['fd_initialization'])) {

                $con->query("delete from $FDtbl where table_alias='$row[table_alias]'");
                $action = 'delete';
            } else
            if ($row['fd_initialization'] == 'update') {

                $action = 'update';
            }


            $i = $j = 1;
            while ($fdCol = $rs->fetch_assoc()) {


                $field = $fdCol['Field'];
                //exit($field);
                if ($i >= $start) {

                    if ($i <= $end) {
                        ////// DEFAULT VALUES FIRST///////////

                        $fdData['help_message'] = $APP_DEFAULT['FD']['help_message'];

                        $fdData['error_message'] = $APP_DEFAULT['FD']['error_message'];

                        $fdData['format_length'] = $APP_DEFAULT['FD']['format_length'];

                        $fdData['privilege_level'] = $APP_DEFAULT['FD']['privilege_level'];

                        $fdData['visibility'] = $APP_DEFAULT['FD']['visibility'];

                        //$fdData['sub_tab_num'] = $DEFAULT['FD']['sub_tab_num'];

                        $fdData['dropdown_alias'] = $APP_DEFAULT['FD']['dropdown_alias'];

                        $fdData['required'] = $APP_DEFAULT['FD']['required'];

                        $fdData['editable'] = $APP_DEFAULT['FD']['editable'];


                        ////////////

                        $fdData['generic_field_name'] = $field;

                        $fdData['table_alias'] = $tblAlias;

                        //$fdData['generic_field_order'] = $i; previous
                        //$fdData['display_field_order'] = $j; previous 
                        $fdData['display_field_order'] = $i;
                        $j++;
                        ///////////////////////
                        /////////////////////////////////////////*********
                        ////******** Special CASES  *******/////////

                        $label = explode('_', $field);







                        ////***** FOR FIELD_label_name ****/////

                        if (!empty($label[0]) && !empty($label[1])) {

                            $strLabel = implode(' ', $label);

                            $fdData['field_label_name'] = ucwords($strLabel);
                        } else {

                            $fdData['field_label_name'] = ucwords($label[0]);
                        }

///////////////////////////////////////////////////////////////////
                        /////////********** field_identifier=KEYFIELD ***********////////


                        if (!empty($label[1])) {

                            if ($label[1] == 'id'){
                                $fdData['field_identifier'] = 'KEYFIELD';
                            $fdData['privilege_level'] = '9';
                            }
                        }


                        //////////////////////////////////////////////////////////////
                        ///**** DEALING WITH FORMAT_TYPE = IMAGE EXTRA ..*****/////
                        //$field = 'ddevelopmentphotoproject11';

                        $field = 'd' . $field;

                        $photo = strpos($field, 'photo'); //// return false if found else true

                        $image = strpos($field, 'image');

                        $profile = strpos($field, 'profile');

                        $user = strpos($field, 'user');

                        $product = strpos($field, 'product');

                        $project = strpos($field, 'project');



                        if ((!empty($photo) || !empty($image)) && empty($user) && empty($profile) && empty($project) && empty($product)) {

                            $fdData['format_type'] = 'image';
                        } else if ((!empty($photo) || !empty($image)) && (!empty($user) || !empty($profile) ) && empty($project) && empty($product)) {


                            preg_match_all('!\d+!', $field, $matches);

                            $matches = array_filter($matches);

                            if (empty($matches)) {

                                $fdData['format_type'] = 'image';
                            } else {

                                $fdData['format_type'] = 'image';
                            }
                        } else if ((!empty($photo) || !empty($image)) && empty($user) && empty($profile) && ( empty($project) || empty($product))) {


                            preg_match_all('!\d+!', $field, $matches);

                            $matches = array_filter($matches);

                            if (empty($matches)) {

                                $fdData['format_type'] = 'image';
                            } else {

                                $fdData['format_type'] = 'image';
                            }
                        }

////************** /////////////////////////
                        /////////////////////////////////////////////////
////////////////////Actuall FD insertion goes here//////////

                        if ($action == 'update') {


                            $fcheck = $con->query("select * from $FDtbl where table_alias='$fdData[table_alias]' and generic_field_name='$fdData[generic_field_name]'");

                            $frow = $fcheck->fetch_assoc();
//print_r($frow);die;

                            if (!isset($frow)) {

                                insert($FDtbl, $fdData);
                            }
                        } else if ($action == 'delete') {

///we don't need to check this record. because it's deleted in advance.
                            $fcheck = $con->query("select * from $FDtbl where table_alias='$fdData[table_alias]' and generic_field_name='$fdData[generic_field_name]'");

                            $frow = $fcheck->fetch_assoc();

                            if (!isset($frow)) {

                                insert($FDtbl, $fdData, $config);
                            } else {

                                mysqli_query($con, "delete * from $FDtbl where table_alias='$fdData[table_alias]' and generic_field_name='$fdData[generic_field_name]'");

                                insert($FDtbl, $fdData, $config);
                            }
                        }

                        ////******************************///////

                        unset($fdData);
                    }
                }///end of start and i if 
                $i++;
            }///FD end of while
            //unset($j);
        } else {

            echo "Table = " . $tbName . "  & table_alias = $tblAlias doesn't exist. <br>";
        }
    }/// DD while ends here


    /*
     * *********************
     * *********************************
     * To Manage Login Page 
     * *********************
     * *********************************
     */

    $fd = $con->query("SELECT * from $FDtbl where table_alias='login'");


    $keyCount = array();
    while ($row = $fd->fetch_assoc()) {

        $keyCount[] = $row['field_def_id'];
    }



    $dd = $con->query("SELECT * from $DDtbl where table_alias='login'");

    $row = $dd->fetch_assoc();


    $rs = $con->query("SHOW COLUMNS FROM $row[database_table_name]");

    $i = 1;
    while ($fdCol = $rs->fetch_assoc()) {


        if ($i == 1) {
          
            if (strpos($fdCol['Field'], 'name') !== false) {
                $userName = $fdCol['Field'];
                $i++;
            }
            
        }

        if ($fdCol['Field'] == 'email') {

            $con->query("update field_dictionary set generic_field_name='email', display_field_order=1,field_label_name='Email',format_type='email' where field_def_id='$keyCount[0]'");
        } else if ($fdCol['Field'] == 'password') {

            $con->query("update field_dictionary set generic_field_name='password', display_field_order=2,field_label_name='Password',format_type='password' where field_def_id='$keyCount[1]'");
        } else if (isset($userName) && !empty($userName)) {

            $con->query("update field_dictionary set generic_field_name='$userName', display_field_order=3,field_label_name='$userName', visibility=0,format_type='username' where field_def_id='$keyCount[2]'");
        }
        
    }

    ///////Manage login page code ends here


    echo "<pre> Field Definition Generated Successfully";
    $con->close();
}

//////////newFD() ends here
////%%%%%%%%%%%%%%%%55///////



function updateFD() {

    require('dictionaryConfig.php');

    $con = connect($config);

   
    
    $con_generic = connect_generic();
    

    $dd = $con->query("SELECT * from $DDtbl");

    while ($row = $dd->fetch_assoc()) {

        
         if ($row['fd_initialization'] == 'update') {

                $action = 'update';
                
                
        $tbName = $row['database_table_name'];

        $checkResult = $con->query("SHOW TABLES LIKE '" . $tbName . "'");

        if ($checkResult->num_rows == 1) {


            echo "Generating FD for table =" . $tbName . "<br>";

            $tblAlias = $row['table_alias'];
            /*
              ///checking whether dd->database_table_name exists or not.
              $rs = $con->query("SHOW COLUMNS FROM $tbName");
              echo "<pre>";
              print_r($rs);
              $fdCol = $rs->fetch_assoc();
             */
            $rs = $con->query("SHOW COLUMNS FROM $tbName");

            $fields_used = $row['fields_used'];

            if (!empty($fields_used)) {
                $fields_used = explode('-', $fields_used);
//    print_r($fields_used);die;

                $start = $end = '';

                if (!empty($fields_used[0]) && !empty($fields_used[1])) {
//// 1-1
                    $start = $fields_used[0];

                    $end = $fields_used[1];
                }
            } else {
                $start = 1;

                $end = $rs->num_rows;
            }


            $i = $j = 1;
            while ($fdCol = $rs->fetch_assoc()) {


                $field = $fdCol['Field'];
                //exit($field);
                if ($i >= $start) {

                    if ($i <= $end) {
                        ////// DEFAULT VALUES FIRST///////////

                        $fdData['help_message'] = $APP_DEFAULT['FD']['help_message'];

                        $fdData['error_message'] = $APP_DEFAULT['FD']['error_message'];

                        $fdData['format_length'] = $APP_DEFAULT['FD']['format_length'];

                        $fdData['privilege_level'] = $APP_DEFAULT['FD']['privilege_level'];

                        $fdData['visibility'] = $APP_DEFAULT['FD']['visibility'];

                        //$fdData['sub_tab_num'] = $DEFAULT['FD']['sub_tab_num'];

                        $fdData['dropdown_alias'] = $APP_DEFAULT['FD']['dropdown_alias'];

                        $fdData['required'] = $APP_DEFAULT['FD']['required'];

                        $fdData['editable'] = $APP_DEFAULT['FD']['editable'];


                        ////////////

                        $fdData['generic_field_name'] = $field;

                        $fdData['table_alias'] = $tblAlias;

                        //$fdData['generic_field_order'] = $i; previous
                        //$fdData['display_field_order'] = $j; previous 
                        $fdData['display_field_order'] = $i;
                        $j++;
                        ///////////////////////
                        /////////////////////////////////////////*********
                        ////******** Special CASES  *******/////////

                        $label = explode('_', $field);







                        ////***** FOR FIELD_label_name ****/////

                        if (!empty($label[0]) && !empty($label[1])) {

                            $strLabel = implode(' ', $label);

                            $fdData['field_label_name'] = ucwords($strLabel);
                        } else {

                            $fdData['field_label_name'] = ucwords($label[0]);
                        }

///////////////////////////////////////////////////////////////////
                        /////////********** field_identifier=KEYFIELD ***********////////


                        if (!empty($label[0]) && !empty($label[1])) {

                            if ($label[1] == 'id')
                                $fdData['field_identifier'] = 'KEYFIELD';
                            
                            $fdData['privilege_level'] = '9';
                        }


                        //////////////////////////////////////////////////////////////
                        ///**** DEALING WITH FORMAT_TYPE = IMAGE EXTRA ..*****/////
                        //$field = 'ddevelopmentphotoproject11';

                        $field = 'd' . $field;

                        $photo = strpos($field, 'photo'); //// return false if found else true

                        $image = strpos($field, 'image');

                        $profile = strpos($field, 'profile');

                        $user = strpos($field, 'user');

                        $product = strpos($field, 'product');

                        $project = strpos($field, 'project');



                        if ((!empty($photo) || !empty($image)) && empty($user) && empty($profile) && empty($project) && empty($product)) {

                            $fdData['format_type'] = 'image';
                        } else if ((!empty($photo) || !empty($image)) && (!empty($user) || !empty($profile) ) && empty($project) && empty($product)) {


                            preg_match_all('!\d+!', $field, $matches);

                            $matches = array_filter($matches);

                            if (empty($matches)) {

                                $fdData['format_type'] = 'image';
                            } else {

                                $fdData['format_type'] = 'image';
                            }
                        } else if ((!empty($photo) || !empty($image)) && empty($user) && empty($profile) && ( empty($project) || empty($product))) {


                            preg_match_all('!\d+!', $field, $matches);

                            $matches = array_filter($matches);

                            if (empty($matches)) {

                                $fdData['format_type'] = 'image';
                            } else {

                                $fdData['format_type'] = 'image';
                            }
                        }

////************** /////////////////////////
                        /////////////////////////////////////////////////
////////////////////Actuall FD insertion goes here//////////
                        
                       

                        if ($action == 'update') {


                            $fcheck = $con->query("select * from $FDtbl where table_alias='$fdData[table_alias]' and generic_field_name='$fdData[generic_field_name]'");

                            $frow = $fcheck->fetch_assoc();
//print_r($frow);die;

                            if (!isset($frow)) {

                                insert($FDtbl, $fdData);
                            }
                        } 

                        ////******************************///////

                        unset($fdData);
                    }
                }///end of start and i if 
                $i++;
            }///FD end of while
            //unset($j);
            
             update($DDtbl, array("fd_initialization" => ""), array("dict_id" => $row['dict_id']));
        } else {

            echo "Table = " . $tbName . "  doesn't exist. <br>";
        }
        
       
         }
    }/// DD while ends here


   

    echo "<pre> Field Definition Generated Successfully";
    $con->close();
}

if (isset($_GET['action'])) {

    $_GET['action']();
}    




////////////////

