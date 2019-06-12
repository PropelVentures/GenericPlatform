<?php

// session_start();

require_once 'functions_loader.php';

echo $_SESSION['child_return_url'];
if(!empty($_GET['values_to_unset'])){

    unset($_SESSION['child_return_url']);
    unset($_SESSION['child_return_url2']);

    unset($_SESSION['search_id']);
    unset($_GET['search_id']);

    unset($_SESSION['search_id2']);
    unset($_GET['search_id2']);

    unset($_SESSION['parent_value']);
    unset($_SESSION['parent_url']);

    // unset($_SESSION['parent_list_tabname']);
    return true;
}

/*on chnage of list filter*/
if (!empty($_GET["check_action"]) && $_GET["check_action"] == 'set_list_filter') {
  if(isset($_GET['dict_id_to_apply_filter']) && isset($_GET['selected_filter'])){
    $_SESSION[$_GET['dict_id_to_apply_filter'].'_selected_filter'] = $_GET['selected_filter'];
  }
  exit;
}

if (!empty($_GET["check_action"]) && $_GET["check_action"] == 'contact_me') {
  sendMessageAndAddLog();
  exit;
}

/*on chnage of list filter*/
if (!empty($_GET["check_action"]) && $_GET["check_action"] == 'set_list_view') {
  if(isset($_GET['dict_id_to_apply_filter']) && isset($_GET['selected_filter'])){
    $_SESSION[$_GET['dict_id_to_apply_filter'].'_selected_view'] = $_GET['selected_filter'];
  }
  exit;
}


/*on chnage of list filter*/
if (!empty($_GET["check_action"]) && $_GET["check_action"] == 'adding_new_options') {
  $table = $_GET['table'];
  $insert =$_GET['data'];
  if(!empty($_GET['selectedKeyField'])){
    $insert[$_GET['selectedKeyField']] = $_GET['selectedprimaryvalue'];
  }
  $id = insert($table,$insert);
  echo $id;
  exit;
}


/*
 *
 * @checklist Multiple Deletion
 */
if (isset($_POST["checkHidden"]) && !empty($_POST["checkHidden"]) && $_POST["checkHidden"] == 'delete') {
    log_event($_GET['display'],'delete');

    $item = implode(",", $_POST['list']);
    $row = get("data_dictionary", "dict_id='" . $_POST['dict_id'] . "'");
    $frow = getWhere("field_dictionary", array("table_alias" => $row['table_alias'], "format_type" => "image"));
    $image_name = getWhere($row['database_table_name'], array(firstFieldName($row['database_table_name']) => $_GET["list_delete"]));
    foreach ($frow AS $val) {
        foreach ($_POST['list'] as $list_id) {
            $image_name = getWhere($row['database_table_name'], array(firstFieldName($row['database_table_name']) => $list_id));
            if (!empty($image_name[0][$val['generic_field_name']])) {
                @unlink(USER_UPLOADS. "" . $image_name[0][$val['generic_field_name']]);
            }
        }/////inside list
    }
    $keyField = firstFieldName($row['database_table_name']);
    mysqli_query($con, "delete from " . $row['database_table_name'] . " where " . $keyField . " IN( $item )");
    if($row['table_type'] == 'parent'){
      $childRow = get("data_dictionary", "parent_table='".$row['database_table_name']."' AND table_type='child' ");
      $childTable = $childRow['database_table_name'];
      $childKey = 'child_'.$keyField;
        mysqli_query($con, "delete from " .$childTable . " where " . $childKey . " IN( $item )");
    }
    if($row['database_table_name'] == 'project'){
        mysqli_query($con, "delete from project_child  where " . firstFieldName($row['database_table_name']) . " IN( $item )");
    }
}



/*
 *
 * @checklist Multiple copy
 */
