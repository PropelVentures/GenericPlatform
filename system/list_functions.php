<?php
/*

function parseListExtraOption($listExtraOptions,$inPx=false){

function listExtraOptions($list_extra_options) {
function editPagePagination()
function listFilter()
function getListSortingValue($list_sort){

function boxViewPagination($pagination, $component_order, $list_select_arr) {
function get_string_between($string, $start, $end){
function getColumnsNames($table){
function listExtraOptions($list_extra_options, $listOperations = false) {
function parseCsvParameters($csvAndParenthesisParameters)
function getOperationsData($operations, $operationType = 'list_operations') {
function listvalues($setlistviews) {
function listpageviews($setpageviews) {
function getAlignmentClass($operation){
function editPagePagination($list_extra_options, $pkey) {
function helperOfEPP($id, $mode = 'false') {
function translateSpecialKeysValueTOSQL($array,&$isexistFilter,&$isExistField){
function convertVariableValuesToRealValues($value){
function convertFilterToSQL($filter,&$isexistFilter,&$isExistField){
function checkORConditionAndConvertToSQL($filter,&$isexistFilter,&$isExistField){
function listFilter($listFilter, $search,&$isexistFilter,&$isExistField) {
function getFiltersArray($list_filters){
function boxViewHscroll($pagination, $component_order, $list_select_arr) { ?>
function plusDivs(per_page,component_order) {
function showDivs(n,per_page,component_order) {
 
 function callBxSlider($component_order,$list_pagination){  ** not USED?

function list_delete($label, $look) {
function list_copy($label, $look) {
function list_add($label, $look) {
function single_delete($loc, $look) {
function single_copy($loc, $look) {
function popup_delete($label, $look) {
function popup_copy($label, $look) {
function popup_add($label, $look) {
function popup_openChild($label, $look) {
function submitOptions($label, $look) {
function isAllowedToShowByPrivilegeLevel($row){
function isStripHtmlTags($value){
function isFileExistFilterFullFillTheRule($row,$isExistFilter,$isExistField){
function getDefaultListViewExtraOptions($con,$displaypage){
// function setLeftRightSideBars($con,$page_name,$posittion,&$left_sidebar,&$right_sidebar,&
(commented out??)
function showListFilterSelection($row,$filters_srray,$selected_filter_index){
function showListViewSelection($row,$filters_srray,$selected_filter_index){
function listColumnWidth($tbRow,$minLimit = 100){
function calculateWidthsInPercentage($array){
function truncateLongDataAsPerAvailableWidth($data,$width,$roundPxls=true){
function format_field_value_length( $tbRow, $value ) {

 
 */

function parseListExtraOption($listExtraOptions,$inPx=false){
	$height = "";
	$width = "100%";
	$align = 'left';
	$divClass = 'left_cont';
	$listExtraOptions = trim($listExtraOptions);
	if(!empty($listExtraOptions)){
		$params = explode(";",$listExtraOptions);
		if(!empty($params)){
			foreach($params as $param){
				list($key,$value) = explode("=",$param);
				switch($key){
					case 'height':
						$height = $value."px";
						break;
					case 'width':
						$width = $value.($inPx ? 'px':"%");
						break;
					case 'align':
						if($value == 'left'){
							$divClass = 'left_cont';
						} elseif($value == 'right'){
							$divClass = 'right_cont';
						} elseif($value == 'center'){
							$divClass = 'center_cont';
						}
						$align = $value;
						break;
					default:
						break;
				}
			}
		}
	}
	return [$height,$width,$align,$divClass];
}

/**
 * Get subsring of a string with specified boundary characters
 * 
 * @param string $string The string to extract from
 * @param string $start The start character
 * @param string $end The end character
 * 
 * @author ph
 * 
 * @return string 
 */
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

