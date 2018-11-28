<?php
/* * *SITE INFORMATION********* */
// require_once '../system/config.php';

define("SESSION_AUTO_TIMEOUT","9200");


// =======================================================================
// DATABASE CONFIG (Location, CREDENTIALS)
// =======================================================================

$config['db_host'] = "localhost";

#$config['db_name'] = "genericplatform";
#$config['db_user'] = "genericinternal";
#$config['db_password'] = "Upwork0814!!";


/*
$config['db_name'] = "genericsandbox2";
$config['db_user'] = "genericsandbox2";
$config['db_password'] = "Upwork!081461!";
*/


$config['db_name'] = "genericplatform";
$config['db_user'] = "genericinternal";
$config['db_password'] = "Upwork0814!!";


$GLOBALS['db-host'] =  $config['db_host'];
$GLOBALS['db-username'] = $config['db_name'] ;
$GLOBALS['db-password'] =  $config['db_password'] ;
$GLOBALS['db-database'] = $config['db_name'] ;


if(!empty($config['db_name'])){

    $_SESSION['config'] = $config;
}else{

    unset($_SESSION['config']);
}

if ($_SERVER['HTTP_HOST'] === 'localhost') {
    $GLOBALS['db-username'] = "root";
    $GLOBALS['db-host'] = "localhost";
    $GLOBALS['db-password'] = "";
    $GLOBALS['db-database'] = "generic";
} else {
    /*
     * Here make changes if you want to change the DB Credentials
     */
     /*
    $GLOBALS['db-host'] = $dbconfig->db_host;
    $GLOBALS['db-username'] = $dbconfig->username;
    $GLOBALS['db-password'] = $dbconfig->password;
    $GLOBALS['db-database'] = $dbconfig->database;
*/
}


// =======================================================================
// SYSTEM CONSTANTS, GLOBAL PARAMETERS etc
// =======================================================================


define("SITE_TITLE", "Generic Platform");
define("BRAND_LOGO", "Generic <span>LOGO</span>");
define("BRAND_LOGO_IMAGE", "");
define("BRAND_LOGO_LINK", "/");
define("BRAND_LOGO_POSITION", "left");
define("LOGIN_LOGO", '<a class="logo-login" href="index.php">Generic <span>Platform</span></a>');
<<<<<<< HEAD


define("TOGGLE_NAVIGATION", "Toggle navigation");


=======
/* * *menu labels for HEADER** */
define("HOME_MENU", "Home");
define("ABOUT_MENU", "About");
define("CONTACT_MENU", "Contact");
define("PROFILE_MENU", "Profile");
define("MYACCOUNT_MENU", "My Account");
define("PROJECTS_MENU", "Products");
define("MY_PROJECTS_MENU", "My Products");
define("MY_FAVORITES", "My Favorites");
define("USER_FOLLOW", "My Follows");
define("USER_FRIENDS", "My Friends");
define("USER_LIKES", "My Likes");
define("LOGOUT_MENU", "Logout");
define("LOGIN_MENU", "Login");
define("SIGNUP_MENU", "Sign Up");
define("TOGGLE_NAVIGATION", "Toggle navigation");

define("POPULAR_PROJECTS", "Popular Products");

/* * *TAB LABELS** */
define("MY_ACCOUNT", "My Account");
define("MY_TRANSACTIONS", "My Transactions");
define("OTHER_TRANSACTIONS", "Other Transactions");
define("USER_INFO", "User Info");
>>>>>>> cbc968c550f50dcbb403cb80a03d701ef47d89cf

/* * ********Form labels for SIGNIN page************* */
define("LOGIN_EMAIL_PLACEHOLDER", "Your Email or Username");
define("PASSWORD", "Password");
define("LOGIN_REMEMBERME", "Remember me");
define("FORGOT_PASSWORD", "Forgot my password");
define("SIGN_IN", "Sign in");
<<<<<<< HEAD
define("LOGOUT_MENU", "Logout");
define("LOGIN_MENU", "Login");
define("SIGNUP_MENU", "Sign Up");

