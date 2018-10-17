<?php

/*
 * 
 * masterFunctions file all forms Actions are recorded here
 * $$$$$$$$44
 * $$$$$$$$$$$$$$$$$$$$$44
 * 
 * Action sequence details are as follow
 * 
 * 
 * if (isset($_GET["button"]) && !empty($_GET["button"]) && $_GET["button"] == 'cancel') {
 * 
 * *****************
 * 
 * if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'add')
 * 
 * **********
 * 
 * if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'update')
 * 
 * ************
 * 
 * if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] == 'login')
 * 
 */



/*
 * 
 * 
 * 
 * 
 * 
 * 
 * ***********************Cancel Button Action
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */



if (isset($_GET["button"]) && !empty($_GET["button"]) && $_GET["button"] == 'cancel') {



    update("data_dictionary", array("dd_editable" => '1'), array("display_page" => $_GET['display']));


    // exit($_SESSION[return_url2]);


    if ($_GET['table_type'] == 'child') {

        $link_to_return = $_SESSION['child_return_url'];
    }
    /*
      else if ($_GET['checkFlag'] == 'true')
      $link_to_return = $_SESSION['return_url2']; */ else if ($_GET['addFlag'] == 'true')
        $link_to_return = BASE_URL . "system/profile.php?display?" . $_GET['display'] . "&tab=" . $_GET['tab'] . "&tabNum=" . $_GET['tabNum'] . "&checkFlag=true" . "&table_type=" . $_GET['table_type'];
    else
        $link_to_return = $_SESSION['return_url2'];


    if ($_GET['fnc'] != 'onepage') {

        echo "<script>window.location='$link_to_return'</script>";
    } else {
        echo "<script>window.location='$link_to_return$_SESSION[anchor_tag]'</script>";
    }
}




/*
 * 
 * 
 * ***********************
 * 8*******************************
 * 
 * Add Function
 * 
 * 
 * ************************************
 * ***************
 * *******************
 * *************************************
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'add') {


    if (array_key_exists('field_name_unique', $_POST)) {

        unset($_POST['field_name_unique']);

        //var_dump($_POST);
    }

    if (array_key_exists('old_audio', $_POST)) {

        unset($_POST['old_audio']);
    }

    /*
      echo
      $_SESSION['dict_id'] .
      $_SESSION['update_table2']['database_table_name'] .
      $_SESSION['update_table2']['keyfield'] . "<br>" . $_SESSION[return_url2];
      die; */
    //print_r($_POST);die; 


    $row = get('data_dictionary', 'dict_id=' . $_SESSION['dict_id']);


    if (!empty($row['list_filter'])) {


    $keyfield = explode(";", $row['list_filter']);


    $firstParent = $keyfield[0];
   //print_r($keyfield);die;
    
        if (!empty($keyfield[1])) {

        $listCond = $keyfield[1];
    }


  //  $checkFlag = false;

    if (!empty($keyfield[0])) {
        $i = 0;


        $keyfield = explode(",", $keyfield[0]);

        foreach ($keyfield as $val) {

            $keyField = explode("=", $val);

            $keyVal[$i] = array(trim($keyField[0]) => trim($keyField[1]));

            $i++;
        }
    }

    foreach ($keyVal as $val) {

        if (!empty($val['projects'])) {

            $pid = $val['projects'];
        }

        if (!empty($val['users'])) {

            $uid = $val['users'];
        }
    }
//print_r($pid);die;

        if (!empty($pid)) {


            $project = array($pid => $_SESSION['search_id2']);
            
            
        }

        if (!empty($uid)) {


            $user = array($uid => $_SESSION['uid']);
        }
    }

