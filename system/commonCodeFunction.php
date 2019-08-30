<?php
/**
 * These function are common for tab and one page layout
 *(tab_num = S-C , S-R, S-L || tab_num = 1, 2,3)
 *
 */
function generateBreadcrumbsAndBackPage($row1,$primary_key,$onepage){
	if(hideBreadCrumb($row1['list_extra_options']) ){
		return;
	}
	if ($_GET['checkFlag'] == 'true' && $row1['dd_editable'] == 11) {
		if ($_GET['table_type'] == 'child'){
			$link_to_return = $_SESSION['child_return_url'];
		} else {
			$link_to_return = $_SESSION['return_url'];
		}

		if(empty($link_to_return)){
			$link_to_return = $_SESSION['return_url'];
		}
		$home_test = explode("display", $link_to_return);
		if ($home_test[1] == '=home'){
			$breadcrumb_display = " Back To <span>Home</span> Page";
		} else {
			$backText = str_replace('*', '', $_SESSION['list_tab_name']);
			$breadcrumb_display = " Back To <span>$backText</span> Lists";
		}

		if($onepage){
			$url = "$_SESSION[return_url2]&button=cancel&fnc=onepage";
		} else {
			$url = "$link_to_return&button=cancel&table_type=$row1[table_type]" .($_GET['fnc']=='onepage' ? '&fnc=onepage' : '' );
		}
		echo "<div class='breadcrumb'>
				<a href='$url' class='back-to-list'>$breadcrumb_display</a>
					".editPagePagination($row1['list_extra_options'], $primary_key)."
			</div>";
	}
}



function generateBreadcrumbsAndBackPageForAdd($row1,$onepage){
		if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true' && $_GET['tabNum'] == $row1['tab_num'] && $_GET['tab'] == $row1['table_alias']) {
			if ($_GET['table_type'] == 'child'){
				$link_to_return = $_SESSION['child_return_url'];
			} else {
				$link_to_return = $_SESSION['return_url'];
			}

			if(empty($link_to_return)){
				$link_to_return = $_SESSION['return_url'];
			}

			$home_test = explode("display", $link_to_return);
			if ($home_test[1] == '=home'){
				$breadcrumb_display = " Back To <span>Home</span> Page";
			} else {
				$backText = str_replace('*', '', $_SESSION['list_tab_name']);
				$breadcrumb_display = " Back To <span>$backText</span> Lists";
			}
			if($onepage){
				$url = "$_SESSION[return_url2]&button=cancel&fnc=onepage";
			} else {
				$url = "$link_to_return&button=cancel&table_type=$row1[table_type]" .($_GET['fnc']=='onepage' ? '&fnc=onepage' : '' );
			}
			echo "<br>
				<div class='breadcrumb'>
					<a href='$url' class='back-to-list'> $breadcrumb_display</a>
				</div>";
		}
}



function generateCustomFunctionArray($customFunctionArray,$showlineBreak=false ){
	if (!empty($customFunctionArray) ) {
		if($showlineBreak){
			echo "<br/>";
		}
		foreach($customFunctionArray as $keyCustomFunction => $customFunction)
		{
			##BUTTON FOR 'addimport' through CUSTOM FUNCTIONS##
			if(strtolower($customFunction['function']) == 'addimport')
			{
				##FOR TESTING AND DEBUG,SHOULD BE REMOVED###
				#$customFunction['params'] = 'add import` import multiple data` ` user_id` pname1` description` product_name';##THIRD param iS CI OR CP | TI OR TP##

				###GET THIRD PARAM FOR I|P(IMPORT FROM FILE OR PROMPT FOR "Import from CSV File, or Manual Import?"#######STARTS####
				$customFunctionParams = $customFunction['params'];
				$customFunctionParams = explode("`", $customFunctionParams);
				$customFunctionParams = array_map('trim', $customFunctionParams);

				$customFunctionThirdParameter = $customFunctionParams['2'];

				###SET SESSION var for holding addimport function parameters######
				$_SESSION['addImportParameters'] = $customFunctionParams;

				$addImportLink = $_SESSION['add_url_list'] . '&addImport=true';

				$buttonHtmlFileImport = '<a class="btn btn-primary importPromptAction" href="' . $addImportLink . '&addImportType=file' . '" data-prompt_action="importFile">Import from CSV File</a>';
				$buttonHtmlManualImport = '<a class="btn btn-primary importPromptAction" href="' . $addImportLink . '&addImportType=manual' . '" data-prompt_action="importManual" >Manual Import</a>';
				#<a data-dismiss="modal" data-toggle="modal" href="#lost">Click</a>

				$buttonHtmlFileImport = '<a data-dismiss="modal" data-toggle="modal" class="btn btn-primary importPromptAction" href="#addimportFileModal" data-prompt_action="importFile">Import from CSV File</a>';
				$buttonHtmlManualImport = '<a data-dismiss="modal" data-toggle="modal" class="btn btn-primary importPromptAction" href="#addimportManualModal" data-prompt_action="importManual" >Manual Import</a>';

				$importPromptMessage = 'Import from CSV File, or Manual Import?';

				###DEFAULT IMPORT TYPE = P i.e. prompt after every import###
				$importButtonActionType = 'P';

				if(stripos($customFunctionThirdParameter, 'I') !== false)
				{
					$importButtonActionType = 'I';
					$importPromptMessage = 'Import from CSV File?';
					$buttonHtmlManualImport = '';
				}

//                            if(stripos($customFunctionThirdParameter, 'P') !== false )
//                            {
//                                $importButtonActionType = 'P';
//                            }
//                            else if(stripos($customFunctionThirdParameter, 'I') !== false)
//                            {
//                                $importButtonActionType = 'I';
//                                $importPromptMessage = 'Import from CSV File?';
//                                $buttonHtmlManualImport = '';
//                            }
//                            else
//                            {
//                                $importButtonActionType = 'P';
//                            }
				###GET THIRD PARAM FOR I|P(IMPORT FROM FILE OR PROMPT FOR "Import from CSV File, or Manual Import?"#######ENDS######
				#<!-- Button trigger modal -->
				echo "<button type='button' class='btn actionImportButton btn-primary {$customFunction['style']}' data-function_name='{$customFunction['function']}'
					data-function_params='{$customFunction['params']}' name='add_import' data-import_type='$importButtonActionType'
					data-toggle='modal' data-target='#addimportModal'>" . $customFunction['label'] . "</button>";

				?>



				<?php
			}
			else
			{
				echo "<button type='button' class='btn actionCustomfunction btn-primary {$customFunction['style']}' data-function_name='{$customFunction['function']}'
					data-function_params='{$customFunction['params']}' name='custom_function_$keyCustomFunction' >" . $customFunction['label'] . "</button>";
				?>

		<?php
			}

		}

		?>

		<?php
	}
}
function getRecordAddUrl($list_select_arr,$table_type){
	$addUrl = "";

	$con = connect();
	$nav = $con->query("SELECT * FROM navigation where target_display_page='$_GET[display]'");
	$navList = $nav->fetch_assoc();
	if (isset($list_select_arr[0]) && !empty($list_select_arr[0])) {
		if (count($list_select_arr[0]) == 2) {
			/// add button url
			$addUrl = "?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
		} else {
			/// add button url
			$addUrl = "?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
		}
	}
	return trim($addUrl);

}

