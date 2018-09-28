<?php

/***** CONSTANT DEFINES HERE ****/
define('EDIT', 'Edit');

/******************************/



////////// Default Values for FD//////////////

$APP_DEFAULT['FD']['help_message'] = '';

$APP_DEFAULT['FD']['error_message'] = '';

$APP_DEFAULT['FD']['format_length'] = '';

$APP_DEFAULT['FD']['privilege_level'] = '0';

$APP_DEFAULT['FD']['visibility'] = '1';

$APP_DEFAULT['FD']['dropdown_alias'] = '';

$APP_DEFAULT['FD']['required'] = '0';

$APP_DEFAULT['FD']['editable'] = 'true';







//**** * Uncomment below variable ***$searchReplace*** if you want search/replace text in DD and NAV*****////


/** here "system" is a string which will replace "comments" in any terms in DD and NAV */

$searchReplace = array('script', 'product','Script', 'Product', 'product', 'product', 'member name', 'username', 'Cyrano', 'Generic');


if (isset($searchReplace) && !empty($searchReplace)) {

    $searchReplace = array_chunk($searchReplace, 2);


    foreach ($searchReplace as $val) {


        $APP_DEFAULT['DD'][$val[0]] = $val[1];
    }
}