$data = $_POST;
    
    if (!empty($user)) {

        $userKey = key($user);

        if (array_key_exists($userKey, $data))
            unset($data[$userKey]);



        $data = array_merge($user, $data);
    }
    
    if (!empty($project)) {
        
        $projectKey = key($project);
        
       // print_r($projectKey);die;

        if (array_key_exists($projectKey, $data))
            unset($data[$projectKey]);



        $data = array_merge($project, $data);
        
      //  print_r($data);die;
    }
    


    unset($data['imgu']);

    ////assigning user_id field a value of current session if list_filter doesn't' have

 $field = 'false';
 
    if (empty($user)) {


        $tblName = $_SESSION['update_table2']['database_table_name'];

        $con = connect();
        $rs = $con->query("SHOW COLUMNS FROM $tblName");


       
        while ($fdCol = $rs->fetch_assoc()) {


            if ($fdCol['Field'] == 'user_id') {

                $field = 'true';

                break;
            }
        }
    }
    
    if( $field == 'true'){
        
        $additional_array = array('user_id' => $_SESSION['uid']);
        
        $data = array_merge($additional_array, $data);
    }
    
    
   // echo "<pre>";print_r($data);die;

    $check = insert($_SESSION['update_table2']['database_table_name'], $data);


    /* if ($_GET['table_type'] == 'child' && $_GET['checkFlag'] == 'true')
      $link_to_return = $_SESSION['add_url_list'];
      else */
    $link_to_return = BASE_URL . "system/profile.php?display=" . $_GET['display'] . "&tab=" . $_GET['tab'] . "&tabNum=" . $_GET['tabNum'] . "&checkFlag=true" . "&table_type=" . $_GET['table_type'];


    if ($_GET['fnc'] != 'onepage') {

        echo "<script>window.location='$link_to_return'</script>";
    } else {
        echo "<script>window.location='$link_to_return$_SESSION[anchor_tag]'</script>";
    }
}


/*
 * 
 * 
 * 
 * 
 * 
 * 
 * Update function STARTS here
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_GET['action'] == 'update') {


//    echo "<pre>";
//    print_r($_POST);
//    print_r($_SESSION);
//    die;


    if (array_key_exists('old_audio', $_POST)) {

        unset($_POST['old_audio']);
    }



    // echo "<pre>";
    //print_r($_POST);
    //exit();

    /*     * **** for pdf files ********** */

    foreach ($_POST['pdf'] as $img => $img2) {
        // print_r($img);
        //print_r($img2['imageName']);
        //print_r($img2['uploadcare']);


        if (!empty($img2['uploadcare']) && !empty($img2['imageName'])) {

            $ret = uploadPdfFile($img2['uploadcare'], $img2['imageName']);

            $oldImage = $_POST[$img]['img_extra'];

            if (!empty($ret['image'])) {

                unset($_POST['pdf'][$img]);

                //var_dump($_POST);

                $_POST[$img] = $ret['image'];
            }

            //if user want to replace current image

            if (!empty($oldImage)) {

                @unlink("../users_uploads/pdf/$oldImage");


                //unset($_POST[$img]);
            }
            //if user didn't touch the with images
        } else {

//if user clicks on remove current image
            if (empty($img2['uploadcare']) && !empty($img2['imageName'])) {

                if (!empty($_POST[$img]['img_extra'])) {

                    @unlink("../users_uploads/pdf/$img2[imageName]");




                    unset($_POST['pdf'][$img]);

                    $_POST[$img] = '';
                } else {

                    unset($_POST['pdf'][$img]);
                }
            } else {

                unset($_POST['pdf'][$img]);
            }
        }
    }
//deleting array which is used for holding imgu values
    if (empty($_POST['pdf']))
        unset($_POST['pdf']);





    /*     * ***For images********** */

    foreach ($_POST['imgu'] as $img => $img2) {
        // print_r($img);
        //print_r($img2['imageName']);
        //print_r($img2['uploadcare']);


        if (!empty($img2['uploadcare']) && !empty($img2['imageName'])) {

            $ret = uploadImageFile($img2['uploadcare'], $img2['imageName']);

            $oldImage = $_POST[$img]['img_extra'];

            if (!empty($ret['image'])) {

                unset($_POST['imgu'][$img]);

                //var_dump($_POST);

                $_POST[$img] = $ret['image'];
            }

            //if user want to replace current image

            if (!empty($oldImage)) {

                @unlink("../users_uploads/$oldImage");

                @unlink("../users_uploads/thumbs/$oldImage");

                //unset($_POST[$img]);
            }
            //if user didn't touch the with images
        } else {

//if user clicks on remove current image
            if (empty($img2['uploadcare']) && !empty($img2['imageName'])) {

                if (!empty($_POST[$img]['img_extra'])) {

                    @unlink("../users_uploads/$img2[imageName]");

                    @unlink("../users_uploads/thumbs/$img2[imageName]");


                    unset($_POST['imgu'][$img]);

                    $_POST[$img] = '';
                } else {

                    unset($_POST['imgu'][$img]);
                }
            } else {

                unset($_POST['imgu'][$img]);
            }
        }
    }