function getRecordAddUrlInner($row){
	$addUrl = "";

	$con = connect();
	$ddQuery = $con->query("SELECT data_dictionary.* FROM field_dictionary INNER JOIN data_dictionary ON (data_dictionary.`table_alias` = field_dictionary.`table_alias`) where data_dictionary.table_alias='".$row['table_alias']."' and data_dictionary.display_page='".$row['display_page']."' and data_dictionary.tab_num='".$row['tab_num']."' order by field_dictionary.display_field_order LIMIT 1");
	if($ddQuery->num_rows){
		$ddRecord = $ddQuery->fetch_assoc();
		$list_select = trim($ddRecord['list_select']);
		$table_type = trim($row['table_type']);
		$list_select_arr = getListSelectParams($list_select);
		$nav = $con->query("SELECT * FROM navigation where target_display_page='$_GET[display]'");
		$navList = $nav->fetch_assoc();
		if (isset($list_select_arr[0]) && !empty($list_select_arr[0])) {
			if (count($list_select_arr[0]) == 2) {
				/// add button url
				$addUrl = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
			} else {
				/// add button url
				$addUrl = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
			}
		}
	}
	return $addUrl;

}

function hideBreadCrumb($extraOptions){
	$temp = parseExtraOptions($extraOptions);
	$temp = array_values($temp);
	$result = array_search('disable_breadcrumb',$temp);
	if($result === false){
		return false;
	}
	return true;
}

function getDataFromExtraOptionForField($extraOptions,$field){
	$isExist = strpos($extraOptions,$field);
	if($isExist !== false){
		$parsed_array = parseExtraOptions($extraOptions);
		foreach ($parsed_array as $key => $value) {
			$temp  = explode(':',$value);
			if(count($temp)> 1 && strtoupper(trim($temp[0]))===strtoupper($field)){
				if(!empty(trim($temp[1]))){
					return trim($temp[1]);
				}else{
					return false;
				}
			}
		}
	}
	return false;
}

function isHaveToShowImage($extraOptions){
	return getDataFromExtraOptionForField($extraOptions,'map_marker_field');
}

function checkListItemsLimit($extraOptions){
	return getDataFromExtraOptionForField($extraOptions,'maxrecords');
}

function parseExtraOptions($string){
	$temp  = explode(';',$string);
	foreach ($temp as $key => $value) {
		if(empty(trim($value))){
			unset($temp[$key]);
		}else{
			$temp[$key] = trim($value);
		}
	}
	return $temp;
}

function checkIfEmptyList($list,$row){
	if ($list->num_rows == 0) {
		$emptyListConfigs = emptyListConfigs($row['list_extra_options']);
		$type  = strtoupper(trim($emptyListConfigs['type']));
		$value = trim($emptyListConfigs['value']);

		if($type ==='TEXT'){
			echo "<h3 style='text-align:center'>$value</h3>";
		}
		if($type ==='IMAGE'){
			echo "<div style='text-align:center'><img src='" . USER_UPLOADS . "" . $value . "' border='0' width='300' class='img-thumbnail img-responsive'/></div>";
		}
		return true;
	}
	return false;
}

function emptyListConfigs($value){
	$result = [];
	$result['type'] = 'text';
	$result['value'] = EMPTY_LISTS_MESSAGE;
	// $value = strtoupper(trim($value));
	$position = strpos($value,"emptylist");
	if($position!==false){
		$all_options = explode(';',$value);
		foreach ($all_options as $key => $sub_option) {
			$position = strpos($sub_option,"emptylist");
			if($position!==false){
				$configs = explode(':',trim($sub_option));
				if($configs[0]==='emptylist'){
					$settings = explode(',',trim($configs[1]));
					if(count($settings)==2){
						$result['type'] = $settings[0];
						$result['value'] = $settings[1];
					}
				}
			}
		}
	}
	return $result;
}
?>
