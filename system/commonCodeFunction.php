<?php
	/*
	
TBD - SOON
ADD dict_id  to URL generation ... 
and re-assess use of table_type for the URLs
	
	
function getRecordAddUrl($list_select_arr,$component_type, $table_type){
function getRecordAddUrlInner($row){


function getDataFromExtraOptionForField($extraOptions,$field){
function checkListItemsLimit($extraOptions){
function parseExtraOptions($string){
function checkIfEmptyList($list,$row){
function emptyListConfigs($value){	

function generateBreadcrumbsAndBackPage($row1,$primary_key,$onepage){
function generateBreadcrumbsAndBackPageForAdd($row1,$onepage){
function generateCustomFunctionArray($customFunctionArray,$showlineBreak=false ){
function hideBreadCrumb($extraOptions){
function isHaveToShowImage($extraOptions){
	
*/
	
	
	
	
	
	
function getRecordAddUrl($list_select_arr,$component_type, $table_type){
	$addUrl = "";

	$con = connect();
	$nav = $con->query("SELECT * FROM navigation where target_page_name='$_GET[page_name]'");
	$navList = $nav->fetch_assoc();

	if (isset($list_select_arr[0]) && !empty($list_select_arr[0])) {
		if (count($list_select_arr[0]) == 2) {
			/// add button url

			// DD OVERHAUL - CJ COMMENTS this is another line that can give is trouble ---  adding component_type to the end of these two lines  ALSO NEED TO ADD dict_id !!

			$addUrl = "?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias" . $list_select_arr[0][0] . "&table_type=" . $table_type;  // &"component_type=" . $component_type;



//			echo "<br><br> addUrl=";echo $addUrl; echo "<br><br><br><br>";exit();

		} else {
			/// add button url
			$addUrl = "?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias" . $list_select_arr[0][0] . "&table_type=" . $table_type;   // &"component_type=" . $component_type;


//			echo "<br><br> addUrl=";echo $addUrl; echo "<br><br><br><br>";exit();

		}
	}


	return trim($addUrl);

}

function getRecordAddUrlInner($row){
	$addUrl = "";

	$con = connect();
	$ddQuery = $con->query("SELECT data_dictionary.* FROM field_dictionary INNER JOIN data_dictionary ON (data_dictionary.`table_alias` = field_dictionary.`table_alias`) where data_dictionary.table_alias='".$row['table_alias']."' and data_dictionary.page_name='".$row['page_name']."' and data_dictionary.component_order='".$row['component_order']."' order by field_dictionary.field_order LIMIT 1");
	
	if($ddQuery->num_rows){
		$ddRecord = $ddQuery->fetch_assoc();
		$list_select = trim($ddRecord['list_select']);
		$table_type = trim($row['table_type']);
		$component_type = trim($row['component_type']);
		$list_select_arr = getListSelectParams($list_select);
		$nav = $con->query("SELECT * FROM navigation where target_page_name='$_GET[page_name]'");
		$navList = $nav->fetch_assoc();
		if (isset($list_select_arr[0]) && !empty($list_select_arr[0])) {
			if (count($list_select_arr[0]) == 2) {
				/// add button url

			// DD OVERHAUL - CJ COMMENTS this is another line that can give is trouble ---  adding component_type to the end of these two lines - ADD dict_id  !!


				$addUrl = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias=" . $list_select_arr[0][0] . "&table_type=" . $table_type&"component_type=" . $component_type;
				
				
			} else {
				/// add button url
				$addUrl = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias=" . $list_select_arr[0][0] . "&table_type=" . $table_type&"component_type=" . $component_type;


			}
		}
	}
	return $addUrl;

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



function generateBreadcrumbsAndBackPage($row1,$primary_key,$onepage){
	if(hideBreadCrumb($row1['list_extra_options']) ){
		return;
	}
	if ($_GET['checkFlag'] == 'true' && $row1['real_dd_editable'] == 11) {
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
			$breadcrumb_display = " Back To2 <span>Home</span> Page";
		} else {
			$backText = str_replace('*', '', $_SESSION['list_component_name']);
			$breadcrumb_display = " Back To3 <span>$backText</span> Lists";
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
		if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true' && $_GET['ComponentOrder'] == $row1['component_order'] && $_GET['table_alias'] == $row1['table_alias']) {
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
				$breadcrumb_display = " Back To4 <span>Home</span> Page";
			} else {
				$backText = str_replace('*', '', $_SESSION['list_component_name']);
				$breadcrumb_display = " Back To5 <span>$backText</span> Lists";
			}

			// DD OVERHAUL - CJ COMMENTS ...  component_type could be needed below .. but I have not added it yet

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
					data-toggle='modal' data-target='#addimportFileModal'>" . $customFunction['label'] . "</button>";

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
function hideBreadCrumb($extraOptions){
	$temp = parseExtraOptions($extraOptions);
	$temp = array_values($temp);
	$result = array_search('disable_breadcrumb',$temp);
	if($result === false){
		return false;
	}
	return true;
}



function isHaveToShowImage($extraOptions){
	return getDataFromExtraOptionForField($extraOptions,'map_marker_field');
}



?>
