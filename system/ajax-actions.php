<?php

session_start();

require_once("../appConfig/appConfig.php");
include_once("../application/database/db.php");
require_once '../application/config.php';
require_once '../application/functions.php';
require_once '../DDICT/masterFunctions.php';



/*
 * 
 * @checklist Multiple Deletion 
 */


if (isset($_POST["checkHidden"]) && !empty($_POST["checkHidden"]) && $_POST["checkHidden"] == 'delete') {

    // exit($_POST['dict_id'][0]);
    //// Image deletion first if there is any /////


    $row = get("data_dictionary", "dict_id='" . $_POST['dict_id'][0] . "'");


    $frow = getWhere("field_dictionary", array("table_alias" => $row['table_alias'], "format_type" => "image"));

    $image_name = getWhere($row['database_table_name'], array(firstFieldName($row['database_table_name']) => $_GET["list_delete"]));


    foreach ($frow AS $val) {


        foreach ($_POST['list'] as $list_id) {


            $image_name = getWhere($row['database_table_name'], array(firstFieldName($row['database_table_name']) => $list_id));

            if (!empty($image_name[0][$val['generic_field_name']])) {

                @unlink("../users_uploads/" . $image_name[0][$val['generic_field_name']]);
            }
        }/////inside list
    }





    //// actuall multiple deletion of records/////
    $item = implode(",", $_POST['list']);

    //exit("delete from " . $row['database_table_name'] ." where " . $row['keyfield'] . " IN( $item )");


    mysqli_query($con, "delete from " . $row['database_table_name'] . " where " . firstFieldName($row['database_table_name']) . " IN( $item )");
    //exit('yasir');

    // delete project child table records related to project
    if($row['database_table_name'] == 'project'){
        mysqli_query($con, "delete from project_child  where " . firstFieldName($row['database_table_name']) . " IN( $item )");
    }
}



/*
 * 
 * @checklist Multiple copy 
 */


if (isset($_POST["checkHidden"]) && !empty($_POST["checkHidden"]) && $_POST["checkHidden"] == 'copy') {


    $item = implode(",", $_POST['list']);



    mysqli_query($con, "CREATE table temporary_table2 AS SELECT * FROM " . $_SESSION['update_table']['database_table_name'] . " WHERE " . $_SESSION['update_table']['keyfield'] . " IN( $item )");


    mysqli_query($con, "UPDATE temporary_table2 SET " . $_SESSION['update_table']['keyfield'] . " =NULL;");

    mysqli_query($con, "INSERT INTO " . $_SESSION['update_table']['database_table_name'] . " SELECT * FROM temporary_table2;");

    mysqli_query($con, "DROP TABLE IF EXISTS temporary_table2;");

    //exit('yasir');
}

/*
 * 
 * @TAking care of Edit option when user click on tabs
 */

if (isset($_GET["tab_check"]) && !empty($_GET["tab_check"]) && $_GET["tab_check"] == 'true') {


    update("data_dictionary", array('dd_editable' => '1'), array('table_alias' => $_GET['tab_name'], 'tab_num' => $_GET['tab_num']));
}


/*
 * 
 * @checklist single deletion
 */

if (isset($_GET["list_delete"]) && !empty($_GET["list_delete"]) && $_GET["check_action"] == 'delete') {

/// Searching and deleting images from targeted table first

    $row = get("data_dictionary", "dict_id='" . $_GET['dict_id'] . "'");


    $frow = getWhere("field_dictionary", array("table_alias" => $row['table_alias'], "format_type" => "image"));

    $image_name = getWhere($row['database_table_name'], array(firstFieldName($row['database_table_name']) => $_GET["list_delete"]));


    foreach ($frow AS $val) {


        if (!empty($image_name[0][$val['generic_field_name']])) {

            @unlink("../users_uploads/" . $image_name[0][$val['generic_field_name']]);
        }
    }

/// deleting actual record////
    // mysqli_query($con, "delete from " . $_SESSION['update_table']['database_table_name'] . " where " . $_SESSION['update_table']['keyfield'] . "=" . $_GET["list_delete"]);

    mysqli_query($con, "delete from " . $row['database_table_name'] . " where " . firstFieldName($row['database_table_name']) . "=" . $_GET["list_delete"]);

    //exit('yasir');
}

