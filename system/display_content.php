<?php

//////////////
//////////////***** DISPALY TABS AS H1 TAG ******

function display_content($row) {
    #echo "<font colo=brown>function display_content() called.</font><br>";die("shiv");
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $editable = 'true';

    $con = connect();


    ///for taking inline anchoring
    $tab_anchor = trim($row['tab_name']);

    $rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$row[tab_num]'   order by field_dictionary.display_field_order");


    $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$row[tab_num]'   order by field_dictionary.display_field_order";

    $rs2 = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$row[tab_num]'   order by field_dictionary.display_field_order");


    $row1 = $rs->fetch_assoc();
	$tableType = trim(strtolower($row1['table_type']));

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

	$userPrivilege = false;
	if(itemHasVisibility($row['dd_visibility']) && itemHasPrivilege($row['dd_privilege_level'])){
		$userPrivilege = true;
	}
	if(loginNotRequired()){
		$userPrivilege = true;
	}
    if ($userPrivilege === true) {
			///////// for displaying image container
			$image_display = 'true';
			//print_r($row1);die;
			//////ASsigning custom class to the form

			//$style = $row1['list_style'];

			////adding class if form is not for editing purpose
			$page_editable = true;
			if ($row1['page_editable'] == 0 && trim($row1['table_type']) != 'transaction') {
				$page_editable = false;
				if (!empty($row1['list_style'])){
					$style = $row1['list_style'] . ' page_not_editable';
				} else {
					$style = 'page_not_editable';
				}
			} elseif ($row1['page_editable'] == 2) {
				$page_editable = false;
				if (!empty($row1['list_style'])){
					$style = $row1['list_style'] . ' profile_page';
				} else {
					$style = 'profile_page';
				}
			}else {
				if (!empty($row1['list_style'])){
					$style = $row1['list_style'] . ' simple_edit_page';
				} else {
					$style = 'simple_edit_page';
				}
			}

			if ($row1['database_table_name'] == $_SESSION['select_table']['database_table_name'])
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


			$_SESSION['update_table']['database_table_name'] = $row1['database_table_name'];

			$primary_key = firstFieldName($row1['database_table_name']);

			$_SESSION['update_table']['keyfield'] = $primary_key;

			if (trim($row1['table_type']) == 'parent') {
				$_SESSION['update_table']['child_parent_key'] = (!empty($row1['keyfield']) ? $row1['keyfield'] : $_SESSION['update_table']['keyfield'] );
				$_SESSION['update_table']['child_parent_key_diff'] = (!empty($row1['keyfield']) ? 'true' : 'false');
			}

			/******** for update ****/

			$_SESSION['list_tab_name'] = $row1['tab_name'];

			if ($row1['dd_editable'] == '11') {

				$_SESSION['dict_id'] = $row1['dict_id'];

				if (!empty($_GET['search_id']))
					$_SESSION['search_id2'] = $_GET['search_id'];
				else
					$_SESSION['search_id2'] = $_SESSION['search_id'];

				$_SESSION['update_table2']['database_table_name'] = $_SESSION['update_table']['database_table_name'];

				$_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];

				$_SESSION['anchor_tag'] = "#" . $tab_anchor;

				if ($_GET['checkFlag'] == 'true') {
					//was giving error in child list propel so made changes///
					//$_SESSION['return_url2'] = $_SESSION['return_url'];
					$_SESSION['return_url2'] = BASE_URL . "system/main.php?display=$_GET[display]&layout=$_GET[layout]&style=$_GET[layout]";
				} else {

					$_SESSION['return_url2'] = $actual_link;
				}
			}

			if (( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id']) ) || $_GET['edit'] == 'true' || !empty($_GET['addFlag']) ) {

				$operationsVarArray = array();
				$operation = '';

				##DD.edit_operation
				if ( ($row1['dd_editable'] == 11 || $row1['dd_editable'] == 1) && $row1['page_editable'] == 1)
				{
					$operation = 'edit_operations';

					if(!empty(trim($row1['edit_operations']) ) )
						$operationsVarArray = getOperationsData($row1['edit_operations'], 'edit_operations');
				}
				else if ($row1['dd_editable'] !== 11 || $row1['page_editable'] == 0) ##DD.view_operation
				{
					$operation = 'view_operations';

					if(!empty(trim($row1['view_operations']) ) )
						$operationsVarArray = getOperationsData($row1['view_operations'], 'view_operations');
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

			$tab_id = $row['display_page'].$row['dict_id'];
			$DD_style_list = trim($row['list_style']);
			echo "<div id='$tab_id' class='$DD_style_list'>";
			/* Show Table Type Header*/
			// ShowTableTypeHeaderContent($row['display_page'],$row['tab_num']);

			echo "<section class='section-sep'><a name='$tab_anchor'></a><h1 class='section-title'>$row[tab_name]</h1><!-- h1-content class not used-->";

			/* Show Table Type SubHeader*/
			// ShowTableTypeSubHeaderContent($row['display_page'],$row['tab_num']);

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
			} else if($operation == 'edit_operations') {
				if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true' && $_GET['tabNum'] == $row1['tab_num'] && $_GET['tab'] == $row1['table_alias']) {
					$updateSaveButton = "<input type='submit' value='" . formSave . "' class='btn btn-primary update-btn' /> &nbsp;";
				} else {
					$updateSaveButton = "<input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' /> &nbsp;";
				}
			} else if($operation == 'view_operations') {
				#$updateSaveButton = "<input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' /> &nbsp;";
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
				$addButton = "<button type='submit' class='btn action-add " . $add_array['style'] . "' name='add' onclick=$href >" . $add_array['label'] . "</button> &nbsp;";
			}


			##CUSTOM FUNCTION BUTTON##
			generateCustomFunctionArray($customFunctionArray); // in codeCommonFunction.php


			if (!empty($_GET['ta']) && $_GET['ta'] == $row1['table_alias'] && !empty($_GET['search_id'])) {

				if ($_GET['table_type'] == 'parent') {


					$_SESSION['parent_value'] = $_GET['search_id'];
				}

				$urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $_GET['search_id']);
			} else {

				$urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $_SESSION['search_id']);
			}


			if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true' && $_GET['tabNum'] == $row1['tab_num'] && $_GET['tab'] == $row1['table_alias']) {
				if (empty($save_add_array) ) {
					unset($_SESSION['save_add_url']);
				}
				$style = $row1['list_style'];

				$_SESSION['dict_id'] = $row1['dict_id'];

				if (!empty($_GET['search_id']))
					$_SESSION['search_id2'] = $_GET['search_id'];
				else
					$_SESSION['search_id2'] = $_SESSION['search_id'];

				$_SESSION['update_table2']['database_table_name'] = $_SESSION['update_table']['database_table_name'];

				$_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];

				/***
				 * ADDING BREADCRUMB FOR PARENT/NORMAL LISTS/PAGES
				 *
				 * Short solution for back to home page
				 */
				generateBreadcrumbsAndBackPageForAdd($row1,$onePage=true); // in codeCommonFunction.php


				if ($_GET['checkFlag'] == 'true') {
					###THIS IS USED FOR ADD FORM DISPLAY WHICH I WILL MODIFY FOR THE addimport UPLOAD FORM FIELDS################
					echo "<form action='$addUrlInner&action=add&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style shivgre-checkFlag-true'><br>";
				} else {
					$_SESSION['return_url2'] = $actual_link;
					echo "<form action='?action=add&tabNum=$_GET[tabNum]&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style shivgre-checkFlag-false'><br>";
				}

				if ($_GET['checkFlag'] == 'true') {
					if ($_GET['table_type'] == 'child'){
						$link_to_return = $_SESSION['child_return_url'];
					} else {
						$link_to_return = $_SESSION['return_url'];
					}
					$actual_link = $link_to_return;

					$_SESSION['return_url2'] = $_SESSION['return_url'];
				}
				$actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]&fnc=onepage";

				//$actual_link = $_SESSION['return_url2'] . "&fnc=onepage";

				$cancelButton = "<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>";
				if(in_array(trim(strtolower($row1['table_type'])),['login','signup','forgotpassword','reset_password','change_password'])){
					$cancelButton = "";// empty
				}

				//echo "<form action='?action=add&checkFlag=true&tabNum=$_GET[tabNum]&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";

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


				while ($row = $rs2->fetch_assoc()) {
					formating_Update($row, $method = 'add', $urow);
				}//// end of while loop

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
					</div>";


				echo "<div style='clear:both'></div></form></section></div>";

			} else {


				/*
				 *
				 * display Forms with fields
				 */

				if (( ( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || ( $_GET['edit'] == 'true' && $_GET['tabNum'] == $row1['tab_num']) && $_GET['ta'] == $row1['table_alias'] ) && $row1['table_type'] != 'content') {

					if (isset($_SESSION['return_url']) && $_GET['checkFlag'] == 'true') {
						echo "<form action='?action=update&checkFlag=true&tabNum=$row1[tab_num]&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";
					} else {
						echo "<form action='?action=update&tabNum=$row1[tab_num]&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";
					}

					$image_display = 'true';

					/***
					 * ADDING BREADCRUMB FOR PARENT/NORMAL LISTS/PAGES
					 *
					 * Short solution for back to home page
					 */
					generateBreadcrumbsAndBackPage($row1,$primary_key,$onePage=true); // in codeCommonFunction.php

					/*
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
							///when edit form is not list
							// $cancel_value = 'Cancel';

							if ($row1['dd_editable'] == 11 && $row1['page_editable'] == 1) {

								if ($_GET['checkFlag'] == 'true') {

									if ($_GET['table_type'] == 'child')
										$link_to_return = $_SESSION['child_return_url'];
									else
										$link_to_return = $_SESSION['return_url'];

									$actual_link = $link_to_return;

									//$cancel_value = 'Cancel';
								}

								$actual_link = $actual_link . "&button=cancel&fnc=onepage";

								if ($tab_status != 'bars') {
									echo "<div class='form-footer' >
										$updateSaveButton
										$saveAddButton
										$copyButton
										$addButton
										$deleteButton
										<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>
									</div>";
									/*echo "<div class='form-footer'>
											<input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' />
											<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>
										</div>";*/
								}
							}/// if for submit and cancel ends here
							// profile-image }
						}
						if ($row1['dd_editable'] == 11) {
							echo "<div style='clear:both'></div><hr>";
						}
					}


					/*                 * ************************************************** */
					/*                 * ************************************************** */
					/*                 * ************************************************** */
					/*                 * ************************************************** */

					if ($row1['dd_editable'] == 1 && $row1['page_editable'] == 1) {
						echo "<button type='button' class='btn btn-default pull-right edit-btn' id='$row1[dict_id]' >" . EDIT . "</button>";
						$image_display = 'false';
					}

					if (isset($_GET['id'])) {
						$urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $_GET['id']);
					}
	//print_r($urow);die;
					while ($row3 = $rs2->fetch_assoc()) {
						formating_Update($row3, $method = 'edit', $urow, $image_display);
					}//// end of while loop
				} else {
	//// fetching child list
	// if ($row1['list_views'] == 'NULL' || $row1['list_views'] == '') {
	/////////////
	////////////////
	//  echo "<form action='?action=update&tabNum=$_GET[tabNum]' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";
	// Child_List($qry);
	// } else {

					if ($row1['table_type'] == 'content') {

						if (strpos(trim($row1['description']), "ttp://")) {

							$url = trim($row1['description']);

							echo "<iframe src='$url'></iframe>";
						} else {
							echo "<div class='$row1[list_style]'>$row1[description]</div>";
						}
					} else {

						list_display($qry, $row1['tab_num'], $tab_anchor); //// list displays

						echo "<div style='clear:both'></div>";
					}
	// }
				}
	//check

				if ($editable == 'true') {
					if (( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || $_GET['edit'] == 'true' && $_GET['tabNum'] == $row1['tab_num']) {
						//if (empty($_SESSION['profile-image'])) {
						// $cancel_value = 'Cancel';

						if ($row1['dd_editable'] == 11 && $row1['page_editable'] == 1) {

							if ($_GET['checkFlag'] == 'true') {

								// $cancel_value = 'Cancel';
							}

							$actual_link = $_SESSION['return_url2'] . "&button=cancel&fnc=onepage";

							echo "<div class='form-footer' >
										$updateSaveButton
										$saveAddButton
										$copyButton
										$addButton
										$deleteButton
										<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>
									</div>";
							/*echo "<div class='form-footer'>
									<input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' />
									<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>
								</div>";*/
						}/// if for submit and cancel ends here
						//profile-image }
					}

					echo "<div style='clear:both'></div></form></section></div><!--<div class='h1-sep'><span></span></div>-->";
				}
			}
			//break;
		//}
    } else {
        echo "<section class='section-sep'><h1>$row[tab_name]</h1>";
        echo "<h3 style='color:red'>You don't have enough privilege to view contents</h3>";

        echo "</section>";
        ///page privilege if its false
    }
}
