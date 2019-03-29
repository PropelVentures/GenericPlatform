<?php
/*
 * @Functions parseFieldType($row)
 *
 * function uploadAudioFile($parameters)
 *
 * function uploadImageFile($uploadCareURL, $imageName)
 *
 * function uploadPdfFile($uploadCareURL, $imageName)
 *
 *
 * function listExtraOptions($list_extra_options) {
 *
 * function editPagePagination()
 *
 * function listFilter()
 *
 * function boxViewPagination($pagination, $tab_num, $list_select_arr) {
 *
 * other small functions for future use
 */

// TEST FUNCTION WILL BE REMOVED AFTER TESTING MULTIPLE CUSTOM FUNCTIONS //SHIVGRE
function simulate($a, $b){}

//echo "<pre>SESSION";
//print_r($_SESSION);
//echo "</pre>";

/*
 *
 * it will parse field type  on the edit form
 */

function parseFieldType($row) {

    $con = connect();

    $result = $con->query("describe $row[database_table_name]");

    while ($result_rec = $result->fetch_assoc()) {
        if ($result_rec['Field'] == $row['generic_field_name']) {

            $field_type = $result_rec['Type'];
        }
    }

    $field_type = explode("(", $field_type);

    $field_length = '40';

    if ($field_type[0] == 'varchar') {

        $field_length = defaultFieldLenText;
    } else if ($field_type[0] == 'text') {

        $field_length = defaultFieldLenTextarea;
    } else if ($field_type[0] == 'int') {

        $field_length = defaultFieldLenInteger;
    } else if ($field_type[0] == 'boolean') {

        $field_length = defaultFieldLenBoolean;
    } else if ($field_type[0] == 'double' || $field_type[0] == 'float' || $field_type[0] == 'tinyint') {

        $field_length = defaultFieldLenOtherInteger;
    }

    return $field_length;
}

function getColumnsNames($table){
	$con = connect();
	$columns = array();
	$query = $con->query("SHOW COLUMNS FROM $table");
	while($row = $query->fetch_assoc()){
		$columns[] = $row['Field'];
	}
	return $columns;
}

/*
 *
 * Function @file_upload
 */

function uploadAudioFile($parameters) {

    $target_dir = USER_UPLOADS . "audio";
    $randName = rand(124, 1000);
    $fileName = $randName . $parameters['name'];

    $target_file = $target_dir . '/' . $fileName;
    $uploadOk = 1;
    $allowedType = ['audio/mp3','audio/wav'];

    if(!in_array($parameters['type'],$allowedType)){
      $uploadOk = 0;
      // throw new Exception("This file type is not allowed to upload")
    }
    
// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        throw new Exception("UploadFail");
// if everything is ok, try to upload file
    } else {


        if (@move_uploaded_file($parameters["tmp_name"], $target_file)) {

            return $fileName;
        } else {

            return FALSE;
        }
    }
}

/*
 *
 * Function @uploadImageFile
 */

