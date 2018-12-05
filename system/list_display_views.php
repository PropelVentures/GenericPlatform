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
//how list will know on basis of which key to show the record///
//$_SESSION['update_table']['keyfield'] = 'uid';
//    echo ($qry);
    $rs = $con->query($qry);
    $row = $rs->fetch_assoc();
//    echo "<pre>";
//    print_r($row);
//    var_dump($_REQUEST);
//    print_r($_SESSION);
//    echo "</pre>";
//    die;

//    if ($row['list_filter'] != 'NULL') {
//
//        //$laterChange = $_SESSION['uid'];
//
//        $laterChange = $row['list_filter'];
//    }

    $listCheck = 'yes';

    $list_sort = explode(',', $row['list_sort']);
//echo count($list_sort);die;


    ####SET SESSION VAR FOR HOLDING TABLE_TYPE='parent' data_dictionary.`keyfield` and its relevant value for that keyfield column if it exists in the table#########
    $keyfield = $row['keyfield'];
    #echo "<font color=green>\$keyfield:$keyfield :: row[keyfield] : {$row[$keyfield]}</font><br>";
    if (strtolower(trim($row['table_type']) ) == 'parent' && !empty($keyfield) )
    {
        $_SESSION['parent_key_value'] = $keyfield;
    }
    else if ( strtolower(trim($row['table_type']) ) !== 'child')
    {
        unset($_SESSION['parent_key_value']);
    }

