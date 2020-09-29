<?php
/*
function form_content_display_loop($row) {
function addCustomFunctionModal($customFunctionArray){
*/


//////////////
//////////////***** DISPALY TABS AS H1 TAG ******

function form_content_display_loop($row) {
	
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $editable = 'true';

    $con = connect();

    ///for taking inline anchoring
    $tab_anchor = trim($row['component_name']);
    $tab_anchor = str_replace('*', '', $tab_anchor);

    if ($row['table_type'] == 'parent') {
        $_SESSION['parent_list_tabname'] = $tab_anchor;

        $_SESSION['parent_url'] = $actual_link;
    }

    $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.table_alias = field_dictionary.table_alias where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and component_order='$row[component_order]' order by field_dictionary.field_order";
    $rs = $con->query($qry);
    $rs2 = $con->query($qry);


    $row1 = $rs->fetch_assoc();
	$tableType = trim(strtolower($row1['table_type']));
	$componentType = trim(strtolower($row1['component_type']));

	$addUrlInner = getRecordAddUrlInner($row);

    /*     * *****************
     * *****************************************
     * *****************************************************************************
     * **************
     * Checking and displaying contents of the page according to the Privilege
     * ***********************
     * ********************************************************
     * *******************************************************************************
     * ****************************************************************************
     *
     */

    /*if ($_SESSION['user_privilege'] < $row1['dd_privilege_level'] && $_SESSION['user_privilege'] <= 9) {

        $userPrivilege = true;
    } else {

        $userPrivilege = false;
    }*/
    $css_style = $row1['dd_css_code'];

    if (isAllowedToShowByPrivilegeLevel($row)) {
		///////// for displaying image container
		$image_display = 'true';
		//print_r($row1);die;
		//////ASsigning custom class to the form


		/**
		 * Sets page_editable flag and dd_css_class based on page_editable and table_type value
		 */
		$page_editable = true;
		$dd_css_class = ' simple_edit_page ';
		if ($row1['page_editable'] == 0 && trim($row1['table_type']) != 'transaction') {
			$page_editable = false;
			$dd_css_class = ' page_not_editable ';
		} elseif ($row1['page_editable'] == 2) {
			$page_editable = false;
			$dd_css_class = ' profile_page ';
		}
		$dd_css_class .= ($row1['dd_css_class'] ?: '');

		if ($row1['table_name'] == $_SESSION['select_table']['table_name'])
			$_SESSION['search_id'] = $_SESSION['uid'];
		else if (trim($row1['table_type']) == 'child') {
			$_SESSION['search_id'] = $_SESSION['parent_value'];
		}/* else
			$_SESSION['search_id'] = '76'; */ /// for displaying one user


		if (isset($_GET['search_id']) && !empty($_GET['search_id'])) {

			//  $_SESSION['search_id'] = $_GET['search_id'];
		}

		if (isset($_GET['id']) && $_GET['id'] != '') {
			$_SESSION['search_id'] = $_GET['id'];
			//$_SESSION['update_table']['keyfield'] = 'id';
		}



		$_SESSION['update_table']['table_name'] = $row1['table_name'];

		$primary_key = firstFieldName($row1['table_name']);

		$_SESSION['update_table']['keyfield'] = $primary_key;

		if (trim($row1['table_type']) == 'parent') {
			$_SESSION['update_table']['child_parent_key'] = (!empty($row1['keyfield']) ? $row1['keyfield'] : $_SESSION['update_table']['keyfield'] );
			$_SESSION['update_table']['child_parent_key_diff'] = (!empty($row1['keyfield']) ? 'true' : 'false');
		}

		/******** for update ****/
		// $row1['component_name'] = str_replace('*', '', $row1['component_name']);

		$_SESSION['list_component_name'] = $row1['component_name'];

		$form_open_for_edit = false;
		$dd_EditAbleHaveValue2  = false;
		$hide_update_cancel = false;

		$DD_EDITABLE = $row1['dd_editable'];
		$DD_EDITABLE_bit1 = $DD_EDITABLE[0];
		$row1['real_dd_editable'] = $row1['dd_editable'];
		if(is_null($DD_EDITABLE[1]) || empty($DD_EDITABLE[1])){
			$DD_EDITABLE[1] = '0';
		}
		$DD_EDITABLE_bit2 = $DD_EDITABLE[1];

		if($DD_EDITABLE_bit2=='0' || $DD_EDITABLE_bit2=='1'){
			$_SESSION[$row1['dict_id'].'current_dd_url_in_tab'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}
		$row1['dd_editable'] = $DD_EDITABLE_bit1;
		if($row1['dd_editable']=='2'){
			$row1['dd_editable'] = '11';
		}else if($row1['dd_editable']=='3'){
			$row1['dd_editable'] = '11';
			$hide_update_cancel = true;
		}

		if(isset($_SESSION['form_open_for_edit_DD']) && $_SESSION['form_open_for_edit_DD']==$row1['dict_id']){
			$form_open_for_edit = true;
			$row1['dd_editable']='11';
			unset($_SESSION['form_open_for_edit_DD']);
		}

		$show_with_edit_button = false;
		if(isset($_SESSION['show_with_edit_button']) && $_SESSION['show_with_edit_button_DD']==$row1['dict_id']){
			$show_with_edit_button = true;
			$row1['dd_editable']='1';
			unset($_SESSION['show_with_edit_button']);
			unset($_SESSION['show_with_edit_button_DD']);
		}

		if ($row1['dd_editable'] == '11') {

			$_SESSION['dict_id'] = $row1['dict_id'];

			$_SESSION['search_id2'] = !empty($_GET['search_id']) ? $_GET['search_id'] : $_SESSION['search_id'];

			$_SESSION['update_table2']['table_name'] = $_SESSION['update_table']['table_name'];

			$_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];

			$_SESSION['anchor_tag'] = "#" . $tab_anchor;

			if ($_GET['checkFlag'] == 'true') {
				//was giving error in child list propel so made changes///
				//$_SESSION['return_url2'] = $_SESSION['return_url'];
				$_SESSION['return_url2'] = BASE_URL . "system/main-loop.php?page_name=$_GET[page_name]&layout=$_GET[layout]&style=$_GET[layout]";
			} else {

				$_SESSION['return_url2'] = $actual_link;
			}
		}

		if (( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id']) ) || $_GET['edit'] == 'true' || !empty($_GET['addFlag']) ) {

			$operationsVarArray = array();
			$operation = '';

			##DD.edit_operation
			if ( ($row1['dd_editable'] == 11 || $row1['dd_editable'] == 1) && $row1['page_editable'] == 1) {
				$operation = 'edit_operations';
			} else if ($row1['dd_editable'] !== 11 || $row1['page_editable'] == 0) { ##DD.view_operation
				$operation = 'view_operations';
			}
			if (!empty(trim($row1[$operation]) ) ) {
				$operationsVarArray = getOperationsData($row1[$operation], $operation);
			}

			list($popupmenu,
				$popup_delete_array,
				$popup_copy_array,
				$popup_add_array,
				$popup_openChild_array,
				$customFunctionArray,
				$del_array,
				$copy_array,
				$add_array,
				$single_delete_array,
				$single_copy_array,
				$submit_array,
				$facebook_array,
				$google_array,
				$linkedin_array,
				$save_add_array
			)  = $operationsVarArray;

		}


		$tab_id = $row['page_name'].$row['dict_id'];
		$DD_style_list = trim($row['dd_css_class']);
		$DD_css_style = trim($row['dd_css_code']);
		echo "<div id='$tab_id' class='$DD_style_list' style='$DD_css_style'>";
		
		/* Show Table Type Header*/
		// ShowComponentTypeHeaderContent($row['page_name'],$row['component_order']);

		echo "<section class='section-sep'><a name='$tab_anchor'></a><h1 class='section-title'>$tab_anchor</h1><!-- h1-content class not used-->";

		/* Show Table Type SubHeader*/
		// ShowComponentTypeSubHeaderContent($row['page_name'],$row['component_order']);

		if(!empty($facebook_array)){
			$facebookButton = generateFacebookButton($facebook_array);
		}

		if(!empty($google_array)){
			$googleButton = generateGoogleButton($google_array);
		}

		if(!empty($linkedin_array)){
			$linkedinButton = generateLinkedinButton($linkedin_array);
		}


		/// setting for  Save/Update button
		if (!empty($submit_array) ) {
			$updateSaveButton = "<input type='submit'  value='" . $submit_array['value'] . "' class='btn btn-primary update-btn " . $submit_array['style'] . "' /> &nbsp;";
		} else if ($operation == 'edit_operations') {
			if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true' && ($_GET['ComponentOrder'] == $row1['component_order'] || $_GET['ComponentOrder'] == $row1['component_column']) && $_GET['table_alias'] == $row1['table_alias']) {
				$updateSaveButton = "<input type='submit' value='" . formSave . "' class='btn btn-primary update-btn' /> &nbsp;";
			} else {
				$updateSaveButton = "<input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' /> &nbsp;";
			}
		} else if($operation == 'view_operations') {
			#$updateSaveButton = "<input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' /> &nbsp;";
		}

		if($hide_update_cancel){
			$updateSaveButton = '';
		}

		/// setting for  save add button
		if (!empty($save_add_array) ) {
			if($addUrlInner){
				$_SESSION['save_add_url'] = $addUrlInner;
			}
			$saveAddButton = "<button type='submit' name='save_add_record' class='btn " . $save_add_array['style'] ."'>" . $save_add_array['label'] . "</button> &nbsp;";
		}

		/// setting for  delete button
		if (!empty($del_array) ) {
			$deleteButton = "<button type='submit' class='btn list-del " . $del_array['style'] . "' name='$row1[dict_id]' id='$_GET[search_id]' fnc='onepage' >" . $del_array['label'] . "</button> &nbsp;";
		}

		//// setting for  copy button
		if (!empty($copy_array) ) {
			$copyButton = "<button type='submit' class='btn list-copy " . $copy_array['style'] . "' name='$row1[dict_id]' id='$_GET[search_id]' fnc='onepage' >" . $copy_array['label'] . "</button> &nbsp;";
		}
		/// ADD BUTTON

		if (!empty($add_array) ) {
			$href = "window.location.href='$addUrlInner'";
		}

		/***
		 * ADDING BREADCRUMB FOR PARENT/NORMAL LISTS/PAGES
		 *
		 * Short solution for back to home page
		 */
		generateBreadcrumbsAndBackPageForAdd($row1,$onePage=true); // in codeCommonFunction.php


		##CUSTOM FUNCTION BUTTON##
		// generateCustomFunctionArray($customFunctionArray); // in codeCommonFunction.php


		if (!empty($_GET['table_alias']) && $_GET['table_alias'] == $row1['table_alias'] && !empty($_GET['search_id'])) {

			if ($_GET['table_type'] == 'parent') {
				$_SESSION['parent_value'] = $_GET['search_id'];
			}

			$urow = get_single_record($_SESSION['update_table']['table_name'], $_SESSION['update_table']['keyfield'], $_GET['search_id']);
		} else {

			$urow = get_single_record($_SESSION['update_table']['table_name'], $_SESSION['update_table']['keyfield'], $_SESSION['search_id']);
		}

		if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true'
			&& ($_GET['ComponentOrder'] == $row1['component_order'] || $_GET['ComponentOrder'] == $row1['component_column'])
			&& $_GET['table_alias'] == $row1['table_alias']) {

			if (empty($save_add_array) ) {
				unset($_SESSION['save_add_url']);
			}
			$dd_css_class = $row1['dd_css_class'];

			$_SESSION['dict_id'] = $row1['dict_id'];

			$_SESSION['search_id2'] = !empty($_GET['search_id']) ? $_GET['search_id'] : $_SESSION['search_id'];
			
			$_SESSION['update_table2']['table_name'] = $_SESSION['update_table']['table_name'];

			$_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];
			addCustomFunctionModal($customFunctionArray);

			if ($_GET['checkFlag'] == 'true') {
				###THIS IS USED FOR ADD FORM DISPLAY WHICH I WILL MODIFY FOR THE addimport UPLOAD FORM FIELDS################
				echo "<form action='$addUrlInner&action=add&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='shivgre-checkFlag-true $dd_css_class' style='$css_style'><br>";
			} else {
				$_SESSION['return_url2'] = $actual_link;
				echo "<form action='?action=add&ComponentOrder=$_GET[ComponentOrder]&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='shivgre-checkFlag-false $dd_css_class' style='$css_style'><br>";
			}

			if ($_GET['checkFlag'] == 'true') {
				if ($_GET['table_type'] == 'child'){
					$link_to_return = $_SESSION['child_return_url'];
				} else {
					$link_to_return = $_SESSION['return_url'];
				}
				if(empty($link_to_return)){
					$link_to_return = $_SESSION['return_url'];
				}

				$actual_link = $link_to_return;

				$_SESSION['return_url2'] = $_SESSION['return_url'];
			}

						// DD OVERHAUL - CJ COMMENTS this is another line that can give is trouble ---  adding component_type to this line below

			$actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]&component_type=$_GET[component_type]&fnc=onepage";

			//$actual_link = $_SESSION['return_url2'] . "&fnc=onepage";

			$cancelButton = "<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>&nbsp;";
			if(in_array(trim(strtolower($row1['component_type'])),['login','signup','forgotpassword','reset_password','change_password']) || $hide_update_cancel){
				$cancelButton = "";// empty
			}

			//echo "<form action='?action=add&checkFlag=true&ComponentOrder=$_GET[ComponentOrder]&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";

			echo "<div class='form-footer'>
					" . (!empty($debug) ? 'Top DD_EDITABLE addFlag|tableAlias' : '') . "
					$updateSaveButton
					$cancelButton
					$saveAddButton
					$facebookButton
					$googleButton
					$linkedinButton
					$copyButton
					$addButton
					$deleteButton";

			generateCustomFunctionArray($customFunctionArray);

			echo "</div>";

			echo "<div style='clear:both'></div><hr>";


			while ($row = $rs2->fetch_assoc()) {
				formating_Update($row, $method = 'add', $urow);
			}//// end of while loop

			echo "<div class='form-footer'>
					" . (!empty($debug) ? 'Bottom DD_EDITABLE addFlag|tableAlias' : '') . "
					$updateSaveButton
					$cancelButton
					$saveAddButton
					$facebookButton
					$googleButton
					$linkedinButton
					$copyButton
					$addButton
					$deleteButton";
					generateCustomFunctionArray($customFunctionArray);
				echo "</div>";


			echo "<div style='clear:both'></div></form></section></div>";

		} else {

			/*
			 *
			 * display Forms with fields
			 */

			if ( ( ( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || ( $_GET['edit'] == 'true' && ($_GET['ComponentOrder'] == $row1['component_order'] || $_GET['ComponentOrder'] == $row1['component_column'])) && $_GET['table_alias'] == $row1['table_alias'] ) && $row1['component_type'] != 'content') {
				addCustomFunctionModal($customFunctionArray);

				if (isset($_SESSION['return_url']) && $_GET['checkFlag'] == 'true') {
					echo "<form action='?action=update&checkFlag=true&ComponentOrder=$row1[component_order]&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='$dd_css_class' style='$css_style'><br>";
				} else {
					echo "<form action='?action=update&ComponentOrder=$row1[component_order]&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='$dd_css_class' style='$css_style'><br>";
				}

				$image_display = 'true';

				/***
				 * ADDING BREADCRUMB FOR PARENT/NORMAL LISTS/PAGES
				 *
				 * Short solution for back to home page
				 */
				generateBreadcrumbsAndBackPage($row1,$primary_key,$onePage=true); // in codeCommonFunction.php


				/**
				 * ****
				 * *********
				 * **************
				 * *****************Displaying save/cancel button on top of form
				 * ***************************
				 * *************************************************
				 */

				if ($editable == 'true') {
					if (( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || $_GET['edit'] == 'true') {
						// if (empty($_SESSION['profile-image'])) {
						// when edit form is not list
						// $cancel_value = 'Cancel';

						if ($row1['dd_editable'] == 11 && $row1['page_editable'] == 1) {

							if ($_GET['checkFlag'] == 'true') {

								if ($_GET['table_type'] == 'child') {
									$link_to_return = $_SESSION['child_return_url'];
								} else {
									$link_to_return = $_SESSION['return_url'];
								}

								if(empty($link_to_return)){
									$link_to_return = $_SESSION['return_url'];
								}

								$actual_link = $link_to_return;

								//$cancel_value = 'Cancel';
							}

							$actual_link = $actual_link . "&button=cancel&fnc=onepage";
							$cancel_button = "<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>&nbsp";
							if($hide_update_cancel) {
								$cancel_button="";
							}
                			if ($tab_style != 'bars') {
								echo "<div class='form-footer' >
									$updateSaveButton
									$cancel_button
									$saveAddButton
									$copyButton
									$addButton
									$deleteButton
									";
                    			generateCustomFunctionArray($customFunctionArray);

								echo "</div>";

							}
						}/// if for submit and cancel ends here
							// profile-image }
					}
					if ($row1['dd_editable'] == 11) {
						echo "<div style='clear:both'></div><hr>";
					}
				}


				/* ************************************************** */
				/* ************************************************** */
				/* ************************************************** */
				/* ************************************************** */

				if ($row1['dd_editable'] == 1 && $row1['page_editable'] == 1) {
					echo "<button type='button' class='btn btn-default pull-right edit-btn' id='$row1[dict_id]' >" . EDIT . "</button>";
					$image_display = 'false';
				}

				if (isset($_GET['id'])) {
					$urow = get_single_record($_SESSION['update_table']['table_name'], $_SESSION['update_table']['keyfield'], $_GET['id']);
				}
				while ($row3 = $rs2->fetch_assoc()) {
            		$row3['dd_editable'] = $row3['dd_editable'][0];
            		if($row3['dd_editable']=='2' || $row3['dd_editable']=='3') {
              			$row3['dd_editable'] = '11';
            		}
					if($form_open_for_edit) {
						$row3['temp_dd_editable'] = '11';
					}else if($show_with_edit_button) {
						$row3['temp_dd_editable'] = '1';
					}

					formating_Update($row3, $method = 'edit', $urow, $image_display);

				}//// end of while loop
			} else {
				//// fetching child list
				// if ($row1['list_views'] == 'NULL' || $row1['list_views'] == '') {
				/////////////
				////////////////
				//  echo "<form action='?action=update&ComponentOrder=$_GET[ComponentOrder]' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";
				// Child_List($qry);
				// } else {

				if ($row1['component_type'] == 'content') {

					if (strpos(trim($row1['description']), "ttp://")) {
						$url = trim($row1['description']);
						echo "<iframe src='$url'></iframe>";
					} else {
						echo "<div class='$row1[dd_css_class]'>$row1[description]</div>";
					}
				} else {
					list_display($qry, $row1['component_order'], $tab_anchor); //// list displays
					echo "<div style='clear:both'></div>";
				}
				// }
			}
			// check

			if ($editable == 'true') {
				if (( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || $_GET['edit'] == 'true' && $_GET['ComponentOrder'] == $row1['component_order']) {
					//if (empty($_SESSION['profile-image'])) {
					// $cancel_value = 'Cancel';

					if ($row1['dd_editable'] == 11 && $row1['page_editable'] == 1) {

						if ($_GET['checkFlag'] == 'true') {
							// $cancel_value = 'Cancel';
						}

						$actual_link = $_SESSION['return_url2'] . "&button=cancel&fnc=onepage";
              			$cancel_button = "<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>&nbsp";
              			if ( $hide_update_cancel ) {
                			$cancel_button= "";
              			}

						echo "<div class='form-footer' >
							$updateSaveButton
							$cancel_button
							$saveAddButton
							$copyButton
							$addButton
							$deleteButton
							";
						generateCustomFunctionArray($customFunctionArray);

						echo "</div>";

					}/// if for submit and cancel ends here
						//profile-image }
				}

				echo "<div style='clear:both'></div></form></section></div><!--<div class='h1-sep'><span></span></div>-->";

            }
		}

			//break;
		//}
    } else {
        echo "<section class='section-sep'><h1>$row[component_name]</h1>";
        echo "<h3 style='color:red'>".ERROR_NOT_ENOUGH_PRIVILEGE_LEVEL."</h3>";
		// echo "<h3 style='color:red'>SPECIAL ERROR BREAK 22222</h3>";
        echo "</section>";
        // page privilege if its false
    }
} // 607


function addCustomFunctionModal($customFunctionArray){
	if (!empty($customFunctionArray) ) {

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