function uploadImageFile($uploadCareURL, $imageName) {

    $src = USER_UPLOADS . "";

    $uploadcare_image_url = $uploadCareURL;
    $filename = $imageName;
    $ext = pathinfo($filename, PATHINFO_EXTENSION);   //returns the extension
    $allowed_types = array('jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'GIF', 'png', 'PNG', 'bmp');
    $randName = rand(124, 1000);
    $imgInfo = array();

    // If the file extension is allowed
    if (in_array($ext, $allowed_types)) {
        $new_filename = $filename;

        //$new_filepath = $base_path.'upload/orig/'.$new_filename;
        $imgpath = $src . $randName . $new_filename;
        $thumb_imgpath = $src . "thumbs/" . $randName . $new_filename;

        // Attempt to copy the image from Uploadcare to our server
        if (copy($uploadcare_image_url, $imgpath)) {
            //Resize the image
            include_once('resizeImage.php');
            $image = new ResizeImage();
            $wk_img_wt = '';
            $wk_img_ht = '';

            list($wk_img_wt, $wk_img_ht) = getimagesize($imgpath);
            if ($wk_img_wt >= 650 && $wk_img_wt > $wk_img_ht) {
                $image->load($imgpath);
                $image->setImgDim($wk_img_wt, $wk_img_ht);
                $image->resizeToWidth(650);
                $image->save($imgpath);
            }
            if ($wk_img_ht > $wk_img_wt && $wk_img_ht >= 430) {
                $image->load($imgpath);
                $image->setImgDim($wk_img_wt, $wk_img_ht);
                $image->resizeToHeight(430);
                $image->save($imgpath);
            }

            //For Thumb
            if ($wk_img_wt > $wk_img_ht && $wk_img_wt >= 325) {
                $image->load($imgpath);
                $image->setImgDim($wk_img_wt, $wk_img_ht);
                $image->resizeToWidth(325);
                $image->save($thumb_imgpath);
            }

            if ($wk_img_ht > $wk_img_wt && $wk_img_ht > 215) {
                $image->load($imgpath);
                $image->setImgDim($wk_img_wt, $wk_img_ht);
                $image->resizeToHeight(215);
                $image->save($thumb_imgpath);
            }

            $imgInfo['image'] = $randName . $new_filename;
            $imgInfo['thumb_image'] = "thumb_" . $randName . $new_filename;
            return $imgInfo;
        } else {
            return $imgInfo;
        }
    } else {
        return $imgInfo;
    }
}

/*
 *
 * Function @uploadPdfFile
 */

function uploadPdfFile($uploadCareURL, $imageName) {

    $src = USER_UPLOADS . "pdf/";

    $uploadcare_image_url = $uploadCareURL;
    $filename = $imageName;
    $ext = pathinfo($filename, PATHINFO_EXTENSION);   //returns the extension
    $allowed_types = array('pdf');
    $randName = rand(124, 1000);
    $imgInfo = array();

    // If the file extension is allowed
    if (in_array($ext, $allowed_types)) {
        $new_filename = $filename;

        //$new_filepath = $base_path.'upload/orig/'.$new_filename;
        $imgpath = $src . $randName . '-' . $new_filename;


        // Attempt to copy the image from Uploadcare to our server
        if (copy($uploadcare_image_url, $imgpath)) {
            //Resize the image


            $imgInfo['image'] =  $randName . '-' .  $new_filename ;

            return $imgInfo;
        } else {
            return $imgInfo;
        }
    } else {
        return $imgInfo;
    }
}


/**
 *
 * @param string $list_extra_options contains a string of comma separated options for list display or profile display options
 * @param string $listOperations options in the 3 CSV value format
 * i.e. popup[delete,Delete Check,popup-class; copy,Copy,copy-class; add,ADD,add-class; openchild,Open Child List,popup-class;] topmenu[delete,Delete,btn-danger; add,Add,btn-primary; copy,Copy,btn-primary; simulate(abc, true), Run Simulator;]
 * from the data_dictionary.list_operations | data_dictionary.edit_operations | data_dictionary.view_operations read from database for current dict_id record
 * @return array
 */
function listExtraOptions($list_extra_options, $listOperations = false) {

//echo "<pre>INSIDE listExtraOptions<br>";
//print_r($list_extra_options);
//echo "\$buttonOptions::<br>";
//print_r($listOperations);
//echo "</pre>";
//die("");


    $list_extra_options = explode(';', $list_extra_options);
    //print_r($list_extra_options);die;

    foreach ($list_extra_options as $opt) {

        $action[] = explode(',', $opt);
    }

    // print_r($action);die;

    foreach ($action as $act) {

        switch (trim($act[0])) {
//            case 'delete':
//                $del_array = list_delete(trim($act[1]), trim($act[2]));
//                break;
//            case 'copy':
//                $copy_array = list_copy(trim($act[1]), trim($act[2]));
//                break;
//            case 'add':
//                $add_array = list_add(trim($act[1]), trim($act[2]));
//                break;
//            case 'checklist':
//                $checklist_array = 'true';
//                break;
//            case 'popupmenu':
//                $popupmenu = 'true';
//                break;
//            case 'single_delete':
//                $single_delete_array = single_delete(trim($act[1]), trim($act[2]));
//                break;
//            case 'single_copy':
//                $single_copy_array = single_copy(trim($act[1]), trim($act[2]));
//                break;
//            case 'popup_delete':
//                $popup_delete_array = popup_delete(trim($act[1]), trim($act[2]));
//                break;
//            case 'popup_copy':
//                $popup_copy_array = popup_copy(trim($act[1]), trim($act[2]));
//                break;
//            case 'popup_add':
//                $popup_add_array = popup_add(trim($act[1]), trim($act[2]));
//                break;
//            case 'popup_openChild':
//                $popup_openChild_array = popup_openChild(trim($act[1]), trim($act[2]));
//                break;

            case 'pagination':
                $pagination_array = trim($act[1]);
                break;


            case 'users':
                $users_array = trim($act[1]);
                break;

            ////for FFFR TABLE

            case 'friends':
                $friends_tbl = trim($act[1]);
                break;
            case 'follow':
                $follow_tbl = trim($act[1]);
                break;

            case 'favorite':
                $favorite_tbl = trim($act[1]);
                break;

            case 'rating':
                $rating_tbl = trim($act[1]);
                break;

            case 'voting':
                $voting_tbl = trim($act[1]);
                break;

            case 'lowerLimit':
                $lowerLimit = trim($act[1]);
                break;

            case 'upperLimit':
                $upperLimit = trim($act[1]);
                break;

            case 'userLimit':
                $userLimit = trim($act[1]);
                break;

            case 'voteLimit':
                $voteLimit = trim($act[1]);
                break;

            case 'userVoteLimit':
                $userVoteLimit = trim($act[1]);
                break;


            case 'timeLimit':
                $timeLimit = trim($act[1]);
                break;

            case 'voteChange':
                $voteChange = trim($act[1]);
                break;

            case 'votingType':
                $votingType = trim($act[1]);
                break;

            case 'editPagePagination':
                $editPagePagination = trim($act[1]);
                break;

            case 'numberLabel':
                $numberLabel = trim($act[1]);
                break;

            case 'pattern':
                $pattern = trim($act[1]);
                break;

            default:
        }
    }




    // Handle new options in the 3 CSV value format i.e. popup[add; delete, Trash it; copy, Duplicate, copyclass;] topmenu[add; delete, Delete Button, deleteClass; simulate(abc, true), Run Simulator;]
    if($listOperations !== false)
    {
        $operationsVarArray = getOperationsData($listOperations, 'list_operations');

        list($popupmenu, $popup_delete_array, $popup_copy_array, $popup_add_array, $popup_openChild_array,
            $customFunctionArray,
            $del_array, $copy_array, $add_array, $single_delete_array, $single_copy_array) = $operationsVarArray;

    }


    $listOptionsArray = array(
        "del_array" => $del_array, "copy_array" => $copy_array, "add_array" => $add_array,
        "custom_function_array" => $customFunctionArray,
        "single_delete_array" => $single_delete_array, "single_copy_array" => $single_copy_array,
        "checklist_array" => $checklist_array,
        "popupmenu" => $popupmenu, "popup_delete" => $popup_delete_array, "popup_copy" => $popup_copy_array, "popup_add" => $popup_add_array, "popup_openChild" => $popup_openChild_array,
        "pagination" => $pagination_array, "users" => $users_array, "friend_tbl" => $friends_tbl, "follow_tbl" => $follow_tbl, "favorite_tbl" => $favorite_tbl, "rating_tbl" => $rating_tbl,
        "voting_tbl" => $voting_tbl, "lowerLimit" => $lowerLimit, "upperLimit" => $upperLimit, "userLimit" => $userLimit, "voteLimit" => $voteLimit, "userVoteLimit" => $userVoteLimit,
        "timeLimit" => $timeLimit, "voteChange" => $voteChange, "votingType" => $votingType,
        "editPagePagination" => $editPagePagination, "numberLabel" => $numberLabel, "pattern" => $pattern
    );

	//pr($listOptionsArray);
    return $listOptionsArray;
}

//// end of list_extra_option function


/**
 *
 * @param string $csvAndParenthesisParameters containing semicolon ; and comma , separated action and params including custom functions identified by parenthesis `(` & `)`
 * @return array of keywords like `popup`, `topmenu` with values containing actions like `add`, `edit`, etc with parameters and `customFunction` with value containing array of parameters for it.
 * Array
    (
        [popup] => Array
            (
                [0] => Array
                    (
                        [0] => add
                    )
                [1] => Array
                    (
                        [0] => copy
                        [1] =>  Duplicate
                        [2] =>  copyclass
                    )
            )
        [topmenu] => Array
        (
            [0] => Array
                (
                    [0] => add
                )
            [customFunction] => Array
                (
                    [simulate] => Array
                        (
                            [0] => abc
                            [1] =>  true
                        )

                )
            [customFunctionButtonLabel] => Run Simulator
            [customFunctionButtonStyle] => ExtraClass
        )
    )
 */
function parseCsvParameters($csvAndParenthesisParameters)
{
    $params = array_filter(explode(";", $csvAndParenthesisParameters) );

    foreach ($params as $param) {
        $param = trim($param);
        // Handle custom functions by checking ending parenthesis `)`
        if(strpos($param, ')') !== false)
        {
            ###All params within parenthesis including function name
            $functionData = strstr($param, ')', true);

            $functionButtonLabelAndStyle = trim(str_replace(')', '', strstr($param, ')') ) );

            $replacementCount = 1;
            $functionButtonLabelAndStyle = trim(preg_replace('/,/', '', $functionButtonLabelAndStyle, $replacementCount) );##Replace only first comma(,) to extract the buttonStyle third param

            $functionButtonLabelAndStyleArray = explode(',', $functionButtonLabelAndStyle);

            $functionButtonLabel = trim($functionButtonLabelAndStyleArray['0']);
            $functionButtonStyle = trim($functionButtonLabelAndStyleArray['1']);

//            echo "<pre>\$functionButtonLabel:$functionButtonLabel <br> \$functionButtonStyle:$functionButtonStyle<br>";
//            print_r(strstr($param, ')'));
//            echo "</pre><br>";die;


            $functionName = strstr($functionData, '(', true);
            #die($functionName);
            $functionData = str_replace('(', '', strstr($functionData, '(') );

            ##last 2 array keys are for custom function buttonLabel and extraCss class respectively##
            $functionParametersArray[$functionName] = array_merge(explode(',', $functionData), array('customFunctionButtonLabel' => $functionButtonLabel, 'customFunctionButtonStyle' => $functionButtonStyle) );

            $csvParamDataArray['customFunction'] = $functionParametersArray;
//            $csvParamDataArray['customFunctionButtonLabel'] = $functionButtonLabel;
//            $csvParamDataArray['customFunctionButtonStyle'] = $functionButtonStyle;
        }
        else
        {
            $csvParamDataArray[] = explode(',', $param);
        }
    }

    return $csvParamDataArray;
}

/**
 * Parses new DD fields for list_operations|edit_operations|view_operations
 * @param string $operations containing 3 comma separated parameters for each button/popup separated by semicolon ; and keywords identified by `popup[` & `topmenu[`
 * @param string $operationType list_operations|edit_operations|view_operations
 * @return array of various variables
 */
function getOperationsData($operations, $operationType = 'list_operations') {
    $operationsKeywordArray = array_filter(explode(']', $operations) );
    $actions = array();

    foreach($operationsKeywordArray as $operationsKeywordData)
    {
        if(stripos($operationsKeywordData, 'popup[') !== false )
        {
            $actions['popup'] = trim(str_ireplace ('popup[', '', $operationsKeywordData) );
            $actions['popup'] = parseCsvParameters($actions['popup']);
        }
        else if(stripos($operationsKeywordData, 'topmenu[') !== false )
        {
            $actions['topmenu'] = trim(str_ireplace ('topmenu[', '', $operationsKeywordData) );
			/*Code Change Start Task ID 5.6.4*/	
            $actions['topmenu'] = parseCsvParameters($actions['topmenu']);
			/*Code Change End Task ID 5.6.4*/	
        }

    }

//    $buttonAndPopupOptions = explode(';', $buttonOptions);
//    //print_r($list_extra_options);die;
//
//    foreach ($buttonAndPopupOptions as $options) {
//        $buttonAndPopupActions[] = explode(',', $options);
//    }

   // Print complete parsed array from the DD record for new parameters format for debuging##
//    echo "<Pre>";
//    print_r($actions);
//    echo "</pre>";

    foreach ($actions as $keywordOperation => $actionData) {

        switch (strtolower(trim($keywordOperation) ) ) {
            case 'popup': {
				if($operationType !== 'list_operations')
					break;

				$popupmenu = 'true';##Required for Popups to work

				foreach ($actionData as $actionKey => $actionValue) {
					if($actionKey === 'customFunction')
					{
						$customFunctionArray = handleCustomFunctionActions($actionValue);
					}
					else
					{
						##SET LABEL TO DEFAULT ACTION NAME IF SINGLE PARAM FORMAT IS USED i.e. add; will result in Add button
						if(empty($actionValue[1]) )
							$actionValue[1] = ucfirst ($actionValue[0]);

						switch (strtolower(trim($actionValue[0]) ) ) {

							case 'delete':
								$popup_delete_array = popup_delete(trim($actionValue[1]), trim($actionValue[2]));
								break;
							case 'copy':
								$popup_copy_array = popup_copy(trim($actionValue[1]), trim($actionValue[2]));
								break;
							case 'add':
								$popup_add_array = popup_add(trim($actionValue[1]), trim($actionValue[2]));
								break;
							case 'openchild':
								$popup_openChild_array = popup_openChild(trim($actionValue[1]), trim($actionValue[2]));
								break;

							default:
						}
					}
				}
				break;
			}
			
            case 'topmenu': {
				foreach ($actionData as $actionKey => $actionValue) {
					if($actionKey === 'customFunction')
					{
						$customFunctionArray = handleCustomFunctionActions($actionValue);
					}
					else
					{
						##SET LABEL TO DEFAULT ACTION NAME IF SINGLE PARAM FORMAT IS USED i.e. add; will result in Add button
						if(empty($actionValue[1]) )
							$actionValue[1] = ucfirst ($actionValue[0]);

						switch (strtolower(trim($actionValue[0]) ) ) {
							case 'delete':
								$del_array = list_delete(trim($actionValue[1]), trim($actionValue[2]));
								break;
							case 'copy':
								$copy_array = list_copy(trim($actionValue[1]), trim($actionValue[2]));
								break;
							case 'add':
								$add_array = list_add(trim($actionValue[1]), trim($actionValue[2]));
								break;
							case 'saveadd':
								$save_add_array = list_add(trim($actionValue[1]), trim($actionValue[2]));
								break;
							case 'single_delete':
								$single_delete_array = single_delete(trim($actionValue[1]), trim($actionValue[2]));
								break;
							case 'single_copy':
								$single_copy_array = single_copy(trim($actionValue[1]), trim($actionValue[2]));
								break;
//                          case 'addimport':
//                               $addImportArray = addImport(trim($actionValue[1]), trim($actionValue[2]) );

							case 'submit':
								$submit_array = submitOptions(trim($actionValue[1]), trim($actionValue[2]));
								break;
								
							case 'facebook_auth':
								$facebook_array = submitOptions(trim($actionValue[1]), trim($actionValue[2]));
								break;
								
							case 'google_auth':
								$google_array = submitOptions(trim($actionValue[1]), trim($actionValue[2]));
								break;
								
							case 'linkedin_auth':
								$linkedin_array = submitOptions(trim($actionValue[1]), trim($actionValue[2]));
								break;
								
							default:
						}

					}

				}
				break;
			}
			
            default:
        }

    }

    return array(
            $popupmenu, $popup_delete_array, $popup_copy_array, $popup_add_array, $popup_openChild_array,
            $customFunctionArray,
            $del_array, $copy_array, $add_array, $single_delete_array, $single_copy_array,
            $submit_array,$facebook_array,$google_array,$linkedin_array,$save_add_array
        );

}

function handleCustomFunctionActions($customFunctionsData)
{
//    echo "<pre>";
//    print_r($customFunctionsData); die;

    $customFunctionArray = array();

    foreach ($customFunctionsData as $customFunctionName => $functionParams) {
        if(function_exists($customFunctionName) )
        {
            ###Remove the last 2 parameters from parameters array as they are used for customFunctionButtonLabel and customFunctionButtonStyle
            $buttonLabel = $functionParams['customFunctionButtonLabel'];
            $buttonStyle = $functionParams['customFunctionButtonStyle'];

            unset($functionParams['customFunctionButtonLabel'], $functionParams['customFunctionButtonStyle']);

            $functionParams = array_values($functionParams);

//            echo "<pre>After removing extra params";
//            print_r($functionParams);
//            echo "</pre>"; die;

            ##Used ` instead of comma , since the function parameter might contain comma char in string
            $customFunctionArray[] = customFunctionParameters($customFunctionName, implode('`', $functionParams), $buttonLabel, $buttonStyle );
        }
    }

    return $customFunctionArray;

}

function listvalues($setlistviews) {


    $listviews = explode(',', $setlistviews);

    // print_r($action);die;

    foreach ($listviews as $act) {

        switch (trim($act)) {
            case 'checklist':
                $checklist = 'true';
                break;
            case 'Xlist':
                $Xlist = 'true';
                break;
            case '*List':
                $List = 'true';
                break;
			case 'boxView':
                $boxView = 'true';
                break;
			case 'thumbView':
                $thumbView = 'true';
                break;
			case 'Cards':
                $Cards = 'true';
                break;
			case '*listView':
                $listView = 'true';
                break;
			case 'listview':
                $listView = 'true';
                break;
			case 'listView':
                $listView = 'true';
                break;

            default:
        }
    }

    // print_r($users);
    return array("checklist" => $checklist, "Xlist" => $Xlist, "*List" => $List, "boxView" => $boxView, "thumbView" => $thumbView, "Cards" => $Cards, "*listView" => $listView, "listview" => $listview, "listView" => $listView);
}


function listpageviews($setpageviews) {

    $pageviews = array_filter( array_map('trim', explode(',', $setpageviews)) );

    return $pageviews;
}

/* To Do:-
 * Get List Select Array
 * For Target Url & trim each values after explode
 */
function getListSelectParams($list_select){
	$list_select_arr = array();
	$list_select_sep = array_filter( array_map('trim', explode(';', $list_select)) );
	foreach ($list_select_sep as $listArray) {
		$list_select_arr[] = array_filter( array_map('trim', explode(',', $listArray)) );
	}
	return $list_select_arr;
}

/* To Do:-
 * Get Alignment Class
 * For Edit & View Operation
 * @params To check 
 * single_line_left , single_line_right, single_line_center 
 */
function getAlignmentClass($operation){
	$operations = array_filter( array_map('trim', explode(';', $operation)) );
	if(in_array('single_line_left',$operations)){
		return 'single_line_left';
	}
	if(in_array('single_line_right',$operations)){
		return 'single_line_right';
	}
	if(in_array('single_line_center',$operations)){
		return 'single_line_center';
	}
}




/* * ***
 * **************
 *
 *
 * *****
 *
 * editPagePagination fucntion code goes here
 * ******************************
 * ************************************
 *
 */


function editPagePagination($list_extra_options, $pkey) {

    /////////////Checking next/prev option on list edit page
    // echo "<font color=green>list extra options from inside function editPagePagination(, \$pkey:$pkey) responsible to display pagination links only(prev, next, first, last buttons)</font><br>";
    $list_extra_options = listExtraOptions($list_extra_options);

    //\\ print_r($list);die;



    $record = getWhere('data_dictionary', array('table_alias' => $_GET['tab'], 'display_page' => $_GET['display'], 'tab_num' => $_GET['tabNum']));


    if (trim($record[0]['table_type']) == 'child')
        $search_key = $_SESSION['parent_value'];
    else
        $search_key = $_SESSION['search_id'];


    ///////fetching forigen keys


    if (!empty($record[0]['list_filter']))
        $clause = listFilter($record[0]['list_filter'], $search_key);



    $next_id = nextKey($record[0]['database_table_name'], $pkey, $_GET['search_id'], $clause);

    $prev_id = prevKey($record[0]['database_table_name'], $pkey, $_GET['search_id'], $clause);

    $first_id = firstKey($record[0]['database_table_name'], $pkey, $clause);

    $last_id = lastKey($record[0]['database_table_name'], $pkey, $clause);




    if (trim($list_extra_options['editPagePagination']) == 'true') {

        $retData = "

<div class='editPagePagination'>

<a href='" . helperOfEPP($first_id, 'url') . "' class='button" . helperOfEPP($first_id) . "'>". pageFirst ."</a>

<a href='" . helperOfEPP($prev_id, 'url') . "' class='button" . helperOfEPP($prev_id) . "'>". pagePrev ."</a>

<a href='" . helperOfEPP($next_id, 'url') . "' class='button" . helperOfEPP($next_id) . "'>". pageNext ."</a>

<a href='" . helperOfEPP($last_id, 'url') . "' class='button" . helperOfEPP($last_id) . "'>". pageLast ."</a>

</div>";
    }

    return $retData;
}

/*
 *
 * /////////////////////////Function Return URL and CSS CLASS NAME FOR
 *
 * *******
 * ******************
 * *******************************
 * **
 * ******function editPagePagination
 *
 * ***
 * **************
 * *********************
 *
 *
 */

function helperOfEPP($id, $mode = 'false') {


    if ($mode == 'url' && !empty($id)) {


        return BASE_URL_SYSTEM . "main.php?display=$_GET[display]&tab=$_GET[tab]&tabNum=$_GET[tabNum]&ta=$_GET[ta]&search_id=$id&checkFlag=true&table_type=$_GET[table_type]&edit=true";
    } else if ($mode == 'false' && ( empty($id) || $id == trim($_GET['search_id']) )) {


        return ' disabled';
    }
}

/*
 *
 * Filtering dd->listFilter for obtaining 2 forigen keyes.. one is of current user and other can be anything
 *
 * Filters data based on DD.list_filter which supports such expressions as
 *  'user_id=#current_user_id,product_id=#current_product_id'
 *  'user_id=#current_user_id,product_id=#current_product_id;exampleColumn=exampleValue'
 *
 * The part after ; is directly appended to the generated sql with a AND condition as it is if parat before ; creates a non empty sql clause.
 * If clause is empty then the first part before ; is added to sql as it is like 'isActive>0' or 'user_id'
 *
 *
 * 1. In the list_filter (field) routine
 *   detect and replace certain keywords
 *       #current_user_id
 *       #current_user_name
 *       #current_product_id
 *       #current_product_name
 *   (the last 2 might not always be relevant or have a value.  for now this is up to the Admin to set up the parameters correctly)
 * 2. So, (for example)
 *   if list_filter =
 *           user_id=#current_user_id
 *   then GPE would replace #current_user_Id with the current user's ID  (18, for example)
 *   and filter using the expression  user_id=18
 *
 *
 *
 */

function listFilter($listFilter, $search) {

//echo "<pre>";
//print_r($listFilter);
//print_r($search);
//die;

    ###HANDLE PARENT->CHILD RELATIONSHIPT BASED ON DD.keyfield and DD.table_type = 'child'####
    if(is_array($listFilter) )
    {
        $listFilterParentChildClause = $listFilter['child_filter'];
        $listFilter = $listFilter['list_filter'];
    }

    $keyfield = explode(";", $listFilter);


    $firstParent = $keyfield[0];
    // print_r($keyfield);die;

    if (!empty($keyfield[1])) {

        $listCond = $keyfield[1];
    }

    //  $checkFlag = false;

    ###CREATES AN ARRAY OF ARRAY WITH KEYFIELD=>DYNAMIC_PART BASED ON CSV##
    if (!empty($keyfield[0])) {
        $i = 0;

        $keyfield = explode(",", $keyfield[0]);

        foreach ($keyfield as $val) {

            $keyField = explode("=", $val);

            $keyVal[$i] = array(trim($keyField[0]) => trim($keyField[1]));

            $i++;
        }
    }
    ###'projects=child_product_id'; kind of parameters, same for 'users='
//    foreach ($keyVal as $val) {
//
//        if (!empty($val['projects'])) {
//
//            $pid = $val['projects'];
//        }
//
//        if (!empty($val['users'])) {
//
//            $uid = $val['users'];
//        }
//    }

    ###DETECT THESE #KEYWORDS AND REPLACE THEM WITH RESPECTIVE DYNAMIC ID FROM USER SESSION
    ###$filterIdKeywordsArray = array('#current_user_id', '#current_user_name', '#current_product_id', '#current_product_name');

    $sqlClause = '';

    foreach ($keyVal as $dataVal) {

        $keywordId = reset($dataVal);
        $keyField = key($dataVal);

        switch ($keywordId) {
            case "#current_user_id":
                $sqlClause .= "$keyField=$_SESSION[uid] ";
                break;
            case "#current_user_name":
                $sqlClause .= "$keyField='$_SESSION[uname]' ";
                break;
            case "#current_product_id":
                #$sqlClause .= "$keyField=$_SESSION[product_id] ";
                break;
            case "#current_product_name":
                #$sqlClause .= "$keyField=$_SESSION[product_id] ";
                break;
            default:
                $sqlClause .= '';
        }

    }
    $sqlClause = str_replace(' ', ' AND ', trim($sqlClause) );


//    if (!empty($pid) && !empty($search)) {
//
//        $clause = "$pid = '$search'";
//
//        //$checkFlag = true;
//    }
//
//    if (!empty($uid)) {
//
//        if (!empty($clause)) {
//
//            $clause = $clause . " and " . $uid . "=" . $_SESSION['uid'];
//        } else {
//
//            $clause = $uid . "=" . $_SESSION['uid'];
//        }
//
//        //  $checkFlag = true;
//    }

    if (!empty($sqlClause) && !empty($listCond)) {
        $sqlClause = $sqlClause . " and " . $listCond;
    } else if (empty($sqlClause) && !empty($firstParent)) {
        $sqlClause = $firstParent;
    }

    ###BIND PARENT CHILD FILTER CLAUSE BASED ON RELATIONSHIP AND DD.keyfield###
    if(!empty($listFilterParentChildClause) && !empty($sqlClause) )
    {
        $sqlClause .= " AND $listFilterParentChildClause";
    }
    else if(!empty($listFilterParentChildClause) )
    {
        $sqlClause .= " $listFilterParentChildClause";
    }

    // exit($clause);

    return $sqlClause;
}

/* boxView hScroll Start */
function boxViewHscroll($pagination, $tab_num, $list_select_arr) { ?>
	<a href="javascript:void(0);" class="prev_slider" onclick="plusDivs(-<?php echo $pagination[0]; ?>,<?php echo $tab_num; ?>)">&#10094;</a>
	<a href="javascript:void(0);" class="next_slider" onclick="plusDivs(+<?php echo $pagination[0]; ?>,<?php echo $tab_num; ?>)">&#10095;</a>
	
	</div>
	
	<?php
	if (!empty($list_select_arr[2][0])) {
		echo "<a href='" . BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[2][2] . "&tab=" . $list_select_arr[2][0] . "&tabNum=" . $list_select_arr[2][1] . "' class='show_all ' id='test-super'>" . SHOW_ALL . "</a>";
	}
	
	if(isset($pagination[1])){
		if(strpos($pagination[1],'#') !== false){
			preg_match_all('!\d+!', $pagination[1], $limitPage);
			$limit = @$limitPage[0][0] * $pagination[0];
		}
	}
	?>
	
	<script>
	var tab_num = <?php echo $tab_num; ?>;
	var limit = <?php echo $limit; ?>;
	var per_page = <?php echo $pagination[0]; ?>;
	if(typeof slideIndex == 'undefined'){
		var slideIndex = [];
	}
	slideIndex[tab_num] = per_page;
	showDivs(slideIndex[tab_num],per_page,tab_num);

	function plusDivs(per_page,tab_num) {
		slideIndex[tab_num] += per_page;
		showDivs(slideIndex[tab_num],per_page , tab_num);
	}

	function showDivs(n,per_page,tab_num) {
		var i;
		var box = $("#content"+tab_num+" .boxView");
		if (n > box.length) {slideIndex[tab_num] = Math.abs(per_page)} 
		if (n < Math.abs(per_page)) {slideIndex[tab_num] = box.length} ;
		for (i = 0; i < box.length; i++) {
			box[i].classList.remove("showDiv"+tab_num);
			box[i].classList.add("showDiv"+tab_num);
			box[i].style.display = "none";
		}
		var start = parseInt(slideIndex[tab_num] - Math.abs(per_page));
		for(var item = start; item < slideIndex[tab_num]; item++){
			box[item].classList.add("hideDiv"+tab_num);
			box[item].style.display = "block"; 
		}
	}
	</script>
<?php
}
/* boxView hScroll End */

/* * ***
 * **************BoxView Pagination function which call jquery function code
 * Goes here
 * ******************************
 * ************************************
 *
 */


function boxViewPagination($pagination, $tab_num, $list_select_arr) {

//    echo "1 - stop here"; die;

	//pr($list_select_arr);
    //Added By Dharmesh 2018-10-12//
    foreach($pagination as $k=>$v){
            if(strpos($v, '#') !== FALSE){
                    preg_match_all('!\d+!',$v, $no_of_pages);
                    $no_of_pages = $no_of_pages[0][0];
            }else {
                    $no_of_pages = 0;
            }
    }
    if(count($pagination)==1) {
            $pagination = $pagination[0];
    }else{
            $pagination = $pagination[0];
    }


    //// BoxView Pagination code inserted here
    ?>
    <!-- An empty div which will be populated using jQuery -->

    <br>
    <div class='page_navigation'></div>
	

    <?php
	/* By Shaily Start*/
	
	if(isset($list_pagination[1])){
		if(strpos($list_pagination[1],'#') !== false){
			preg_match_all('!\d+!', $list_pagination[1], $limitPage);
			$limit = @$limitPage[0][0] * $list_pagination[0];
		}
	}
    if (!empty($list_select_arr[2][0])) {

// echo "got here<br><br>"; die;

        echo " <a href='" . BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[2][2] . "&tab=" . $list_select_arr[2][0] . "&tabNum=" . $list_select_arr[2][1] . "' class='show_all ' id='test-super'>" . SHOW_ALL . "</a>";
    }
    ?>


    </div>


    <?php
    if ($pagination != 'ALL')
    {
    ?>

        <script type="text/javascript">

            var go_to_page<?= $tab_num; ?>;

            $(document).ready(function () {

                function setpaginations(pagerecordchange, page_num) {
                    pag = $('#content<?= $tab_num; ?>');
                    pag_id = "#content<?= $tab_num; ?>";

                    //getting the amount of elements inside content div
                    var number_of_items = $('#content<?= $tab_num; ?>').children('.boxView').size();
                    var show_per_page;
                    <?php
                    if (!empty($pagination) && !empty($no_of_pages)) {
                    ?>
                        var number_of_pages = <?= $no_of_pages; ?>;
                        var without_added_pages = <?= $no_of_pages; ?>;
                        var pagination = <?= $pagination; ?>;
                        var totalleft = without_added_pages*pagination;
                    <?php
                    }
                    else
                    {
                    ?>
                        //calculate the number of pages we are going to have

                        var number_of_pages = Math.ceil(number_of_items / 8);

                    <?php
                    }
                    ?>

                    //}else {

                    //var number_of_pages = <?= $no_of_pages; ?>;
                    //}
                    //set the value of our hidden input fields

                    //now when we got all we need for the navigation let's make it '
                    /*
                     what are we going to have in the navigation?
                     - link to previous page
                     - links to specific pages
                     - link to next page
                     */
                    var navigation_html = '<a class="previous_link" href="#">Prev</a>';
                    var current_link = 0;

                    while (number_of_pages > current_link) {
                        navigation_html += '<a class="page_link" href="javascript:go_to_page<?= $tab_num; ?>(' + current_link + ')" longdesc="' + current_link + '">' + (current_link + 1) + '</a>';
                        current_link++;
                    }
                    navigation_html += '<a class="next_link" href="#">Next</a>';
                    $('#content<?= $tab_num; ?>').find('.page_navigation').html(navigation_html);
                    //add active_page class to the first page link
                    $('#content<?= $tab_num; ?>').find('.page_navigation .page_link:first').addClass('active_page');
                    //hide all the elements inside content div
                    $('#content<?= $tab_num; ?>').children('.boxView').css('display', 'none');
                    //and show the first n (show_per_page) elements


                    //$('#content<?= $tab_num; ?>').children('.boxView').slice(0, show_per_page).css('display', 'block');

                    <?php
                    if (!empty($pagination) && !empty($no_of_pages)) {
                    ?>
                        if(pagerecordchange == false){
                            show_per_page = number_of_items - totalleft;
                            //get the element number where to start the slice from
                            start_from = page_num+2;
                        } else {
                            //how much items per page to show
                            show_per_page = <?= $pagination; ?>;
                            //get the element number where to start the slice from
                            start_from = page_num * show_per_page;
                        }
                    <?php
                    }
                    else {
                    ?>
                        //how much items per page to show
                        show_per_page = 9;
                        //get the element number where to start the slice from
                        start_from = page_num * show_per_page;
                    <?php
                    }
                    ?>

                    //get the element number where to end the slice
                    end_on = start_from + show_per_page;
                    //hide all children elements of content div, get specific items and show them
                    $('#content<?= $tab_num; ?>').children('.boxView').css('display', 'none') .slice(start_from, end_on).css('display', 'block');
                    $('#content<?= $tab_num; ?>').children('.current_page').val(0);
                    $('#content<?= $tab_num; ?>').children('.show_per_page').val(show_per_page);
                    ///next function goes here
                    $('#content<?= $tab_num; ?>').on("click", ".next_link", function (event) {
                        event.preventDefault();
                        new_page = parseInt($(this).parents("#content<?= $tab_num; ?>").find('.current_page').val()) + 1;
                        //if there is an item after the current active link run the function
                        if ($(this).parents("#content<?= $tab_num; ?>").find('.active_page').next('.page_link').length == true) {
                            go_to_page<?= $tab_num; ?>(new_page);
                        }
                    });
                    ////previous function goes here
                    $('#content<?= $tab_num; ?>').on("click", ".previous_link", function (event) {
                        event.preventDefault();
                        new_page = parseInt($(this).parents("#content<?= $tab_num; ?>").find('.current_page').val()) - 1;
                        //if there is an item before the current active link run the function
                        if ($(this).parents("#content<?= $tab_num; ?>").find('.active_page').prev('.page_link').length == true) {
                            go_to_page<?= $tab_num; ?>(new_page);
                        }
                    });
                }

                setpaginations(true,0);

                go_to_page<?= $tab_num; ?> = function(page_num) {
                    if(page_num == <?= $no_of_pages; ?>)
                        setpaginations(false, page_num);
                    else
                        setpaginations(true, page_num);
                    //get the number of items shown per page
                    var show_per_page = parseInt($('#content<?= $tab_num; ?>').children('.show_per_page').val());
                    //alert(page_num+1);

                    /*get the page link that has longdesc attribute of the current page and add active_page class to it
                     and remove that class from previously active page link*/
                    $('#content<?= $tab_num; ?>').find('.page_link[longdesc=' + page_num + ']').addClass('active_page').siblings('.active_page').removeClass('active_page');
                    //update the current page input field
                    $('#content<?= $tab_num; ?>').find('.current_page').val(page_num);
                };

            });

        </script>

    <?php
    }
}
//////boxView functions end here

// Not in use;
function callBxSlider($tab_num,$list_pagination){
	$slideWidth = "200";
	if(isset($list_pagination[1]) && !empty($list_pagination[1])){
		$slideWidth = $list_pagination[1];
	}
	?>
	</div>
	
	<script type="text/javascript">
		$(document).ready(function(){
			$('#content<?php echo $tab_num; ?>').bxSlider({
				pager : false,
				minSlides: 1,
				maxSlides: 4,
				slideWidth: <?php echo $slideWidth; ?>,
				moveSlides: 3,
				//slideMargin: 20,
				//adaptiveHeight: true,
				//shrinkItems: true,
				/* onSliderLoad: function () {
					$('#content<?php echo $tab_num; ?>').css("visibility", "visible");
				}, */
			});
		});
	</script>
<?php 
}



/*
 * these functions are for future enhancements
 */
function customFunctionParameters($function, $parameters, $label, $extraCssClass)
{
    return array('function' => $function, 'params' => $parameters, 'label' => $label, 'style' => $extraCssClass);
}


/**
 * $options "intake style" (I = import a file, M = manual text box input, or P for prompt for either one)
 *   if no C/T - default to C (C=CSV, T=TSV)
 *   if no I M P - default to P
 *   V = force preview - and prompt before importing(For future, will not code this as of now)
 * @param string $title To be displayed above the import file/textarea field.
 * @param string $description
 * @param string $options C|T, I|M|P, V. Example - CI, CM, CMV, TI, TM, TIV or blank '' which means CP as default
 * @param string $f1
 * @param string $f2
 * @param string $f3
 * @param string $f4
 * @param string $f5
 * @param string $f6
 * @param string $f7
 * @param string $f8
 * @param string $f9
 * @param string $f10
 * @param string $f11
 * @param string $f12
 */
function addimport($title, $description, $options, $fields, $f1 = false, $f2 = false, $f3 = false, $f4 = false, $f5 = false, $f6 = false, $f7 = false, $f8 = false, $f9 = false, $f10 = false, $f11 = false, $f12 = false)
{


}

/*
 * these functions are for future enhancements
 */
function list_delete($label, $look) {

    return array("label" => $label, "style" => $look);
}

function list_copy($label, $look) {

    return array("label" => $label, "style" => $look);
}

function list_add($label, $look) {

    return array("label" => $label, "style" => $look);
}

function single_delete($loc, $look) {

    return array("loc" => $loc, "style" => $look);
}

function single_copy($loc, $look) {

    return array("loc" => $loc, "style" => $look);
}

function popup_delete($label, $look) {

    return array("label" => $label, "style" => $look);
}

function popup_copy($label, $look) {

    return array("label" => $label, "style" => $look);
}

function popup_add($label, $look) {

    return array("label" => $label, "style" => $look);
}

function popup_openChild($label, $look) {

    return array("label" => $label, "style" => $look);
}

function submitOptions($label, $look) {
    return array("value" => $label, "style" => $look);
}

function generateFacebookButton($facebook_array){
	$facebookButton = "<a onclick='fbLogin();' class='btn btn-primary update-btn " . $facebook_array['style'] . "'>
					<span class='fa fa-facebook'></span>
					".$facebook_array['value']."
				</a> &nbsp;"; ?>
	<script>
	window.fbAsyncInit = function() {
		// FB JavaScript SDK configuration and setup
		FB.init({
			appId      : '<?php echo FACEBOOK_APP_ID; ?>', // FB App ID
			cookie     : true,  // enable cookies to allow the server to access the session
			xfbml      : true,  // parse social plugins on this page
			version    : 'v2.8' // use graph api version 2.8
		});
		
		// Check whether the user already logged in
		/* FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
				//display user data
				//getFbUserData();
			}
		}); */
	};

	// Load the JavaScript SDK asynchronously
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));

	// Facebook login with JavaScript SDK
	function fbLogin() {
		FB.login(function (response) {
			if (response.authResponse) {
				// Get and display the user profile data
				getFbUserData();
			} else {
				alert('User cancelled signup or did not fully authorize.');
			}
		}, {scope: 'email'});
	}

	// Fetch the user profile data from facebook
	function getFbUserData(){
		FB.api('/me', {locale: 'en_US', fields: 'id,first_name,last_name,email,link,gender,locale,picture'},
		function (response) {
			$.ajax({
				type: 'POST',
				url: '?action=update&table_type=facebookLogin',
				dataType: 'json',
				data: response,
				beforeSend: function(xhr) {
					
				},
				success: function(response){
					if(response.message){
						alert(response.message);
						window.location.href = response.returnUrl;
					} else {
						window.location.href = response.returnUrl;
					}
				},
				error: function(xhr, status, error) {
					alert("Something went wrong. Please try again.");
				}
			});
		});
	}
	</script>
	<?php
	return $facebookButton;
}

