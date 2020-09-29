<?php
	/*
	function component_display_loop($component_location,$table_alias, $page_name, $tab_style = 'tabbed', $component_order = 0, $editable = 'true') {
		(HUGE FUNCTION)
	*/


	// ***************************************************************************************************************
	// ***************************************************************************************************************

	function component_display_loop($component_location,$table_alias, $page_name, $tab_style = 'tabbed', $component_order = 0, $editable = 'true') {
		
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		if($_GET['edit'] || $_GET['addFlag']){
			$actual_link = $_SESSION['return_url'];
		}
		

		$con = connect();

		$dict_id=$_GET['dict_id'];

		// NOTES - Right now, we are checking for BOTH dict_id<>'' or page_name
		// because currently we are transitioning the method that we determine the unique component ID
		if ($dict_id <> '') {
			$rs = $con->query("SELECT * FROM data_dictionary where dict_id='$dict_id'");
			$row = $rs->fetch_assoc();
			$details = $row;
			$page_name=$row['page_name'];
		}

        // these are components that are handled differently that typical GPE components
		
		$_special_component_types='header|banner|slider|content|url|text|subheader|image|icon';
						
        // these are components that default to being displayed Above the content areas.
					
		$_above_special_component_types='header|subheader';
		
		// retreiving DD records for "regular' DD components, these have a component_order
		
		if (empty($_GET['ComponentOrder'])) {
			$rs = $con->query("
			SELECT component_order FROM data_dictionary
			where page_name='$page_name'
			AND
			component_type NOT REGEXP '$_special_component_types'
			AND
			component_order REGEXP '^[0-9]+$' AND component_order >'0'
			order by component_order
			");
			$row = $rs->fetch_assoc();
			$_GET['ComponentOrder'] = $row['component_order'];
		}
				
		// -----------------------------------------------------------------------
		// Process components for SERIAL Page Layout
		// -----------------------------------------------------------------------

		if ($tab_style == 'serial' ) {

			// This complex query below selects DD components that might appear ABOVE the content area.  This is either when component_location=above (explicitly) or location is blank but the component_default is one of the SPECIAL_COMPONENT_TYPES_DEFAULT_ABOVE
			
			$rs =   $con->query("SELECT * FROM data_dictionary
						where
						page_name='$page_name'  AND
						(
							(component_location='above' AND component_type NOT REGEXP '$_above_special_component_types' )
						OR
							(
							(component_location IS NULL OR component_location!='above')
							AND component_order REGEXP '^[0-9]+$'
							AND component_order >'0'
							AND component_type NOT REGEXP '$_above_special_component_types'
							)
						)
						order by component_order"
					);

			 

			// ***********************************************************
			// ***** Display different Components ************************
			// ***********************************************************
			// Note that it appears that in SERIAL layouts ... any components can appear
			// But in TABBED layouts - only forms???

			while ($row = $rs->fetch_assoc()) {
				
				if(!isAllowedToShowByPrivilegeLevel($row)){
					continue;
				}
				
				switch($row['component_type']){
					case 'slider':
						ShowComponentTypeSlider($row['page_name'],$row['component_order']);
						break;
					case 'banner':
						ShowComponentTypeBanner($row['page_name'],$row['component_order']);
						break;
					// case 'p_banner':
					//     ShowComponentTypeParallaxBanner($row['page_name'],$row['component_order']);
					//     break;
					case 'url':
						ShowComponentTypeURL($row['page_name'],$row['component_order']);
						break;
					case 'content':
						ShowComponentTypeContent($row['page_name'],$row['component_order']);
						break;
					case 'image':
						ShowComponentTypeImage($row['page_name'],$row['component_order']);
						break;
					case 'icon':
						ShowComponentTypeIcon($row['page_name'],$row['component_order']);
						break;
					case 'form':
						form_content_display_loop($row);
						break;
					case 'list':
						//list_content_display_loop($row['dict_id'], $row['table_alias'], $row['compoent_order']);  // in list_display_views.php
						//print_r($row);
					
						
						if ($_GET["table_alias"] == 'products') {
							form_content_display_loop($details);
						} else {
							if ($row['table_alias']=='transactions' && $_GET["table_alias"] == 'transactions'&& $row['component_name'] =='Product Purchased') {
								form_content_display_loop($details);
							} else {
								list_display($row['dict_id'], $row['table_alias'], $row['component_order']);  // in list_display_views.php
							}
						}
						//list_display($row);  // in list_display_views.php
						break;  
					default:
						form_content_display_loop($row);
						break;
				}  // end switch
	
                // End while
			} 

			// -----------------------------------------------------------------------
		    // END Serial Component Layout
		    // -----------------------------------------------------------------------

		} else {

			// ****************
			// CJ - I am not sure why sidebar content and tabbed content are grouped here ... (under the "else" above)
			// or why it seems that only form content is formatted/available ... (and not available in the SERIAL pages)
			// ****************

			// * *****************************************************************************
			// SIDEBAR CONTENT
			// * *****************************************************************************
			if ($tab_style == 'sidebars') {
				$rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.page_name='$page_name' and data_dictionary.component_column='$component_order'    order by field_dictionary.field_order");

				$qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.page_name='$page_name' and component_column='$component_order'    order by field_dictionary.field_order";
				$_SESSION['mydata'] = $table_alias . " " . $page_name;

				$rs2 = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.page_name='$page_name'  and component_column='$component_order'  order by field_dictionary.field_order");
			}

			// * *****************************************************************************
			// TABBED CONTENT
			// * *****************************************************************************
			if ($tab_style == 'tabbed') {
				$rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.page_name='$page_name' and component_order='$_GET[ComponentOrder]'  order by field_dictionary.field_order");

				$qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.page_name='$page_name' and component_order='$_GET[ComponentOrder]'   order by field_dictionary.field_order";

				$rs2 = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.page_name='$page_name'  and component_order='$_GET[ComponentOrder]'  order by field_dictionary.field_order");
			}

			$row1 = $rs->fetch_assoc();
			$form_open_for_edit = false;
			$dd_EditAbleHaveValue2  = false;
			$hide_update_cancel = false;

			/* ==========================================================
			// **** Retrieve "data_dictionary->dd_editable" Parameters (2 bytes/characters)
			Parameter-Character 1
			-----------------------------------------
			Initial State     
			0 = not editable (at all).  read-only form.
			1 = initially UNeditable (but with 'edit' button to edit) 
			2 = initially editable  (with update/cancel button) 
			3=  initially editable (without update cancel button - ie - enter submits/updates) 
			
			Parameter-Character 2 (not applicable if initial state = 0, read-only form)
			-----------------------------------------
			What happens after a submit/cancel   		
			0 = form screen remains.  
			(update/cancel buttons remain, if they were already there) 
			1 = form displays the "uneditable" form screen with "edit" option 
			2 = IF this form navigated as a result of a prior "list_select" then it will return to the prior list
			Otherwise - after submit - the form remains in the same state (editable, with submit/cancel buttons)
			========================================================== */
						
			$DD_EDITABLE = $row1['dd_editable'];     //dd_editable should be two characters  00  01  10  11 12 ...
			$DD_EDITABLE_bit1 = $DD_EDITABLE[0];

			if (is_null($DD_EDITABLE[1]) || empty($DD_EDITABLE[1])) {
				$DD_EDITABLE[1] = '0';
			}

			$DD_EDITABLE_bit2 = $DD_EDITABLE[1];

			$row1['real_dd_editable'] = $row1['dd_editable'];
			$row1['dd_editable'] = $DD_EDITABLE_bit1;
			
			if ($row1['dd_editable'] == '2') {
				$row1['dd_editable'] = '11';
			} else if($row1['dd_editable'] == '3') {
				$row1['dd_editable'] = '11';
				$hide_update_cancel = true;
			}

			if ($DD_EDITABLE_bit2=='2') {
				$dd_EditAbleHaveValue2  = true;
			} else {		
				unset($_SESSION['link_in_case_of_dd_editable_2']);
			}

			if (isset($_SESSION['form_open_for_edit_DD']) && $_SESSION['form_open_for_edit_DD']==$row1['dict_id']) {	
				$form_open_for_edit = true;
				$row1['dd_editable']='11';
				unset($_SESSION['form_open_for_edit_DD']);
				unset($_SESSION['form_open_for_edit']);
			}

			$show_with_edit_button = false;
			if (isset($_SESSION['show_with_edit_button']) && $_SESSION['show_with_edit_button_DD']==$row1['dict_id']) {
				$show_with_edit_button = true;
				$row1['dd_editable']='1';
				unset($_SESSION['show_with_edit_button']);
				unset($_SESSION['show_with_edit_button_DD']);
			}


				

			// True will display texts in the operations - button area.

			$debug = false;

			// ---------------------------------------------------------------------------
			// BELOW - Handling FORMS - "editable" form status AND
			//     The "operations" buttons  above the forms
			// ---------------------------------------------------------------------------


			// -----------------------------------------------------------------
			// --- this is an "editable" form
			// -----------------------------------------------------------------
			if ($editable == 'true') {
				
				// Make sure this is NOT a list (list_views must be empty)
				if (( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id']) ) || $_GET['edit'] == 'true' || !empty($_GET['addFlag']) ) {
					$operationsVarArray = array();
					$operation = '';

					$defaultOptions = getDefaultListViewExtraOptions($con,$row['page_name']);

					##DD.edit_operation
					if ( ($row1['dd_editable'] == 11 || $row1['dd_editable'] == 1) && $row1['page_editable'] == 1) {
						$operation = 'edit_operations';
						if(!empty(trim($row1['edit_operations']) ) ) {
							$operationsVarArray = getOperationsData($row1['edit_operations'], 'edit_operations');
						} else {
							$operationsVarArray = getOperationsData($defaultOptions['edit_operations'], 'edit_operations');
						}
					} else if ($row1['dd_editable'] !== 11 || $row1['page_editable'] == 0) {
						##DD.view_operation
						$operation = 'view_operations';
						if (!empty(trim($row1['view_operations']) ) ) {
							$operationsVarArray = getOperationsData($row1['view_operations'], 'view_operations');
						} else {
							$operationsVarArray = getOperationsData($defaultOptions['view_operations'], 'view_operations');
						}
					}
					// Grab the "menu operations" paramters
					// from either edit_operations, or view_operations
					// (loaded into $operationsVarArray, above)

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
			}
			// ------------------------------------------
			// below grabs info for special login buttons
			// (only if specified in the menus above
			// ------------------------------------------
			if (!empty($facebook_array)) {
				$facebookButton = generateFacebookButton($facebook_array);
			}

			if (!empty($google_array)) {
				$googleButton = generateGoogleButton($google_array);
			}

			if (!empty($linkedin_array)) {
				$linkedinButton = generateLinkedinButton($linkedin_array);
			}


			/* 
			// not sure why this is here ???******
			// possibly for use further below (and it is here, early)
			$_SESSION['list_pagination'] = $row1['list_pagination'];
			$tableType = trim(strtolower($row1['table_type']));
			$componentType = trim(strtolower($row1['component_type']));
			*/
					
			/*---- setting for  Save/Update button ----*/
			if (!empty($submit_array) ) {
				$updateSaveButton = "<input type='submit'  value='" . $submit_array['value'] . "' class='btn btn-primary update-btn " . $submit_array['style'] . "' /> &nbsp;";
			} else if($operation == 'edit_operations') {
				if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true' && ($_GET['ComponentOrder'] == $row1['component_order'] || $_GET['ComponentOrder'] == $row1['component_column']) && $_GET['table_alias'] == $row1['table_alias']) {
					$updateSaveButton = "<input type='submit'  value='" . formSave . "' class='btn btn-primary update-btn' /> &nbsp;";
				} else {
					$updateSaveButton = "<input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' /> &nbsp;";
				}
			} else if($operation == 'view_operations') {
				#$updateSaveButton = "<input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' /> &nbsp;";
			}
			if ($hide_update_cancel) {
				$updateSaveButton = '';
			}

			// current URL (page-state) so we can return to it
			
			$addUrlInner = getRecordAddUrlInner($row1);   

			/// setting for  save add button
			if (!empty($save_add_array) ) {
				$_SESSION['save_add_url'] = $addUrlInner;
				$saveAddButton = "<button type='submit' name='save_add_record' class='btn " . $save_add_array['style'] ."'>" . $save_add_array['label'] . "</button> &nbsp;";
			}

			/// setting for  delete button
			if (!empty($del_array) ) {
				$deleteButton = "<button type='submit' class='btn list-del " . $del_array['style'] . "' name='$row1[dict_id]' id='$_GET[search_id]' >" . $del_array['label'] . "</button> &nbsp;";
			}

			//// setting for  copy button
			if (!empty($copy_array) ) {
				$copyButton = "<button type='submit' class='btn list-copy " . $copy_array['style'] . "'  name='$row1[dict_id]' id='$_GET[search_id]'>" . $copy_array['label'] . "</button> &nbsp;";
			}

			/// ADD BUTTON
			if (!empty($add_array) ) {
				$href = "window.location.href='$addUrlInner'";
				$addButton = "<button type='submit' class='btn action-add " . $add_array['style'] . "' name='add' onclick=$href>" . $add_array['label'] . "</button> &nbsp;";
			}

			// *******  Process/Add any "custom buttons" (functions)
			
			generateCustomFunctionArray($customFunctionArray); // in codeCommonFunction.php


			// * *****************************************************************************
			// * Checking and displaying contents of the page according to the Privilege
			// * ****************************************************************************

			$css_style = $row1['dd_css_code'];

			// $userPrivilege = isAllowedToShowByPrivilegeLevel($row1);
			
			$userPrivilege = false;      

			if ( ( loginNotRequired() || itemHasPrivilege($row1['dd_privilege_level']) ) && itemHasVisibility($row1['dd_visibility']) ) {
				$userPrivilege = true;
			}

			// BEGIN LARGE BLOCK (ends at the end of this file)
				
			if ($userPrivilege === true) {

				////adding class if form is not for editing purpose
				$page_editable = true;
				$dd_css_class = 'simple_edit_page';

				if ($row1['page_editable'] == 0 && trim($row1['component_type']) != 'transaction') {
					$page_editable = false;
					$dd_css_class = 'page_not_editable';
				} elseif ($row1['page_editable'] == 2) {
					$page_editable = false;
					$dd_css_class = 'profile_page';
				}
				$dd_css_class .= ($row1['dd_css_class'] ?: '');

				if ($row1['table_name'] == $_SESSION['select_table']['table_name']) {
					$_SESSION['search_id'] = $_SESSION['uid'];
				} else if (trim($row1['table_type']) == 'child') {
					$_SESSION['search_id'] = $_SESSION['parent_value'];
				} else {
					$_SESSION['search_id'] = $_SESSION['uid']; /// for displaying one user
				}
					/*
					if (isset($_GET['search_id']) && !empty($_GET['search_id'])) {
					// $_SESSION['search_id'] = $_GET['search_id'];
					}
					*/

				if (isset($_GET['id']) && $_GET['id'] != '') {
					$_SESSION['search_id'] = $_GET['id'];
					// $_SESSION['update_table']['keyfield'] = 'id';
				}

				$_SESSION['update_table']['table_name'] = $row1['table_name'];
				$primary_key = firstFieldName($row1['table_name']);
				$_SESSION['update_table']['keyfield'] = $primary_key;

				if (trim($row1['table_type']) == 'parent') {
					$_SESSION['update_table']['child_parent_key'] = (!empty($row1['keyfield']) ? $row1['keyfield'] : $_SESSION['update_table']['keyfield'] );
					$_SESSION['update_table']['child_parent_key_diff'] = (!empty($row1['keyfield']) ? 'true' : 'false');
				}


				/******** for update or ADD *** */

				if ($row1['dd_editable'] == '11') {

					$_SESSION['dict_id'] = $row1['dict_id'];

					$_SESSION['search_id2'] = $_GET['search_id'] ?: $_SESSION['search_id'];
					$_SESSION['update_table2']['table_name'] = $_SESSION['update_table']['table_name'];
					$_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];

					if (trim($row1['table_type']) == 'parent') {
						$_SESSION['update_table2']['child_parent_key'] = (!empty($row1['keyfield']) ? $row1['keyfield'] : $_SESSION['update_table']['keyfield']);
						$_SESSION['update_table2']['child_parent_key_diff'] = (!empty($row1['keyfield']) ? 'true' : 'false');
					}

					//////updating tab_anchor for home pages

					$_SESSION['anchor_tag'] = "#" . trim($row1['component_name']);
					if ($_GET['checkFlag'] == 'true') {
						if ($_GET['table_type'] == 'child') {
							$_SESSION['child_return_url2'] = $_SESSION['child_return_url'];
						} else {
							$_SESSION['return_url2'] = $_SESSION['return_url'];
						}
					} else {
						$_SESSION['return_url2'] = $actual_link;
					}
					//$_SESSION['table_alias'] = $row1['table_alias'];
				}

				// FIXES THE SESSION HOLDING INSERT TABLE NAME NOT
				// SETTING UP WHEN DD.dd_editable != 11###
				else {
					$_SESSION['dict_id'] = $row1['dict_id'];
					$_SESSION['search_id2'] = $_GET['search_id'] ?: $_SESSION['search_id'];
					$_SESSION['update_table2']['table_name'] = $_SESSION['update_table']['table_name'];
					$_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];
				}

				if (!empty($_GET['table_alias']) && $_GET['table_alias'] == $row1['table_alias'] && !empty($_GET['search_id'])) {
					if ($_GET['table_type'] == 'parent') {
						if ($_SESSION['update_table']['child_parent_key_diff'] == 'true') {
							$child_parent_value = getWhere($row1['table_name'], array($_SESSION['update_table']['keyfield'] => $_GET['search_id']));
							$_SESSION['parent_value'] = $child_parent_value[0][$_SESSION[update_table][child_parent_key]];
						} else {
							$_SESSION['parent_value'] = $_GET['search_id'];
						}
					}
					$urow = get_single_record($_SESSION['update_table']['table_name'], $_SESSION['update_table']['keyfield'], $_GET['search_id']);
				} else {
					// * This check is deploying for Transaction feature.
					$user_id  = !empty($_GET['search_id'])?$_GET['search_id']:$_SESSION['search_id'];
					if (trim($row1['component_type']) != 'transaction')
						$urow = get_single_record($_SESSION['update_table']['table_name'], $_SESSION['update_table']['keyfield'], $user_id);
				}

				// displaying the heading of tab page
				// SPECIFICALLY FOR TABBED SECTIONS


				$component_name = explode("/", $row1['component_name']);
				if (!empty(trim($component_name[1]))) {
					echo "<h1 class='tab-header'>$component_name[1]</h1>";
				}

				// * *************************Generating session to capture component_name

				$_SESSION['list_component_name'] = $component_name[0];
				$_SESSION['return_url'] = $actual_link;

				/////generating session for capturing parent List tabname
				if ($row1['table_type'] == 'parent') {
					$_SESSION['parent_list_tabname'] = $component_name[0];

					$_SESSION['parent_url'] = $actual_link;
				}

				// * *******************************
				//  * BREADCUMB for child lists
				//  * *******************************
				
				if (($row1['component_type']=="list" && ( $row1['list_views'] != 'NULL' || $row1['list_views'] != '' ) && trim($row1['table_type']) == 'child' && $_GET['edit'] != 'true' ) && $_GET['addFlag'] != 'true') {
					//ECHO "<br>BREADCUMB for child lists<br>";
					if(!empty($_SESSION['child_return_url'])){
						$backText = str_replace('*', '', $_SESSION['parent_list_tabname']);
						echo "<br><ol class='breadcrumb'>
									<li><a href='$_SESSION[parent_url]&button=cancel' class='back-to-list'>Back To <span>$backText</span> List</a></li>
								</ol>";
					}
				}
				
				// IF THE FORM is Generated as part of an "Add" (a blank form with a new record)
				// ************  Below

				// ************************
				// ADDING Form flags code goes here
				// ************************				 
						
				if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true' && ($_GET['ComponentOrder'] == $row1['component_order'] || $_GET['ComponentOrder'] == $row1['component_column']) && $_GET['table_alias'] == $row1['table_alias']) {
					
					//ECHO "<br>GOT HERE --- 				 ADDING Form flags code goes here<br>";
					//ECHO "<br>dict_id=";echo $row1['dict_id'];

					if (empty($save_add_array) ) {
						unset($_SESSION['save_add_url']);
					}

					// ADDING BREADCRUMB FOR PARENT/NORMAL LISTS/PAGES
					// Short solution for back to home page

					generateBreadcrumbsAndBackPageForAdd($row1, $onePage=false); // in codeCommonFunction.php

					$dd_css_class = $row1['dd_css_class'];

					$_SESSION['dict_id'] = $row1['dict_id'];

					if (!empty($_GET['search_id'])) {
						$_SESSION['search_id2'] = $_GET['search_id'];
					} else {
						$_SESSION['search_id2'] = $_SESSION['search_id'];
					}
					$_SESSION['update_table2']['table_name'] = $_SESSION['update_table']['table_name'];

					$_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];


					if ($_GET['checkFlag'] == 'true') {

						// THIS IS USED FOR ADD FORM DISPLAY WHICH I WILL MODIFY FOR THE addimport UPLOAD FORM FIELDS
								
						echo "<form action='$_SESSION[add_url_list]&action=add' method='post' id='user_profile_form' enctype='multipart/form-data' class='shivgre-checkFlag-true $dd_css_class' style='$css_style'><br>";
					} else {

						$_SESSION['return_url2'] = $actual_link;

						ECHO "<br>SPECIAL POINT 2 - echo form action= $ actual_link";echo $actual_link;echo "<br>";
						echo "<form action='?action=add&ComponentOrder=$_GET[ComponentOrder]' method='post' id='user_profile_form' enctype='multipart/form-data' class='shivgre-checkFlag-false $dd_css_class' style='$css_style'><br>";
					}
					if ($_GET['checkFlag'] == 'true') {
						if ($_GET['table_type'] == 'child'){
							$link_to_return = $_SESSION['child_return_url'];
						}
						else {
							$link_to_return = $_SESSION['return_url'];
						}
						if(empty($link_to_return)){
							$link_to_return = $_SESSION['return_url'];
						}
						$actual_link = $link_to_return;

						//   $cancel_value = formCancel;
					}


					$actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]&component_type=$_GET[component_type]";

					$cancelButton = "<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>";
					if(in_array(trim(strtolower($row1['component_type'])),['login','signup','forgotpassword','reset_password','change_password']) || $hide_update_cancel){
						// empty
						$cancelButton = "";
					}


					echo "<div class='form-footer'>

							" . (!empty($debug) ? 'Top DD_EDITABLE addFlag|tableAlias' : '') . "
							$updateSaveButton
							$saveAddButton
							$facebookButton
							$googleButton
							$linkedinButton
							$copyButton
							$addButton
							$deleteButton
							$cancelButton
						</div>";

					echo "<div style='clear:both'></div><hr>";


									## addimport FORM FIELDS#######GET THE I | P for import from file or PROMPT
					//                if($_GET['addImport'] == 'true' && !empty($_SESSION['addImportParameters']) )
					//                {
					//
					////                    echo "<Pre>";
					////                    print_r($_SESSION['addImportParameters']);
					////                    print_r($_GET);
					////                    echo "</pre>";
					//
					//
					//
					//                    if(strtolower($_GET['addImportType']) == 'file')
					//                    {
					//                        echo "<div class='new_form'><label>" . ucwords($_SESSION['addImportParameters']['1']) . "</label>";###$_SESSION['addImportParameters']['1'] == description###
					//                            echo "<input type='file' name='addImportFile' required title='' size='' class='form-control' style='height: auto;' >";
					//                        echo "</div>";
					//                    }
					//                    else if(strtolower($_GET['addImportType']) == 'manual')
					//                    {
					//                        $customFunctionParameters = $_SESSION['addImportParameters'];
					//
					//                        array_splice($customFunctionParameters, 0, 3);
					//
					//                        $customFunctionParameters = array_map('ucwords', $customFunctionParameters);
					//
					//                        echo "<div class='new_form'><label>" . ucwords($_SESSION['addImportParameters']['1']) . "</label>";###$_SESSION['addImportParameters']['1'] == description###
					//                            echo "<br>Fields : " . implode(', ', $customFunctionParameters) . "<br>";
					//                            echo '<textarea name="addImportText" class="form-control" cols="100" required ></textarea>';
					//                        echo "</div>";
					//                    }
					//
					//
					//
					//                }
					//                else
					//                {


					while ($row = $rs2->fetch_assoc()) {
						formating_Update($row, $method = 'add', $urow);
					}
					//if ($_GET['checkFlag'] == 'true') {
					//}

					/* if ($_GET['table_type'] == 'child' && $_GET['checkFlag' == 'true'])
					$actual_link = $_SESSION['add_url_list'] . "&button=cancel";
					else */


					// }

					echo "<div class='form-footer'>

							" . (!empty($debug) ? 'Bottom DD_EDITABLE addFlag|tableAlias' : '') . "
							$updateSaveButton
							$saveAddButton
							$facebookButton
							$googleButton
							$linkedinButton
							$copyButton
							$addButton
							$deleteButton
							$cancelButton
						</div>

						<!--</div>-->";###COMMENTED OUT AS IT DOESN"T HAVE OPENING <DIV> TAG


					echo "<div style='clear:both'></div></form>";
				} else {
					if (( ( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || $_GET['edit'] == 'true') && $row1['component_type'] != 'content') {

						$row1['table_type'] = trim(strtolower($row1['table_type']));

						switch($row1['table_type']){
							case 'child':
								$table_type = 'child';
								break;
							default:
								$table_type = 'parent';
								break;
						}
						//ECHO "<br>GOT HERE --- 6<br>";
						
						$row1['component_type'] = trim(strtolower($row1['component_type']));
						// ECHO "<br>component_type=";
						// ECHO $row1['component_type'];
						// ECHO "<br><br>";
								
						switch($row1['component_type']){
							case 'login':
								$component_type = 'login';
								break;
							case 'signup':
								$component_type = 'signup';
								break;
							case 'forgotpassword':
								$component_type = 'forgotpassword';
								break;
							case 'reset_password':
								$component_type = 'reset_password';
								break;
							case 'change_password':
								$component_type = 'change_password';
								break;
							default:
								$component_type = 'form';
								// ECHO "<br>NEW component_type=";
								// ECHO $component_type;
								// ECHO "<br><br>";								
								break;
						}

						/*
						* short solution for now to add separate fffr sytling for FFFR edit page.
						*/


						// DD OVERHAUL CJ Notes - Adding component_type below

						if (isset($_SESSION['return_url']) || isset($_SESSION['child_return_url']) && $_GET['checkFlag'] == 'true') {

							echo "<form action='?action=update&checkFlag=true&ComponentOrder=$_GET[ComponentOrder]&table_type=$table_type&component_type=$component_type' method='post' id='user_profile_form' enctype='multipart/form-data' class='$dd_css_class' style='$css_style'><br>";
						} else {
							echo "<form action='?action=update&ComponentOrder=$_GET[ComponentOrder]&table_type=$table_type&component_type=$component_type' method='post' id='user_profile_form' enctype='multipart/form-data' class='$dd_css_class' style='$css_style'><br>";
						}

						//ECHO "<br><br>AFTER form action<br>";

						///// To show image uploader buttons

						/***
						 * ADDING BREADCRUMB FOR PARENT/NORMAL LISTS/PAGES
						 * Short solution for back to home page
						 */
						// in codeCommonFunction.php
						generateBreadcrumbsAndBackPage($row1,$primary_key,$onePage=false); 
						##VIEW OPERATION CUSTOM BUTTONS
						if($operation == 'view_operations'){
							echo "<div class='form-footer'>

										" . (!empty($debug) ? 'View operation Buttons' : '') . "
										$updateSaveButton
										$saveAddButton
										$facebookButton
										$googleButton
										$linkedinButton
										$copyButton
										$addButton
										$deleteButton

										<!--<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a> -->
									</div>

									<div style='clear:both'></div>
									<hr>

									";
						}

						/*
						* ****
						* *********
						* **************
						* *****************Displaying save/cancel button on top of form
						* ***************************
						* *************************************************
						*/


						// ECHO "<br><br>BEFORE form editable ....<br>";
						// ECHO "<br><br>$ editable=";echo $editable ;
						// ECHO "<br>dd_editable=";echo $row1['dd_editable'] ;
						// ECHO "<br>page_editable=";echo $row1['page_editable'] ;
						// ECHO "<br>list_views=";echo  $row1['list_views'] ;
						// ECHO "<br>$ _GET [table_type]=";echo  $_GET['table_type'] ;
						// ECHO "<br>$ _GET [component_type]=";echo  $_GET['component_type'] ;

						if ($editable == 'true') {
							if (( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || $_GET['edit'] == 'true') {


								if ($row1['dd_editable'] == 11 && $row1['page_editable'] == 1) {
									if ($_GET['checkFlag'] == 'true' ||  $dd_EditAbleHaveValue2) {

										if ($_GET['table_type'] == 'child'){
											$link_to_return = $_SESSION['child_return_url'];
										}else{
										$link_to_return = $_SESSION['return_url'];
										}

										if(empty($link_to_return)){
											$link_to_return = $_SESSION['return_url'];
										}

										// if(empty($link_to_return)){
										// 	$link_to_return = $_SESSION['return_url'];
										// }

										if($dd_EditAbleHaveValue2){
										$_SESSION['link_in_case_of_dd_editable_2'] = $link_to_return;
										}

										$actual_link = $link_to_return;
										//   $cancel_value = formCancel;
									}


									//ECHO "<br><br>AFTER form editable ....<br>";

									$actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]&component_type=$_GET[component_type]";

									$cancelButton = "<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>";

									if(in_array($component_type,['login','signup','forgotpassword','reset_password','change_password']) || $hide_update_cancel){
										$cancelButton = "";// empty
									}
									if ($tab_style != 'sidebars') {

										echo "<div class='form-footer' >

												" . (!empty($debug) ? 'Top DD_EDITABLE' : '') . "
												$updateSaveButton
												$saveAddButton
												$facebookButton
												$googleButton
												$linkedinButton
												$copyButton
												$addButton
												$deleteButton
												$cancelButton
											</div>";
									}
								}
							}
							if ($row1['dd_editable'] == 11 && $row1['page_editable'] == 1) {
								echo "<div style='clear:both'></div><hr>";
							}
						}
						/* *************************************************** */
						/* *************************************************** */
						/* *************************************************** */
						/* *************************************************** */

						$image_display = 'true';  // for displaying image container (later?)

						if ($row1['dd_editable'] == 1 && $row1['page_editable'] == 1) {
							echo "<button type='button' class='edit-btn btn btn-default pull-right' id='$row1[dict_id]'>" . EDIT . "</button>";
							$image_display = 'false';
						}
						if (isset($_GET['id'])) {
							$urow = get_single_record($_SESSION['update_table']['table_name'], $_SESSION['update_table']['keyfield'], $_GET['id']);
						}

						while ($row = $rs2->fetch_assoc()) {
							if (in_array($component_type,['login','signup','forgotpassword','reset_password','change_password'])) {
								$row['format_type'] =  trim($row['format_type']);
								if ($row['format_type'] == 'email') {
									$_SESSION['user_field_email'] = $row['generic_field_name'];
								}
								if ($row['format_type'] == 'uname') {
									$_SESSION['user_field_uname'] = $row['generic_field_name'];
								}
								if ($row['format_type'] == 'password') {
									$_SESSION['user_field_password'] = $row['generic_field_name'];
								}
								if ($row['format_type'] == 'confirm_password') {
									$_SESSION['user_field_confirm_password'] = $row['generic_field_name'];
								}
								if ($row['format_type'] == 'old_password') {
									$_SESSION['user_field_old_password'] = $row['generic_field_name'];
								}
								$urow = array();
							}

							$row['dd_editable'] = $row['dd_editable'][0];
							if ($row['dd_editable']=='2' || $row['dd_editable']=='3') {
								$row['dd_editable'] = '11';
							}
							if ($form_open_for_edit) {
								$row['temp_dd_editable'] = '11';
							} else if($show_with_edit_button) {
								$row['temp_dd_editable'] = '1';
							}

							formating_Update($row, $method = 'edit', $urow, $image_display, $page_editable);		
									
						}//// end of while loop

					} else {
						//// fetching child list
						// if ($row1['list_views'] == 'NULL' || $row1['list_views'] == '') {
						/////////////
						////////////////
						//  echo "<form action='?action=update&ComponentOrder=$_GET[ComponentOrder]' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";
						// Child_List($qry);
						// } else {

						if (trim($row1['component_type']) == 'content') {
							if (strpos(trim($row1['description']), "ttp://")) {
								$url = trim($row1['description']);
								echo "<iframe src='$url'></iframe>";
							} else {
								echo "<div class='$row1[list_style]'>$row1[description]</div>";
							}
						} else {
							list_display($row1['dict_id'], $row1['table_alias'], $row1['component_order']);
							echo "<div style='clear:both'></div>";
						}
					}

					if ($editable == 'true') {
						// ECHO "<br>editable=True ...  userPrivilege = ";echo ($userPrivilege == true ? "true" : "false"); echo "<BR><br>";

						if (( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || $_GET['edit'] == 'true') {
							// if (empty($_SESSION['profile-image'])) {
							///when edit form is not list
							//  $cancel_value = 'Cancel';

							if ($row1['dd_editable'] == 11 && $row1['page_editable'] == 1) {

								if ($_GET['checkFlag'] == 'true') {

									if ($_GET['table_type'] == 'child') {
										$link_to_return = $_SESSION['child_return_url'];
									} else {
										$link_to_return = $_SESSION['return_url'];
									}

									if (empty($link_to_return)) {
										$link_to_return = $_SESSION['return_url'];
									}
									$actual_link = $link_to_return;

									// $cancel_value = 'Cancel';
								}

								$actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]&component_type=$_GET[component_type]";

								//if( $row1['dd_editable'] != 0 ){

								$cancelButton = "<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>";
								if (in_array($component_type,['login','signup','forgotpassword','reset_password','change_password']) || $hide_update_cancel) {
									$cancelButton = "";// empty
								}

								echo "<div class='form-footer'>

										" . (!empty($debug) ? 'Bottom DD_EDITABLE' : '') . "
										$updateSaveButton
										$saveAddButton
										$facebookButton
										$googleButton
										$linkedinButton
										$copyButton
										$addButton
										$deleteButton
										$cancelButton
									</div>";

								// }
							}/// if for submit and cancel ends here
							// profile-image }
						}

						echo "<div style='clear:both'></div></form>";
					}
				}

						//break;
					// SWITCH END
					// if USER PRIVILEGE = TRUE 
			} else {
				echo "<h3 style='color:red'>".ERROR_NOT_ENOUGH_PRIVILEGE_LEVEL."</h3>";
				echo "<h3 style='color:red'>SPECIAL ERROR BREAK 111111</h3>";
			} // if USER PRIVILEGE = FALSE

		}//else ends here where component_order=0 is not part of dd->page_name
	}

