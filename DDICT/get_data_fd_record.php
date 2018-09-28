<?php

function Get_Data_FieldDictionary_Record($table_alias, $display_page, $tab_status = 'false', $tab_num = 'false', $editable = 'true') {

//    echo "<pre>";
//    print_r($_REQUEST);
//    print_r($_SESSION);
//    die;
    
    
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $con = connect();

    ///setting form editable if user click on list for editing purpose

    if (!empty($_GET['edit']) && $_GET['edit'] == 'true') {

        $con->query("update data_dictionary set dd_editable='1' where display_page=$_GET[display]");

        $con->query("update data_dictionary set dd_editable='11' where display_page='$_GET[display]' and tab_num='$_GET[tabNum]' and table_alias='$_GET[tab]'");
    }


    if (empty($_GET['tabNum'])) {
        $rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page' and tab_num != 0 and tab_name != 'fffr_icon' order by tab_num");
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

        $rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' and tab_num != 0  and tab_name != 'fffr_icon' order by tab_num");
        while ($row = $rs->fetch_assoc()) {

            /////display_content.php////
            display_content($row);
        }
    } else {


        /*         * *****************
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
        $_SESSION['list_pagination'] = $row1['list_pagination'];
        ///////// for displaying image container
        $image_display = 'true';
        
//        echo "<pre>";
//        print_r($row1);die;

        /* profile-image
          if (trim($row1['table_type']) == 'profile-image') {

          $_SESSION['profile-image'] = 'profile-image';
          } else {

          unset($_SESSION['profile-image']);
          } */

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



        if ($_SESSION['user_privilege'] < $row1['dd_privilege_level'] && $_SESSION['user_privilege'] <= 9) {

            $userPrivilege = true;
        } else {

            $userPrivilege = false;
        }

        if ($userPrivilege === false) {





            ////adding class if form is not for editing purpose

            $page_editable = true;

            if ($row1['page_editable'] == 0 && trim($row1['table_type']) != 'transaction') {


                $page_editable = false;

                if (!empty($row1['list_style']))
                    $style = $row1['list_style'] . ' page_not_editable';
                else
                    $style = 'page_not_editable';
            }else if ($row1['page_editable'] == 2) {

                $page_editable = false;

                if (!empty($row1['list_style']))
                    $style = $row1['list_style'] . ' profile_page';
                else
                    $style = 'profile_page';
            }else {

                if (!empty($row1['list_style']))
                    $style = $row1['list_style'] . ' simple_edit_page';
                else
                    $style = 'simple_edit_page';
            }

            if ($row1['database_table_name'] == $_SESSION['select_table']['database_table_name'])
                $_SESSION['search_id'] = $_SESSION['uid'];
            else if (trim($row1['table_type']) == 'child') {

                $_SESSION['search_id'] = $_SESSION['parent_value'];
            } else
                $_SESSION['search_id'] = '76'; /// for displaying one user


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


                if (trim($row1['table_type']) != 'transaction')
                    $urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $_SESSION['search_id']);
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


                echo "<br><ol class='breadcrumb'>
                            <li><a href='$_SESSION[parent_url]&button=cancel' class='back-to-list'>Back To <span>$_SESSION[parent_list_tabname]</span> List</a></li>
                          </ol>";
            }


            /*             * ******************
             * ************************ADDING Form flags code goes here
             * ************************
             */
            if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true' && $_GET['tabNum'] == $row1['tab_num'] && $_GET['tab'] == $row1['table_alias']) {


                /*                 * **********BREADCRUMB
                 * 
                 * 
                 * 
                 * 
                 */






                if ($_GET['table_type'] == 'child')
                    $link_to_return = $_SESSION['child_return_url'];
                else
                    $link_to_return = $_SESSION['return_url'];



                /*                 * ***
                 * 
                 * 
                 * *********
                 * *************ADDING BREADCRUMB FOR PARENT/NORMAL LISTS/PAGES
                 * 
                 * *************
                 * ***********************
                 * *******
                 * 
                 * 
                 */



                /*                 * **
                 * 
                 * Short solution for back to home page
                 * 
                 */

                $home_test = explode("display", $link_to_return);

                //print_r($home_test);die;

                if ($home_test[1] == '=home')
                    $breadcrumb_display = " Back To <span>Home</span> Page";
                else
                    $breadcrumb_display = " Back To <span>$_SESSION[list_tab_name]</span> Lists";

                echo "<br>
                    <div class='breadcrumb'>
                        <a href='$link_to_return&button=cancel&table_type=$row1[table_type]" . ( $_GET['fnc'] == 'onepage' ? '&fnc=onepage' : '' ) . "' class='back-to-list'> $breadcrumb_display</a>
                    </div>";



                /*                 * ********************************** */


                $style = $row1['list_style'];

                $_SESSION['dict_id'] = $row1['dict_id'];

                if (!empty($_GET['search_id']))
                    $_SESSION['search_id2'] = $_GET['search_id'];
                else
                    $_SESSION['search_id2'] = $_SESSION['search_id'];



                $_SESSION['update_table2']['database_table_name'] = $_SESSION['update_table']['database_table_name'];

                $_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];


                if ($_GET['checkFlag'] == 'true') {
                    /*
                      if ($_GET['table_type'] == 'child')
                      $_SESSION['child_return_url2'] = $_SESSION['child_return_url'];
                      else
                      $_SESSION['return_url2'] = $_SESSION['return_url'];

                     */


                    // $style = trim($style);


                    echo "<form action='$_SESSION[add_url_list]&action=add' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";
                } else {
                    $_SESSION['return_url2'] = $actual_link;

                    echo "<form action='?action=add&tabNum=$_GET[tabNum]' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";
                }


                if ($_GET['checkFlag'] == 'true') {

                    if ($_GET['table_type'] == 'child')
                        $link_to_return = $_SESSION['child_return_url'];
                    else
                        $link_to_return = $_SESSION['return_url'];

                    $actual_link = $link_to_return;

                    //   $cancel_value = formCancel;
                }

                $actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]";




                echo "<div class='form-footer'>                            
                        <input type='submit'  value='" . formSave . "' class='btn btn-primary update-btn' />
                        <a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>
                    </div>";

                echo "<div style='clear:both'></div><hr>";

                while ($row = $rs2->fetch_assoc()) {

                    formating_Update($row, $method = 'add', $urow);
                }//// end of while loop
                //if ($_GET['checkFlag'] == 'true') {


                /* if ($_GET['table_type'] == 'child' && $_GET['checkFlag' == 'true'])
                  $actual_link = $_SESSION['add_url_list'] . "&button=cancel";
                  else */


                // }

                echo "<div class='form-footer'>                            
                        <input type='submit'  value='" . formSave . "' class='btn btn-primary update-btn' />                                
                        <a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>
                    </div>
                            
                    <!--</div>-->";###COMMENTED OUT AS IT DOESN"T HAVE OPENING <DIV> TAG


                echo "<div style='clear:both'></div></form>";
            } else {

                if (( ( $row1['list_views'] == 'NULL' || $row1['list_views'] == '' ) || ( isset($_GET['id'])) || $_GET['edit'] == 'true') && $row1['table_type'] != 'content') {


                    if ($row1['table_type'] == 'child')
                        $table_type = 'child';
                    else
                        $table_type = 'parent';



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
                        echo "<form action='?action=update&checkFlag=true&tabNum=$_GET[tabNum]&table_type=$table_type' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";
                    } else {
                        echo "<form action='?action=update&tabNum=$_GET[tabNum]&table_type=$table_type' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";
                    }


                    ///// To show image uploader buttons

                    if ($_GET['checkFlag'] == 'true' && $row1['dd_editable'] == 11) {


                        if ($_GET['table_type'] == 'child')
                            $link_to_return = $_SESSION['child_return_url'];
                        else
                            $link_to_return = $_SESSION['return_url'];



                        /*                         * ***
                         * 
                         * 
                         * *********
                         * *************ADDING BREADCRUMB FOR PARENT/NORMAL LISTS/PAGES
                         * 
                         * *************
                         * ***********************
                         * *******
                         * 
                         * 
                         */



                        /*                         * **
                         * 
                         * Short solution for back to home page
                         * 
                         */

                        $home_test = explode("display", $link_to_return);

                        //print_r($home_test);die;
                        if ($home_test[1] == '=home')
                            $breadcrumb_display = " Back To <span>Home</span> Page";
                        else
                            $breadcrumb_display = " Back To <span>$_SESSION[list_tab_name]</span> Lists";

                        echo "<div class='breadcrumb'>
                                <a href='$link_to_return&button=cancel&table_type=$row1[table_type]" . ( $_GET['fnc'] == 'onepage' ? '&fnc=onepage' : '' ) . "' class='back-to-list'> $breadcrumb_display</a>      
                                    " . editPagePagination($row1['list_extra_options'], $primary_key) . "
                              </div>";
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

                                if ($_GET['checkFlag'] == 'true') {

                                    if ($_GET['table_type'] == 'child')
                                        $link_to_return = $_SESSION['child_return_url'];
                                    else
                                        $link_to_return = $_SESSION['return_url'];

                                    $actual_link = $link_to_return;

                                    //   $cancel_value = formCancel;
                                }

                                $actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]";

                                if ($tab_status != 'bars') {

                                    echo "<div class='form-footer'>  
                                    
                           
                                <input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' />
                            
                                    <a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>
                               
                            
                          
                            
                                </div>";
                                }
                            }/// if for submit and cancel ends here
                            // profile-image }
                        }
                        if ($row1['dd_editable'] == 11 && $row1['page_editable'] == 1) {
                            echo "<div style='clear:both'></div><hr>";
                        }
                    }


                    /*                     * ************************************************** */
                    /*                     * ************************************************** */
                    /*                     * ************************************************** */
                    /*                     * ************************************************** */


                    if ($row1['dd_editable'] == 1 && $row1['page_editable'] == 1) {
                        echo "<button type='button' class='btn btn-default pull-right edit-btn' id='$row1[dict_id]'>" . EDIT . "</button>";

                        $image_display = 'false';
                    }


                    if (isset($_GET['id'])) {
                        $urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $_GET['id']);
                    }
//print_r($urow);die;

                    while ($row = $rs2->fetch_assoc()) {
                        //echo "<pre>"; print_r($row); 
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
//                        print_r($_SESSION); die;
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

                                if ($_GET['table_type'] == 'child')
                                    $link_to_return = $_SESSION['child_return_url'];
                                else
                                    $link_to_return = $_SESSION['return_url'];

                                $actual_link = $link_to_return;

                                //$cancel_value = 'Cancel';
                            }

                            $actual_link = $actual_link . "&button=cancel&table_type=$_GET[table_type]";

                            //if( $row1['dd_editable'] != 0 ){

                            echo "<div class='form-footer'>                           
                                    <input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' />                                
                                    <a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a> 
                                </div>";

                            // }
                        }/// if for submit and cancel ends here
                        // profile-image }
                    }

                    echo "<div style='clear:both'></div></form>";
                }
            }

            ////////page privilege if true
        } else {

            echo "<h3 style='color:red'>You don't have enough privilege to view contents</h3>";
            ///page privilege if its false   
        }
    }//else ends here where tab_num=0 is not part of dd->display_page
}