=======
>>>>>>> cbc968c550f50dcbb403cb80a03d701ef47d89cf
define("COPY_RIGHTS", "&#169;2001-2014 All Rights Reserved.");
define("REGISTRATION_MESSAGE1", "If you don't have account please");
define("REGISTRATION_MESSAGE2", "Register here.");
define("LOGIN_MESSAGE1", "You just have to input your email id.Or ");
define("LOGIN_MESSAGE2", "here");
define("LOGIN_REQUIRED_MESSAGE", "Your not logged in. Make sure you are logged in.");
define("LOGIN_REQUIRED_MESSAGE_WITH_URL", 'You are not logged in. Please <a href="' . BASE_URL . 'login.php">Log In</a> to comment.');
define("RETRIEVE_PASS", "Retrieve Password");

/* * *Form labels for REGISTER page** */
define("USER_NAME_PLACEHOLDER", "Enter Username");
define("USER_EMAIL_PLACEHOLDER", "Email");
define("USER_PASSWORD_PLACEHOLDER", "Password");
define("USER_REPASSWORD_PLACEHOLDER", "Re-Password");
define("USER_COUNTRY_PLACEHOLDER", "Enter Country");
define("CREATE_ACCOUNT_BUTTON", "Create Account");
define("CANCEL_BUTTON", "Cancel");
define("EMAIL_ALREADY_EXISTS", "Registration not successfull. Email Already Exsits.");
define("REGISTRATION_SUCCESS", "Registration successfull.");
define("REGISTRATION_NOT_SUCCESS", "Registration not successfull");
define("PROFILE_COMPLETE_MESSAGE", "Welcome. Please complete your profile");

/* * *********HOME PAGE********* */
<<<<<<< HEAD
define("HOME_SLIDER_IMAGES_URL", "../application/banner-images/");
=======
define("HOME_SLIDER_IMAGES_URL", "application/banner-images/");
>>>>>>> cbc968c550f50dcbb403cb80a03d701ef47d89cf
define("HOME_SLIDER_HEIGHT", "550px");
define("HOME_SLIDER_TITLE1", "Generic Platform");
define("HOME_SLIDER_CONTENT1", "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.");
define("HOME_SLIDER_BUTTON_TEXT1", "Sign up today");

define("HOME_SLIDER_TITLE2", "Another example headline.");
define("HOME_SLIDER_CONTENT2", "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.");
define("HOME_SLIDER_BUTTON_TEXT2", "Learn more");

define("HOME_SLIDER_TITLE3", "One more for good measure.");
define("HOME_SLIDER_CONTENT3", "Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.");
define("HOME_SLIDER_BUTTON_TEXT3", "Browse gallery");

<<<<<<< HEAD

/* * *********From Ajax Page********* */
/* define("PROJECTS_NOT_AVAILABLE", PROJECT . "s Not Available"); */
=======
define("HOME_FOOTER_TITLE", "Where products come from");
define("HOME_FOOTER_CONTENT", " Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. ");
define("HOME_FOOTER_BUTTON_TEXT", "Learn more");

define("SEARCH_PROJECTS", "Search Products");
define("SORT_BY", "Sort by");
define("ALPHABETICALL", "Alphabetically");
define("DATE_JOINED", "Date Joined");
define("RELEVANCE", "Relevance");
define("TODAY", "Today");
define("LAST_WEEK", "Last Week");
define("LAST_MONTH", "Last Month");

