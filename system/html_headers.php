<?php
// @session_start();
// $_SESSION['lang'] = 'en';
/*
* As the name explain it content all the css, js and other required files which required to render any page
* on the browser
*/

/*
* It is checking for the api request if yes then its checking for the required params.
* so that send the response or render the page accordingly
*
*/
if($_GET['source'] == 'api'){

    $_SESSION['uid'] = $_GET['uid'];
    $_SESSION['uname'] = $_GET['uname'];
    $_SESSION['email'] = $_GET['email'];
    $_SESSION['country'] = $_GET['country'];

    $dictId = $_GET['did'];

    /*
    * " get_single_record " is function which is inherited  from the
    *  get_record.php file.
    *  get_single_record this function take three params
    *       data_dictionary => this the database table name.
    *       dict_id => primary key of the data_dictionary table.
    *       dictId => this is search  key.
    *
    *  For details definition  is given in the "get_record.php" file
    */

    $dict = get_single_record('data_dictionary','dict_id', $dictId);

    $_GET['page_name'] = $dict['page_name'];

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


        <link rel="stylesheet" href="<?php echo CUSTOM_CSS_URL ?>custom-css.css" type="text/css">
<!--        <script src="http://scrollrevealjs.org/js/scrollReveal.min.js?ver=0.2.0-rc.1"></script> -->
        <script src="https://unpkg.com/scrollreveal"></script>

        <link rel="stylesheet" href="<?php echo BASE_URL_SYSTEM ?>star-rating/star-rating.css" media="all"  type="text/css" />
        <script src="<?php echo BASE_JS_URL ?>jquery-1.11.1.min.js"></script>
        <script src="<?php echo BASE_JS_URL ?>jquery-ui.js"></script>
        <script src="<?php echo BASE_JS_URL ?>jquery.mobile-events.js"></script>

         <script src="<?php echo BASE_JS_URL ?>tag-it.min.js"></script>

        <script src="<?php echo BASE_JS_URL ?>mobileDetector.js"></script>
        <script src="<?php echo BASE_JS_URL ?>modernizr.js"></script>
        <script src="<?php echo BASE_JS_URL?>bootstrap.min.js"></script>


        <!-- including voice javascript -->

        <script src="<?php echo BASE_JS_URL ?>Fr.voice.js"></script>
        <script src="<?php echo BASE_JS_URL ?>recorder.js"></script>
        <script src="<?php echo BASE_JS_URL ?>recorderWorker.js"></script>



        <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/s/dt/dt-1.10.10/datatables.min.css'/>
        <script type='text/javascript' src='https://cdn.datatables.net/s/dt/dt-1.10.10/datatables.min.js'></script>
        <script src="http://malsup.github.com/jquery.form.js"></script>

        <script src="<?php echo BASE_URL_SYSTEM ?>star-rating/star-rating.js" type="text/javascript"></script>

       <script src="https://ucarecdn.com/widget/2.9.0/uploadcare/uploadcare.full.min.js" charset="utf-8"></script>
        <!-- CAPSTONE: Override Uploadcare text -->
        <script src="<?php echo BASE_URL_SYSTEM ?>ckeditor/ckeditor.js"></script>

         <script src="<?= BASE_JS_URL ?>pdfLoader.js"></script>
        <script src="<?= BASE_JS_URL ?>imageLoader.js"></script>
		<!-- CAPSTONE: Override Uploadcare text -->
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>"></script>
		<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
		</script>

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
