<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class smallUtil {

    
    ///for making crossref DD entries .. run FD update after this 
    //assuming one entry is already there in Nav and DD for one crossref table Afflilation
    public function crossRef() {
        require('dictionaryConfig.php');

        $con = connect();



        //$arr = array('location_types', 'privacy_type', 'scripts_categories', 'scripts_launch_status', 'scripts_permissions', 'scripts_type', 'scripts_visibility', 'sharing_types', 'standard_messages', 'states', 'tone_styles', 'transaction_type', 'user_roles', 'user_type', 'visibility', 'voice_commands', 'voice_styles', 'wallet','user_privileges');




        $i = 1;

        foreach ($arr AS $value) {


            ////////Fetching Label//////

            $rs = $con->query("SHOW COLUMNS FROM $value");

            $row = $rs->fetch_assoc();

            $pKey = $row['Field'];



            $label = explode('_', $value);

            ////***** FOR FIELD_label_name ****/////

            if (!empty($label[0]) && !empty($label[1])) {

                $strLabel = implode(' ', $label);

                $labeName = ucwords($strLabel);
            } else {

                $labeName = ucwords($label[0]);
            }

           
////insertion in navigation
            $qry = "INSERT INTO $NAVtbl (`nav_id`, `display_page`, `menu_location`, `item_number`, `item_label`, `item_target`, `target_display_page`, `loginRequired`, `item_help`, `item_style`, `item_privilege`, `item_icon`, `item_visibility`, `admin_level`, `enabled`, `menu_orientation`, `page_layout_style`) VALUES (NULL, 'home', 'header', '4.$i', '$labeName', 'system/profile.php', '$value', 'true', '', '', '', '', '1', '0', '1', 'HORIZONTAL', '');";


            mysqli_query($con, $qry);


            //insertion in DD


            $qry = "INSERT INTO $DDtbl (`dict_id`, `table_alias`, `database_table_name`, `table_type`, `parent_table`, `keyfield`, `display_page`, `tab_num`, `tab_name`, `dd_editable`, `dd_visibility`, `user_type`, `dd_privilege_level`, `list_views`, `list_filter`, `list_sort`, `list_extra_options`, `list_style`, `description`, `created`, `list_fields`, `fields_used`, `fd_initialization`, `list_select`) VALUES (NULL, '$value', '$value', '', '', '$pKey', '$value', '1', '$labeName', '1', 'DEFAULT', 'ALL', 'USER', 'listview', '', '','checklist; delete,Delete,btn-danger; add,Add,btn-primary;copy,Copy,btn-primary; popupmenu; popup_delete,Delete Check,popup-class;popup_copy,Copy,copy-class;popup_add,ADD,add-class;','', '', '2014-12-02 15:22:51', '', '', 'update', '$value,1,$value');";


            mysqli_query($con, $qry);

            $i++;
        }
    }
    
    /*
     * 
     * will set single table list visible only for Admin
     */
    
    public function adminTBL() {
        require('dictionaryConfig.php');

        $con = connect();

        
        /*
         * uncomment if there is no entry at all for admin tables 
         * 
         * INSERT INTO `navigation` (`nav_id`, `display_page`, `menu_location`, `item_number`, `item_label`, `item_target`, `target_display_page`, `loginRequired`, `item_help`, `item_style`, `item_privilege`, `item_icon`, `item_visibility`, `admin_level`, `enabled`, `menu_orientation`, `page_layout_style`) VALUES (NULL, 'home', 'header', '5.0', 'Admin tables', '#', '', 'true', '', '', '', '', '1', '1', '1', 'HORIZONTAL', '');
         */


              //$arr = array('devices','locations','scripts','scripts_commands','triggers_alarms','user','user_friends');

$arr = array('user');



        $i = 6;

        foreach ($arr AS $value) {


            ////////Fetching Label//////

            $rs = $con->query("SHOW COLUMNS FROM $value");

            $row = $rs->fetch_assoc();

            $pKey = $row['Field'];

            $label = explode('_', $value);

            ////***** FOR FIELD_label_name ****/////

            if (!empty($label[0]) && !empty($label[1])) {

                $strLabel = implode(' ', $label);

                $labeName = ucwords($strLabel);
            } else {

                $labeName = ucwords($label[0]);
            }

           $labeName = $labeName . " Lists";
           
           $tblName = $value;
           $value = $value . "_list";
           
            ////////adding this link to all navigation//////////
            
            $rs = $con->query("SELECT * FROM `navigation` WHERE item_number=5.0");
            
            while( $row = $rs->fetch_assoc() ){
                
                ////insertion in navigation
            $qry = "INSERT INTO $NAVtbl (`nav_id`, `display_page`, `menu_location`, `item_number`, `item_label`, `item_target`, `target_display_page`, `loginRequired`, `item_help`, `item_style`, `item_privilege`, `item_icon`, `item_visibility`, `admin_level`, `enabled`, `menu_orientation`, `page_layout_style`) VALUES (NULL, '$row[display_page]', 'header', '5.$i', '$labeName', 'system/profile.php', '$value', 'true', '', '', '', '', '1', '1', '1', 'HORIZONTAL', '');";


            mysqli_query($con, $qry);
                
                
            }

            //insertion in DD


            $qry = "INSERT INTO $DDtbl (`dict_id`, `table_alias`, `database_table_name`, `table_type`, `parent_table`, `keyfield`, `display_page`, `tab_num`, `tab_name`, `dd_editable`, `dd_visibility`, `user_type`, `dd_privilege_level`, `list_views`, `list_filter`, `list_sort`, `list_extra_options`, `list_style`, `description`, `created`, `list_fields`, `fields_used`, `fd_initialization`, `list_select`) VALUES (NULL, '$value', '$tblName', 'parent', '', '$pKey', '$value', '1', '$labeName', '1', 'DEFAULT', 'ALL', '0', 'listview', '', '', 'checklist; delete,Delete,btn-danger; add,Add,btn-primary;copy,Copy,btn-primary; popupmenu; popup_delete,Delete Check,popup-class;popup_copy,Copy,copy-class;popup_add,ADD,add-class;', '', '', '2014-12-02 15:22:51', '', '', 'update', '$value,1,$value');";


            mysqli_query($con, $qry);
            

            $i++;
        }
        
        /////////Run generated_fd->update_fd() after thiss
    }
    
    
    
    
     public function childTabs() {
        require('dictionaryConfig.php');

        $con = connect();


        //$arr = array('devices','locations','user_devices','user_follow','user_friends','user_liked');

        $arr = array('user_settings');

        $i = 7;

        foreach ($arr AS $value) {


            ////////Fetching Label//////

            $rs = $con->query("SHOW COLUMNS FROM $value");

            $row = $rs->fetch_assoc();

            $pKey = $row['Field'];

            $label = explode('_', $value);

            ////***** FOR FIELD_label_name ****/////

            if (!empty($label[0]) && !empty($label[1])) {

                $strLabel = implode(' ', $label);

                $labeName = ucwords($strLabel);
            } else {

                $labeName = ucwords($label[0]);
            }

           $labeName = $labeName . " Lists";
           
           $tblName = $value;
           $value = $value;

//echo $value . "<br>";

            //insertion in DD


            $qry = "INSERT INTO $DDtbl (`dict_id`, `table_alias`, `database_table_name`, `table_type`, `parent_table`, `keyfield`, `display_page`, `tab_num`, `tab_name`, `dd_editable`, `dd_visibility`, `user_type`, `dd_privilege_level`, `list_views`, `list_filter`, `list_sort`, `list_extra_options`, `list_style`, `description`, `created`, `list_fields`, `fields_used`, `fd_initialization`, `list_select`) VALUES (NULL, '$value', '$tblName', 'child', '', '$pKey', 'userchild', '$i', '$labeName', '1', 'DEFAULT', 'ALL', 'USER', 'listview', 'users=user_id', '', 'checklist; delete,Delete,btn-danger; add,Add,btn-primary;copy,Copy,btn-primary; popupmenu; popup_delete,Delete Check,popup-class;popup_copy,Copy,copy-class;popup_add,ADD,add-class;', '', '', '2014-12-02 15:22:51', '', '', 'update', '$value,$i,userchild');";

            

           mysqli_query($con, $qry);

            $i++;
        }
    }
    
    
    
    /*
     * Removing deadlinks from pages
     * 
     * Enter dd->display_page value for creating same navigation which is visible on the homepage
     */

    
    public function removeDeadLinks(){
        
         require('dictionaryConfig.php');

        $con = connect();
        
        //$dp = 'userchild'; 
        
        
        //$arr = array('crossref','location_types', 'privacy_type', 'scripts_categories', 'scripts_launch_status', 'scripts_permissions', 'scripts_type', 'scripts_visibility', 'sharing_types', 'standard_messages', 'states', 'tone_styles', 'transaction_type', 'user_roles', 'user_type', 'visibility', 'voice_commands', 'voice_styles', 'wallet','user_privileges');
        
          //$arr = array('devices','locations','scripts','scripts_commands','triggers_alarms','user_friends');
        
         // $arr = array('myaccount','myscripts');
        
        //$arr = array('user_list');
        
        $arr = array('script_commands_page');
        
        foreach( $arr as $dp){
        
           // $dp =  $dp. '_list';
            
         $rs = $con->query("select * from navigation where display_page='home'");

            while( $row = $rs->fetch_assoc() ){
                
              
                $con->query("insert into navigation values('','$dp','header','$row[item_number]','$row[item_label]','$row[item_target]','$row[target_display_page]','$row[loginRequired]','$row[item_help]','$row[item_style]','$row[item_privilege]','$row[item_icon]','$row[item_visibility]','$row[admin_level]','$row[enabled]','HORIZONTAL','')");
                
            }
        
        
        }
    }

    
  
    
     /*
     * updating Navigation table
     */

    
    public function updateNav(){
        
         require('dictionaryConfig.php');

        $con = connect();
        
        //$dp = 'userchild'; 
        
        
        $arr = array('crossref','location_types', 'privacy_type', 'scripts_categories', 'scripts_launch_status', 'scripts_permissions', 'scripts_type', 'scripts_visibility', 'sharing_types', 'standard_messages', 'states', 'tone_styles', 'transaction_type', 'user_roles', 'user_type', 'visibility', 'voice_commands', 'voice_styles', 'wallet','user_privileges');
        
          //$arr = array('devices','locations','scripts','scripts_commands','triggers_alarms','user_friends');
        
          //$arr = array('myaccount','myscripts');
        
        foreach( $arr as $dp){
                      
              
                $con->query("update navigation set admin_level='0' where display_page='$dp'"); 
        
        
        }
    }

    
    
    
    /*
     * 
     * Make dropdowns
     */
    
    public function dropdowns() {
        require('dictionaryConfig.php');

        $con = connect();
       
       // $arr = array('myaccount','user_privileges','user_privilege_level','user_privilege_level','user_privilege_id');
        
        
         // $arr = array('projects','user','user_id','user_id','username');
          
         // $arr = array('projects','tone_styles','pre_tone_style','id','tone_name');
         
        //$arr = array('projects','scripts_type','script_type','project_type_id','project_type');
        
      //  $arr = array('project_child','scripts','scripts_id','script_id','script_name');
      
        ///location table
 //$arr = array('locations_list','scripts_commands','command_id','command_id','command_name');
 
        ///triggers_alarms
 // $arr = array('triggers_alarms_list','devices','device_id','device_id','device_name');       
 // 
 
         $arr = array('projects','transaction_type','transaction_type','transaction_type_id','transaction_type_name');       
// //////////////////////
        ////////////////////////////////
        
        $tbl_alias = $arr[0];
        
        $tbl_name = $arr[1];
        
        $target_field = $arr[2];
        
        $primary_field = $arr[3];
        
        $display_field = $arr[4];
        
        
/////entry in field field_dictionary
$con->query("update field_dictionary set format_type='dropdown', dropdown_alias='dropdown_$tbl_name' where table_alias='$tbl_alias' and generic_field_name='$target_field'");



///////dropdown entry in DD////////
$con->query("INSERT INTO data_dictionary (`dict_id`, `table_alias`, `database_table_name`, `table_type`, `parent_table`, `keyfield`, `display_page`, `tab_num`, `tab_name`, `dd_editable`, `dd_visibility`, `user_type`, `dd_privilege_level`, `list_views`, `list_filter`, `list_sort`, `list_extra_options`, `list_style`, `description`, `created`, `list_fields`, `fields_used`, `fd_initialization`, `list_select`) VALUES (NULL, 'dropdown_$tbl_name', '$tbl_name', '', '', 'id', 'dropdown_$tbl_name', '1', 'dropdown_$tbl_name', '1', 'DEFAULT', 'ALL', '0', 'listview', '', '', 'ck,popup-class; ', '', '', '2014-12-02 15:22:51', '*~$primary_field,$display_field', '', '', '');");




///////////new insertion in field dictionary
$con->query("INSERT INTO field_dictionary (`field_def_id`, `table_alias`, `generic_field_name`, `display_field_order`, `field_identifier`, `format_type`, `format_length`, `field_label_name`, `visibility`, `privilege_level`, `editable`, `required`, `error_message`, `help_message`, `dropdown_alias`) VALUES (NULL, 'dropdown_$tbl_name', '$primary_field', '1', NULL, '', '', '$primary_field', '1', '1', 'true', '0', '', '', '');");

///second field insertion in fd

$con->query("INSERT INTO field_dictionary (`field_def_id`, `table_alias`, `generic_field_name`, `display_field_order`, `field_identifier`, `format_type`, `format_length`, `field_label_name`, `visibility`, `privilege_level`, `editable`, `required`, `error_message`, `help_message`, `dropdown_alias`) VALUES (NULL, 'dropdown_$tbl_name', '$display_field', '1', NULL, '', '', '$display_field', '1', '1', 'true', '0', '', '', '');");
    
            }

            
    }//// end of smallUtil class




$obj = new smallUtil();


//$obj->crossRef();

//$obj->adminTBL();

//$obj->childTabs();

//$obj->removeDeadLinks();

//$obj->updateNav();

//$obj->dropdowns();