/* * *Form labels for CREATE PROJECT page** */
define("PROJECT", "Product");
define("PROJECT_NAME", PROJECT . " Name");
define("PROJECT_PRICE", PROJECT . " Price");
define("PROJECT_QUANTITY", PROJECT . " Quantity");
define("PROJECT_CATEGORY", "Category");
define("PROJECT_EXPIRY_DATE", "Expiry Date");
define("PROJECT_CREATED_DATE", "Created Date");
define("PROJECT_LAUNCH", "Launch");
define("PROJECT_TAGS", "Tags");
define("PROJECT_AFFILIATION_ONE", "Affiliation 1");
define("PROJECT_AFFILIATION_TWO", "Affiliation 2");
define("PROJECT_TAG_PLACEHOLDER", "Add tags here");
define("ADD_TAG_BUTTON", "Add Tag");
define("PROJECT_DESCRIPTION", "Description");
define("RESET_BUTTON", "Reset");
define("PROJECT_SAVE_BUTTON", "Save " . PROJECT);
define("PROJECT_NAME_PLACEHOLDER", PROJECT . " Name");
define("PROJECT_DESC_PLACEHOLDER", PROJECT . " Description");
define("PROJECT_ADDED_SUCCESS", PROJECT . " added successfully.");
define("PROJECT_NOT_ADDED_SUCCESS", PROJECT . " could not be added.Please try again");
define("PROJECT_UPDATE_SUCCESS", PROJECT . " Updated successfully.");
define("PROJECT_NOT_UPDATE_SUCCESS", PROJECT . " could not be updated.Please try again");
define("PROJECT_IMAGE_NOT_UPDATE_SUCCESS", PROJECT . " Image could not be updated.Please try again");
define("PROJECT_DELETE_SUCCESS", PROJECT . " Deleted successfully.");
define("PROJECT_IMAGE_REMOVE_SUCCESS", PROJECT . " Image Removed Successfull.");
define("PROJECT_IMAGE_REMOVE_NOT_SUCCESS", PROJECT . " Image Removed Not Successfull.");
define("FORK_PROJECT", "Allow Fork ");
define("COPY_PROJECT", "Allow Copy ");
define("SUBSCRIBE_PROJECT", "Allow Subscribe ");
define("SHARE_PROJECT", "Allow Share ");
define("SHOW_PROJECT_DESC", "Show Description ");
define("SHOW_PROJECT_IMG_GALLERY", "Show Image Gallery ");
define("SHOW_PROJECT_TRANSACTIONS", "Show Transactions ");
define("SHOW_PROJECT_COMMENTS", "Show Comments ");
define("PROJECT_SCRIPT", "Script");
define("PROJECT_FAVORITES", "Product Favorites");
define("PROJECT_LAUNCH_STATUS_LABEL", "Product Launch Status");


/* * *Form labels for PROFILE page** */
define("USER", "User");
define("USER_NAME", "Name");
define("USER_FIRST_NAME", "First Name");
define("USER_LAST_NAME", "Last Name");
define("USER_ABOUT_ME", "About me");
define("USER_INTERESTS", "Interests");
define("USER_SKILLS", "Skills");
define("USER_EMAIL", "Email");
define("USER_COMPANY", "Company");
define("USER_CITY", "City");
define("USER_STATE", "State");
define("USER_ZIP", "Zip");
define("USER_FACEBOOK_ACCOUNT", "Your Facebook account");
define("USER_GOOGLEPLUS_ACCOUNT", "Your Google+ account");
define("USER_TWITTER_ACCOUNT", "Your Twitter account");
define("USER_DESCRIPTION", "Profile");
define("USER_COUNTRY", "Country");
define("UPDATE_PROFILE_BUTTON", "Update Profile");
define("USER_IMAGE_SAVE_BUTTON", "SAVE");
define("USER_IMAGE_CANCEL_BUTTON", "CANCEL");
define("REMOVE_IMAGE_BUTTON", "Remove Image");
define("PROFILE_UPDATE_SUCCESS", "Profile Updated Successfully.");
define("PROFILE_UPDATE_NOT_SUCCESS", "Profile Updated Not Successfully.");
define("PROFILE_COMPLETE_LABEL", "Profile Completion");
define("SEARCH_TRANSACTIONS", "Search Transaction");
define("PROFILE_IMAGE_UPLOAD_SUCCESS", "Image successfully uploaded");
define("PROFILE_IMAGE_UPLOAD_ERROR", "Image could not be uploaded. Please try again");
define("SEARCH_USERS", "Search " . USER);
define("USER_TYPE", "User Types");
/* * *********From Ajax Page********* */
define("PROJECTS_NOT_AVAILABEL", PROJECT . "s Not Available");
>>>>>>> cbc968c550f50dcbb403cb80a03d701ef47d89cf
define("SEARCH", "Search");
define("CREATED", "Created");
define("SORT", "Sort");
define("SELECT", "Select");
define("PAGE", "Page");
define("OF", "of");
define("FIRST", "First");
define("PREV", "Prev");
define("NEXT", "Next");
define("LAST", "Last");
define("SHOW_ALL", "Show All");