/*
 * 
 * @checklist single copy
 */
if (isset($_GET["list_copy"]) && !empty($_GET["list_copy"]) && $_GET["check_action"] == 'copy') {





    mysqli_query($con, "CREATE table temporary_table2 AS SELECT * FROM " . $_SESSION['update_table']['database_table_name'] . " WHERE " . $_SESSION['update_table']['keyfield'] . " = $_GET[list_copy]");


    mysqli_query($con, "UPDATE temporary_table2 SET " . $_SESSION['update_table']['keyfield'] . " =NULL;");

    mysqli_query($con, "INSERT INTO " . $_SESSION['update_table']['database_table_name'] . " SELECT * FROM temporary_table2;");

    mysqli_query($con, "DROP TABLE IF EXISTS temporary_table2;");
}

/*
 * 
 * @checklist single deletion
 */

if (isset($_GET["list_add"]) && !empty($_GET["list_add"]) && $_GET["check_action"] == 'add') {

    $row = get("data_dictionary", "dict_id='" . $_GET['list_add'] . "'");


    exit($_GET['url'] . "&addFlag=true&checkFlag=true&ta=$row[table_alias]&table_type=$row[table_type]");
}

/*
 * 
 * @checklist openChild
 */

if (isset($_GET["childID"]) && !empty($_GET["childID"]) && $_GET["check_action"] == 'openChild') {

    //exit($_GET['display']);

    $search_key = $_GET["childID"];

    $row = get("data_dictionary", "dict_id='" . $_GET['dict_id'] . "'");

    if (trim($row['table_type']) == 'parent') {


        if ($_SESSION['update_table']['child_parent_key_diff'] == 'true') {



            $child_parent_value = getWhere($row['database_table_name'], array($_SESSION['update_table']['keyfield'] => $_GET['childID']));


            $search_key = $_SESSION['parent_value'] = $child_parent_value[0][$_SESSION[update_table][child_parent_key]];
        } else {


            $search_key = $_SESSION['parent_value'] = $_GET['childID'];
        }
    }


    $list_select_sep = explode(';', $row['list_select']);

    foreach ($list_select_sep as $listArray) {

        $list_select_arr[] = explode(",", $listArray);
    }

    //print_r($list_select_arr);die;
    $nav = $con->query("SELECT * FROM navigation where target_display_page='$_GET[display]'");
    $navList = $nav->fetch_assoc();

    //print_r($navList);          
    //if (count($list_select_arr) == 2) {



    $target_url = BASE_URL . $navList['item_target'] . "?display=" . trim($list_select_arr[1][2]) . "&tab=" . trim($list_select_arr[1][0]) . "&tabNum=" . trim($list_select_arr[1][1]) . "&layout=" . trim($navList['page_layout_style']) . "&style=" . trim($navList['item_style']) . "&ta=" . trim($list_select_arr[1][0]) . "&search_id=" . $search_key . "&checkFlag=true&table_type=child";
    //}

    exit($target_url);
    ///////openChild ends here
}





/*
 * ***********
 * ***********************
 * **********************************
 * Enabling submit buttons for forms
 * 
 * ****
 * ***********
 * *********************
 * ******************************
 */

if (isset($_GET["id"]) && !empty($_GET["id"]) && $_GET["check_action"] == 'enable_edit') {



    $check = getWhere('data_dictionary', array('dict_id' => $_GET["id"]));

    $dp_page = $check[0]['display_page'];

    $row = getWhere('data_dictionary', array('dd_editable' => '11', 'display_page' => $dp_page));

    if ($row) {

        if ($_GET['form_edit_conf'] == 'changed')
            exit('active');
        else {

            query("update data_dictionary set dd_editable=1 where display_page='$dp_page' and dict_id != $_GET[id]");

            update('data_dictionary', array('dd_editable' => 11), array('dict_id' => $_GET['id']));

            exit('not-active');
        }
    } else {

        update('data_dictionary', array('dd_editable' => 11), array('dict_id' => $_GET['id']));

        exit('not-active');
    }
}

