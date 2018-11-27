<?php

//********************************  Configuration  ********************

require_once("../application/system-config.php");
// mainly need this for the DB credentials ...
// might later put them here ... since later we will only need the main genericdev credentials (for a reference database)
// and the CURRENT database credentials

require_once("../system/dbFunctions.php");
//  might just need to move a copy into /porting



ini_set('max_execution_time', 600);

$FDtbl='field_dictionary';
$DDtbl='data_dictionary';
$DDT= 'data_dictionary_template';
$NAVtbl = 'navigation';
$NAVT = 'navigation_template';
$DATA= array();
//**********************************************************************

$DEFAULT = array();


////////// Default Values for FD//////////////

$APP_DEFAULT['FD']['help_message'] = '';
$APP_DEFAULT['FD']['error_message'] = '';
$APP_DEFAULT['FD']['format_length'] = '';
$APP_DEFAULT['FD']['privilege_level'] = '0';
$APP_DEFAULT['FD']['visibility'] = '1';
$APP_DEFAULT['FD']['dropdown_alias'] = '';
$APP_DEFAULT['FD']['required'] = '0';
$APP_DEFAULT['FD']['editable'] = 'true';



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




/*

// **** * Uncomment below variable ***$searchReplace*** if you want search/replace text in DD and NAV*****  ////
// ** here "system" is a string which will replace "comments" in any terms in DD and NAV
$searchReplace = array('script', 'product','Script', 'Product', 'product', 'product', 'member name', 'username', 'Cyrano', 'Generic');
if (isset($searchReplace) && !empty($searchReplace)) {
    $searchReplace = array_chunk($searchReplace, 2);
    foreach ($searchReplace as $val) {
        $APP_DEFAULT['DD'][$val[0]] = $val[1];
    }
}

*/
