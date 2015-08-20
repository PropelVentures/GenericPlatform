<?php

if ($_SERVER['HTTP_HOST'] === 'localhost')
  include_once($_SERVER['DOCUMENT_ROOT'] . '/application/config.php');
else
  include_once($_SERVER['DOCUMENT_ROOT'] . '/generic/application/config.php');

/* * *SITE INFORMATION********* */
define("SITE_TITLE", "Generic Platform");
define("BRAND_LOGO", "Generic <span>LOGO</span>");
define("LOGIN_LOGO", '<a class="logo-login" href="index.php">Generic <span>Platform</span></a>');
/* * *menu labels for HEADER** */
define("HOME_MENU", "Home");
define("ABOUT_MENU", "About");
define("CONTACT_MENU", "Contact");
define("PROFILE_MENU", "Profile");
define("MYACCOUNT_MENU", "My Account");
define("PROJECTS_MENU", "Projects");
define("MY_PROJECTS_MENU", "My Projects");
define("MY_FAVORITES", "My Favorites");
define("USER_FOLLOW", "My Follows");
define("USER_FRIENDS", "My Friends");
define("USER_LIKES", "My Likes");
define("LOGOUT_MENU", "Logout");
define("LOGIN_MENU", "Login");
define("SIGNUP_MENU", "Sign Up");
define("TOGGLE_NAVIGATION", "Toggle navigation");

define("POPULAR_PROJECTS", "Popular Projects");

/* * *TAB LABELS** */
define("MY_ACCOUNT", "My Account");
define("MY_TRANSACTIONS", "My Transactions");
define("OTHER_TRANSACTIONS", "Other Transactions");
define("USER_INFO", "User Info");
define("MY_TRANSACTIONS", "My Transactions");
define("OTHER_TRANSACTIONS", "Other Transactions");

/* * ********Form labels for SIGNIN page************* */
define("LOGIN_EMAIL_PLACEHOLDER", "Your Email or Username");
define("PASSWORD", "Password");
define("LOGIN_REMEMBERME", "Remember me");
define("FORGOT_PASSWORD", "Forgot my password");
define("SIGN_IN", "Sign in");
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
define("HOME_SLIDER_TITLE1", "Generic Platform");
define("HOME_SLIDER_CONTENT1", "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.");
define("HOME_SLIDER_BUTTON_TEXT1", "Sign up today");

define("HOME_SLIDER_TITLE2", "Another example headline.");
define("HOME_SLIDER_CONTENT2", "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.");
define("HOME_SLIDER_BUTTON_TEXT2", "Learn more");

define("HOME_SLIDER_TITLE3", "One more for good measure.");
define("HOME_SLIDER_CONTENT3", "Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.");
define("HOME_SLIDER_BUTTON_TEXT3", "Browse gallery");

define("HOME_FOOTER_TITLE", "Where projects come from");
define("HOME_FOOTER_CONTENT", " Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. ");
define("HOME_FOOTER_BUTTON_TEXT", "Learn more");

define("SEARCH_PROJECTS", "Search Projects");
define("SORT_BY", "Sort by");
define("ALPHABETICALL", "Alphabetically");
define("DATE_JOINED", "Date Joined");
define("RELEVANCE", "Relevance");
define("TODAY", "Today");
define("LAST_WEEK", "Last Week");
define("LAST_MONTH", "Last Month");

/* * *Form labels for CREATE PROJECT page** */
define("PROJECT", "Project");
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
define("PROJECT_FAVORITES", "Project Favorites");
define("PROJECT_LAUNCH_STATUS_LABEL", "Project Launch Status");


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

/* * *******PROJECTS PAGE**************** */
define("MY_PROJECTS_TITLE", "My " . PROJECT . "s");
define("OTHERS_PROJECTS_TITLE", "Others " . PROJECT . "s");
define("CREATE_PROJECTS_TITLE", "Create " . PROJECT);
define("PROJECTS_NOT_AVAILABLE_MESSAGE", "You currently do not have any " . PROJECT . ". Please <a href='" . BASE_URL . "createProject.php'>CREATE</a> one.");
define("OTHERS_PROJECTS_NOT_AVAILABLE_MESSAGE", "Currently There are no " . PROJECT . "s available.");
define("MANAGE_PROJECT", "Manage " . PROJECT);
define("DELETE_PROJECT", "Delete");

/* * *******UPLOAD CARE PLUGIN MESSAGES**************** */
define("ERROR_PORTRAIT", "Landscape images only");
define("ERROR_PORTRAIT_TITLE", "No portrait images are allowed.");
define("ERROR_PORTRAIT_TEXT", "We are sorry but image should be landscape.");
define("ERROR_DIMENSIONS", "Dimensions should be more or equal to 650 X 130");
define("ERROR_DIMENSIONS_TITLE", "Dimensions should be more or equal to 650 X 130");
define("ERROR_DIMENSIONS_TEXT", "We are sorry but image Dimensions should be more or equal to 650 X 130.");
define("BACK_BUTTON", "Back");

/* * *******COMMENTS**************** */
define("COMMENT", "Comment");
define("COMMENT_BUTTON", "Post a Comment");
define("NO_COMMENTS", "No " . COMMENT . "s has been posted yet");
define("COMMENTS_POST_ERROR", "Some internal error. Your " . COMMENT . " couldnt be posted.");
define("NO_COMMENT_EMPTY", "Please make sure you " . COMMENT . " box in not empty");

/* * *Form labels for USERS page** */
define("SORT_BUTTON", "SORT");
define("SEARCH_BUTTON", "Searech Users");
define("SORT_BY", "Sort By");
define("PROJECT_VISBILITY_LABEL", "Project Visibility");
?>