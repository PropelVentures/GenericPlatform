<?php
    /*

    function list_display($qry, $component_order = 'false', $tab_anchor = 'false')

    function listViews($boxStyles,$boxClass,$listData, $component_type, $table_type, $target_url, $imageField, $listRecord, $keyfield, $target_url2, $tab_anchor, $user_field, $list_select_arr) {
        
    function wideListViews($boxStyles,$boxClass,$listData, $component_type, $table_type, $target_url, $imageField, $listRecord, $keyfield, $target_url2, $tab_anchor, $user_field, $list_select_arr) {	
        
    */



    // function list_content_display_loop($row) {
    function list_content_display_loop($dict_id) {
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $con = connect();

    ECHO "<br><br>INSIDE list_content_display_loop($ row) <br>";



    ECHO "<br><br>list_content_display()<br> - $ dict_id=";echo $dict_id;
    ECHO "<br><br>";
        
    exit;

        ///for taking inline anchoring
        $tab_anchor = trim($row['component_name']);
        $tab_anchor = str_replace('*', '', $tab_anchor);

        if ($row['table_type'] == 'parent') {
            $_SESSION['parent_list_tabname'] = $tab_anchor;

            $_SESSION['parent_url'] = $actual_link;
        }

        $rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and component_order='$row[component_order]'   order by field_dictionary.field_order");

        $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and component_order='$row[component_order]'   order by field_dictionary.field_order";



        $rs2 = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and component_order='$row[component_order]'   order by field_dictionary.field_order");


        $row1 = $rs->fetch_assoc();
        $tableType = trim(strtolower($row1['table_type']));
        $componentType = trim(strtolower($row1['component_type']));

        $addUrlInner = getRecordAddUrlInner($row);
        
    ECHO "<br><br>list_content_display()<br> - $ tab_anchor=";echo $tab_anchor;
    ECHO "<br>row1 - component_order=";echo $row1['component_order'];
    ECHO "<br>$ qry=";echo $qry;
    ECHO "<br><br>";
        
    exit;
                            list_display($qry, $row1['component_order'], $tab_anchor); //// list displays

                            echo "<div style='clear:both'></div>";	
    }	


    /**
     * Renders display with multiple records
     * Common use case is renedering table of rows with data
     * TODO: Need to check other use cases
     * 
     * @param integer $dict_id TODO: Need to check data type based on use cases
     * @param string $table_alias
     * @param integer $component_order
     * 
     * @author ph
     * 
     * @return void
     */
    function list_display($dict_id, $table_alias, $component_order) {

        $tab_anchor = 'false';
        
        $con = connect();
        $rs = $con->query("SELECT * FROM data_dictionary where dict_id='$dict_id'");
        $row = $rs->fetch_assoc();
        $dict_id=$row['dict_id'];

        /**
         * Query is used in the list rendering later in the code 
        */
        
        $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.dict_id='$dict_id' order by field_dictionary.field_order";

        //$qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and component_order='$row[component_order]'   order by field_dictionary.field_order";
        
        /****
         * Query Parameters 
         * table_type, 
         * component_name
         * dict_id
         * table_type
         * component_type
         * keyfield
        */
        $rs2 = $con->query($qry);

        $rs = $con->query($qry);
        $row = $rs->fetch_assoc();

        $listCheck = 'yes';
        $list_sort = array_filter( array_map('trim', explode(',', $row['list_sort'])) );

        // for taking inline anchoring
        $tab_anchor = trim($row['component_name']);
        $tab_anchor = str_replace('*', '', $tab_anchor);

        if ($row['table_type'] == 'parent') {
            $_SESSION['parent_list_tabname'] = $tab_anchor;

            $_SESSION['parent_url'] = $actual_link;
        }

        
        // SET SESSION VAR FOR HOLDING TABLE_TYPE='parent' data_dictionary.`keyfield` 
        // and its relevant value for that keyfield column if it exists in the table
        
        $keyfield = $row['keyfield'];
        if (strtolower(trim($row['table_type']) ) == 'parent' && !empty($keyfield) ) {
            $_SESSION['parent_key_value'] = $keyfield;
        } else if ( strtolower(trim($row['table_type']) ) !== 'child') {
            unset($_SESSION['parent_key_value']);
        }

        // if table_type="parent" OR table_type= $internal_table_types[0] OR table_type= $internal_table_types[1]
        // internal_table_types` array so `$internal_table_types[0]` == 'USER'` and  `$internal_table_types[1]` == 'PROJECT'`
        // adjusting for lower case)

        $tableTypeUppercase = strtoupper(trim($row['table_type']) );
        $componentTypeUppercase = strtoupper(trim($row['component_type']) );
        
        if (strtolower(trim($row['table_type']) ) == 'child') { // || $tableTypeUppercase == $internal_table_types['0'] || $tableTypeUppercase == $internal_table_types['1']

            // **********************************************************************************
            //  CJ-NOTE ***
            // Two comments - see below
            // **********************************************************************************

            // **********************************************************************************
            // 1) it is confusing right now to know where $_SESSION['update_table']['search'] comes from ...
            // but it is important - because this may be the critical problem wiith parent-child processing
            // **********************************************************************************
            $search_key = $_SESSION['update_table']['search'];
            if (!empty($_REQUEST['search_id'])  && !empty($row['keyfield'])) {
                $search_key = $_REQUEST['search_id'];
                // **********************************************************************************
                // 2) The $row[keyfield]  term below also MAY be the source of the problem
                // because it would have been easy or likelyy for the prior coder to capture the wrong value beforehand
                // **********************************************************************************
                $row['list_filter'] = array('list_filter' => $row['list_filter'], 'child_filter' => "$row[table_name].$row[keyfield]='$search_key'");
            }
            //      if(empty($row['list_filter']) && $row['parent_table'] == 'product' )
            //	{
            //            $row['list_filter'] = "projects=$row[keyfield]";#'projects=DD.keyfield' from the child dict_id
            //	}
        } else {
            $search_key = $_SESSION['search_id'];
        }
        $isExistFilter = null;
        $isExistField = null;
        $filters_srray = getFiltersArray($row['list_filter']);
        $view_types_array = getFiltersArray($row['list_views']);

        $selected_filter_index = 0;

        /****
         * Setting selected filter index and row filter value
         * unsetting $_SESSION selected filter
        */

        if (count($filters_srray) > 0) {
            if (isset($_SESSION[$row['dict_id'].'_selected_filter'])) {
                $selected_filter_index = $_SESSION[$row['dict_id'].'_selected_filter'];
                $selected_row_filter = $filters_srray[$_SESSION[$row['dict_id'].'_selected_filter']]['filter'];
                unset($_SESSION[$row['dict_id'].'_selected_filter']);
            } else {
                $selected_row_filter = $filters_srray[0]['filter'];
            }
        } else {
            $selected_row_filter = $row['list_filter'];
        }
        $selected_view_index = 0;

        /**
         * Setting selected view index and row value
         * unsetting $_SESSION selected view
         */
        
        if (count($view_types_array) > 0) {
            if (isset($_SESSION[$row['dict_id'].'_selected_view'])) {
                $selected_view_index = $_SESSION[$row['dict_id'].'_selected_view'];
                $selected_row_view = $view_types_array[$_SESSION[$row['dict_id'].'_selected_view']]['filter'];
                unset($_SESSION[$row['dict_id'].'_selected_view']);
            } else {
                $selected_row_view = $view_types_array[0]['filter'];
            }
        } else {
            $selected_row_view = $row['list_views'];
        }

        $listView = strtolower($selected_row_view);

        /**
         * get_multi_record used to the get list on myaccount page we are setting the table getting from the "rs" query
         */
        $_SESSION['update_table']['table_name'] = $row['table_name'];  
        $_SESSION['update_table']['keyfield'] = $row['keyfield'];  
        if (count($list_sort) == 1 && !empty($row['list_sort'])) {
            $list = get_multi_record($_SESSION['update_table']['table_name'], $_SESSION['update_table']['keyfield'], $search_key, $selected_row_filter, $list_sort[0], $listCheck,$isExistFilter,$isExistField);
        } else {
            $list = get_multi_record($_SESSION['update_table']['table_name'], $_SESSION['update_table']['keyfield'], $search_key, $selected_row_filter, $listSort = 'false', $listCheck,$isExistFilter,$isExistField);
        }

        $availableRecords =   $list->num_rows;
        $limitOnAddButton = checkListItemsLimit($row['list_extra_options']);
        $disableAddButton = false;
        if ($limitOnAddButton !== false && $availableRecords >= $limitOnAddButton) {
            $disableAddButton = true;
        }
        
        $list_views = listvalues($row['list_views']);
        $list_pagination = listpageviews($row['list_pagination']);

        if (!isset($list_pagination['itemsperpage']) || empty($list_pagination['itemsperpage'])) {
            $list_pagination['itemsperpage'] = 9 ;// set default
        }
        if (!isset($list_pagination['totalpages']) || empty($list_pagination['totalpages'])) {
            $list_pagination['totalpages'] = "#".( ceil($list->num_rows/$list_pagination['itemsperpage']) ) ;
        } else {
            $list_pagination['totalpages'] = '#'.$list_pagination['totalpages'];
        }

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

        $defaultOptions = getDefaultListViewExtraOptions($con,$row['page_name']);
        if(!empty($row['list_select']) ){
                if((empty($row['list_operations'])) || ($row['list_operations'] == NULL)){
            $buttonOptions = $defaultOptions['list_operations'];
                }else{
                    $buttonOptions = $row['list_operations'];
                }
        }else if($row['dd_editable'] == '11' ){
            if((empty($row['edit_operations'])) || ($row['edit_operations'] == NULL)){
            $buttonOptions = $defaultOptions['edit_operations'];
            }else{
                    $buttonOptions = $row['edit_operations'];
            }
            //$buttonOptions = $row['edit_operations'];
        }else if($row['dd_editable'] == '1' ){
            if((empty($row['view_operations'])) || ($row['view_operations'] == NULL)){
            $buttonOptions = $defaultOptions['view_operations'];
            }else{
                    $buttonOptions = $row['view_operations'];
            }
            //$buttonOptions = $row['view_operations'];
        }
        /*Code Change End Task ID 5.6.4*/
        if(empty($row['list_extra_options']) || $row['list_extra_options'] == NULL){
        $row['list_extra_options'] = $defaultOptions['list_extra_options'];
        }
        $ret_array = listExtraOptions($row['list_extra_options'], $buttonOptions);
        global $popup_menu;
        
    
        $popup_menu = array(
            "popupmenu" => $ret_array['popupmenu'],
            "popup_delete" => $ret_array['popup_delete'],
            "popup_copy" => $ret_array['popup_copy'],
            "popup_add" => $ret_array['popup_add'],
            "popup_openChild" => $ret_array['popup_openChild']
        );
        
        if (count($list_sort) > 1 && ($listView == 'boxview' || $listView == 'boxview')) { ?>
        <div class="col-6 col-sm-6 col-lg-6 sortby boxview-sort sorting-<?=$row['dict_id']?>">
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
                            echo "<li class='sorting-li' id='sort-li' data-value='$val' data-dict='".$row['dict_id']."'>
                                    <a>$fdField[field_label_name]$order</a>
                                </li>";
                        }
                        ?>
                    </ul>
                </div>
            </span>
        </div>
        <?php } 
        ////list sort if ends here

        /********* setting DisplayView icons **** *//////
        $list_select = trim($row['list_select']);
        $table_type = trim($row['table_type']);
        $component_type = trim($row['component_type']);

        // *** The second parameter will be removed after completion of the new list_option protocol.
        $list_select_arr = getListSelectParams($list_select, $row['dict_id']);
        $addRecordUrl = getRecordAddUrl($list_select_arr,$component_type, $table_type);
        $list_style = $row['dd_css_class'];
        $keyfield = firstFieldName($row['table_name']);
        $table_name = trim($row['table_name']);
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
        $tbQry = '';
        if (!empty($list_fields)){
            if (preg_match('/^\d+\.?\d*$/', $row['list_fields']))
            {
                if ($component_order == 'false') {
                    $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and component_order='$_GET[ComponentOrder]'  order by field_dictionary.field_order LIMIT " . $row['list_fields'];
                } else {
                    $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and component_order='$component_order'  order by field_dictionary.field_order LIMIT " . $row['list_fields'];
                }
            }
            else
            {
                $fields = array_filter( array_map('trim',explode(",", $row['list_fields'])) );
                $fieldsFinal = '';
                foreach ($fields as $f) {
                    if (empty($fieldsFinal))
                        $fieldsFinal = "'" . $f . "'";
                    else
                        $fieldsFinal = "'" . $f . "' , " . $fieldsFinal;
                }
                if ($component_order == 'false') {
                    $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and  component_order='$_GET[ComponentOrder]'  and field_dictionary.generic_field_name IN(  $fieldsFinal ) order by field_dictionary.field_order";
                } else {
                    $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and  component_order='$component_order'  and field_dictionary.generic_field_name IN(  $fieldsFinal ) order by field_dictionary.field_order";
                }
            }
        }else{////when list field is empty
            if ($component_order == 'false') {
                $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and component_order='$_GET[ComponentOrder]'  order by field_dictionary.field_order";
            } else {
                $tbQry = $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.page_name='$row[page_name]' and component_order='$component_order'  order by field_dictionary.field_order";
            }
        }
        ?>
        <script>
            function clearFunction(){
                // document.getElementById("list-form").reset();
                /*Palak Task 5.4.77 Changes Start
                var table = $('#example').sble();*/
                // var table = $('.clear1').DataTable();
                /*Palak Changes End*/
                // table.search( '' ).columns().search( '' ).draw();

                //edit by Akshay S (2019-Aug-04 18:30 IST)
                //jQuery code added to refresh DataTable when search field is empty (Task ID: 8.3.501)
                $("#list-form")[0].reset();
                var table = $('table.clear1').DataTable({ "bInfo" : false });
                table.search('').draw();
            }
        </script>
        <div class="row start_render_view" id="popular_users" >
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
                    if (isset($ret_array['add_array']) && !empty($ret_array['add_array'])) {
                    if ($disableAddButton) { ?>
                        <button  class="  btn action-add  <?php echo $ret_array['add_array']['style'] ; ?>" name="add" onclick="limitIsFull()" title="Maximum limit reached" ><?php echo $ret_array['add_array']['label'] ; ?></button>
                    <?php } else { ?>
                    <button type="submit" class="btn action-add <?php echo $ret_array['add_array']['style'] ; ?>" name="add" onclick="window.location.href='<?php echo $addRecordUrl.$_SESSION['anchor_tag']; ?>'"><?php echo $ret_array['add_array']['label'] ; ?></button>
                    <?php }
                    }
                    
                    /***Checklist if starts */

                    if ($list_views['checklist'] == 'true') {
                        $thisDDid  = $row['dict_id'];
                        /// setting for  delete button
                        if (isset($ret_array['del_array']) && !empty($ret_array['del_array'])) {
                            echo "<button type='submit' data-id='$thisDDid' class='btn action-delete " . $ret_array['del_array']['style'] . "' name='delete' >" . $ret_array['del_array']['label'] . "</button>";
                        }
                        //// setting for  copy button
                        if (isset($ret_array['copy_array']) && !empty($ret_array['copy_array'])) {
                            echo "<button type='submit' data-id='$thisDDid' class='btn action-copy " . $ret_array['copy_array']['style'] . "' name='copy' >" . $ret_array['copy_array']['label'] . "</button>";
                        }
                        echo "";
                    }/// checklist if ends here


                    ##CUSTOM FUNCTION BUTTON##
                    if (!empty($ret_array['custom_function_array']) ) {
                        generateCustomFunctionArray($ret_array['custom_function_array']);
                    } /// select checkbox div ends here

                
                    ?>
                </div>
                <?php } ?>
                <?php

                if (count($filters_srray) > 1) {
                    showListFilterSelection($row,$filters_srray,$selected_filter_index);
                }
                if (count($view_types_array) > 1) {
                    showListViewSelection($row,$view_types_array,$selected_view_index);
                }

                /*
                *
                * ********
                * if no record to display
                * ******************DD->list_select values
                */

                
                if ($list->num_rows == 0) {
                    $nav = $con->query("SELECT * FROM navigation where target_page_name='$_GET[page_name]'");
                    $navList = $nav->fetch_assoc();
                    /// Extracting action ,when user click on edit button or on list
                    // DD OVERHAUL - CJ Notes = adding component type to the lines below

                    if (isset($list_select_arr[0]) && !empty($list_select_arr[0])) {
                        if (count($list_select_arr[0]) == 2) {
                            $target_url = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&table_alias" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;  //&"component_type=" . $component_type;
                            /// add button url
                            $_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&layout=&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias" . $list_select_arr[0][0] . "&table_type=" . $table_type; //&"component_type=" . $component_type;
                        } else {
                            $target_url = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&table_alias" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;; //&"component_type=" . $component_type;
                            /// add button url
                            $_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias" . $list_select_arr[0][0] . "&table_type=" . $table_type; //&"component_type=" . $component_type;
                        }
                    }
                    $_SESSION['return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                }//// if record is zero... ends here
                
                
                
        
                if (!checkIfEmptyList($list, $row)) {
                    $isExistField = chop($isExistField, ";");
                    $isExistFilter = chop($isExistFilter, ";");
                    $listView  = chop($listView, ";");
                    switch($listView){
                        case 'mapview':
                            include_once('renderMapView.php');
                            renderMapView($isExistFilter,$isExistField,$row,$tbQry,$list,$qry,$list_pagination,$tab_anchor,$component_order,$imageField,$ret_array,$mapAddress=false); // renderMapView.php
                            break;

                        case 'mapaddress':
                            include_once('renderMapView.php');
                            renderMapView($isExistFilter,$isExistField,$row,$tbQry,$list,$qry,$list_pagination,$tab_anchor,$component_order,$imageField,$ret_array,$mapAddress=true); // renderMapView.php
                            break;

                        case 'boxview':
                            include_once('renderBoxView.php');
                            renderBoxView($isExistFilter,$isExistField,$row,$tbQry,$list,$qry,$list_pagination,$tab_anchor,$component_order,$imageField,$ret_array); // renderBoxView.php

                            break;

                        case 'boxwide':
                            include_once('renderBoxWide.php');
                            renderBoxWide($isExistFilter,$isExistField,$row,$tbQry,$list,$qry,$list_pagination,$tab_anchor,$component_order,$imageField,$ret_array); // renderBoxView.php
                            break;

                        default:
                            include_once('renderListView.php');
                            renderListView($isExistFilter, $isExistField, $row, $tbQry, $list, $qry, $list_pagination, $tab_anchor); // renderListView.php
                            break;
                    } 
                }?>
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

    <?php }

    /*
    * @listViews function
    *
    * give LIST UI and data inside lists
    */

    function listViews($boxStyles,$boxClass,$listData, $component_type, $table_type, $target_url, $imageField, $listRecord, $keyfield, $target_url2, $tab_anchor, $user_field, $list_select_arr) {
        /*
        *
        * displaying of image in list
        */

        $tbl_img = $listRecord[$imageField['generic_field_name']];
        $filename = USER_UPLOADS . "" . $tbl_img;

        //************* Added by Marko ********************************************
        if(count($list_select_arr) == 0)
        {
            $image_link = "#";
        } else {
            $image_link = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&table_alias" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type."&component_type=" . $component_type;
        }

        // Profile image link 
        echo "<a href='" . $image_link . "' class='profile-image'>";

        // Profile Image
        if (!empty($tbl_img) && file_exists($filename)) {
            echo "<img src='" . USER_UPLOADS . "$tbl_img' alt='' class='img-responsive'></a>";
        } else {
            echo "<img src='" . USER_UPLOADS . "NO-IMAGE-AVAILABLE-ICON.jpg' alt='' class='img-responsive'></a>";
        }

        // Print buttons
        for($i = 1; $i < count($list_select_arr); $i++)
        {
            $vertical_css = ($i-1) * 40;
            $label = $list_select_arr[$i][3];
            if(empty($label)) // this code is temporary. will be removed in the future.
                $label = "Edit";
            $btn_url = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&table_alias" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type."&component_type=" . $component_type;
            echo "<a href='$btn_url&edit=true#$tab_anchor' class='btn btn-primary "
                .$list_select_arr[$i][4]."' style='bottom:{$vertical_css}px;width:100%;position: absolute;'>".$label."</a>";
        }

        if ($_GET['table_type'] == 'child' && !empty($_GET['search_id'])) {
            $_SESSION['child_return_url'] = $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        } else {
            ////parent link
            $_SESSION['return_url'] = $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }

        // Print list content
        $listData = array_filter($listData);
        echo "<div class='boxView_content list-data $boxClass' style='$boxStyles'>";

            if(!empty($listData)){
                foreach($listData as $data){
                    if(isset($data['data_length'])){
                    $value = truncateLongDataAsPerAvailableWidth($data['field_value'],$data['data_length']);
                    }else{
                    $value = trim($data['field_value']);
                    }
                    echo "<div class='boxView_line ".$data['field_style']."'>".$value."</div>";
                }
            }
        echo "</div>";

        /////////////////
        return;

        //****************** Original logic ********************************************************
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
                   //echo "<a href='$target_url&edit=true#$tab_anchor' class='btn btn-primary edit' >Edit</a>";
                }
            }
        }

        if ($_GET['table_type'] == 'child' && !empty($_GET['search_id'])) {
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
        echo "<div class='boxView_content list-data $boxClass' style='$boxStyles'>";

            if(!empty($listData)){
                foreach($listData as $data){
                    if(isset($data['data_length'])){
                    $value = truncateLongDataAsPerAvailableWidth($data['field_value'],$data['data_length']);
                    }else{
                    $value = trim($data['field_value']);
                    }
                    echo "<div class='boxView_line ".$data['field_style']."'>".$value."</div>";
                }
            }
        echo "</div>";
    }///end of listViews function



    function wideListViews($boxStyles,$boxClass,$listData, $component_type, $table_type, $target_url, $imageField, $listRecord, $keyfield, $target_url2, $tab_anchor, $user_field, $list_select_arr) {
        /*
        *
        * displaying of image in list
        */

        //************* Added by Marko ********************************************
        if(count($list_select_arr) == 0)
        {
            $image_link = "#";
        } else {
            $image_link = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[1][2] . "&table_alias=" . $list_select_arr[1][0] . "&table_alias" . $list_select_arr[1][0] . "&ComponentOrder=" . $list_select_arr[1][1] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&edit=true&fnc=onepage";
        }

        // Profile image link 
        echo "<div class='row' ><div class='col-lg-3' style='margin-right:-5%'>";
        $tbl_img = $listRecord[$imageField['generic_field_name']];
        $filename = USER_UPLOADS . "" . $tbl_img;
        echo "<a href='" . $image_link . "' class='profile-image'>";
        if (!empty($tbl_img) && file_exists($filename)) {
            echo "<img src='" . USER_UPLOADS . "$tbl_img' alt='' class='img-responsive'></a>";
        } else {
            echo "<img src='" . USER_UPLOADS . "NO-IMAGE-AVAILABLE-ICON.jpg' alt='' class='img-responsive'></a>";
        }
        echo "</div><div class='col-lg-9'>";

        
        // Print buttons
        for($i = 1; $i < count($list_select_arr); $i++)
        {
            $horizon_css = ($i - 1) * 100;
            $label = $list_select_arr[$i][3];
            if(empty($label)) // this code is temporary. will be removed in the future.
                $label = "Edit";
            $btn_url = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&table_alias" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type."&component_type=" . $component_type;
            echo "<a href='$btn_url&edit=true#$tab_anchor' class='btn btn-primary "
                .$list_select_arr[$i][4]."' style='margin-left:{$horizon_css}px;position: absolute;'>".$label."</a>";
        }

        if ($_GET['table_type'] == 'child') {
            $_SESSION['child_return_url'] = $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        } else {
            ////parent link
            $_SESSION['return_url'] = $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }

        // Print list content
        $listData = array_filter($listData);
        //  This is the
        echo "<div class='boxView_content list-data $boxClass' style='$boxStyles'>";
            if(!empty($listData)){
                foreach($listData as $data){
                    echo "<div class='boxView_line ".$data['field_style']."'>".$data['field_value']."</div>";
                }
            }
        echo "</div></div></div>";

        /////////////////
        return;


        echo "<div class='row' ><div class='col-lg-3' style='margin-right:-5%'>";
        $tbl_img = $listRecord[$imageField['generic_field_name']];
        $filename = USER_UPLOADS . "" . $tbl_img;
        echo "<a href='" . (!empty($target_url2) ? $target_url2 : "#" ) . "' class='profile-image'>";
        if (!empty($tbl_img) && file_exists($filename)) {
            echo "<img src='" . USER_UPLOADS . "$tbl_img' alt='' class='img-responsive'></a>";
        } else {
            echo "<img src='" . USER_UPLOADS . "NO-IMAGE-AVAILABLE-ICON.jpg' alt='' class='img-responsive'></a>";
        }
        echo "</div><div class='col-lg-9'>";
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
                    //echo "<a href='$target_url&edit=true#$tab_anchor' class='btn btn-primary edit' >Edit</a>";
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
        echo "<div class='boxView_content list-data $boxClass' style='$boxStyles'>";
            if(!empty($listData)){
                foreach($listData as $data){
                    echo "<div class='boxView_line ".$data['field_style']."'>".$data['field_value']."</div>";
                }
            }
        echo "</div></div></div>";
    }///end of listViews function