//deleting array which is used for holding imgu values
    if (empty($_POST['imgu']))
        unset($_POST['imgu']);

    //echo "<pre>";
    //print_r($_POST);
    //exit();
    //echo '<br><br>';



    foreach ($_FILES as $file => $file2) {


        //checking if audio file is not empty
        if (!empty($_FILES[$file]['name'])) {

            $file_name = uploadAudioFile($file2);
            // if file successfully uploaded to target dir
            if (!empty($file_name)) {

                $_POST[$file] = $file_name;
            }
        } else {

            $_POST[$file] = '';
        }

        //Dealing with database now
        $row = getWhere($_SESSION['update_table2']['database_table_name'], array($_SESSION['update_table2']['keyfield'] => $_SESSION['search_id2']));

        $oldFile = $row[0][$file];

        if ($oldFile != "") {
            if (file_exists("../users_uploads/audio/" . $oldFile)) {
                @unlink("../users_uploads/audio/" . $oldFile);
            }
        }
    }


//print_r($_POST);die;
    //exit($_SESSION['dict_id']);

    $status = update($_SESSION['update_table2']['database_table_name'], $_POST, array($_SESSION['update_table2']['keyfield'] => $_SESSION['search_id2']));
      

//die("shiv");
    update('data_dictionary', array('dd_editable' => '1'), array('dict_id' => $_SESSION['dict_id']));
//die("shiv update 2");
    //echo ($_SESSION['return_url2']);
    //if ($check == 1) {


    if ($_GET['checkFlag'] == 'true') {

        if ($_GET['table_type'] == 'child')
            $link_to_return = $_SESSION['child_return_url2'];
        else
            $link_to_return = $_SESSION['return_url2'];


        if ($_GET['fnc'] != 'onepage') {

            //exit($link_to_return);
            if($status === true)
                echo "<script>window.location='$link_to_return';</script>";
            else
            {
                echo "<script> alert(\"$status\"); window.location='$link_to_return'; </script>";
            }
        } else {
            if($status === true)
                echo "<script>window.location='$link_to_return$_SESSION[anchor_tag]';</script>";
            else
            {
                echo "<script> alert(\"$status\"); window.location='$link_to_return$_SESSION[anchor_tag]'; </script>";
            }
        }
    } else {


        if (!empty($_SESSION['display2'])) {

            $_SESSION[display] = $_SESSION['display2'];
        }

        if ($_GET['fnc'] != 'onepage') {
            if($status === true)
                echo "<script>window.location = '?display=$_SESSION[display]&tab=$_SESSION[tab]&tabNum=$_GET[tabNum]';</script>";
            else
            {
                echo "<script> alert(\"$status\"); window.location = '?display=$_SESSION[display]&tab=$_SESSION[tab]&tabNum=$_GET[tabNum]'; </script>";
            }
        } else {
            if($status === true)
                echo "<script>window.location='$_SESSION[return_url2]$_SESSION[anchor_tag]';</script>";
            else
            {
                echo "<script> alert(\"$status\"); window.location='$_SESSION[return_url2]$_SESSION[anchor_tag]'; </script>";
            }
        }
    }
    // } else
    // echo('update error');
}




/*
 * 
 * 
 * 
 * ********************Login Function
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 


 *  */



if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] == 'login') {


    $tbl = $_SESSION['select_table']['database_table_name'];

    $pKey = $_SESSION['select_table']['keyfield'];

    $con = connect();

    $loginKeys = array_keys($_POST);


    $value1 = $_POST[$loginKeys[0]];

    $value2 = $_POST[$loginKeys[1]];

    $userName = $_SESSION['select_table']['username'];

    //exit("SELECT * FROM $tbl where $loginKeys[0] = '$value1' or $userName = '$value1' and $loginKeys[1] = '$value2' ");

    $rs = $con->query("SELECT * FROM $tbl where $loginKeys[0] = '$value1' or $userName = '$value1' and $loginKeys[1] = '$value2' ");

    $row = $rs->fetch_assoc();


    if ($row) {

        $_SESSION['uid'] = $row[$pKey];

        $_SESSION['uname'] = $row[$_SESSION['select_table']['username']];

        $_SESSION['user_privilege'] = $row[user_privilege_level];

        if (isset($_SESSION['callBackPage'])) {


            echo "<META http-equiv='refresh' content='0;URL=" . $_SESSION['callBackPage'] . "'>";

            unset($_SESSION['callBackPage']);
            exit();
        } else {

            FlashMessage::add(PROFILE_COMPLETE_MESSAGE);
            echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL . "index.php'>";
            exit();
        }
    } else {

        FlashMessage::add('UserName or Password Incorrect.');
        echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "login.php'>";
        exit();
    }
}