function generateGoogleButton($google_array){
	$googleButton = "<a id='googleSignup' class='btn btn-primary update-btn " . $google_array['style'] . "'>
							<span class='fa fa-google'></span>
							".$google_array['value']."
						</a> &nbsp;"; ?>
	<script src="https://apis.google.com/js/api:client.js"></script>
	<script>
		var googleUser = {};
		var googleSignup = function() {
			gapi.load('auth2', function(){
				// Retrieve the singleton for the GoogleAuth library and set up the client.
				auth2 = gapi.auth2.init({
					client_id: '<?php echo GOOGLE_CLIENT_ID; ?>',
					cookiepolicy: 'single_host_origin',
					//callback : 'googleCallback',
					scope: 'profile email',
					//approvalprompt:'force',
					//scope : 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read'
				});
				attachSignin(document.getElementById('googleSignup'));
			});
		};

		function attachSignin(element) {
			auth2.attachClickHandler(element, {},
				function(googleUser) {
					$.ajax({
						type: 'POST',
						url: '?action=update&table_type=googleLogin',
						dataType: 'json',
						data: { email : googleUser.getBasicProfile().getEmail() , name : googleUser.getBasicProfile().getName() },
						beforeSend: function(xhr) {
							
						},
						success: function(response){
							if(response.message){
								alert(response.message);
								window.location.href = response.returnUrl;
							} else {
								window.location.href = response.returnUrl;
							}
						},
						error: function(xhr, status, error) {
							alert("Something went wrong. Please try again.");
						}
					});
				}, function(error) {
					//JSON.stringify(error.error, undefined, 2)
					alert(error.error);
				}
			);
		}
		
		googleSignup();
		</script>
	<?php 
	return $googleButton;
}

