<?php

session_start();

//echo $_SESSION['callBackPage'];die;
$_SESSION['lang'] = 'en';
include("../kint/Kint.class.php");

require_once("../appConfig/appConfig.php");
include_once("../application/database/db.php");
include_once("../application/config.php");
include_once("../application/functions.php");
include_once("../DDICT/masterFunctions.php");



if (isset($_SESSION['lang']))
{
  include_once($GLOBALS['LANGUAGE_APP_DIR'] . $_SESSION['lang'] . ".php");
}
else
{
  include_once($GLOBALS['LANGUAGE_APP_DIR'] . "en.php");
}

if (isset($_SESSION['lang']))
{
  include_once($GLOBALS['LANGUAGE_APP_DIR'] . $_SESSION['lang'] . ".php");
}
else
{
  include_once($GLOBALS['LANGUAGE_APP_DIR'] . "en.php");
}
if (isUserLoggedin())
{
	echo "<script>window.location='../index.php';</script>";
  //echo "<META http-equiv='refresh' content='0;URL=/generic-platforms/index.php'>";
  exit;
}

$alias = 'login';


?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE_TITLE ?></title>
    <link href='http://fonts.googleapis.com/css?family=Galdeano' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:700italic,400,600,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>common-responsive.css" type="text/css">
    <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>style.css" type="text/css">
    <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>responsive.css">
    <link rel="stylesheet" href="<?php echo BASE_URL ?>appConfig/custom-css.css" type="text/css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?php echo BASE_JS_URL ?>bootstrap.min.js"></script>
    <style>
      .validate-error{
        border-color: #b94a48;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392;
      }
      .forget-pass-div{
        display:none;
      }
    </style>
  </head>
  <body class="login-body">
    <div class="bubbles"></div>
    <div class="jumbotron login-window">
      <div class="container">
        <div class="row">
          <div class="col-6 height2">
            <?php
            if (isset($_SESSION["messages"]))
            {
              echo "<div class='alert alert-info'>";
              echo " <a href='#' class='close' data-dismiss='alert'>&times;</a>";
              echo FlashMessage::render();
              echo "</div>";
            }
            ?>
          </div>
          <div class="col-lg-12">
            <!--Login block starts here-->
            <div class="login-div sign-in">
              <form class="form-signin" role="form" action="profile.php?action=login" method="post" onSubmit="return loginFormvalidate()">
                <h2 class="form-signin-heading"><?php echo LOGIN_LOGO ?></h2>
                <p><?php echo REGISTRATION_MESSAGE1 ?><a href="register.php"><?php echo REGISTRATION_MESSAGE2 ?></a></p>
               

 <?= Select_Data_FieldDictionary_Record($alias);?>




                <label class="checkbox">
                  <input type="checkbox" value="1" name="remember_me" <?php
                  if (isset($_COOKIE['remember_me']))
                  {
                    echo 'checked="checked"';
                  }
                  else
                  {
                    echo '';
                  }
                  ?>>
                  &nbsp; &nbsp; &nbsp; <?php echo LOGIN_REMEMBERME ?> <span style="font-size:14px; text-align:right;color:#3366FF;cursor:pointer; float:right;" ><a id="forget-pass" href="#"><?php echo FORGOT_PASSWORD ?></a></span></label>

                <button class="btn btn-lg btn-primary btn-block" type="submit" name="" value=""><?php echo SIGN_IN ?></button>
              </form>
              <p class="cpy-ryt"> <?php echo COPY_RIGHTS ?></p>
            </div>
            <!--Login block ends here-->

            <!--Forgot Password block starts here-->
            <div class="login-div forget-pass-div">
              <form class="form-signin" role="form" action="<?php echo BASE_URL_SYSTEM ?>form-actions.php" method="post" onSubmit="return forgotPassValidator()">
                <h2 class="form-signin-heading"><?php echo LOGIN_LOGO ?></h2>
                <p><?php echo LOGIN_MESSAGE1 ?><a id="back-to-login" href="#"><?php echo LOGIN_MENU ?></a> <?php echo LOGIN_MESSAGE2 ?></p>
                <?php
                if (isset($_SESSION["messages"]))
                {
                  echo "<p>" . FlashMessage::render() . "</p>";
                }
                ?>
                <input type="email" class="form-control" placeholder="<?php echo LOGIN_EMAIL_PLACEHOLDER ?>"  autofocus name="log_email" id="forgot-pass-email">
                <button class="btn btn-lg btn-primary btn-block" type="submit" name="forgot-pass" value="submit"><?php echo RETRIEVE_PASS ?></button>
              </form>
              <p class="cpy-ryt">  <?php echo COPY_RIGHTS ?></p>
            </div>
            <!--Forgot password block ends here-->
          </div>
        </div>
        <!-- /container -->
      </div>
    </div>
    <script type="text/javascript">
      function loginFormvalidate() {
        var username = $("#log_email").val();
        var password = $("#log_pwd").val();
        var username_flag = true;
        var pwd_flag = true;

        if (username == "") {
          $("#log_email").addClass("validate-error");
          username_flag = false;
        }
        else {
          $("#log_email").removeClass("validate-error");
        }

        if (password == "") {
          $("#log_pwd").addClass("validate-error");
          pwd_flag = false;
        }
        else {
          $("#log_email").removeClass("validate-error");
        }

        if (username_flag && pwd_flag) {
          return true;
        }
        else {
          return false;
        }
      }

      function forgotPassValidator() {
        var useremail = $("#forgot-pass-email").val();
        var usermail_flag = true;

        if (useremail == "") {
          $("#forgot-pass-email").addClass("validate-error");
          usermail_flag = false;
        }
        else {
          $("#forgot-pass-email").removeClass("validate-error");
        }
        if (usermail_flag) {
          return true;
        }
        else {
          return false;
        }
      }

      $(document).ready(function () {
        $(document).on('click', '#forget-pass', function () {
          $(".sign-in").hide("fast");
          $(".forget-pass-div").show("slow");
        });

        $(document).on('click', '#back-to-login', function () {
          $(".forget-pass-div").hide("fast");
          $(".sign-in").show("slow");
        });
      });
    </script>
  </body>
</html>