if (isset($_POST["checkHidden"]) && !empty($_POST["checkHidden"]) && $_POST["checkHidden"] == 'copy') {
    log_event($_GET['display'],'copy');
    $item = implode(",", $_POST['list']);
    $row = get("data_dictionary", "dict_id='" . $_POST['dict_id'] . "'");
    $table = $row['database_table_name'];
    $keyField = $row['keyfield'];
    $isParent = false;
    if($row['table_type'] == 'parent'){
      $isParent = true;
      $childRow = get("data_dictionary", "parent_table='".$row['database_table_name']."' AND table_type='child' ");
      $childTable = $childRow['database_table_name'];
      $childKey = 'child_'.$keyField;
    }
    $allRecords = getWhere($table, "$keyField IN( $item )","",false);
    foreach ($allRecords as $key => $record) {
      $tempRecord = $record;
     unsetExtraRows($tempRecord);
      $parentId= $record[$keyField];
      unset($tempRecord[$keyField]);
      $newId = insert($table,$tempRecord);
      if($isParent){
        $llChildren = getWhere($childTable,"$childKey='$parentId'",'',false);
        foreach ($llChildren as $key => $value) {
          unsetExtraRows($value);
          unset($value['id']);
          $value[$childKey] = $newId;
          insert($childTable,$value);
        }
      }
    }
}

/*
 *
 * @TAking care of Edit option when user click on tabs
 */

if (isset($_GET["tab_check"]) && !empty($_GET["tab_check"]) && $_GET["tab_check"] == 'true') {


// ***>    update("data_dictionary", array('dd_editable' => '1'), array('table_alias' => $_GET['tab_name'], 'tab_num' => $_GET['tab_num']));
}


/*
 *
 * @checklist single deletion
 */

if (isset($_GET["list_delete"]) && !empty($_GET["list_delete"]) && $_GET["check_action"] == 'delete') {

/// Searching and deleting images from targeted table first
    log_event($_GET['display'],'delete');

    $row = get("data_dictionary", "dict_id='" . $_GET['dict_id'] . "'");
    if(!isAllowedToPerformListAction($row)){
      echo 'false';
      die();
    }
    $frow = getWhere("field_dictionary", array("table_alias" => $row['table_alias'], "format_type" => "image"));

    $image_name = getWhere($row['database_table_name'], array(firstFieldName($row['database_table_name']) => $_GET["list_delete"]));


    foreach ($frow AS $val) {


        if (!empty($image_name[0][$val['generic_field_name']])) {

            @unlink(USER_UPLOADS . "" . $image_name[0][$val['generic_field_name']]);
        }
    }

/// deleting actual record////
    // mysqli_query($con, "delete from " . $_SESSION['update_table']['database_table_name'] . " where " . $_SESSION['update_table']['keyfield'] . "=" . $_GET["list_delete"]);

    mysqli_query($con, "delete from " . $row['database_table_name'] . " where " . firstFieldName($row['database_table_name']) . "=" . $_GET["list_delete"]);

	$returnUrl = $_SESSION['return_url2'];
	if($_GET['fnc'] == 'onepage'){
		$returnUrl .= $_SESSION['anchor_tag'];
	}
	echo $returnUrl; exit;

    //exit('yasir');
}

/*
 *
 * @checklist single copy
 */