////////////
////////////////////////
if (isset($_GET["checkEmail"]) && !empty($_GET["checkEmail"])) {

    $email = getWhere('users', array('email' => $_GET["email"]));
    if ($email) {
        echo "true";
    } else {
        echo "false";
    }
}


if (isset($_GET["checkUserName"]) && !empty($_GET["checkUserName"])) {


    $uname = getWhere('users', array('uname' => $_GET["userName"]));

    if ($uname) {
        echo "true";
    } else {
        echo "false";
    }
}

/* * ********************************* */
/* * ********Image Submit******************* */
/* * ********************************* */
if (!empty($_GET["check_action"]) && $_GET["check_action"] == 'image_submit') {

    $uploadcare_image_url = $_GET['cdnUrl'];
    $filename = $_GET['imgName'];
    $fieldName = $_GET['fieldName'];

    $imageInfo = fileUploadCare($uploadcare_image_url, $filename, "../users_uploads");

    if ($_GET['profile_img'] != 'no-profile') {

        update("data_dictionary", array("dd_editable" => '1'), array("dict_id" => $_GET['profile_img']));
    }

    if (!empty($imageInfo)) {

        update($_SESSION['update_table2']['database_table_name'], array($fieldName => $imageInfo['image']), array($_SESSION['update_table2']['keyfield'] => $_SESSION['search_id2']));
    } else {
        exit('notSaved');
    }
}


/* * ********************************* */
/* * ********Remove Image******************* */
/* * ********************************* */
if (!empty($_GET["check_action"]) && $_GET["check_action"] == 'image_delete') {


    $fieldName = $_GET['fieldName'];

    $row = getWhere($_SESSION['update_table2']['database_table_name'], array($_SESSION['update_table2']['keyfield'] => $_SESSION['search_id2']));

    $fileName = $row[0][$fieldName];


    if ($fileName != "") {
        if (file_exists("../users_uploads/" . $fileName)) {
            unlink("../users_uploads/" . $fileName);
        }
    }


    $check = update($_SESSION['update_table2']['database_table_name'], array($fieldName => ''), array($_SESSION['update_table2']['keyfield'] => $_SESSION['search_id2']));

    if ($_GET['profile_img'] != 'no-profile') {

        update("data_dictionary", array("dd_editable" => '1'), array("dict_id" => $_GET['profile_img']));
    }

    if ($check)
        exit('Deleted');
}


/* * ********************************* */
/* * ********Image Revert******************* */
/* * ********************************* */
if (!empty($_GET["img_revert"]) && $_GET["img_revert"] == 'img-revert') {

    $query = getwhere($_SESSION['update_table2']['database_table_name'], array($_SESSION['update_table2']['keyfield'] => $_SESSION['search_id2']));

    $fieldValue = trim($query[0][$_GET[field_name]]);

    $fieldValue = explode("-", $fieldValue);

    exit($fieldValue[1]);
}




/*
 * *************
 * ************************
 * ************************************
 * 
 * @Friend me Ajax Code goes here
 */