/* * *******TRANSACTION MESSAGES**************** */
define("TRANSACTION", "Transaction");
define("LOGIN_TO_BUY", "Please Login to buy a " . PROJECT . ".");
define("TRANSACTION_HISTORY", TRANSACTION . " History");
define("TRANSACTION_SUCCESS", TRANSACTION . " successfull.");
define("TRANSACTION_FAIL", TRANSACTION . " Not successfull. Try again.");
define("WALLET_BALANCE_ERROR", "Your Wallet dosen\'t have enough balance. " . TRANSACTION . " in pending.");

<<<<<<< HEAD
=======
/* * *******PROJECTS PAGE**************** */
define("MY_PROJECTS_TITLE", "My " . PROJECT . "s");
define("OTHERS_PROJECTS_TITLE", "Others " . PROJECT . "s");
define("CREATE_PROJECTS_TITLE", "Create " . PROJECT);
define("PROJECTS_NOT_AVAILABLE_MESSAGE", "You currently do not have any " . PROJECT . ". Please <a href='" . BASE_URL . "createProduct.php'>CREATE</a> one.");
define("OTHERS_PROJECTS_NOT_AVAILABLE_MESSAGE", "Currently There are no " . PROJECT . "s available.");
define("MANAGE_PROJECT", "Manage " . PROJECT);
define("DELETE_PROJECT", "Delete");
>>>>>>> cbc968c550f50dcbb403cb80a03d701ef47d89cf

/* * *******UPLOAD CARE PLUGIN MESSAGES**************** */
define("ERROR_PORTRAIT", "Landscape images only");
define("ERROR_PORTRAIT_TITLE", "No portrait images are allowed.");
define("ERROR_PORTRAIT_TEXT", "We are sorry but image should be landscape.");
define("ERROR_DIMENSIONS", "Dimensions should be more or equal to 650 X 130");
define("ERROR_DIMENSIONS_TITLE", "Dimensions should be more or equal to 650 X 130");
define("ERROR_DIMENSIONS_TEXT", "We are sorry but image Dimensions should be more or equal to 650 X 130.");
define("BACK_BUTTON", "Back");

<<<<<<< HEAD
=======
/* * *******COMMENTS**************** */
define("COMMENT", "Comment");
define("COMMENT_BUTTON", "Post a Comment");
define("NO_COMMENTS", "No " . COMMENT . "s has been posted yet");
define("COMMENTS_POST_ERROR", "Some internal error. Your " . COMMENT . " couldnt be posted.");
define("NO_COMMENT_EMPTY", "Please make sure you " . COMMENT . " box in not empty");

/* * *Form labels for USERS page** */
define("SORT_BUTTON", "SORT");
define("SEARCH_BUTTON", "Searech Users");
#define("SORT_BY", "Sort By");
define("PROJECT_VISBILITY_LABEL", "Product Visibility");

>>>>>>> cbc968c550f50dcbb403cb80a03d701ef47d89cf

/* ****** SPECIAL CONSTANTS  ****  */

define('EDIT', 'Edit');


/*  Constants for DD DataDictionary Special Table Types  */
$internal_table_types = array(
"USER",
"PROJECT",
"CROSSREF",
"TRANSACTION",
"CHILD",
"P2P");

