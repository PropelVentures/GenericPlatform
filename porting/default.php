<?php

/**
 * Created by Susmit.
 * User: Admin
 * Date: 10/19/14
 * Time: 6:07 AM
 */
$DEFAULT = array();


ini_set('max_execution_time', 600);



$DATA = array();
//$DEFAULT['start_page']=1;
//$DEFAULT['start_page']=1;
////////// Default Values for FD//////////////



if (!empty($config['db_name'])) {

    $DEFAULT['FD'] = $APP_DEFAULT['FD'];

    $DEFAULT['DD'] = $APP_DEFAULT['DD'];
   
    $DEFAULT['NAV'] = $APP_DEFAULT['NAV'];
    
} else {
    
    $DEFAULT['FD']['help_message'] = '';

    $DEFAULT['FD']['error_message'] = '';

    $DEFAULT['FD']['format_length'] = '';

    $DEFAULT['FD']['privilege_level'] = '1';

    $DEFAULT['FD']['visibility'] = '1';

    $DEFAULT['FD']['dropdown_alias'] = '';

    $DEFAULT['FD']['required'] = '0';

    $DEFAULT['FD']['editable'] = 'true';

    /*
     * Uncomment below variable if you want search/replace text in DD or Navigation
     */
//$DEFAULT['DD']['comments'] = 'system';
//$DEFAULT['NAV']['change'] = 'script';
}