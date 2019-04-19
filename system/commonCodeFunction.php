<?php
/**
 * These function are common for tab and one page layout
 *(tab_num = S-C , S-R, S-L || tab_num = 1, 2,3)
 *
 */
function generateBreadcrumbsAndBackPage($row1,$primary_key,$onepage){

	if(hideBreadCrumb($row1['list_extra_options'])){
		return;
	}

	if ($_GET['checkFlag'] == 'true' && $row1['dd_editable'] == 11) {
		if ($_GET['table_type'] == 'child'){
			$link_to_return = $_SESSION['child_return_url'];
		} else {
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
	if ($_GET['table_type'] == 'child'){
		$link_to_return = $_SESSION['child_return_url'];
	} else {
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


function generateCustomFunctionArray($customFunctionArray){
	if (!empty($customFunctionArray) ) {
		echo "<br/>";
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


				<!-- addimport prompt Modal -->
				<div class="modal fade" id="addimportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel">Import</h4>
							</div>
							<div class="modal-body">
								<?= $importPromptMessage; ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<?= $buttonHtmlFileImport; ?>
								<?= $buttonHtmlManualImport; ?>
								<!--<button type="button" class="btn btn-primary">Save changes</button>-->
							</div>
						</div>
					</div>
				</div>

				<!-- addimport Status Success/Error Modal -->
				<div class="modal fade" id="addimportStatusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel">Import Status</h4>
							</div>
							<div class="modal-body">
								<?php

								$statusText = "Completed... ";

								if(!empty($_SESSION['SuccessAddImport']) )
								{
									$statusText .= count($_SESSION['SuccessAddImport']) . " records processed. ";
								}
								if(!empty($_SESSION['errorsAddImport']) )
								{
									$statusText .= count($_SESSION['errorsAddImport']) . " records did not process due to errors.";
								}

								echo $statusText;

								?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
				<script>
				jQuery(document).ready(function($){

					//##SUCCESS/ERROR MODAL DISPLAYED ON REDIRECT USING SESSION##
					//###NEED TO UNSET THE addimport SESSION for STATUS SINCE THIS FILE IS GETTING CALLED TWICE FOR SOME REASON, SO USED AJAX TO UNSET THOSE####
					$.ajax({
						method: "POST",
						url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
						data: {action: 'addimport_session_unset'}
					})
					<?php
					if(!empty($_SESSION['errorsAddImport']) || !empty($_SESSION['SuccessAddImport']) )
					{
						echo "$('#addimportStatusModal').modal('show');";
					}
					#unset($_SESSION['SuccessAddImport'], $_SESSION['errorsAddImport']);
					?>
				});
				</script>
				<?php
			}
			else
			{
				echo "<button type='button' class='btn actionCustomfunction btn-primary {$customFunction['style']}' data-function_name='{$customFunction['function']}'
					data-function_params='{$customFunction['params']}' name='custom_function_$keyCustomFunction' >" . $customFunction['label'] . "</button>";
				?>
				<script>
				jQuery(document).ready(function($){
					$('#list-form').on('click', '.actionCustomfunction', function(event){

						if (confirm( $(this).text() ) == true) {

							$.ajax({
								method: "POST",
								url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
								data: {function: $(this).data('function_name'), params: $(this).data('function_params'), action: 'custom_function'}
							})
							.done(function (msg) {
								alert('Success');
								//location.reload();
							});

						} else {
							event.stopImmediatePropagation();
						}
					});
				});
				</script>
		<?php
			}

		}

		?>
		<!--####addimport FORM FIELDS#######GET THE I | P for import from file or PROMPT#######-->
		<!--File modal addimport-->
		<div class="modal fade" id="addimportFileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Import from CSV File</h4>
					</div>

					<form action='<?= $_SESSION[add_url_list]; ?>&action=add&actionType=addimport&search_id=<?= $_GET['search_id']; ?>&edit=<?= $_GET['edit']; ?>' method='post' id='user_profile_form' enctype='multipart/form-data' class=''>

						<div class="modal-body">

							<div class='new_form col-sm-12'><label><?= ucwords($_SESSION['addImportParameters']['1']); ?></label>
								<input type='file' name='addImportFile' required title='' size='' class='form-control' style='height: auto;' />
							</div>

						</div>
						<div class="modal-footer" style="border-top: none;">
							<div class='new_form col-sm-12 text-right'>
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>

					</form>

				</div>
			</div>
		</div>

		<!--Manual import modal addimport-->
		<div class="modal fade" id="addimportManualModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Import</h4>
					</div>

					<form action='<?= $_SESSION[add_url_list]; ?>&action=add&actionType=addimport&search_id=<?= $_GET['search_id']; ?>&edit=<?= $_GET['edit']; ?>' method='post' id='user_profile_form' enctype='multipart/form-data' class=''>

						<div class="modal-body">

							<?php
							#if(strtolower($_GET['addImportType']) == 'manual')
							{
								$customFunctionParameters = $_SESSION['addImportParameters'];

								array_splice($customFunctionParameters, 0, 3);

								$customFunctionParameters = array_map('ucwords', $customFunctionParameters);
								###$_SESSION['addImportParameters']['1'] == description###
							}
							?>

							<div class='new_form col-sm-12'><label><?= ucwords($_SESSION['addImportParameters']['1']); ?></label>
								<br>Fields : <?= implode(', ', $customFunctionParameters); ?> <br>
								<textarea name="addImportText" class="form-control" cols="100" required ></textarea>
							</div>

						</div>
						<div class="modal-footer" style="border-top: none;">
							<div class='new_form col-sm-12 text-right'>
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div>
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
			$addUrl = "?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
		} else {
			/// add button url
			$addUrl = "?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
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
				$addUrl = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
			} else {
				/// add button url
				$addUrl = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
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
?>
