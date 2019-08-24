<?php

if ($_SERVER['HTTP_HOST'] === 'localhost') {
    define('APP_DIR', $_SERVER['DOCUMENT_ROOT'] . '/GenericPlatform/'); // Base Root or Directory Path For Application
    $GLOBALS['APP_DIR'] = $_SERVER['DOCUMENT_ROOT'] . '/GenericPlatform/';#'generic/'
    define('BASE_URL', 'http://localhost/GenericPlatform/');#http://localhost/generic/
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

} else {
        define('APP_DIR', (!empty($_SERVER['SUBDOMAIN_DOCUMENT_ROOT'])) ? $_SERVER['SUBDOMAIN_DOCUMENT_ROOT'] : $_SERVER['DOCUMENT_ROOT']); // Base Root or Directory Path For Application
        $GLOBALS['APP_DIR'] = (!empty($_SERVER['SUBDOMAIN_DOCUMENT_ROOT'])) ? $_SERVER['SUBDOMAIN_DOCUMENT_ROOT'] . '/' : $_SERVER['DOCUMENT_ROOT'] . '/'; // Base Root or Directory Path For Application

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
   define('BASE_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/');
}else{

         define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/');
    }

    ini_set('display_error', 1);
    error_reporting(1);
}


$GLOBALS['session_set'] = 0;

//echo APP_DIR . "<br>" . $GLOBALS['APP_DIR'] . "<br>" . BASE_URL;exit();
/* System URLS */
define('BASE_URL_SYSTEM', BASE_URL . 'system/');
define('BASE_URL_ADMIN', BASE_URL . 'admin/');
define('BASE_URL_APP', BASE_URL . 'application/');
define('CUSTOM_CSS_URL',  '../application/custom_css/');
define('BASE_CSS_URL', '../system/css/');
define('BASE_JS_URL',  '../system/js/');
define('BASE_IMAGES_URL',  '../system/system_images/');
define('CHILD_FILES_URL', BASE_URL . 'childPages/');


define('USER_UPLOADS',  '../application/user_uploads/');
define('USER_UPLOADS_THUMB', '../application/user_uploads/thumbs/');
define('PROJECT_UPLOADS', BASE_URL . '../application/project_uploads/');
define('PROJECT_UPLOADS_THUMB', BASE_URL . '../application/project_uploads/thumbs/');

/* System Configurations */
define('PROJECT_TEAM_SIZE', '5');  // Project team size
define('PROJECT_VISIBILITY', true);  // Project visibility

/* * ************User Type/Privacy Configurations @starts******************* */

define('USER_TYPES_ENABLED', true); // [true/false] user-types enabled in the app
define('USER_TYPES_DISPLAYED', true); // [true/false] if UserTypesEnabled and UserTypesDisplayed enabled user type will be displayed next to username in the header.
define('USER_TYPES_SELF_CHANGE', true);

/* [FALSE/true] (can the user Change their usertype once it has been set?)
  this is really UserTypesSelfChange = [FALSE/true]  AND UserTypesSelfSelect */

define('USER_TYPES_APPROVAL_NEEDED', true);
/* [TRUE/false] (does the user need admin approval when they select a type)
  if so - then their self-selection of a user type is Pending notification and approval by the admin. */

define('USER_TYPES_MULTIPLE', true);
/* [TRUE/false]	(will the user be able to have more than 1 type assigned - and thus be able to log in as a different user-type) */

define('USER_TYPES_SELF_SELECT', true);
/* [TRUE/false] (While Registration we have to show.)(can the user self-select their own user type or is this only for admins) this is really UserTypesSelfSelect = [TRUE/false] AND UserTypesDisplayed */


/* * ************User Type/Privacy Configurations @ends******************* */

/* * ************Project Type/Privacy Configurations @starts******************* */

define('PROJECT_PRIVACY_ENABLED', false);
define('PROJECT_DRAFT_MODE_ENABLED', false);
define('PROJECT_LAUNCH_APPROVAL_NEEDED', false);
/* * ************Project Type/Privacy Configurations @ends******************* */


define('EMPTY_LISTS_MESSAGE','No Record Found');

/* ****** Top & Bottom table types @starts ******* */
define("DD_DEFAULT_TOP", "'header', 'header1', 'header2', 'subheader1', 'subheader2'");
define("DD_DEFAULT_BOTTOM", "");
/* ****** Top & Bottom table types @ends ******* */

$MYPATH = APP_DIR;


return $MYPATH;
