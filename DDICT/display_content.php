<?php

//////////////
//////////////***** DISPALY TABS AS H1 TAG ******

function display_content($row) {
    
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $editable = 'true';

    $con = connect();


    ///for taking inline anchoring
    $tab_anchor = trim($row[tab_name]);

    $rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$row[tab_num]'   order by field_dictionary.display_field_order");


    $qry = "SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$row[tab_num]'   order by field_dictionary.display_field_order";

    $rs2 = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$row[table_alias]' and data_dictionary.display_page='$row[display_page]' and tab_num='$row[tab_num]'   order by field_dictionary.display_field_order");


    $row1 = $rs->fetch_assoc();


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

    if ($_SESSION['user_privilege'] < $row1['dd_privilege_level'] && $_SESSION['user_privilege'] <= 9) {

        $userPrivilege = true;
    } else {

        $userPrivilege = false;
    }

    if ($userPrivilege === false) {

        ///////// for displaying image container
        $image_display = 'true';
        //print_r($row1);die;
        //////ASsigning custom class to the form

        $style = $row1['list_style'];

        ////adding class if form is not for editing purpose

        $page_editable = true;

        if ($row1['page_editable'] == 0 || $row1['dd_editable'] == 1) {

            $style = $style . ' page_not_editable';


            $page_editable = false;
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

        $_SESSION['update_table']['keyfield'] = firstFieldName($row1['database_table_name']);

        /*         * ****** for update *** */

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

                $_SESSION['return_url2'] = BASE_URL . "system/profile.php?display=$_GET[display]&layout=$_GET[layout]&style=$_GET[layout]";
            } else {

                $_SESSION['return_url2'] = $actual_link;
            }
        }


        ///rs use
        //if($tab_anchor == 'My Gallery')
        //  $tab_anchor= 'my_gallery';
        echo "<section class='section-sep'><a name='$tab_anchor'></a><h1>$row[tab_name]</h1><!-- h1-content class not used-->";



        if (!empty($_GET['ta']) && $_GET['ta'] == $row1['table_alias'] && !empty($_GET['search_id'])) {

            if ($_GET['table_type'] == 'parent') {


                $_SESSION['parent_value'] = $_GET['search_id'];
            }

            $urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $_GET['search_id']);
        } else {

            $urow = get_single_record($_SESSION['update_table']['database_table_name'], $_SESSION['update_table']['keyfield'], $_SESSION['search_id']);
        }


        if (isset($_GET['addFlag']) && $_GET['addFlag'] == 'true' && $_GET['tabNum'] == $row1['tab_num'] && $_GET['tab'] == $row1['table_alias']) {

            $_SESSION['dict_id'] = $row1['dict_id'];

            if (!empty($_GET['search_id']))
                $_SESSION['search_id2'] = $_GET['search_id'];
            else
                $_SESSION['search_id2'] = $_SESSION['search_id'];

            $_SESSION['update_table2']['database_table_name'] = $_SESSION['update_table']['database_table_name'];

            $_SESSION['update_table2']['keyfield'] = $_SESSION['update_table']['keyfield'];


            if ($_GET['checkFlag'] == 'true') {
                $_SESSION['return_url2'] = $_SESSION['return_url'];
            } else {
                $_SESSION['return_url2'] = $actual_link;
            }

            echo "<form action='?action=add&checkFlag=true&tabNum=$_GET[tabNum]&fnc=onepage' method='post' id='user_profile_form' enctype='multipart/form-data' class='$style'><br>";



            while ($row = $rs2->fetch_assoc()) {
                formating_Update($row, $method = 'add', $urow);
            }//// end of while loop
            //if ($_GET['checkFlag'] == 'true') {

            $actual_link = $_SESSION[return_url2] . "&fnc=onepage";
            // }


            echo "<div class='form-footer'>
                           
                    <input type='submit'  value='" . formSave . "' class='btn btn-primary update-btn' />

                    <a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>
                </div>   
                            
            <!--</div>-->";###THE LAST </DIV> IS NOT PART OF ANY OPEN <DIV> tag so commented out


            echo "<div style='clear:both'></div></form>";
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

                if ($_GET['checkFlag'] == 'true' && $row1['dd_editable'] == 11) {



                    echo "<ol class='breadcrumb'> 
                            <li><a href='$_SESSION[return_url2]&button=cancel&fnc=onepage' class='back-to-list'>Back To <span>$_SESSION[list_tab_name]</span> List</a></li>
                          </ol>";
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

                                echo "<div class='form-footer'>
                                        <input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' />                            
                                        <a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>                            
                                    </div>";
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


                        echo "<div class='form-footer'>                           
                                <input type='submit'  value='" . formUpdate . "' class='btn btn-primary update-btn' />                           
                                <a href='$actual_link' ><input type='button' name='profile_cancel' value='" . formCancel . "' class='btn btn-primary update-btn' /></a>                              
                            </div>";
                    }/// if for submit and cancel ends here
                    //profile-image }
                }

                echo "<div style='clear:both'></div></form></section><!--<div class='h1-sep'><span></span></div>-->";
            }
        }
    } else {
        echo "<section class='section-sep'><h1>$row[tab_name]</h1>";
        echo "<h3 style='color:red'>You don't have enough privilege to view contents</h3>";

        echo "</section>";
        ///page privilege if its false      
    }
}
