<?php
@session_start();

$_SESSION['lang'] = 'en';

require_once 'config.php';
//exit($GLOBALS['APP_DIR']);
include_once($GLOBALS['LANGUAGE_APP_DIR'] . $_SESSION['lang'] . ".php");
include_once($GLOBALS['APP_DIR'] . "application/functions.php");
include_once($GLOBALS["APP_DIR"] . "models/GenericDBFunctions.php");
require_once($GLOBALS["APP_DIR"] . "DDICT/masterFunctions.php");


if($_GET['source'] == 'api'){

    $_SESSION['uid'] = $_GET['uid'];
    $_SESSION['uname'] = $_GET['uname'];
    $_SESSION['email'] = $_GET['email'];
    $_SESSION['country'] = $_GET['country'];

    $dictId = $_GET['did'];

    $dict = get_single_record('data_dictionary','dict_id', $dictId);

    $_GET['display'] = $dict['display_page'];

    $_GET['layout'] = $_GET['layout'];

    $_GET['style'] = $_GET['style'];

    $_SESSION['user_privilege'] = $_GET['user_privilege'];
}

?>

<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link href='http://fonts.googleapis.com/css?family=Galdeano' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:700italic,400,600,800' rel='stylesheet' type='text/css'>

        <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>bootstrap.min_1.css" type="text/css">
        <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>carousel.css" type="text/css">
        <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>font-awesome.css" type="text/css">
        
        <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>style.css" type="text/css">
        <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>common-responsive.css" type="text/css">
        <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>responsive.css">
        
      
        <link rel="stylesheet" href="<?php echo BASE_URL ?>appConfig/custom-css.css" type="text/css">
<!--        <script src="http://scrollrevealjs.org/js/scrollReveal.min.js?ver=0.2.0-rc.1"></script>-->
        <script src="https://unpkg.com/scrollreveal"></script>

        <link rel="stylesheet" href="<?php echo BASE_URL ?>star-rating/star-rating.css" media="all"  type="text/css" />
        <script src="<?php echo BASE_JS_URL ?>jquery-1.11.1.min.js"></script>
        <script src="<?php echo BASE_JS_URL ?>jquery-ui.js"></script>
        <script src="<?php echo BASE_JS_URL ?>jquery.mobile-events.js"></script>
        
         <script src="<?php echo BASE_JS_URL ?>tag-it.min.js"></script>
        
        <script src="<?php echo BASE_JS_URL ?>mobileDetector.js"></script>
        <script src="<?php echo BASE_JS_URL ?>modernizr.js"></script>

        <!-- including voice javascript -->

        <script src="<?php echo BASE_JS_URL ?>Fr.voice.js"></script>
        <script src="<?php echo BASE_JS_URL ?>recorder.js"></script>
        <script src="<?php echo BASE_JS_URL ?>recorderWorker.js"></script>



        <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/s/dt/dt-1.10.10/datatables.min.css'/>
        <script type='text/javascript' src='https://cdn.datatables.net/s/dt/dt-1.10.10/datatables.min.js'></script>
        <script src="http://malsup.github.com/jquery.form.js"></script> 

        <script src="<?php echo BASE_URL ?>star-rating/star-rating.js" type="text/javascript"></script>
        
       <script src="https://ucarecdn.com/widget/2.9.0/uploadcare/uploadcare.full.min.js" charset="utf-8"></script>
        <!-- CAPSTONE: Override Uploadcare text -->
        <script src="<?php echo BASE_URL ?>ckeditor/ckeditor.js"></script>

         <script src="<?= BASE_JS_URL ?>pdfLoader.js"></script>
        <script src="<?= BASE_JS_URL ?>imageLoader.js"></script>
        
        
        <!-- CAPSTONE: Override Uploadcare text -->
        <script type="text/javascript">

            UPLOADCARE_PUBLIC_KEY = '4c3637988f9b93d343e8';
            UPLOADCARE_LOCALE_TRANSLATIONS = {
                ready: 'Update Photo'
            };
            UPLOADCARE_LOCALE_TRANSLATIONS = {
                errors: {
                    'portrait': "<?php echo ERROR_PORTRAIT ?>",
                    'dimensions': "<?php echo ERROR_DIMENSIONS ?>"  // message for widget
                },
                dialog: {tabs: {preview: {error: {
                                'portrait': {// messages for dialog's error page
                                    title: "<?php echo ERROR_PORTRAIT_TITLE ?>",
                                    text: "<?php echo ERROR_PORTRAIT_TEXT ?>",
                                    back: "<?php echo BACK_BUTTON ?>"
                                },
                                'dimensions': {// messages for dialog's error page
                                    title: "<?php echo ERROR_DIMENSIONS_TITLE ?>",
                                    text: "<?php echo ERROR_DIMENSIONS_TEXT ?>",
                                    back: "<?php echo BACK_BUTTON ?>"
                                }
                            }}}}
            };
            UPLOADCARE_PATH_VALUE = true;
            UPLOADCARE_CROP = "2:3";
        </script>
        
        <?php include 'js/record.php';  ?>
    </head>
    <body>


      