if (isset($_GET["list_copy"]) && !empty($_GET["list_copy"]) && $_GET["check_action"] == 'copy') {
  $row = get("data_dictionary", "dict_id='" . $_SESSION['dict_id'] . "'");
    if(!isAllowedToPerformListAction($row)){
      echo 'false';
      die();
    }
    log_event($_GET['display'],'copy');



    mysqli_query($con, "CREATE table temporary_table2 AS SELECT * FROM " . $row['database_table_name'] . " WHERE " . firstFieldName($row['database_table_name']) . " = $_GET[list_copy]");

    mysqli_query($con, "UPDATE temporary_table2 SET " . firstFieldName($row['database_table_name']) . " =NULL;");

    mysqli_query($con, "INSERT INTO " . $row['database_table_name'] . " SELECT * FROM temporary_table2;");

    mysqli_query($con, "DROP TABLE IF EXISTS temporary_table2;");

	$returnUrl = $_SESSION['return_url2'];
	if($_GET['fnc'] == 'onepage'){
		$returnUrl .= $_SESSION['anchor_tag'];
	}
	echo $returnUrl; exit;
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
    $_SESSION['child_return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $search_key = $_GET["childID"];
    $row = get("data_dictionary", "dict_id='" . $_GET['dict_id'] . "'");
    if (trim($row['table_type']) == 'parent') {

// **********************************************************************************
//  CJ-NOTE ***
// I am not sure what the line below does or why 'child_parent_key_diff' exists ... it almost seems as a fix to cover a mistake.
// **********************************************************************************
        if ($_SESSION['update_table']['child_parent_key_diff'] == 'true') {

			$primary_key = firstFieldName($row['database_table_name']);
            $child_parent_value = getWhere($row['database_table_name'], array($primary_key => $_GET['childID']));
			$key = (!empty($row['keyfield']) ? $row['keyfield'] : $primary_key );
            $search_key = $_SESSION['parent_value'] = $child_parent_value[0][$key];
        } else {
// **********************************************************************************
//  CJ-NOTE ***
// hard to tell based on just label names but the line below does not quite seem right
// ansd maybe the source of paarent child problems why would a parent_value be set to a child ID?
// **********************************************************************************
            $search_key = $_SESSION['parent_value'] = $_GET['childID'];
        }
    }
    $list_select_sep = explode(';', $row['list_select']);
    foreach ($list_select_sep as $listArray) {
        $list_select_arr[] = explode(",", $listArray);
    }

// **********************************************************************************
//  CJ-NOTE ***
// Why is this  Nav Table code relevant in this "open child" block of code??
// **********************************************************************************

    $nav = $con->query("SELECT * FROM navigation where target_display_page='$_GET[display]'");
    $navList = $nav->fetch_assoc();
    $display_page = trim($list_select_arr[1][2]);
    $tab_alias = trim($list_select_arr[1][0]);
    $tab_num = trim($list_select_arr[1][1]);
    $result = get('data_dictionary',"display_page='$display_page' AND table_alias='$tab_alias' AND tab_num='$tab_num'");
    if(isAllowedToPerformListAction($result)){
      $target_url = "" . $navList['item_target'] . "?display=" . trim($list_select_arr[1][2]) . "&tab=" . trim($list_select_arr[1][0]) . "&tabNum=" . trim($list_select_arr[1][1]) . "&layout=" . trim($navList['page_layout_style']) . "&style=" . trim($navList['item_style']) . "&ta=" . trim($list_select_arr[1][0]) . "&search_id=" . $search_key . "&checkFlag=true&table_type=child";
    }else{
      $target_url = 'false';
    }

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

    $imageInfo = fileUploadCare($uploadcare_image_url, $filename, USER_UPLOADS, "");

    if ($_GET['profile_img'] != 'no-profile') {

// ***>        update("data_dictionary", array("dd_editable" => '1'), array("dict_id" => $_GET['profile_img']));
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
        if (file_exists(USER_UPLOADS . "" . $fileName)) {
            unlink(USER_UPLOADS . "" . $fileName);
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

##CUSTOM FUNCTION CODE GOES HERE##Params are tilde ` separated
if(!empty($_POST['action']) && $_POST['action'] == 'custom_function')
{
    $functionName = $_POST['function'];
    $functionParams = $_POST['params'];

    $functionParams = explode("`", $functionParams);

    $functionParams = array_map('trim', $functionParams);

    if(function_exists($functionName) )
    {
        call_user_func_array($functionName, $functionParams);
    }
}

###UNSET VIEW/EDIT OPERATIONS addimport SUCCESS/FAILURE SESSIONS#####
if(!empty($_POST['action']) && $_POST['action'] == 'addimport_session_unset')
{
    unset($_SESSION['SuccessAddImport'], $_SESSION['errorsAddImport']);
}

function isAllowedToPerformListAction($row){
  $user_privilege = $_SESSION['user_privilege'];
  $DD_privilege = $row['dd_privilege_level'];
  $DD_visibility = $row['dd_visibility'];
  if(is_null($user_privilege)){
    $user_privilege = 0;
  }
  if($DD_visibility ==0){
    return false;
  }

  if($user_privilege >= $DD_privilege){
    return true;
  }
  return false;
}