function generateLinkedinButton($linkedin_array){
	$linkedinButton = "<a onclick='onLinkedInLoad()' class='btn btn-primary update-btn " . $linkedin_array['style'] . "'>
							<span class='fa fa-linkedin'></span>
							".$linkedin_array['value']."
						</a> &nbsp;"; ?>
	<script type="text/javascript" src="//platform.linkedin.com/in.js">
		api_key		: <?php echo LINKEDIN_APP_ID. PHP_EOL; ?>
		authorize	: true
		scope		: r_basicprofile r_emailaddress
	</script>
	<script>
		function onLinkedInLoad() {
			IN.UI.Authorize().place();
			<?php if (stripos($_SERVER['HTTP_USER_AGENT'], 'Edge/') !== false) {  ?>
				IN.Event.on(IN, "auth", getProfileData());
			<?php } else {  ?>
				IN.Event.on(IN, "auth", getProfileData);
			<?php } ?>
		} 
		function getProfileData() {
			//IN.API.Raw("/people/~").result(displayProfileData).error(onError);
			IN.API.Profile("me").fields("id", "first-name", "last-name", "headline", "location", "picture-url", "public-profile-url", "email-address","summary").result(displayProfileData).error(onError);
		}
		function displayProfileData(data){
			var user = data.values[0];
			/* document.getElementById("picture").innerHTML = '<img src="'+user.pictureUrl+'" />';
			document.getElementById("name").innerHTML = user.firstName+' '+user.lastName;
			document.getElementById("intro").innerHTML = user.headline;
			document.getElementById("email").innerHTML = user.emailAddress;
			document.getElementById("location").innerHTML = user.location.name;
			document.getElementById("link").innerHTML = '<a href="'+user.publicProfileUrl+'" target="_blank">Visit profile</a>';
			document.getElementById('profileData').style.display = 'block'; */
			saveUserData(user);
		}
		
		// Save user data to the database
		function saveUserData(userData){
			$.ajax({
				type: 'POST',
				url: '?action=update&table_type=linkedinLogin',
				dataType: 'json',
				//data: { email : userData.getBasicProfile().getEmail() , name : googleUser.getBasicProfile().getName() },
				data: userData,
				beforeSend: function(xhr) {
					
				},
				success: function(response){
					if(response.message){
						alert(response.message);
						window.location.href = response.returnUrl;
					} else {
						window.location.href = response.returnUrl;
					}
				},
				error: function(xhr, status, error) {
					alert("Something went wrong. Please try again.");
				}
			});
		}

		// Handle an error response from the API call
		function onError(error) {
			console.log(error);
		}
		
		// Destroy the session of linkedin
		function logout(){
			IN.User.logout(removeProfileData);
		}
		
		// Remove profile data from page
		function removeProfileData(){
			//document.getElementById('profileData').remove();
		}
		</script>
	<?php 
	return $linkedinButton;
}