$arrlength=count($internal_table_types);
for($x=0;$x<$arrlength;$x++)
  {
  $z=$internal_table_types[$x]."_TABLETYPE";
  define($z, $internal_table_types[$x]);
  }

/******
 *
 * Defining default field_length for field_types
 */
define("defaultFeildLenInteger", "30");
define("defaultFeildLenOtherInteger", "20");
define("defaultFeildLenText", "40");
define("defaultFeildLenTextarea", "50");
define("defaultFeildLenBoolean", "10");

define("showClear", "true"); ///set rating icon
define("dropdownSeparator", "&nbsp;");///dropdown separator
define("favoriteTitle", "Favorite");///FFFR favorite title
define("friendTitle", "Friend");///
define("followTitle", "Follow");///

//define("ratingTitle", "Rate Me");///dropdown separator

define("AlERTBOX", "Alert Box"); ////Alert box of voting(FFFR)
define("voteInserted", "<p>Vote Inserted Successfully</p>"); ////Alert box of vote in number format
define("editBtnAlertMsg", "Please update/cancel the current data before trying to edit a different column"); ////Alert box of Edit button
define("backAlertMsg", "Are you sure ,You want to go to Main List without Saving!"); ////Alert box when user want to go back to list without saving the form.

/************ On page Form pagination Constants *********/

define("pageFirst", "First");
define("pagePrev", "Prev");
define("pageNext", "Next");
define("pageLast", "Last");



/************ Audio upload Constants *********/

/**** buttons TEXT are enraped in anchor tag. so you can replace
 *  these names with html tag as well , for using glyphicon icons etc ****/

define("audioRecord", "Record");
define("audioPause", "Pause");
define("audioClear", "Clear");
define("audioCancel", "Cancel");
define("audioResume", "Resume");
define("audioStop", "Stop");
define("audioRecordingMsg", "Recording has been Started!");
define("audioRecordingResume", "Recording has been Resumed/Started!");
define("audioPauseMsg", "Recording has been Paused!");


/**************FFFR ICONS CONSTANTS***********/
#define("audioClear", "Clear");


/******** update & cancel button & Save Record ***********/
define("formUpdate", "Update");
define("formCancel", "Cancel");
define("formSave", "Save Record");


/************* FFFR ICONS **************/

define("friendOn", "Friend");
define("friendOff", "Un-Friend");
define("followOn", "Follow me");
define("followOff", "Un-Follow me");
define("voteChangeOptionDisable", "<p>You Can not Change your vote</p>");
define("votingNoSubmitBtn", "<i class='glyphicon glyphicon-ok-sign'></i>");
#define("formSave", "Save Record");


<<<<<<< HEAD
=======


>>>>>>> cbc968c550f50dcbb403cb80a03d701ef47d89cf
/************* List filter multiple Selection of Delete/copy Confirmation Msgs **************/
define("deleteConfirm", "Are you sure ,You want to Delete the Records!");
define("copyConfirm", "Are you sure ,You want to Copy the Records!");
define("pdfInline", "Click Me!");
define("noFile", "No File!");


<<<<<<< HEAD

/******** Constant use for user_privilege_level *******/
define("user_privilege_level", "user_privilege_level");
define("USER_PRIVILEGE", 'YES');
=======
/******** Constant use for user_privilege_level *******/
define("user_privilege_level", "user_privilege_level");
>>>>>>> cbc968c550f50dcbb403cb80a03d701ef47d89cf

/******** Constant use for Transaction popup Title *******/
define("transTtile", "Title Goes here");
define("transSuccess", "Transaction Successful!");
<<<<<<< HEAD
define("transFail", "Transaction Fail, Please Try again!");

/**************** ERROR CODE *********************/
define("ERROR_403","You don't have enough privilege to view contents");




