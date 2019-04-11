<?php
/*
 *
 * function list_display($qry, $tab_num = 'false', $tab_anchor = 'false')
 * ***************************
 *
 *  function listViews($listData, $table_type, $target_url, $imageField, $listRecord,
 *  $keyfield, $target_url2, $tab_anchor, $user_field, $list_select_arr)
 */

function list_display($qry, $tab_num = 'false', $tab_anchor = 'false') {
    $con = connect();
    $rs = $con->query($qry);
    $row = $rs->fetch_assoc();
    $listCheck = 'yes';
    $list_sort = array_filter( array_map('trim', explode(',', $row['list_sort'])) );

    ####SET SESSION VAR FOR HOLDING TABLE_TYPE='parent' data_dictionary.`keyfield` and its relevant value for that keyfield column if it exists in the table#########
    $keyfield = $row['keyfield'];
    if (strtolower(trim($row['table_type']) ) == 'parent' && !empty($keyfield) )
    {
        $_SESSION['parent_key_value'] = $keyfield;
    }
    else if ( strtolower(trim($row['table_type']) ) !== 'child')
    {
        unset($_SESSION['parent_key_value']);
    }

// **********************************************************************************
//  CJ-NOTE ***
//  Below ... this is BAD coding .... defining internal_table_types as 0,1 ...
//  vague for coding and for other programmers to look at ... we need to change soon
//  using defined constants for different table types  eg ... define("TABLE_TYPE_PARENT",...)
// **********************************************************************************
    ###if table_type="parent" OR table_type= $internal_table_types[0] OR table_type= $internal_table_types[1]
    ###`$internal_table_types` array so `$internal_table_types[0]` == 'USER'` and  `$internal_table_types[1]` == 'PROJECT'`
    ###(adjusting for lower case)


    $tableTypeUppercase = strtoupper(trim($row['table_type']) );
    if (strtolower(trim($row['table_type']) ) == 'child')# || $tableTypeUppercase == $internal_table_types['0'] || $tableTypeUppercase == $internal_table_types['1']
    {

// **********************************************************************************
//  CJ-NOTE ***
// Two comments - see below
// **********************************************************************************

// **********************************************************************************
// 1) it is confusing right now to know where $_SESSION['update_table']['search'] comes from ...
// but it is important - because this may be the critical problem wiith parent-child processing
// **********************************************************************************
        $search_key = $_SESSION['update_table']['search'];
        if(!empty($_REQUEST['search_id'])  && !empty($row['keyfield']) )
        {
            $search_key = $_REQUEST['search_id'];
// **********************************************************************************
// 2) The $row[keyfield]  term below also MAY be the source of the problem
// because it would have been easy or likelyy for the prior coder to capture the wrong value beforehand
// **********************************************************************************
            $row['list_filter'] = array('list_filter' => $row['list_filter'], 'child_filter' => "$row[database_table_name].$row[keyfield]='$search_key'");
        }
        //      if(empty($row['list_filter']) && $row['parent_table'] == 'product' )
        //	{
        //            $row['list_filter'] = "projects=$row[keyfield]";#'projects=DD.keyfield' from the child dict_id
        //	}
    }
    else
        $search_key = $_SESSION['search_id'];


    if (count($list_sort) == 1 && !empty($row['list_sort'])) {
        $list = get_multi_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $search_key, $row['list_filter'], $list_sort[0], $listCheck);
    } else {
        $list = get_multi_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $search_key, $row['list_filter'], $listSort = 'false', $listCheck);
    }

    $listView = trim($row['list_views']);
	//Added BY Dharmesh 2018-10-07
	$list_views = listvalues($row['list_views']);
	//Code End//
	//Added BY Dharmesh 2018-10-12

    $list_pagination = listpageviews($row['list_pagination']);
	if (!isset($list_pagination['itemsperpage']) || empty($list_pagination['itemsperpage'])){
		$list_pagination['itemsperpage'] = 9 ;// set default
	}
	if (!isset($list_pagination['totalpages']) || empty($list_pagination['totalpages'])){
		$list_pagination['totalpages'] = "#".( ceil($list->num_rows/$list_pagination['itemsperpage']) ) ;
	}else{
        $list_pagination['totalpages'] = '#'.$list_pagination['totalpages'];
    }
	//Code End//

    /*
     * @function listExtraOptions
     *
     * Fetching list_extra_options
     */

    #array('list_operations' => $row['list_operations'], 'edit_operations' => $row['edit_operations'], 'view_operations' => $row['view_operations'] );

    ##CHECK DD.list_select if empty then its for single page/profile view then we check DD.dd_editable=11 for page editable(dd_editable=1 for view only). If list_select is not empty its for a list page.
    /*Code Change Start Task ID 5.6.4*/
	/*if(!empty($row['list_select']) )
        $buttonOptions = $row['list_operations'];
    else if($row['dd_editable'] == '11' )
        $buttonOptions = $row['edit_operations'];
    else if($row['dd_editable'] == '1' )
        $buttonOptions = $row['view_operations'];*/

	if(!empty($row['list_select']) ){
			if((empty($row['list_operations'])) || ($row['list_operations'] == NULL)){
				$sql1 = $con->query("SELECT list_operations FROM data_dictionary where data_dictionary.display_page='$row[display_page]' and data_dictionary.table_alias='default'");
				$list_operations = $sql1->fetch_assoc();
				$buttonOptions = $list_operations['list_operations'];
				//$displaypage = $row['display_page'];
				//$buttonOptions = check_dd_defaults($displaypage);
			}else{
				$buttonOptions = $row['list_operations'];
			}
    }else if($row['dd_editable'] == '11' ){
		if((empty($row['edit_operations'])) || ($row['edit_operations'] == NULL)){
				$sql1 = $con->query("SELECT edit_operations FROM data_dictionary where data_dictionary.display_page='$row[display_page]' and data_dictionary.table_alias='default'");
				$edit_operations = $sql1->fetch_assoc();
				$buttonOptions = $edit_operations['edit_operations'];
		}else{
				$buttonOptions = $row['edit_operations'];
		}
		//$buttonOptions = $row['edit_operations'];
    }else if($row['dd_editable'] == '1' ){
       	if((empty($row['view_operations'])) || ($row['view_operations'] == NULL)){
				$sql1 = $con->query("SELECT view_operations FROM data_dictionary where data_dictionary.display_page='$row[display_page]' and data_dictionary.table_alias='default'");
				$view_operations = $sql1->fetch_assoc();
				$buttonOptions = $view_operations['view_operations'];
		}else{
				$buttonOptions = $row['view_operations'];
		}
		//$buttonOptions = $row['view_operations'];
	}
	/*Code Change End Task ID 5.6.4*/

    $ret_array = listExtraOptions($row['list_extra_options'], $buttonOptions);
    global $popup_menu;

    $popup_menu = array(
		"popupmenu" => $ret_array['popupmenu'],
		"popup_delete" => $ret_array['popup_delete'],
		"popup_copy" => $ret_array['popup_copy'],
		"popup_add" => $ret_array['popup_add'],
		"popup_openChild" => $ret_array['popup_openChild']
	);

    if (count($list_sort) > 1 && ($listView == 'boxView' || $listView == 'boxView')) { ?>
        <div class="col-6 col-sm-6 col-lg-6 sortby">
            <h3>Sort by </h3>
            <span>
                <div class="btn-group select2">
                    <button type="button" class="btn btn-danger main-select2" id="sort_popular_users_value">
						---Select----
					</button>
                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
						<span class="caret"></span>
						<span class="sr-only">Toggle navigation</span>
					</button>
                    <ul class="dropdown-menu" role="menu" id="sort_popular_users">
                        <?php
                        $tbl = $row['table_alias'];
                        if (!empty($_GET['sort']) && empty($_GET['orderFlag'])) {
                            $orderFlag = 'down';
                        } else if (!empty($_GET['sort']) && $_GET['orderFlag'] == 'down') {
                            $orderFlag = 'up';
                        } else if (!empty($_GET['sort']) && $_GET['orderFlag'] == 'up') {
                            $orderFlag = 'down';
                        }
                        $order = "<span class='glyphicon glyphicon-chevron-up'></span>";
                        foreach ($list_sort as $val) {
                            if ($val != $_GET['sort']) {
                                $order = "<span class='glyphicon'></span>";
                            } else {

                                switch ($orderFlag) {
                                    case 'up':
                                        $order = "<span class='glyphicon glyphicon-chevron-up'></span>";
                                        break;
                                    case 'down':
                                        $order = "<span class='glyphicon glyphicon-chevron-down'></span>";
                                        break;
                                    default:
                                        $order = "<span class='glyphicon'></span>";
                                }
                            }
                            $q = $con->query("select field_label_name from field_dictionary where generic_field_name='$val' and table_alias='$tbl'");
                            $fdField = $q->fetch_assoc();
                            echo "<li id='sort-li' data-value='$val'>
                                    <a>$fdField[field_label_name]$order</a>
                                </li>";
                        }
                        ?>
                    </ul>
                </div>
            </span>
        </div>
<?php
    } ////list sort if ends here

    /********* setting DisplayView icons **** *//////
    $list_select = trim($row['list_select']);
	$table_type = trim($row['table_type']);
	$list_select_arr = getListSelectParams($list_select);
	$addRecordUrl = getRecordAddUrl($list_select_arr,$table_type);
    $list_style = $row['dd_css_class'];
    $keyfield = firstFieldName($row['database_table_name']);
    $table_name = trim($row['database_table_name']);
    $list_fields = trim($row['list_fields']);
    $dict_id = $row['dict_id'];
    //////for boxView
    // $boxView_dd = $row;
    /*
     * getting image field name from FD
     */
    $fdRS = $con->query("SELECT generic_field_name FROM `field_dictionary` WHERE table_alias='$row[table_alias]' and format_type like '%image%' limit 1");
    $imageField = $fdRS->fetch_assoc();

    /****** checking list_fields **** */
    if (!empty($list_fields)) {
        if (preg_match('/^\d+\.?\d*$/', $row['list_fields'])) {
            if ($tab_num == 'false') {
                $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$_GET[tabNum]'  order by field_dictionary.display_field_order LIMIT " . $row['list_fields'];
            } else {
                $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$tab_num'  order by field_dictionary.display_field_order LIMIT " . $row['list_fields'];
            }
        } else {
            $fields = array_filter( array_map('trim',explode(",", $row['list_fields'])) );
            $fieldsFinal = '';
            foreach ($fields as $f) {
                if (empty($fieldsFinal))
                    $fieldsFinal = "'" . $f . "'";
                else
                    $fieldsFinal = "'" . $f . "' , " . $fieldsFinal;
            }
            if ($tab_num == 'false') {
                $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and  tab_num='$_GET[tabNum]'  and field_dictionary.generic_field_name IN(  $fieldsFinal ) order by field_dictionary.display_field_order";
            } else {
                $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and  tab_num='$tab_num'  and field_dictionary.generic_field_name IN(  $fieldsFinal ) order by field_dictionary.display_field_order";
            }
        }
    } else {////when list field is empty
        if ($tab_num == 'false') {
            $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$_GET[tabNum]'  order by field_dictionary.display_field_order";
        } else {
            $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$tab_num'  order by field_dictionary.display_field_order";
        }
    }
    //exit($tbQry);
    ?>
	<script>
	function clearFunction() {
		document.getElementById("list-form").reset();
		/*Palak Task 5.4.77 Changes Start
		var table = $('#example').DataTable();*/
		var table = $('.clear1').DataTable();
		/*Palak Changes End*/
		table.search( '' ).columns().search( '' ).draw();
	}
	</script>
    <div class="row" id="popular_users" >
        <form name="list-form" id="list-form" action="ajax-actions.php" method="post">
			<?php if(!empty(array_filter($ret_array))) { ?>
            <div id='checklist-div'>
                <?php
                if ($list_views['checklist'] == 'true') {
                    echo "  <input type='hidden' name='checkHidden' id='checkHidden'>
                            <input type='checkbox' id='selectAll'> &nbsp;<strong>Select All </strong>
                        &nbsp;&nbsp;";
				}


				/// ADD BUTTON
                if (isset($ret_array['add_array']) && !empty($ret_array['add_array'])) { ?>
                    <button type="submit" class="btn action-add <?php echo $ret_array['add_array']['style'] ; ?>" name="add" onclick="window.location.href='<?php echo $addRecordUrl.$_SESSION['anchor_tag']; ?>'"><?php echo $ret_array['add_array']['label'] ; ?></button>
                <?php
				}
				if ($list_views['checklist'] == 'true') {
                    /// setting for  delete button
                    if (isset($ret_array['del_array']) && !empty($ret_array['del_array'])) {

                        echo "<button type='submit' class='btn action-delete " . $ret_array['del_array']['style'] . "' name='delete' >" . $ret_array['del_array']['label'] . "</button>";
                    }

                    //// setting for  copy button
                    if (isset($ret_array['copy_array']) && !empty($ret_array['copy_array'])) {

                        echo "<button type='submit' class='btn action-copy " . $ret_array['copy_array']['style'] . "' name='copy' >" . $ret_array['copy_array']['label'] . "</button>";
                    }
                    echo "";
                }/// checklist if ends here


                ##CUSTOM FUNCTION BUTTON##
                if (!empty($ret_array['custom_function_array']) ) {

                    foreach($ret_array['custom_function_array'] as $keyCustomFunction => $customFunction)
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

//                            echo "<font color=red>\$customFunction['params']:$customFunction[params] ::::::\$customFunctionThirdParameter:$customFunctionThirdParameter</font><br>";
//                            echo "<pre>";
//                            print_r($_SESSION['addImportParameters']);
//                            echo "</pre>";

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
                                <?php
                                if(!empty($_SESSION['errorsAddImport']) || !empty($_SESSION['SuccessAddImport']) )
                                {
                                    echo "$('#addimportStatusModal').modal('show');";
                                }
                                unset($_SESSION['SuccessAddImport'], $_SESSION['errorsAddImport']);
                                ?>


                                $('#list-form').on('click', '.actionImportButton, .importPromptAction', function(event){
                                    //$('#addimportModal').modal('hide');
//                                    if (confirm( $(this).text() ) == true) {
//
//                                        $.ajax({
//                                            method: "POST",
//                                            url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
//                                            data: {function: $(this).data('function_name'), params: $(this).data('function_params'), action: 'custom_function'}
//                                        })
//                                        .done(function (msg) {
//                                            alert('Success');
//                                            //location.reload();
//                                        });
//
//                                    } else {
//                                        event.stopImmediatePropagation();
//                                    }
                                });
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
                }

                /// select checkbox div ends here
                ?>
            </div>
			<?php } ?>
            <?php

            /*
             *
             * ********
             * ******************DD->list_select values
             */


			if ($list->num_rows == 0) {
				$nav = $con->query("SELECT * FROM navigation where target_display_page='$_GET[display]'");
                $navList = $nav->fetch_assoc();
                /// Extracting action ,when user click on edit button or on list
                if (isset($list_select_arr[0]) && !empty($list_select_arr[0])) {
                    if (count($list_select_arr[0]) == 2) {
                        $target_url = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&ta=" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;
                        /// add button url
                        $_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
                    } else {
                        $target_url = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&ta=" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;
                        /// add button url
                        $_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
                    }
                }
                $_SESSION['return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            }//// if record is zero... ends here

			switch($listView){
				case 'mapView':
					include_once('renderMapView.php');
					renderMapView($row,$tbQry,$list,$qry,$list_pagination,$tab_anchor,$tab_num,$imageField,$ret_array,$mapAddress=false); // renderMapView.php
					break;

				case 'mapAddress':
					include_once('renderMapView.php');
					renderMapView($row,$tbQry,$list,$qry,$list_pagination,$tab_anchor,$tab_num,$imageField,$ret_array,$mapAddress=true); // renderMapView.php
					break;

				case 'boxView':
					include_once('renderBoxView.php');
					renderBoxView($row,$tbQry,$list,$qry,$list_pagination,$tab_anchor,$tab_num,$imageField,$ret_array); // renderBoxView.php
					break;

				default:
					include_once('renderListView.php');
					renderListView($row,$tbQry,$list,$qry,$list_pagination,$tab_anchor); // renderListView.php
					break;
			} ?>
        </form>
    </div>


    <!--####addimport FORM FIELDS#######GET THE I | P for import from file or PROMPT#######-->
    <!--File modal addimport-->
    <div class="modal fade" id="addimportFileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Import from CSV File</h4>
                </div>

                <form action='<?= $_SESSION[add_url_list]; ?>&action=add&actionType=addimport&search_id=<?= $_GET['search_id']; ?>' method='post' id='user_profile_form' enctype='multipart/form-data' class=''>

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

                <form action='<?= $_SESSION[add_url_list]; ?>&action=add&actionType=addimport&search_id=<?= $_GET['search_id']; ?>' method='post' id='user_profile_form' enctype='multipart/form-data' class=''>

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

/*
 * @listViews function
 *
 * give LIST UI and data inside lists
 */

function listViews($listData, $table_type, $target_url, $imageField, $listRecord, $keyfield, $target_url2, $tab_anchor, $user_field, $list_select_arr) {
    /*
     *
     * displaying of image in list
     */
    $tbl_img = $listRecord[$imageField['generic_field_name']];
    $filename = USER_UPLOADS . "" . $tbl_img;
    echo "<a href='" . (!empty($target_url2) ? $target_url2 : "#" ) . "' class='profile-image'>";
    if (!empty($tbl_img) && file_exists($filename)) {
        echo "<img src='" . USER_UPLOADS . "$tbl_img' alt='' class='img-responsive'></a>";
    } else {
        echo "<img src='" . USER_UPLOADS . "NO-IMAGE-AVAILABLE-ICON.jpg' alt='' class='img-responsive'></a>";
    }
	// *************************************
    /*
     * displaying Edit button
     */
    if (!empty($list_select_arr[0][0])) {
        if ($_SESSION['user_privilege'] > 8) {
            echo "<a href='$target_url&edit=true#$tab_anchor' class='btn btn-primary edit' >Edit</a>";
        } else {
            if (!empty($user_field)) {
                //exit($_SESSION['uid']);
                if ($listRecord[$user_field] == $_SESSION['uid']) {
                    echo "<a href='$target_url&edit=true#$tab_anchor' class='btn btn-primary edit' >Edit</a>";
                }
            } else {
//                echo "<a href='$target_url&edit=true#$tab_anchor' class='btn btn-primary edit' >Edit</a>";
            }
        }
    }

    if ($_GET['table_type'] == 'child') {
        $_SESSION['child_return_url'] = $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    } else {
        ////parent link
        $_SESSION['return_url'] = $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

	// *************************************
	// ****  these are the lines that format and display the test inside the Boxview list (cards)
	// we need to refine this ... because not every field needs a <br> after it
	// also we want to tag each field/line with some generic CSS so that
	// later we can (using list_style)  have control over the css formatting of the
	// first line of the text, and successive lines
	$listData = array_filter($listData);

	//  This is the
	echo "<div class='boxView_content list-data'>";
		if(!empty($listData)){
			foreach($listData as $data){
				echo "<div class='boxView_line ".$data['field_style']."'>".$data['field_value']."</div>";
			}
		}
	echo "</div>";
}

///end of listViews function
