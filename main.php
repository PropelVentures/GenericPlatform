<?php
session_start();
include("Factory.php");
if(!isset($_SESSION["dbHost"])) {
    include_once "setup.php";
}

if(empty($oFactory)){
    $oFactory = new Factory();
}
$displayPage = $_GET["display"]
?>
<html>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </head>
    <body style="padding-top:7rem;">
        <?php
            //Generate the navigation for the current page
            $oFactory->NavigationPageBuilder()->GenerateNavigation($displayPage, "header", $oFactory);
            //end navigation generation
        ?>
        <div class="container">
            <div style="text-align: center;">
            <?php
                //Start populating page
                //Creates Tabs
                $oFactory->MainPageBuilder()->LoadMainContent($displayPage, "header", $oFactory);
                //end populating page
            ?>
        </div>
    </body>
</html>