/* * *Form labels for PROFILE page** */
/* THESE ARE NO LONGER NEEDED!
define("USER", "User");
define("USER_NAME", "Name");
define("USER_FIRST_NAME", "First Name");
define("USER_LAST_NAME", "Last Name");
define("USER_ABOUT_ME", "About me");
define("USER_INTERESTS", "Interests");
define("USER_SKILLS", "Skills");
define("USER_EMAIL", "Email");
define("USER_COMPANY", "Company");
define("USER_CITY", "City");
define("USER_STATE", "State");
define("USER_ZIP", "Zip");
define("USER_FACEBOOK_ACCOUNT", "Your Facebook account");
define("USER_GOOGLEPLUS_ACCOUNT", "Your Google+ account");
define("USER_TWITTER_ACCOUNT", "Your Twitter account");
define("USER_DESCRIPTION", "Profile");
define("USER_COUNTRY", "Country");
define("UPDATE_PROFILE_BUTTON", "Update Profile");
define("USER_IMAGE_SAVE_BUTTON", "SAVE");
define("USER_IMAGE_CANCEL_BUTTON", "CANCEL");
define("REMOVE_IMAGE_BUTTON", "Remove Image");
define("PROFILE_UPDATE_SUCCESS", "Profile Updated Successfully.");
define("PROFILE_UPDATE_NOT_SUCCESS", "Profile Updated Not Successfully.");
define("PROFILE_COMPLETE_LABEL", "Profile Completion");
define("SEARCH_TRANSACTIONS", "Search Transaction");
define("PROFILE_IMAGE_UPLOAD_SUCCESS", "Image successfully uploaded");
define("PROFILE_IMAGE_UPLOAD_ERROR", "Image could not be uploaded. Please try again");
define("SEARCH_USERS", "Search " . USER);
define("USER_TYPE", "User Types");
*/



/* * *Form labels for CREATE PROJECT page** */
/* THESE ARE NO LONGER NEEDED!
define("PROJECT", "Product");
define("PROJECT_NAME", PROJECT . " Name");
define("PROJECT_PRICE", PROJECT . " Price");
define("PROJECT_QUANTITY", PROJECT . " Quantity");
define("PROJECT_CATEGORY", "Category");
define("PROJECT_EXPIRY_DATE", "Expiry Date");
define("PROJECT_CREATED_DATE", "Created Date");
define("PROJECT_LAUNCH", "Launch");
define("PROJECT_TAGS", "Tags");
define("PROJECT_AFFILIATION_ONE", "Affiliation 1");
define("PROJECT_AFFILIATION_TWO", "Affiliation 2");
define("PROJECT_TAG_PLACEHOLDER", "Add tags here");
define("ADD_TAG_BUTTON", "Add Tag");
define("PROJECT_DESCRIPTION", "Description");
define("RESET_BUTTON", "Reset");
define("PROJECT_SAVE_BUTTON", "Save " . PROJECT);
define("PROJECT_NAME_PLACEHOLDER", PROJECT . " Name");
define("PROJECT_DESC_PLACEHOLDER", PROJECT . " Description");
define("PROJECT_ADDED_SUCCESS", PROJECT . " added successfully.");
define("PROJECT_NOT_ADDED_SUCCESS", PROJECT . " could not be added.Please try again");
define("PROJECT_UPDATE_SUCCESS", PROJECT . " Updated successfully.");
define("PROJECT_NOT_UPDATE_SUCCESS", PROJECT . " could not be updated.Please try again");
define("PROJECT_IMAGE_NOT_UPDATE_SUCCESS", PROJECT . " Image could not be updated.Please try again");
define("PROJECT_DELETE_SUCCESS", PROJECT . " Deleted successfully.");
define("PROJECT_IMAGE_REMOVE_SUCCESS", PROJECT . " Image Removed Successfull.");
define("PROJECT_IMAGE_REMOVE_NOT_SUCCESS", PROJECT . " Image Removed Not Successfull.");
define("FORK_PROJECT", "Allow Fork ");
define("COPY_PROJECT", "Allow Copy ");
define("SUBSCRIBE_PROJECT", "Allow Subscribe ");
define("SHARE_PROJECT", "Allow Share ");
define("SHOW_PROJECT_DESC", "Show Description ");
define("SHOW_PROJECT_IMG_GALLERY", "Show Image Gallery ");
define("SHOW_PROJECT_TRANSACTIONS", "Show Transactions ");
define("SHOW_PROJECT_COMMENTS", "Show Comments ");
define("PROJECT_SCRIPT", "Script");
define("PROJECT_FAVORITES", "Product Favorites");
define("PROJECT_LAUNCH_STATUS_LABEL", "Product Launch Status");
*/