//    echo "<pre>AFTER SESSION<br>";
//    print_r($_SESSION);
//    echo "</pre>";


    ###if table_type="parent" OR table_type= $internal_table_types[0] OR table_type= $internal_table_types[1]
    ###`$internal_table_types` array so `$internal_table_types[0]` == 'USER'` and  `$internal_table_types[1]` == 'PROJECT'`
    ###(adjusting for lower case)
    $tableTypeUppercase = strtoupper(trim($row['table_type']) );
    if (strtolower(trim($row['table_type']) ) == 'child')# || $tableTypeUppercase == $internal_table_types['0'] || $tableTypeUppercase == $internal_table_types['1']
    {
        $search_key = $_SESSION['update_table']['search'];
        if(!empty($_REQUEST['search_id'])  && !empty($_SESSION['parent_key_value']) )
        {
            $search_key = $_REQUEST['search_id'];
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


    //print_r($list->fetch_assoc());die;

    $listView = trim($row['list_views']);
	//echo $row['list_views']; exit;
	//Added BY Dharmesh 2018-10-07
	$list_views = listvalues($row['list_views']);
	//print_r($list_views); exit;
	//Code End//

	//Added BY Dharmesh 2018-10-12
	$list_pagination = listpageviews($row['list_pagination']);
	//pr($list_pagination);
	//Code End//


    /*
     * @function listExtraOptions
     *
     * Fetching list_extra_options
     */

#echo "<font color=green>callling listExtraOptions for list process</font><br/>";

    #array('list_operations' => $row['list_operations'], 'edit_operations' => $row['edit_operations'], 'view_operations' => $row['view_operations'] );

    ##CHECK DD.list_select if empty then its for single page/profile view then we check DD.dd_editable=11 for page editable(dd_editable=1 for view only). If list_select is not empty its for a list page.
    if(!empty($row['list_select']) )
        $buttonOptions = $row['list_operations'];
    else if($row['dd_editable'] == '11' )
        $buttonOptions = $row['edit_operations'];
    else if($row['dd_editable'] == '1' )
        $buttonOptions = $row['view_operations'];

    $ret_array = listExtraOptions($row['list_extra_options'], $buttonOptions);

	$ret_array['pagination'] = ( (isset($list_pagination[0]) && !empty($list_pagination[0]) ) ? $list_pagination[0] : 8);
	
	
	#echo "<pre>";print_r($ret_array);echo "</pre>";
    #die;



    global $popup_menu;

    $popup_menu = array("popupmenu" => $ret_array['popupmenu'], "popup_delete" => $ret_array['popup_delete'], "popup_copy" => $ret_array['popup_copy'], "popup_add" => $ret_array['popup_add'], "popup_openChild" => $ret_array['popup_openChild']);

//echo "<pre>";
//var_dump($popup_menu);
//echo "</pre>"; die;

    if (count($list_sort) > 1 && $listView == 'boxView') {
?>

        <div class="col-6 col-sm-6 col-lg-6 sortby">
            <h3>Sort by </h3>

            <span>
                <div class="btn-group select2">

                    <button type="button" class="btn btn-danger main-select2" id="sort_popular_users_value">
                        ---Select----</button>
                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span>

                    <span class="sr-only">Toggle navigation</span> </button>
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

                        //$orderFlag = $_GET['orderFlag'];

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


    /*     * ******* setting DisplayView icons **** *//////
    /*
      $listView = str_replace('*', '', $listView);

      //print_r($listView);die;

      if (count($listView) != 1) {
      echo "<div class='col-6 col-sm-6 col-lg-6 grid-type'>";

      foreach ($listView as $v) {

      $v = trim($v);

      if ($v == "listView") {

      echo "<span id='listView' class='glyphicon glyphicon-align-right'></span>";
      } else if ($v == "boxView") {

      echo "<span id='boxView' class='glyphicon glyphicon-th-large'></span>";
      } else if ($v == "thumbView") {

      echo "<span id='thumbView' class='glyphicon glyphicon-th-list'></span>";
      }
      }


      echo " <span></span></div>";
      }
     */
    $list_select = trim($row['list_select']);

    $list_style = $row['list_style'];

    $keyfield = firstFieldName($row['database_table_name']);

    $table_type = trim($row['table_type']);

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




    /**     * *** checking list_fields **** */
    if (!empty($list_fields)) {



        if (preg_match('/^\d+\.?\d*$/', $row['list_fields'])) {

            if ($tab_num == 'false') {
                $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$_GET[tabNum]'  order by field_dictionary.display_field_order LIMIT " . $row['list_fields'];
            } else {
                $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$tab_num'  order by field_dictionary.display_field_order LIMIT " . $row['list_fields'];
            }
        } else {

            $fields = explode(",", $row[list_fields]);

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
var table = $('#example').DataTable();
table.search( '' ).columns().search( '' ).draw();
}

</script>


    <div class="row" id="popular_users" >
        <form name="list-form" id="list-form" action="ajax-actions.php" method="post">
            <div id='checklist-div'>
                <?php
                if ($list_views['checklist'] == 'true') {

                    echo "  <input type='hidden' name='checkHidden' id='checkHidden'>
                            <input type='checkbox' id='selectAll'> &nbsp;<strong>Select All </strong>
                        &nbsp;&nbsp;";

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
                /// ADD BUTTON

                if (isset($ret_array['add_array']) && !empty($ret_array['add_array'])) {

                    echo "<button type='button' class='btn action-add " . $ret_array['add_array']['style'] . "' name='add' >" . $ret_array['add_array']['label'] . "</button>";
                }

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
            <br>
            <?php

            /*
             *
             * ********
             * ******************DD->list_select values
             */

            $list_select_sep = explode(';', $list_select);

            foreach ($list_select_sep as $listArray) {

                $list_select_arr[] = explode(",", $listArray);
            }




            if ($list->num_rows == 0) {


                //print_r($list_select_arr);die;


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

            if ($listView != 'boxView') {

                /*
                 *
                 *
                 *
                 * *******
                 * *************DataTables widget code goes here *********
                 * ********
                 * *******************
                 * ****************************
                 *
                 *
                 */

                echo "<input type='button' onclick='clearFunction()' id='test' value='X' class='clearFunction'><table id='example' class='display nowrap compact' cellspacing='0' width='100%'>
                        <thead>
                            <tr class='tr-heading'>
                                <th class='tbl-action'><span style='visibility:hidden;'>12<span></th>";

                $tbRs = $con->query($tbQry);
                ///fetching table headings
                while ($tbRow = $tbRs->fetch_assoc()) {

                    if ($_SESSION['user_privilege'] >= $tbRow[privilege_level] && $tbRow[format_type] != 'list_fragment') {
                        echo "<th>$tbRow[field_label_name]</th>";
                    }
                }
                echo "</tr></thead><tbody>";
            } else if (isset($ret_array['pagination']) && !empty($ret_array['pagination'])) {

                //// BoxView Pagination code inserted here


                echo "

                    <!-- Content div. The child elements will be used for paginating(they don't have to be all the same,
                            you can use divs, paragraphs, spans, or whatever you like mixed together). '-->
                    <div id='content$tab_num'>

                        <!-- the input fields that will hold the variables we will use -->
                        <input type='hidden' class='current_page' />
                        <input type='hidden' class='show_per_page' />
                    ";
            }

            if ($list->num_rows != 0)
            {
			$i=0;
			/* By Shaily Start*/
			$count = 1;
			$limit = $list->num_rows;
			if(isset($list_pagination[1])){
				if(strpos($list_pagination[1],'#') !== false){
					preg_match_all('!\d+!', $list_pagination[1], $limitPage);
					$limit = @$limitPage[0][0] * $list_pagination[0];
				}
			}
			/* By Shaily End*/
            while ($listRecord = $list->fetch_assoc()) {
				/* By Shaily Start*/
				if($count > $limit){
					break;
				}
				/* By Shaily End*/
				foreach($list_pagination as $k=>$v){
				//echo $v[1][0];
					if(strpos($v, '#') !== FALSE)
					{
						preg_match_all('!\d+!',$v, $no_of_pages);
						$no_of_pages = $no_of_pages[0][0];
						break;
					}else {
						$no_of_pages = 0;
					}
				}
				//echo $i;
				if(count($list_pagination)==1) {
					$list_pagination_no = $list_pagination[0];
				}else{
					$list_pagination_no = $list_pagination[0];
				}

				unset($_SESSION['list_pagination']);
				if(!empty($list_pagination_no) && !empty($no_of_pages)){
					$_SESSION['list_pagination'] = array($list_pagination_no,$no_of_pages);
				}elseif(!empty($list_pagination_no && empty($no_of_pages))){
					$_SESSION['list_pagination'] = array($list_pagination_no);
				}

                $rs = $con->query($qry);

                if ($listView == 'boxView') {
                ?>
                    <div class="boxView <?php echo (!empty($list_style) ? $list_style : '') ?>" data-scroll-reveal="enter bottom over 1s and move 100px" >
                <?php
                }///boxview ends here

                        if (!empty($list_select) || $table_type == 'child') {


                            if (strpos($list_select, '()')) {

                                exit('function calls');
                            } else
                            if (strpos($list_select, '.php')) {

                                exit('php file has been called');
                            } else {


                                //    print_r($list_select_arr);die;

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


                                /// Extracting action, when user click on boxView Image of list
                                if (isset($list_select_arr[1][0]) && !empty($list_select_arr[1][0])) {


                                    if (count($list_select_arr[1]) == 2) {
                                        $target_url2 = BASE_URL_SYSTEM . $navList['item_target'] . "?display=" . $list_select_arr[1][2] . "&tab=" . $list_select_arr[1][0] . "&ta=" . $list_select_arr[1][0] . "&tabNum=" . $list_select_arr[1][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&edit=true&fnc=onepage";
                                    } else {

                                        $target_url2 = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[1][2] . "&tab=" . $list_select_arr[1][0] . "&ta=" . $list_select_arr[1][0] . "&tabNum=" . $list_select_arr[1][1] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&edit=true&fnc=onepage";
                                    }
                                }


                                /*
                                 *
                                 * Value putting in table lists
                                 *
                                 * *****
                                 * **********
                                 * ***************
                                 * *********************
                                 * ************************
                                 *
                                 *
                                 *
                                 */
                                if ($listView != 'boxView') {

                                    // $target_url2 = $target_url . "#$tab_anchor";

                                    echo "<tr id='$target_url&edit=true#$tab_anchor' class='boxview-tr'>
                                                <td class='dt-body-center'><!--<a href='$target_url&edit=true#$tab_anchor' title='Edit' class='btn btn-default' style='color: #E6B800' >
                                                <span class='glyphicon glyphicon-edit'></span>
                                            </a>-->";


                                    $checkbox_id = $listRecord[$_SESSION['update_table']['keyfield']];



                                    /*
                                     * displaying checkboxes
                                     * checking in database if checklest is there
                                     */


                                    if ($list_views['checklist'] == 'true') {

                                        echo "<span class='span-checkbox'><input type='checkbox'  name='list[]'  value='$checkbox_id' class='list-checkbox tabholdEvent' style='margin:right:6px;'/></span>";

                                        echo "<input type='hidden' name='dict_id[]' value='$dict_id' >";
                                    }



                                    /*
                                     * Putting Delete icon on left/right side of lists
                                     */

                                    /*
                                      if (isset($ret_array['single_delete_array']) && !empty($ret_array['single_delete_array'])) {

                                      $sing_del_style = $ret_array['single_delete_array']['style'];

                                      echo " <a  class='glyphicon glyphicon-remove list-del $sing_del_style' title='Delete' style='color:#FF3300' id='$checkbox_id' name='$dict_id'>

                                      </a>";
                                      }
                                     *
                                     */

                                    /*
                                     * Putting Copy icon on left/Right side of lists



                                      if (isset($ret_array['single_copy_array']) && !empty($ret_array['single_copy_array'])) {

                                      $sing_del_style = $ret_array['single_copy_array']['style'];

                                      echo "
                                      <a class='btn btn-default' title='Copy' >
                                      <span class='list-copy $sing_del_style' title='Delete' id='$checkbox_id' name='$row[dict_id]'>
                                      <img src='" . BASE_IMAGES_URL . "copy.ico' width='15' height='15'>
                                      </span>
                                      </a>
                                      ";

                                      }

                                     */

                                    /////////// for popup menu handling span tag goes here ****************

                                    echo "<span class='list-del' id='$checkbox_id' name='$dict_id' ></span>";
                                    ////data td ends here

                                    echo "</td>";
                                }
//                                        if ($listView != 'boxView') {
//
//
//                                        }

                                if ($listView == 'boxView') {

                                    /*
                                     * @while loop
                                     * get FD info and put data into @lisdata array
                                     */

                                    $listData = array();


                                    while ($row = $rs->fetch_assoc()) {

                                        $listData[] = strip_tags($listRecord[$row[generic_field_name]]);
                                    }

                                    /*
                                     * setting up an array to pass to listViews function
                                     */


                                    //$listPara = array("listData" =>$listData, "table_type" =>$table_type, "target_url" );

                                    /*
                                     * @listViews function
                                     *
                                     * give bOX LIST UI and data inside lists
                                     */



                                    listViews($listData, $table_type, $target_url, $imageField, $listRecord, $keyfield, $target_url2, $tab_anchor, $ret_array['users'], $list_select_arr); ///boxview ends here
                                } else {
                                    /*
                                     *
                                     *
                                     * ******
                                     * *************************
                                     * ******************************
                                     * ***********************************FETCHING DATA AND PUTING IN CORRESPONDING TDS
                                     * **************
                                     * *************************
                                     * ******************************************************************
                                     *
                                     */
                                    /////table view starts here
                                    //fetching data from corresponding table

                                    while ($row = $rs->fetch_assoc()) {
										//if($listView!='BoxView') {

										//}
                                        $fieldValue = $listRecord[$row[generic_field_name]];


                                        if (!empty($row[dropdown_alias])) {


                                            $fieldValue = dropdown($row, $urow = 'list_display', $fieldValue);
                                        }



                                        //will temprory truncate

                                        $fieldValue = substr($fieldValue, 0, 30);

                                        if ($_SESSION['user_privilege'] >= $row[privilege_level] && $tbRow[format_type] != 'list_fragment')
                                            echo "<td>$fieldValue</td>";

									}
                                    if ($table_type == 'child') {

                                        $_SESSION['child_return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                    } else {

                                        $_SESSION['return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                    }
                                }
                            }///end of else if
                        }/// end of mainIF

                if ($listView == 'boxView') {
                ?>
                    </div>
                <?php
                } else {
                    echo "</tr>";
                }
				$count++;
            }//// end of while loop
            }

            if ($listView != 'boxView') {
                echo "</tbody></table> ";
/*             } else if (isset($ret_array['pagination']) && !empty($ret_array['pagination'])) {

                /*
                 *
                 * Pagination Function goes here
                 */

             /*   echo boxViewPagination($ret_array['pagination'], $tab_num, $list_select_arr);
            } */

			//Added By Dharmesh 2018-10-10//
			} else {
				if (!isset($list_pagination[0]) || empty($list_pagination[0])){
					$list_pagination[0] = 8 ;
				}
				if (!isset($list_pagination[1]) || empty($list_pagination[1])){
					$list_pagination[1] = "#".( ceil($list->num_rows/$list_pagination[0]) ) ;
				}
				//echo "<pre>";print_r($list_pagination);echo "</pre>";
                /*
                 *
                 * Pagination Function goes here
                 */

                echo boxViewPagination($list_pagination, $tab_num, $list_select_arr);
            }
			//Code End//
			//echo $ret_array['pagination'];

?>

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

	//$listRecord =array_slice($listRecord, 0, 50, true);


    $tbl_img = $listRecord[$imageField['generic_field_name']];


    $filename = USER_UPLOADS . "" . $tbl_img;




    echo "<a href='" . (!empty($target_url2) ? $target_url2 : "#" ) . "' class='profile-image'>";
    if (!empty($tbl_img) && file_exists($filename)) {

        echo "        <img src='" . USER_UPLOADS . "$tbl_img' alt='' class='img-responsive'></a>";
    } else {

        echo "<img src='" . USER_UPLOADS . "NO-IMAGE-AVAILABLE-ICON.jpg' alt='' class='img-responsive'></a>";
    }


    // echo "</a>";now rapping the text too. before it was only image

    if ($_GET['table_type'] == 'child') {

        $_SESSION['child_return_url'] = $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    } else {
        ////parent link
        $_SESSION['return_url'] = $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }


    /* if( $tab_anchor != 'false')
      $_SESSION['return_url'] = $_SESSION['return_url'] . "#$tab_anchor"; */

// *************************************

// ****  these are the lines that format and display the test inside the Boxview list (cards)
// we need to refine this ... because not every field needs a <br> after it
// also we want to tag each field/line with some generic CSS so that
// later we can (using list_style)  have control over the css formatting of the
// first line of the text, and successive lines

//    $listData = implode(" ", $listData);
	$listData = implode("<br>", $listData);

//  This is the
	echo "<span  class='list-data'>" . substr($listData, 0, 90) . "</span>";

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

                echo "<a href='$target_url&edit=true#$tab_anchor' class='btn btn-primary edit' >Edit</a>";
            }
        }
    }
}

///end of listViews function