/**
 * Get names of columns from a database table
 * 
 * @param string $table The table to list column names
 * 
 * @author ph
 * 
 * @return array Indexed array of column names
 */
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
		$act = chop($act, ";");
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
    $data = explode(';',$setpageviews);
    $result = [];
    foreach ($data as $key => $value) {
        if (empty($value)) {
            continue;
        }
        $temp = explode(':',$value);
        if (!empty(trim($temp[1]))) {
            $result[trim($temp[0])] = trim($temp[1]);
        }
    }
    return $result;
}

/* To Do:-
 * Get List Select Array
 * For Target Url & trim each values after explode
 * 
 * If parent_dict_id is specified and an old protocol is detected, the old protocol is converted to the new protocol.(By Marko)
 */
function getListSelectParams($list_select, $parent_dict_id = -1){
	$list_select_arr = array();
	$list_select_sep = array_filter( array_map('trim', explode(';', $list_select)) );
	foreach ($list_select_sep as $listArray) {
		$list_select_arr[] = array_filter( array_map('trim', explode(',', $listArray)) );
  }
 
  // Added by Marko. new select_option structure: [dict_id OR url OR function_name], [label], [css_style_class], [visibility priv];
  $select_options = array();
  $con = connect();
  $convert_query = '';
  foreach($list_select_arr as $item)
  {
    if ( is_numeric($item[0]) || preg_match('/^https?:\/\//', $item[0]) )
    {
      if ( is_numeric($item[0]) ) 
      {
        // dict_id: fetch needed columns from data_dictionary of the dict_id and set in target url
        $query = $con->query("select page_name, component_order, table_alias from data_dictionary where dict_id={$item[0]}");
        $row = $query->fetch_assoc();
        $dict_id = $item[0];
        $page_name = $row['page_name'];
        $component_order = $row['component_order'];
        $table_alias = $row['table_alias'];
      } else {
        parse_str((parse_url($item[0]))['query'] , $querys);
        $page_name = $querys['page_name'];
        $component_order = $querys['ComponentOrder'];
        $table_alias = $querys['table_alias'];

        // Get dict_id from url query. After completion of the new protocol , following commands will be removed.
        $query = $con->query("select dict_id from data_dictionary where page_name='{$page_name}' and table_alias='{$table_alias}'");
        $row = $query->fetch_assoc();
        $dict_id = $row['dict_id'];
      }

      $btn_label = $item[1];
      $css_class = $item[2];
      $vis_priv = $item[3];
    }
    else
    {
      // if the old structure is detected, then we convert the old structure to new structure.
      $page_name = $item[2];
      $component_order = $item[1];
      $table_alias = $item[0];
      $btn_label = null;
      $css_class = null;
      $vis_priv = null;

      $query = $con->query("select dict_id from data_dictionary where page_name='{$page_name}' and table_alias='{$table_alias}' and component_order='{$component_order}'");
      $row = $query->fetch_assoc();
      $dict_id = $row['dict_id'];
      
    }
   
    // create a new protocol string.
    $convert_query .= ($dict_id.','.$btn_label.','.$css_class.",".$vis_priv.";");
    
    $select_options[] = array($table_alias, $component_order, $page_name,  $btn_label,  $css_class, $vis_priv);
    
  }

  // create a new query for new protocol
  if ($parent_dict_id != -1) 
  {
    $convert_query = "update data_dictionary set list_select='{$convert_query}' where dict_id = '{$parent_dict_id}'";
    $con->query($convert_query);
    
  }
  
	return $select_options;
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


    // component_order changed to component_order

    $record = getWhere('data_dictionary', array('table_alias' => $_GET['table_alias'], 'page_name' => $_GET['page_name'], 'component_order' => $_GET['ComponentOrder']));


    if (trim($record[0]['table_type']) == 'child')
        $search_key = $_SESSION['parent_value'];
    else
        $search_key = $_SESSION['search_id'];


    ///////fetching forigen keys

    $isExistFilter;
    $isExistField;
    if (!empty($record[0]['list_filter']))
        $clause = listFilter($record[0]['list_filter'], $search_key,$isExistFilter,$isExistField);



    $next_id = nextKey($record[0]['table_name'], $pkey, $_GET['search_id'], $clause);

    $prev_id = prevKey($record[0]['table_name'], $pkey, $_GET['search_id'], $clause);

    $first_id = firstKey($record[0]['table_name'], $pkey, $clause);

    $last_id = lastKey($record[0]['table_name'], $pkey, $clause);




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


        return BASE_URL_SYSTEM . "main-loop.php?page_name=$_GET[page_name]&table_alias=$_GET[tab]&ComponentOrder=$_GET[ComponentOrder]&table_alias$_GET[ta]&search_id=$id&checkFlag=true&table_type=$_GET[table_type]&edit=true";
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
function translateSpecialKeysValueTOSQL($array,&$isexistFilter,&$isExistField){

  $result = '';
  if(empty($array[0]) || empty($array[1])){
    return $result;
  }
  $condition = trim($array[0]);
  $key = trim($array[1]);
  if(strtoupper($condition) ==='EMPTY'){
    $result = "$key=''";
  }else if(strtoupper($condition) ==='!EMPTY'){
    $result = "$key!=''";
  }else if(strtoupper($condition) ==='NULL'){
    $result = "$key=NULL";
  }else if(strtoupper($condition) ==='!NULL'){
    $result = "$key!=NULL";
  }else if(strtoupper($condition) ==='FILEEXISTS'){
    $isexistFilter = 'exist';
    $isExistField = $key;
  }else if(strtoupper($condition) ==='!FILEEXISTS'){
    $isexistFilter = 'not_exist';
    $isExistField = $key;
  }
  return $result;
}

function convertVariableValuesToRealValues($value){
  if(strpos($value,'#current_user_id') !==false){
    $value = str_replace('#current_user_id',$_SESSION[uid],$value);
  }else if(strpos($value,'#current_user_name') !==false){
    $value = str_replace('#current_user_name',"'".$_SESSION[uname]."'",$value);
  }
  return $value;
}

function convertFilterToSQL($filter,&$isexistFilter,&$isExistField){
  $filter = trim($filter);
  $result = '';
  if(empty($filter)){
    return $result;
  }else{
    $specialKeys = explode(':',$filter);
    if(count($specialKeys) > 1){
      $result = translateSpecialKeysValueTOSQL($specialKeys,$isexistFilter,$isExistField);
      $result = convertVariableValuesToRealValues($result);
    }else{
      $result = $filter;
    }

    return convertVariableValuesToRealValues($result);
  }
}

function checkORConditionAndConvertToSQL($filter,&$isexistFilter,&$isExistField){
  $filter = trim($filter);
  if(empty($filter)){
    return '';
  }else{
    $allConditions = explode('OR',$filter);
    if(count($allConditions) > 1){
      foreach ($allConditions as $key => $value) {
        $allConditions[$key] = convertFilterToSQL($value,$isexistFilter,$isExistField);
      }
      $result = '';
      $length = count($allConditions);
      foreach ($allConditions as $key => $value) {
        $result .= $value;
        if($key+1 != $length){
          $result .= ' OR ';
        }
      }
      return $result;
    }else{
      return convertFilterToSQL($filter,$isexistFilter,$isExistField);
    }
  }
}

function listFilter($listFilter, $search,&$isexistFilter,&$isExistField) {

    if(is_array($listFilter) )
    {
        $listFilterParentChildClause = $listFilter['child_filter'];
        $listFilter = $listFilter['list_filter'];
    }
    $allFilters = explode('AND',$listFilter);

    foreach ($allFilters as $key => $value) {
      if(empty(trim($value))){
        unset($allFilters[$key]);
      }else{
        $allFilters[$key] = checkORConditionAndConvertToSQL($value,$isexistFilter,$isExistField);
      }

    }
    $result = '';
    $itration = 1;
    $length = count($allFilters);
    foreach ($allFilters as $key => $value) {
      $result = $result.' '.trim($value).' ';

      if($length > $itration){
        $result .= "AND";
        $itration++;
      }
    }
    if(!empty($listFilterParentChildClause) && !empty($result) )
    {
        $result .= " AND $listFilterParentChildClause";
    }
    else if(!empty($listFilterParentChildClause) )
    {
        $result .= " $listFilterParentChildClause";
    }
    return $result;

}


function getListSortingValue($list_sort){
  $field_lists = explode(';',$list_sort);
  $field_str = '';
  foreach ($field_lists as $key => $value) {
      if(strpos($value,'~') !==false){
          $value = str_replace('~', '', $value);
          if($key==0){
              $field_str .= $value.' DESC';
          }else{
              $field_str .= ','.$value.' DESC';
          }
      }else{
          if($key==0){
              $field_str .= $value;
          }else{
              $field_str .= ','.$value;
          }
      }
  }
  return $field_str;
}
function getFiltersArray($list_filters){
  $result = [];
  $counter =0;
  $allFilters = explode(';',trim($list_filters));
  foreach ($allFilters as $key => $value) {
    $value = trim($value);
    if(!empty($value)){
        $keyValue = explode(',',$value);
        if(count($keyValue)>1){
          $result[$counter]['label'] = trim($keyValue[1]);
          $result[$counter]['filter'] = trim($keyValue[0]);
          $counter++;
        }
    }
  }
  return $result;
}
/* boxView hScroll Start */
function boxViewHscroll($pagination, $component_order, $list_select_arr) { ?>

	<a href="javascript:void(0);" class="prev_slider" onclick="plusDivs(-<?php echo $pagination['itemsperpage']; ?>,<?php echo $component_order; ?>)">&#10094;</a>
	<a href="javascript:void(0);" class="next_slider" onclick="plusDivs(+<?php echo $pagination['itemsperpage']; ?>,<?php echo $component_order; ?>)">&#10095;</a>

	</div>

	<?php
    if(!isset($pagination['itemsperpage']))
        $pagination['itemsperpage'] = 9;

	if (!empty($list_select_arr[2][0])) {
		echo "<a href='" . BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[2][2] . "&table_alias=" . $list_select_arr[2][0] . "&ComponentOrder=" . $list_select_arr[2][1] . "' class='show_all ' id='test-super'>" . SHOW_ALL . "</a>";
	}
	if(isset($pagination['totalpages'])){
		if(strpos($pagination['totalpages'],'#') !== false){
			preg_match_all('!\d+!', $pagination['totalpages'], $limitPage);
			$limit = @$limitPage[0][0] * $pagination['itemsperpage'];
		}
	}
	?>

	<script>
	var component_order = <?php echo $component_order; ?>;
	var limit = <?php echo $limit; ?>;
	var per_page = <?php echo $pagination['itemsperpage'];?>;
	if(typeof slideIndex == 'undefined'){
		var slideIndex = [];
	}
	slideIndex[component_order] = per_page;
	showDivs(slideIndex[component_order],per_page,component_order);

	function plusDivs(per_page,component_order) {
		slideIndex[component_order] += per_page;
		showDivs(slideIndex[component_order],per_page , component_order);
	}

	function showDivs(n,per_page,component_order) {
		var i;
		var box = $("#content"+component_order+" .boxView");
		if (n > box.length) {slideIndex[component_order] = Math.abs(per_page)}
		if (n < Math.abs(per_page)) {slideIndex[component_order] = box.length} ;
		for (i = 0; i < box.length; i++) {
			if(typeof box[item] != "undefined" && box[i].length > 0) {
				box[i].classList.remove("showDiv"+component_order);
				box[i].classList.add("showDiv"+component_order);
				box[i].style.display = "none";
			}
		}
		var start = parseInt(slideIndex[component_order] - Math.abs(per_page));
		for(var item = start; item < slideIndex[component_order]; item++){
			if(typeof box[item] != "undefined" && box[item].length > 0) {
				box[item].classList.add("hideDiv"+component_order);
				box[item].style.display = "block";
			}
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


function boxViewPagination($pagination, $component_order, $list_select_arr) {
//    echo "1 - stop here"; die;

    //Added By Dharmesh 2018-10-12//
    if(isSet($pagination['totalpages'])){
        preg_match_all('!\d+!',$pagination['totalpages'], $no_of_pages);
        $no_of_pages = $no_of_pages[0][0];
    }else{
        $no_of_pages = 0;
    }
    if(count($pagination)==1) {
            $pagination = $pagination['itemsperpage'];
    }else{
            $pagination = $pagination['itemsperpage'];
    }


    //// BoxView Pagination code inserted here
    ?>
    <!-- An empty div which will be populated using jQuery -->

    <br>
    <div class='page_navigation'></div>


    <?php
	/* By Shaily Start*/

	if(isset($list_pagination['totalpages'])){
		if(strpos($list_pagination['totalpages'],'#') !== false){
			preg_match_all('!\d+!', $list_pagination['totalpages'], $limitPage);
			$limit = @$limitPage[0][0] * $list_pagination['itemsperpage'];
		}
	}
    if (!empty($list_select_arr[2][0])) {

// echo "got here<br><br>"; die;

        echo " <a href='" . BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[2][2] . "&table_alias=" . $list_select_arr[2][0] . "&ComponentOrder=" . $list_select_arr[2][1] . "' class='show_all ' id='test-super'>" . SHOW_ALL . "</a>";
    }
    ?>


    </div>


    <?php
    if ($pagination != 'ALL')
    {
    ?>

        <script type="text/javascript">

            var go_to_page<?= $component_order; ?>;

            $(document).ready(function () {

                function setpaginations(pagerecordchange, page_num) {
                    pag = $('#content<?= $component_order; ?>');
                    pag_id = "#content<?= $component_order; ?>";

                    //getting the amount of elements inside content div
                    var number_of_items = $('#content<?= $component_order; ?>').children('.boxView').size();
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
                        navigation_html += '<a class="page_link" href="javascript:go_to_page<?= $component_order; ?>(' + current_link + ')" longdesc="' + current_link + '">' + (current_link + 1) + '</a>';
                        current_link++;
                    }
                    navigation_html += '<a class="next_link" href="#">Next</a>';
                    $('#content<?= $component_order; ?>').find('.page_navigation').html(navigation_html);
                    //add active_page class to the first page link
                    $('#content<?= $component_order; ?>').find('.page_navigation .page_link:first').addClass('active_page');
                    //hide all the elements inside content div
                    $('#content<?= $component_order; ?>').children('.boxView').css('display', 'none');
                    //and show the first n (show_per_page) elements


                    //$('#content<?= $component_order; ?>').children('.boxView').slice(0, show_per_page).css('display', 'block');

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
                    $('#content<?= $component_order; ?>').children('.boxView').css('display', 'none') .slice(start_from, end_on).css('display', 'block');
                    $('#content<?= $component_order; ?>').children('.current_page').val(0);
                    $('#content<?= $component_order; ?>').children('.show_per_page').val(show_per_page);
                    ///next function goes here
                    $('#content<?= $component_order; ?>').on("click", ".next_link", function (event) {
                        event.preventDefault();
                        new_page = parseInt($(this).parents("#content<?= $component_order; ?>").find('.current_page').val()) + 1;
                        //if there is an item after the current active link run the function
                        if ($(this).parents("#content<?= $component_order; ?>").find('.active_page').next('.page_link').length == true) {
                            go_to_page<?= $component_order; ?>(new_page);
                        }
                    });
                    ////previous function goes here
                    $('#content<?= $component_order; ?>').on("click", ".previous_link", function (event) {
                        event.preventDefault();
                        new_page = parseInt($(this).parents("#content<?= $component_order; ?>").find('.current_page').val()) - 1;
                        //if there is an item before the current active link run the function
                        if ($(this).parents("#content<?= $component_order; ?>").find('.active_page').prev('.page_link').length == true) {
                            go_to_page<?= $component_order; ?>(new_page);
                        }
                    });
                }

                setpaginations(true,0);

                go_to_page<?= $component_order; ?> = function(page_num) {
                    if(page_num == <?= $no_of_pages; ?>)
                        setpaginations(false, page_num);
                    else
                        setpaginations(true, page_num);
                    //get the number of items shown per page
                    var show_per_page = parseInt($('#content<?= $component_order; ?>').children('.show_per_page').val());
                    //alert(page_num+1);

                    /*get the page link that has longdesc attribute of the current page and add active_page class to it
                     and remove that class from previously active page link*/
                    $('#content<?= $component_order; ?>').find('.page_link[longdesc=' + page_num + ']').addClass('active_page').siblings('.active_page').removeClass('active_page');
                    //update the current page input field
                    $('#content<?= $component_order; ?>').find('.current_page').val(page_num);
                };

            });

        </script>

    <?php
    }
}
//////boxView functions end here

// Not in use;
function callBxSlider($component_order,$list_pagination){
	$slideWidth = "200";
	if(isset($list_pagination[1]) && !empty($list_pagination[1])){
		$slideWidth = $list_pagination[1];
	}
	?>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			$('#content<?php echo $component_order; ?>').bxSlider({
				pager : false,
				minSlides: 1,
				maxSlides: 4,
				slideWidth: <?php echo $slideWidth; ?>,
				moveSlides: 3,
				//slideMargin: 20,
				//adaptiveHeight: true,
				//shrinkItems: true,
				/* onSliderLoad: function () {
					$('#content<?php echo $component_order; ?>').css("visibility", "visible");
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


/**
 * function to use in all over the project to set the privalleges either have to show or not
 */
function isAllowedToShowByPrivilegeLevel($row){
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


/**
 * to check either we have to show data with html tags in lists or we have to strip those tags,
 * by default it strip tags but if there is a keyworld available in list_extra_options
 *which is "showtags" then we do not strip them
 */
function isStripHtmlTags($value){
  $value = strtoupper(trim($value));
  $position = strpos($value,"SHOWTAGS");
  if($position===false){
    return true;
  }
  $all_options = explode(';',$value);
  if(in_array("SHOWTAGS", $all_options)){
    return false;
  }
  return true;
}

function isFileExistFilterFullFillTheRule($row,$isExistFilter,$isExistField){
	if($isExistField == null || $isExistFilter == null){
		return true;
	}
	if(!isset($row[$isExistField])){
		return true;
	}

	$value = trim($row[$isExistField]);
	if($isExistFilter=='exist'){
		if(empty($value)){
		  return false;
		}

		if(file_exists(USER_UPLOADS.$value)){
		  return true;
		}else{
		  return false;
		}
	}
	else if($isExistFilter=='not_exist'){
		if(file_exists(USER_UPLOADS.$value)){
		  return false;
		}else{
		  return true;
		}
	}
}

function getDefaultListViewExtraOptions($con,$displaypage){
  $sql1 = $con->query("SELECT * FROM data_dictionary where data_dictionary.page_name='$displaypage' and data_dictionary.table_type='default'");
  $defaultOptions = $sql1->fetch_assoc();
  return $defaultOptions;
}


// function renderTheMianStructure($con,$page_name,$page_layout_style,$posittion=''){
//
//   $left_sidebar;
//   $right_sidebar;
//   $both_sidebar;
//   $left_sidebar_width;
//   $right_sidebar_width;
//   // setLeftRightSideBars($con,$page_name,$posittion,$left_sidebar,$right_sidebar,$both_sidebar,$left_sidebar_width,$right_sidebar_width)
//   //
// 	// sidebar($left_sidebar, $both_sidebar, $page_name, $left_sidebar_width);
//
// }
//
// function setLeftRightSideBars($con,$page_name,$posittion,&$left_sidebar,&$right_sidebar,&$both_sidebar,&$left_sidebar_width,&$right_sidebar_width){
//   if(strtoupper($posittion) =='TOP'){
//     $positionCheck = ' position="top"';
//   }else{
//     $positionCheck = ' position != "top"';
//   }
//   $rs = $con->query("SELECT component_order FROM data_dictionary where page_name='$page_name' AND $positionCheck ");
//   $right_sidebar = $left_sidebar = '';
//   $left_sidebar_width = $right_sidebar_width = 0;
//   while ($row = $rs->fetch_assoc()) {
//     $r1 = explode('w', trim($row['component_order']));
//     if (!empty($r1[1])) {
//       if ($r1[0] == 'R1')
//       $right_sidebar_width = $r1[1];
//       else
//       $left_sidebar_width = $r1[1];
//     }
//     if ($r1[0] == 'R1') {
//       $right_sidebar = 'right';
//     }
//     if ($r1[0] == 'L1') {
//       $left_sidebar = 'left';
//     }
//   }
//   /* Nav Body-Left or Body-right Code Start*/
//   $navBodyLeftQuery = $con->query("SELECT * FROM navigation where (page_name='$page_name' OR page_name='ALL' ) AND (menu_location='body-left') AND nav_id > 0 AND loginRequired='1' AND (item_number LIKE '%.0' OR item_number REGEXP '^[0-9]$') ORDER BY item_number ASC");
//   if($navBodyLeftQuery->num_rows){
//     if($left_sidebar ==''){
//       $left_sidebar = 'left';
//     }
//   }
//   $navBodyRightQuery = $con->query("SELECT * FROM navigation where (page_name='$page_name' OR page_name='ALL' ) AND (menu_location='body-right') AND nav_id > 0 AND loginRequired='1' AND (item_number LIKE '%.0' OR item_number REGEXP '^[0-9]$') ORDER BY item_number ASC");
//   if($navBodyRightQuery->num_rows){
//     if($right_sidebar ==''){
//       $right_sidebar = 'right';
//     }
//   }
//   /* Nav Body-Left or Body-right Code End*/
//   /* Tab TTl1 or Tl2 Start */
//   $tabLeftExist = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order LIKE 'S-L%' AND $positionCheck ");
//   if($tabLeftExist->num_rows){
//     if($left_sidebar ==''){
//       $left_sidebar = 'left';
//     }
//   }
//   $tabRightExist = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order LIKE 'S-R%' AND $positionCheck ");
//   if($tabRightExist->num_rows){
//     if($right_sidebar ==''){
//       $right_sidebar = 'right';
//     }
//   }
//   /* Tab TTl1 or Tl2 End */
//   if ($left_sidebar == 'left' && $right_sidebar == 'right') {
//     $both_sidebar = 'both';
//   }
//
//   /*
//    * Check If Center_Body content exist
//    * If not exist then check the width of aone and asign the other
//    * or if width not exist then divide 50% each
//    */
//   $Center_Body_Content_Exist = true;
//   $checkCenter_BodyContentQuery = $con->query("SELECT component_order FROM data_dictionary where page_name='$page_name'  and component_order REGEXP '^[0-9]+$' AND component_order >'0' AND $positionCheck");
//   if($checkCenter_BodyContentQuery->num_rows == 0 ){
//     $Center_Body_Content_Exist = false;
//     if (!empty($right_sidebar_width) && !empty($left_sidebar_width)) {
//       // do nothing
//     } else if (!empty($right_sidebar_width) && empty($left_sidebar_width)) {
//       $left_sidebar_width = 100 - $right_sidebar_width;
//     } else if (empty($right_sidebar_width) && !empty($left_sidebar_width)) {
//       $right_sidebar_width = 100 - $left_sidebar_width;
//     } else {
//       if ($both_sidebar == 'both') {
//         $left_sidebar_width = $right_sidebar_width = 50;
//       } else if ($both_sidebar != 'both' && ( $right_sidebar == 'right' || $left_sidebar == 'left' )) {
//         $left_sidebar_width = $right_sidebar_width = 50;
//       } else {
//         $left_sidebar_width = $right_sidebar_width = 0;
//       }
//     }
//   }
// }

function showListFilterSelection($row,$filters_srray,$selected_filter_index){
  $select_menu_id = $row['dict_id'].'filter_select_box';
  $this_DD_id = $row['dict_id'];
  echo "<select id='$select_menu_id' data-dd='$this_DD_id' onChange=listFilterChange(this)>";
  foreach ($filters_srray as $key => $value) {
    $label = $value['label'];
    if($key==$selected_filter_index){
      echo "<option value='$key' selected>$label</option>";
    }else{
      echo "<option value='$key' >$label</option>";
    }
  }
  echo "</select>";
}

function showListViewSelection($row,$filters_srray,$selected_filter_index){
  $this_DD_id = $row['dict_id'];
  foreach ($filters_srray as $key => $value) {
    $label = $value['label'];
    $checked = '';
    if($key ==$selected_filter_index){
      $checked = 'checked';
    }
    echo "<label style='margin-left:15px' class='radio-inline'>
    <input onchange='listViewChange(this)' type='radio'".$checked." name='$this_DD_id' value='$key'>$label
    </label>";
  }
}

function listColumnWidth($tbRow,$minLimit = 100){
  if(!empty(trim($tbRow['format_length']))){
  		$colWidth = explode(',',trim($tbRow['format_length']));
  		$colWidth = $colWidth[0];
  		if(empty($colWidth) ||  $colWidth<100){
  			$colStyle = 40;
  		}
  }else{
  	$colWidth = parseFieldType($tbRow);
  }
  if($colWidth < $minLimit){
    $colWidth = $minLimit;
  }

  return $colWidth;
}

function calculateWidthsInPercentage($array){
  $count  = count($array)+1;
  $total = 0;
  foreach ($array as $key => $value) {
    $total = $total+$value;
  }
  foreach ($array as $key => $value) {
    $array[$key] ='"'. ($value*97)/$total.'%"';
  }
  return $array;
}

function truncateLongDataAsPerAvailableWidth($data,$width,$roundPxls=true){
  $data= trim($data);
  if($roundPxls){
    $width = $width/6.7;
  }
  return substr($data, 0, $width);
}

function format_field_value_length( $tbRow, $value ) {

	$fieldValue = $value;
	if ( !isset($tbRow['format_type']) || $tbRow['format_type'] == "" || empty($row['format_type'])) {
		$tbRow['format_type'] = 'text';
	}
	$format_type = $tbRow['format_type'];
	if( ! isset($tbRow['format_length']) || $tbRow['format_length'] == "" || ! $tbRow['format_length'] || ! is_numeric($tbRow['format_length']) ) {
		$create_constant_name =  "defaultFieldLen".ucfirst($format_type)."Max";
		$tbRow['format_length'] = constant($create_constant_name);
	}

	$min_length1 = "defaultFieldLen".ucfirst($format_type)."Min";
	$min_length = constant($min_length1);

	if( ! $min_length ) {
		$min_length = 5;
	}
	if( ! $tbRow['format_length'] ) {
		$tbRow['format_length'] = 25;
	}

	if( isset($tbRow['format_length']) && $tbRow['format_length'] != "" && $tbRow['format_length'] && is_numeric($tbRow['format_length']) ) {
		$old_field_value = $fieldValue;
		//$row['format_length'] = 5;
		$fieldValue = substr($fieldValue, 0, $tbRow['format_length']);
		if( $fieldValue != "" && strlen($old_field_value) > $tbRow['format_length'] ) {
			$fieldValue .= " ...";
		}
		else if ( $min_length && strlen($old_field_value) < $tbRow['format_length'] ) {
			$fieldValue = str_pad($fieldValue, $min_length, " ");
		}
	}
	return array( "min_length" => $min_length, "max_length" => $tbRow['format_length'], "fieldValue" => $fieldValue );

}