/* * *Form labels for USERS page** */
/* THESE ARE NO LONGER NEEDED!
define("SORT_BUTTON", "SORT");
define("SEARCH_BUTTON", "Searech Users");
#define("SORT_BY", "Sort By");
define("PROJECT_VISBILITY_LABEL", "Product Visibility");
*/



/* * *******PROJECTS PAGE**************** */
/* THESE ARE NO LONGER NEEDED!

define("MY_PROJECTS_TITLE", "My " . PROJECT . "s");
define("OTHERS_PROJECTS_TITLE", "Others " . PROJECT . "s");
define("CREATE_PROJECTS_TITLE", "Create " . PROJECT);
define("PROJECTS_NOT_AVAILABLE_MESSAGE", "You currently do not have any " . PROJECT . ". Please <a href='" . BASE_URL . "createProduct.php'>CREATE</a> one.");
define("OTHERS_PROJECTS_NOT_AVAILABLE_MESSAGE", "Currently There are no " . PROJECT . "s available.");
define("MANAGE_PROJECT", "Manage " . PROJECT);
define("DELETE_PROJECT", "Delete");
define("POPULAR_PROJECTS", "Popular Products");


define("SEARCH_PROJECTS", "Search Products");
define("SORT_BY", "Sort by");
define("ALPHABETICALL", "Alphabetically");
define("DATE_JOINED", "Date Joined");
define("RELEVANCE", "Relevance");
define("TODAY", "Today");
define("LAST_WEEK", "Last Week");
define("LAST_MONTH", "Last Month");
*/



/* * *TAB LABELS** */
/* THESE ARE NO LONGER NEEDED!
define("MY_ACCOUNT", "My Account");
define("MY_TRANSACTIONS", "My Transactions");
define("OTHER_TRANSACTIONS", "Other Transactions");
define("USER_INFO", "User Info");
*/


/* * *menu labels for HEADER** */
/* THESE ARE NO LONGER NEEDED!
define("HOME_MENU", "Home");
define("ABOUT_MENU", "About");
define("CONTACT_MENU", "Contact");
define("PROFILE_MENU", "Profile");
define("MYACCOUNT_MENU", "My Account");
define("PROJECTS_MENU", "Products");
define("MY_PROJECTS_MENU", "My Products");
define("MY_FAVORITES", "My Favorites");
define("USER_FOLLOW", "My Follows");
define("USER_FRIENDS", "My Friends");
define("USER_LIKES", "My Likes");
*/
/*
define("HOME_FOOTER_TITLE", "Where products come from");
define("HOME_FOOTER_CONTENT", " Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. ");
define("HOME_FOOTER_BUTTON_TEXT", "Learn more");
*/



/* * *******COMMENTS**************** */
/* THESE ARE NO LONGER NEEDED!
define("COMMENT", "Comment");
define("COMMENT_BUTTON", "Post a Comment");
define("NO_COMMENTS", "No " . COMMENT . "s has been posted yet");
define("COMMENTS_POST_ERROR", "Some internal error. Your " . COMMENT . " couldnt be posted.");
define("NO_COMMENT_EMPTY", "Please make sure you " . COMMENT . " box in not empty");
*/
=======
define("transFail", "Transaction Fail, Please Try again!");
>>>>>>> cbc968c550f50dcbb403cb80a03d701ef47d89cf
