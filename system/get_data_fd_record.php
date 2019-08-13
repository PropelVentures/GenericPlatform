<?php

function Get_Data_FieldDictionary_Record($dd_position,$table_alias, $display_page, $tab_status = 'false', $tab_num = 'false', $editable = 'true',$list_sort='tab_num') {
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if($_GET['edit'] || $_GET['addFlag']){
        $actual_link = $_SESSION['return_url'];
    }

    $con = connect();
    ///setting form editable if user click on list for editing purpose
    // if (!empty($_GET['edit']) && $_GET['edit'] == 'true') {
    //
    //     $con->query("update data_dictionary set dd_editable='1' where display_page=$_GET[display]");
    //
    //     $con->query("update data_dictionary set dd_editable='11' where display_page='$_GET[display]' and tab_num='$_GET[tabNum]' and table_alias='$_GET[tab]'");
    // }

    $aboveThanTabs = false;
    if($dd_position =='above'){
      $aboveThanTabs =true;
    }
    if (empty($_GET['tabNum'])) {

        $rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page' AND table_type NOT REGEXP 'header|banner|slider|content|url|text|subheader|image|icon' and tab_num REGEXP '^[0-9]+$' AND tab_num >'0' order by tab_num");
        $row = $rs->fetch_assoc();
        $_GET['tabNum'] = $row['tab_num'];
    }


    /*     * *****************
     * *****************************************
     * *****************************************************************************
     * **************
     * Displaying contents of the page Without tabs
     * ***********************
     * ********************************************************
     * *******************************************************************************
     * ****************************************************************************
     *
     */
    if ($tab_status == 'true') {
      if($aboveThanTabs){
        $rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page'  AND dd_component_location='above' order by tab_num");
      }else{

          $rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page'  AND (dd_component_location IS NULL OR dd_component_location!='above') and tab_num REGEXP '^[0-9]+$' AND tab_num >'0' AND table_type NOT REGEXP 'header|subheader' order by $list_sort");
      }
      while ($row = $rs->fetch_assoc()) {
          if(!isAllowedToShowByPrivilegeLevel($row)){
            continue;
          }
    			switch(true){
    				case $row['table_type'] == 'slider':
    					ShowTableTypeSlider($row['display_page'],$row['tab_num']);
    					break;
    				case $row['table_type'] == 'banner':
    					ShowTableTypeBanner($row['display_page'],$row['tab_num']);
    					break;
            // case 'p_banner':
            //     ShowTableTypeParallaxBanner($row['display_page'],$row['tab_num']);
            //     break;
    				case $row['table_type'] == 'url':
    					ShowTableTypeURL($row['display_page'],$row['tab_num']);
    					break;
            case $row['table_type'] == 'content':
    					ShowTableTypeContent($row['display_page'],$row['tab_num']);
    					break;
    				case $row['table_type'] == 'image':
    					ShowTableTypeImage($row['display_page'],$row['tab_num']);
    					break;
    				case $row['table_type'] == 'icon':
    					ShowTableTypeIcon($row['display_page'],$row['tab_num']);
    					break;
            default:
    					/////display_content.php////
    					display_content($row);
    					break;
    			}

        }
    } else {
        /* ******************
         * *****************************************
         * *****************************************************************************
         * **************
         * Displaying contents of the page TABS
         * ***********************
         * ********************************************************
         * *******************************************************************************
         * ****************************************************************************
         *
         */

        if ($tab_status == 'bars') {
            $rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.display_page='$display_page' and data_dictionary.tab_num='$tab_num'    order by field_dictionary.display_field_order");

            $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.display_page='$display_page' and tab_num='$tab_num'    order by field_dictionary.display_field_order";
            $_SESSION['mydata'] = $table_alias . " " . $display_page;
            $rs2 = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.display_page='$display_page'  and tab_num='$tab_num'  order by field_dictionary.display_field_order");
        } else {
            $rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.display_page='$display_page' and tab_num='$_GET[tabNum]'  order by field_dictionary.display_field_order");

            $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.display_page='$display_page' and tab_num='$_GET[tabNum]'   order by field_dictionary.display_field_order";

            $rs2 = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$table_alias' and data_dictionary.display_page='$display_page'  and tab_num='$_GET[tabNum]'  order by field_dictionary.display_field_order");
        }
        $row1 = $rs->fetch_assoc();
        $form_open_for_edit = false;
        $dd_EditAbleHaveValue2  = false;
        $hide_update_cancel = false;
        $DD_EDITABLE = $row1['dd_editable'];
        $DD_EDITABLE_bit1 = $DD_EDITABLE[0];
        if(is_null($DD_EDITABLE[1]) || empty($DD_EDITABLE[1])){
          $DD_EDITABLE[1] = '0';
        }
        $DD_EDITABLE_bit2 = $DD_EDITABLE[1];
        $row1['real_dd_editable'] = $row1['dd_editable'];
        $row1['dd_editable'] = $DD_EDITABLE_bit1;
        if($row1['dd_editable']=='2'){
          $row1['dd_editable'] = '11';
        }else if($row1['dd_editable']=='3'){
          $row1['dd_editable'] = '11';
          $hide_update_cancel = true;
        }

        if($DD_EDITABLE_bit2=='2'){
          $dd_EditAbleHaveValue2  = true;
        }else{
          unset($_SESSION['link_in_case_of_DDetiable_2']);
        }

        if(isset($_SESSION['form_open_for_edit_DD']) && $_SESSION['form_open_for_edit_DD']==$row1['dict_id']){
          $form_open_for_edit = true;
          $row1['dd_editable']='11';
          unset($_SESSION['form_open_for_edit_DD']);
          unset($_SESSION['form_open_for_edit']);
        }

        $show_with_edit_button = false;
        if(isset($_SESSION['show_with_edit_button']) && $_SESSION['show_with_edit_button_DD']==$row1['dict_id']){
          $show_with_edit_button = true;
          $row1['dd_editable']='1';
          unset($_SESSION['show_with_edit_button']);
          unset($_SESSION['show_with_edit_button_DD']);
        }

    $addUrlInner = getRecordAddUrlInner($row1);

        $_SESSION['list_pagination'] = $row1['list_pagination'];
    $tableType = trim(strtolower($row1['table_type']));
        ///////// for displaying image container
        $image_display = 'true';

        /* profile-image
          if (trim($row1['table_type']) == 'profile-image') {

          $_SESSION['profile-image'] = 'profile-image';
          } else {

          unset($_SESSION['profile-image']);
          } */


        ####NEW 3 PARAM BUTTON PARAMETER FROM DD.view_operations|DD.edit_operations#######STARTS########################################################################################

        ##Debuging which buttons are in use:
        $debug = false;##True will display texts in the button area.
        if ($editable == 'true') {
            if (( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id']) ) || $_GET['edit'] == 'true' || !empty($_GET['addFlag']) ) {

                $operationsVarArray = array();
                $operation = '';

                $defaultOptions = getDefaultListViewExtraOptions($con,$row['display_page']);

                ##DD.edit_operation
                if ( ($row1['dd_editable'] == 11 || $row1['dd_editable'] == 1) && $row1['page_editable'] == 1)
                {
                    $operation = 'edit_operations';

                    if(!empty(trim($row1['edit_operations']) ) ){
                        $operationsVarArray = getOperationsData($row1['edit_operations'], 'edit_operations');
                    /*Code Change Start Task ID 5.6.4*/
                    }else{
                        $operationsVarArray = getOperationsData($defaultOptions['edit_operations'], 'edit_operations');
                    }
          /*Code Change End Task ID 5.6.4*/
                }
                else if ($row1['dd_editable'] !== 11 || $row1['page_editable'] == 0) ##DD.view_operation
                {

                    $operation = 'view_operations';

                    if(!empty(trim($row1['view_operations']) ) ){
                        $operationsVarArray = getOperationsData($row1['view_operations'], 'view_operations');
                    /*Code Change Start Task ID 5.6.4*/
                    }else{
                        $operationsVarArray = getOperationsData($defaultOptions['view_operations'], 'view_operations');
                    }
                    /*Code Change Start Task ID 5.6.4*/
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
        }
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
            $updateSaveButton = "<input type='submit'  value='" . formSave . "' class='btn btn-primary update-btn' /> &nbsp;";
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

        ##CUSTOM FUNCTION BUTTON##
    generateCustomFunctionArray($customFunctionArray); // in codeCommonFunction.php

        ####NEW 3 PARAM BUTTON PARAMETER FROM DD.view_operations|DD.edit_operations#######ENDS##########################################################################################


        /*         * *****************
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

        $css_style = $row1['dd_css_code'];
    $userPrivilege = false;
    // $userPrivilege = isAllowedToShowByPrivilegeLevel($row1);
    if(itemHasPrivilege($row1['dd_privilege_level'])){
      $userPrivilege = true;
    }
    if(!itemHasVisibility($row1['dd_visibility'])){
      $userPrivilege = false;
    }
    if(loginNotRequired()){
      $userPrivilege = true;
    }
        if ($userPrivilege === true) {
          ////adding class if form is not for editing purpose
          $page_editable = true;

        if ($row1['page_editable'] == 0 && trim($row1['table_type']) != 'transaction') {
          $page_editable = false;
          if (!empty($row1['dd_css_class'])){
            $dd_css_class ='page_not_editable '. $row1['dd_css_class'];
          } else {
            $dd_css_class = 'page_not_editable';
          }
        } elseif ($row1['page_editable'] == 2) {
          $page_editable = false;
          if (!empty($row1['dd_css_class'])){
            $dd_css_class = 'profile_page '. $row1['dd_css_class'];
          } else {
            $dd_css_class = 'profile_page';
          }
        }else {
          if (!empty($row1['dd_css_class'])){
            $dd_css_class ='simple_edit_page '. $row1['dd_css_class'];
          } else {
            $dd_css_class = 'simple_edit_page';
          }
        }

        if ($row1['database_table_name'] == $_SESSION['select_table']['database_table_name'])
          $_SESSION['search_id'] = $_SESSION['uid'];
        else if (trim($row1['table_type']) == 'child') {

          $_SESSION['search_id'] = $_SESSION['parent_value'];
        } else
          $_SESSION['search_id'] = $_SESSION['uid']; /// for displaying one user


          /* if (isset($_GET['search_id']) && !empty($_GET['search_id'])) {

            // $_SESSION['search_id'] = $_GET['search_id'];
          } */

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

        /*             * ****** for update or ADD *** */



        if ($row1['dd_editable'] == '11') {



          $_SESSION['dict_id'] = $row1['dict_id'];

          //$_SESSION['table_alias_image'] = $row1['table_alias'];

          if (!empty($_GET['search_id']))
            $_SESSION['search_id2'] = $_GET['search_id'];
          else
            $_SESSION['search_id2'] = $_SESSION['search_id'];

          $_SESSION['update_table2']['database_table_name'] = $_SESSION['update_table']['database_table_name'];

          $_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];


          if (trim($row1['table_type']) == 'parent') {


            $_SESSION['update_table2']['child_parent_key'] = (!empty($row1['keyfield']) ? $row1['keyfield'] : $_SESSION['update_table']['keyfield']);


            $_SESSION['update_table2']['child_parent_key_diff'] = (!empty($row1['keyfield']) ? 'true' : 'false');
          }
          //////updating tab_anchor for home pages

          $_SESSION['anchor_tag'] = "#" . trim($row1['tab_name']);

          if ($_GET['checkFlag'] == 'true') {

            if ($_GET['table_type'] == 'child')
              $_SESSION['child_return_url2'] = $_SESSION['child_return_url'];
            else
              $_SESSION['return_url2'] = $_SESSION['return_url'];
          } else {
            $_SESSION['return_url2'] = $actual_link;
          }
          //$_SESSION['table_alias'] = $row1['table_alias'];
        }
        ###FIXES THE SESSION HOLDING INSERT TALBE NAME NOT SETTING UP WHEN DD.dd_editable != 11###
        else
        {
          $_SESSION['dict_id'] = $row1['dict_id'];

          //$_SESSION['table_alias_image'] = $row1['table_alias'];

          if (!empty($_GET['search_id']))
            $_SESSION['search_id2'] = $_GET['search_id'];
          else
            $_SESSION['search_id2'] = $_SESSION['search_id'];

          $_SESSION['update_table2']['database_table_name'] = $_SESSION['update_table']['database_table_name'];

          $_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];
        }


        if (!empty($_GET['ta']) && $_GET['ta'] == $row1['table_alias'] && !empty($_GET['search_id'])) {

          if ($_GET['table_type'] == 'parent') {


            if ($_SESSION['update_table']['child_parent_key_diff'] == 'true') {

              $child_parent_value = getWhere($row1['database_table_name'], array($_SESSION['update_table']['keyfield'] => $_GET['search_id']));


              $_SESSION['parent_value'] = $child_parent_value[0][$_SESSION[update_table][child_parent_key]];
            } else {


              $_SESSION['parent_value'] = $_GET['search_id'];
            }
          }



          $urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $_GET['search_id']);
        } else {

          /*
           * This check is deploying for Transaction feature.
           */

          #Added By Dharmesh 2018-27-10#
          $user_id  = !empty($_GET['search_id'])?$_GET['search_id']:$_SESSION['search_id'];

          if (trim($row1['table_type']) != 'transaction')
            $urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $user_id);

  /*        if (trim($row1['table_type']) != 'transaction')
            $urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $_SESSION['search_id']); */

        }


        /*
         *
         *
          /////////displaying the heading of tab page
         *
         *
         */

        $tab_name = explode("/", $row1['tab_name']);
        if (!empty(trim($tab_name[1]))) {
          echo "<h1 class='tab-header'>$tab_name[1]</h1>";
        }

        /*
         *
         * *************************Generating session to caputure tab_name
         */

        $_SESSION['list_tab_name'] = $tab_name[0];
                $_SESSION['return_url'] = $actual_link;
        /////generating session for capturing parent List tabname
        if ($row1['table_type'] == 'parent') {
          $_SESSION['parent_list_tabname'] = $tab_name[0];

          $_SESSION['parent_url'] = $actual_link;
        }
        /*
         *
         *
         *
         *
         *
         *
         *
         * *****************
         * *******************************
         * BREADCUMB for child lists
         *
         * **********
         * ********************
         * *************************
         *
         *
         *
         *
         */


        if ((( $row1['list_views'] != 'NULL' || $row1['list_views'] != '' ) && trim($row1['table_type']) == 'child' && $_GET['edit'] != 'true' ) && $_GET['addFlag'] != 'true') {
                    if(!empty($_SESSION['child_return_url'])){
                        $backText = str_replace('*', '', $_SESSION['parent_list_tabname']);
                        echo "<br><ol class='breadcrumb'>
                                    <li><a href='$_SESSION[parent_url]&button=cancel' class='back-to-list'>Back To <span>$backText</span> List</a></li>
                                  </ol>";
                    }
        }


        /*             * ******************
         * ************************ADDING Form flags code goes here
         * ************************
         */
        if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true' && $_GET['tabNum'] == $row1['tab_num'] && $_GET['tab'] == $row1['table_alias']) {

          if (empty($save_add_array) ) {
            unset($_SESSION['save_add_url']);
          }

					$dd_css_class = $row1['dd_css_class'];
          /***
           * ADDING BREADCRUMB FOR PARENT/NORMAL LISTS/PAGES
           *
           * Short solution for back to home page
           */
          generateBreadcrumbsAndBackPageForAdd($row1,$onePage=false); // in codeCommonFunction.php

          $_SESSION['dict_id'] = $row1['dict_id'];

          if (!empty($_GET['search_id']))
            $_SESSION['search_id2'] = $_GET['search_id'];
          else
            $_SESSION['search_id2'] = $_SESSION['search_id'];


          $_SESSION['update_table2']['database_table_name'] = $_SESSION['update_table']['database_table_name'];

          $_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];


          if ($_GET['checkFlag'] == 'true') {
            ###THIS IS USED FOR ADD FORM DISPLAY WHICH I WILL MODIFY FOR THE addimport UPLOAD FORM FIELDS################
            echo "<form action='$_SESSION[add_url_list]&action=add' method='post' id='user_profile_form' enctype='multipart/form-data' class='shivgre-checkFlag-true $dd_css_class' style='$css_style'><br>";
          } else {
            $_SESSION['return_url2'] = $actual_link;

            echo "<form action='?action=add&tabNum=$_GET[tabNum]' method='post' id='user_profile_form' enctype='multipart/form-data' class='shivgre-checkFlag-false $dd_css_class' style='$css_style'><br>";
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

					$actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]";

					$cancelButton = "<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>";
					if(in_array(trim(strtolower($row1['table_type'])),['login','signup','forgotpassword','reset_password','change_password']) || $hide_update_cancel){
						$cancelButton = "";// empty
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

					####addimport FORM FIELDS#######GET THE I | P for import from file or PROMPT#######
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
						}//// end of while loop
						//if ($_GET['checkFlag'] == 'true') {
	//                }





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
					if (( ( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || $_GET['edit'] == 'true') && $row1['table_type'] != 'content') {

						$row1['table_type'] = trim(strtolower($row1['table_type']));
						switch($row1['table_type']){
							case 'child':
								$table_type = 'child';
								break;
							case 'login':
								$table_type = 'login';
								break;
							case 'signup':
								$table_type = 'signup';
								break;
							case 'forgotpassword':
								$table_type = 'forgotpassword';
								break;
							case 'reset_password':
								$table_type = 'reset_password';
								break;
							case 'change_password':
								$table_type = 'change_password';
								break;
							default:
								$table_type = 'parent';
								break;
						}
						/*
						 *
						 *
						 *
						 * short solution for now to add separate fffr sytling for FFFR edit page.
						 *
						 *
						 *
						 *
						 *
						 */

						if (isset($_SESSION['return_url']) || isset($_SESSION['child_return_url']) && $_GET['checkFlag'] == 'true') {

							echo "<form action='?action=update&checkFlag=true&tabNum=$_GET[tabNum]&table_type=$table_type' method='post' id='user_profile_form' enctype='multipart/form-data' class='$dd_css_class' style='$css_style'><br>";
						} else {
							echo "<form action='?action=update&tabNum=$_GET[tabNum]&table_type=$table_type' method='post' id='user_profile_form' enctype='multipart/form-data' class='$dd_css_class' style='$css_style'><br>";
						}


						///// To show image uploader buttons

						/***
						 * ADDING BREADCRUMB FOR PARENT/NORMAL LISTS/PAGES
						 *
						 * Short solution for back to home page
						 */
						generateBreadcrumbsAndBackPage($row1,$primary_key,$onePage=false); // in codeCommonFunction.php
						##VIEW OPERATION CUSTOM BUTTONS
						if($operation == 'view_operations')
						{
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
                                    //  $link_to_return = $_SESSION['return_url'];
                                    // }

                                        if($dd_EditAbleHaveValue2){
                                          $_SESSION['link_in_case_of_DDetiable_2'] = $link_to_return;
                                        }

                    $actual_link = $link_to_return;
                    //   $cancel_value = formCancel;
                  }

                  $actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]";

                  $cancelButton = "<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>";

                  if(in_array($table_type,['login','signup','forgotpassword','reset_password','change_password']) || $hide_update_cancel){
                    $cancelButton = "";// empty
                  }
                  if ($tab_status != 'bars') {

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
                }/// if for submit and cancel ends here
                // profile-image }
              }
              if ($row1['dd_editable'] == 11 && $row1['page_editable'] == 1) {
                echo "<div style='clear:both'></div><hr>";
              }
            }


            /* *************************************************** */
            /* *************************************************** */
            /* *************************************************** */
            /* *************************************************** */


            if ($row1['dd_editable'] == 1 && $row1['page_editable'] == 1) {
                echo "<button type='button' class='edit-btn btn btn-default pull-right' id='$row1[dict_id]'>" . EDIT . "</button>";
              $image_display = 'false';
            }

            if (isset($_GET['id'])) {
              $urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $_GET['id']);
            }

            while ($row = $rs2->fetch_assoc()) {
              if(in_array($table_type,['login','signup','forgotpassword','reset_password','change_password'])){
                $row['format_type'] =  trim($row['format_type']);
                if($row['format_type'] == 'email'){
                  $_SESSION['user_field_email'] = $row['generic_field_name'];
                }
                if($row['format_type'] == 'uname'){
                  $_SESSION['user_field_uname'] = $row['generic_field_name'];
                }
                if($row['format_type'] == 'password'){
                  $_SESSION['user_field_password'] = $row['generic_field_name'];
                }
                if($row['format_type'] == 'confirm_password'){
                  $_SESSION['user_field_confirm_password'] = $row['generic_field_name'];
                }
                if($row['format_type'] == 'old_password'){
                  $_SESSION['user_field_old_password'] = $row['generic_field_name'];
                }
                $urow = array();
              }

              $row['dd_editable'] = $row['dd_editable'][0];
              if($row['dd_editable']=='2' || $row['dd_editable']=='3'){
                $row['dd_editable'] = '11';
              }
              if($form_open_for_edit){
                $row['temp_dd_editable'] = '11';
              }else if($show_with_edit_button){
                $row['temp_dd_editable'] = '1';
              }
              formating_Update($row, $method = 'edit', $urow, $image_display, $page_editable);
            }//// end of while loop
          } else {
            //// fetching child list
            // if ($row1['list_views'] == 'NULL' || $row1['list_views'] == '') {
            /////////////
            ////////////////
            //  echo "<form action='?action=update&tabNum=$_GET[tabNum]' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";
            // Child_List($qry);
            // } else {

            if (trim($row1['table_type']) == 'content') {


              if (strpos(trim($row1['description']), "ttp://")) {

                $url = trim($row1['description']);

                echo "<iframe src='$url'></iframe>";
              } else {
                echo "<div class='$row1[list_style]'>$row1[description]</div>";
              }
            } else {
              #echo ("INSIDE FD RECORD.PHP called list_display()<br> ");
  //                        echo "<pre>";

              list_display($qry, $row1['tab_num']); //// list displays

              echo "<div style='clear:both'></div>";
            }
  // }
          }
          /// formating ends here  ///

          if ($editable == 'true') {
            if (( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || $_GET['edit'] == 'true') {
              // if (empty($_SESSION['profile-image'])) {
              ///when edit form is not list
              //  $cancel_value = 'Cancel';

              if ($row1['dd_editable'] == 11 && $row1['page_editable'] == 1) {

                if ($_GET['checkFlag'] == 'true') {

                  if ($_GET['table_type'] == 'child'){
                                        $link_to_return = $_SESSION['child_return_url'];
                                    }
                  else
                    $link_to_return = $_SESSION['return_url'];

                                    if(empty($link_to_return)){
                                        $link_to_return = $_SESSION['return_url'];
                                    }
                  $actual_link = $link_to_return;

                  //$cancel_value = 'Cancel';
                }

                $actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]";

                //if( $row1['dd_editable'] != 0 ){

                $cancelButton = "<a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>";
                if(in_array($table_type,['login','signup','forgotpassword','reset_password','change_password']) || $hide_update_cancel){
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
      //}
            ////////page privilege if true
        } else {
            echo "<h3 style='color:red'>You don't have enough privilege to view contents</h3>";
            ///page privilege if its false
        }

    }//else ends here where tab_num=0 is not part of dd->display_page
}