if (isset($_GET["action"]) && !empty($_GET["action"]) && $_GET["action"] == 'friend_me') {



    $check = getWhere($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

    if (empty($check[0])) {


        insert($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

        exit('inserted');
    } else {

        delete($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

        exit('deleted');
    }
}


/*
 * *************
 * ************************
 * ************************************
 * 
 * @Follow me Ajax Code goes here
 */

if (isset($_GET["action"]) && !empty($_GET["action"]) && $_GET["action"] == 'follow_me') {

    $check = getWhere($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

    if (empty($check[0])) {


        insert($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

        exit('inserted');
    } else {

        delete($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

        exit('deleted');
    }
}



/*
 * *************
 * ************************
 * ************************************
 * 
 * @Favorite me Ajax Code goes here
 */


if (isset($_GET["action"]) && !empty($_GET["action"]) && $_GET["action"] == 'favorite_me') {


    $check = getWhere($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

    if (empty($check[0])) {


        insert($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

        exit('inserted');
    } else {

        delete($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

        exit('deleted');
    }
}




/*
 * *************
 * ************************
 * ************************************
 * 
 * @rate me Ajax Code goes here
 */


if (isset($_GET["action"]) && !empty($_GET["action"]) && $_GET["action"] == 'rate_me') {

    $check = getWhere($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));


    if (!empty($check[0]))
        $oldvalue = $check[0]['value'];
    else
        $oldvalue = 0;


    if ($_GET['value'] == 'clear') {

        delete($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

        exit('deleted');
    } else {

        /////////Checking the limitsss


        $fffr = getWhere('data_dictionary', array('display_page' => $_SESSION[display], 'table_alias' => $_GET['ta'], 'tab_num' => $_GET['tabNum']));

        $icons_table = listExtraOptions($fffr[0]['list_extra_options']);



        $disable_status = 'false';

        $dilog_msg = '';


        ///////////total vote allowed for profile//////


        if (!empty(trim($icons_table['voteLimit']))) {


            $records = sumValues($icons_table['rating_tbl']);


            if ($icons_table['voteLimit'] < $records + $_GET['value'] - $oldvalue) {


                $disable_status = 'true';

                $dilog_msg .= "<p>Total Vote Limit Of $icons_table[voteLimit] Has Been Reached</p>";
            }
        }






        ///////////total vote allowed for SINGLE USER//////


        if (!empty(trim($icons_table['userVoteLimit']))) {


            $records = sumValues($icons_table['rating_tbl'], array('user_id' => $_SESSION['uid']));

            //print_r($records);die;

            if ($icons_table['userVoteLimit'] < $records + $_GET['value'] - $oldvalue) {

                $disable_status = 'true';

                $dilog_msg .= "<p>Your Total Vote Limit Of $icons_table[userVoteLimit] Has Been Reached</p>";
            }
        }



        ///////////Checking Upper Lower Limit//////
        
   

        if (!empty(trim($icons_table['lowerLimit'])) || !empty(trim($icons_table['upperLimit']))) {



            //print_r($records);die;

            if ($icons_table['lowerLimit'] > $_GET['value'] || $icons_table['upperLimit'] < $_GET['value']) {


                $dilog_msg .= "<p>Lower Limit = $icons_table[lowerLimit] & Upper Limit = $icons_table[upperLimit]</p>";
            }
        }


        /*
         * Patter Checking here ,whether to accept only INTEGER
         */

        if (!empty(trim($icons_table['pattern']) && trim($icons_table['pattern']) == 'integer')) {



            if (!preg_match("/^[0-9]*$/", $_GET['value'])) {


                $dilog_msg .= "<p>Only Integers are Allowed</p>";
            }
        }



        if (!empty(trim($icons_table['pattern']) && trim($icons_table['pattern']) == 'float')) {



            if (!preg_match("/^[0-9]*.[0-9]*$/", $_GET['value'])) {


                $dilog_msg .= "<p>Only One Decimal Point is Allowed</p>";
            }
        }



        if (empty($dilog_msg) && $disable_status == 'false') {


            if (empty($check[0])) {


                insert($_GET['table_name'], array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id'], 'value' => $_GET['value']));

                exit('deleted');
            } else {

                update($_GET['table_name'], array('value' => $_GET['value']), array('user_id' => $_SESSION['uid'], 'target_id' => $_GET['fffr_search_id']));

                exit('deleted');
            }
        } else {

            /*
             * Limit Critera has not met
             */


            exit($dilog_msg);
        }
    }
}




