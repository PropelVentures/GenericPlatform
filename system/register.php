    <?php
session_start();

require_once("../appConfig/appConfig.php");


include_once("../application/database/db.php");
require_once '../application/config.php';
require_once '../application/functions.php';
include_once($GLOBALS['APP_DIR'] . "actions/CustomHtml.php");

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
  echo "<META http-equiv='refresh' content='0;URL=index.php'>";
  exit;
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generic CJcornell</title>
    <link href='http://fonts.googleapis.com/css?family=Galdeano' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:700italic,400,600,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>style.css" type="text/css">
    <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>common-responsive.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="css/styele.css" />
    <link rel="stylesheet" href="<?php echo BASE_CSS_URL ?>responsive.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?php echo BASE_JS_URL ?>bootstrap.min.js"></script>
    <style>
      .validate-error{
        border-color: #FF0000;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392;
      }
      #message div {
        font-size: 16px;
        text-align: center;
      }
    </style>
  </head>
  <body class="reg-body">
    <!--<div class="bubbles"></div>-->
    <div class="jumbotron register-window">
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
            <div class="login-div">
              <form class="form-signin" role="form" action="<?php echo BASE_URL_SYSTEM ?>form-actions.php" method="post" id="createAccountForm" enctype="multipart/form-data" >
                <h2 class="form-signin-heading"><a class="logo-login" href="<?php echo BASE_URL ?>">Generic <span>CJcornell</span></a></h2>
                <p>If already registered please <a href="<?php echo BASE_URL_SYSTEM ?>login.php">Login here.</a></p>
                <p id="message">
                <div id="password_error"></div>
                <div id="email_error"></div>
                <div id="uname_error"></div>
                </p>
                <input type="text" class="form-control" placeholder="Enter Username" required autofocus name="uname" id="uname">
                <input type="email" class="form-control" placeholder="Email" required autofocus name="email" id="email" >
                <input type="password" class="form-control" placeholder="Password" required name="password" id="password">
                <input type="password" class="form-control" placeholder="Re-Password" id="repassword" name="repassword">
                <?php
                if (USER_TYPES_ENABLED && USER_TYPES_SELF_SELECT)
                {
                  $obj = new CustomHtml;
                  $obj->getDropDown('user_type');
                }
                ?>
                <!--<input type="text" class="form-control" placeholder="Enter Country" required autofocus name="country" id="country">-->
                <input name="reg_submit" value="Create Account" type="hidden" />
                <input  class="btn btn-lg btn-primary btn-block" name="reg_submit" value="Create Account" type="button" onClick="return createAccountValidation()">
                <button class="btn btn-lg btn-primary btn-block" type="reset" name="cancel" id="cancel" value="Cancel">Cancel</button>
              </form>
              <p class="cpy-ryt">&#169;2001-2014 All Rights Reserved.
                <!--generic.cjcornell� is a registered trademark of generic.cjcornell. <a href="#">Privacy and Terms</a>-->
              </p>
            </div>
          </div>
        </div>
        <!-- /container -->
      </div>
    </div>
    <!-- SCRIPTS -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="http://multia.in/miapp/gradient/assets/js/notifyMe.js"></script>
    <script type="text/javascript" src="http://multia.in/miapp/gradient/assets/js/jquery.placeholder.js"></script>
    <script type="text/javascript" src="http://multia.in/miapp/gradient/assets/js/jquery.simple-text-rotator.js"></script>
    <script type="text/javascript" src="http://multia.in/miapp/gradient/assets/js/init.js"></script>
    <script type="text/javascript">

                  var email_flag1 = false;
                  
                  function createAccountValidation() {
                    var username = $("#uname").val();
                    var password = $("#password").val();
                    var repassword = $("#repassword").val();
                    var email = $("#email").val();
                    var country = $("#country").val();
                    var username_flag = false;
                    var pwd_flag = false;
                    var repwd_flag = false;
                    var country_flag = false;

                    if (username == "") {
                      $("#uname").addClass("validate-error");
                    }
                    else {
                      $("#uname").removeClass("validate-error");
                      username_flag = true;
                    }

                    if (password == "") {
                      $("#password").addClass("validate-error");
                    }
                    else {
                      $("#password").removeClass("validate-error");
                      pwd_flag = true;
                    }

                    if (repassword == "") {
                      $("#repassword").addClass("validate-error");
                    }
                    else {
                      if (password == repassword) {
                        $('#password_error').hide();
                        $("#repassword").removeClass("validate-error");
                        repwd_flag = true;
                      }
                      else {
                        $('#password_error').html('Password and re-password missmatch.');
                        $("#repassword").addClass("validate-error");
                      }
                    }

                    if (country == "") {
                      $("#country").addClass("validate-error");
                    }
                    else {
                      $("#country").removeClass("validate-error");
                      country_flag = true;
                    }

                    if (email == "") {
                      $("#email").addClass("validate-error");
                    }
                    else {
                      $("#email").removeClass("validate-error");
                      email_flag = true;
                    }

                    if (username_flag && pwd_flag && repwd_flag && email_flag && country_flag) {
                      
                      query_string = "";
                      $.get("<?php echo BASE_URL?>system/ajax-actions.php?userName=" + username + "&checkUserName=true", query_string, function (data) {
                         
                         data = $.trim(data);
                         
                         //alert(data);
                        if (data == 'false') {
                            
                          if (validateEmail(email)) {
                            $.get("<?php echo BASE_URL ?>system/ajax-actions.php?email=" + email + "&checkEmail=true", query_string, function (data) {
                                data = $.trim(data);
                              if (data == 'true') {
                                $('#uname_error').html('');
                                $('#email_error').html('Email alreday exists Please use another email.');
                                $("#email").addClass("validate-error");
                                return false;
                              }
                              else {
                                $("#createAccountForm").submit();
                              }
                            });
                          }
                          else {
                            $('#email_error').html('Email format not correct.');
                            $("#email").addClass("validate-error");
                            return false;
                          }
                        }
                        else {
                          $('#email_error').html('');
                          $('#uname_error').html('User Name alreday exists.');
                          $("#uname").addClass("validate-error");
                          return false;
                        }
                      });
                    }
                    else {
               
                      return false;
                    }
                  }

                  function validateEmail(email)
                  {
                    var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
                    if (reg.test(email)) {
                      return true;
                    }
                    else {
                      return false;
                    }
                  }

                  $(document).on('change', '.btn-file :file', function () {
                    var input = $(this),
                            numFiles = input.get(0).files ? input.get(0).files.length : 1,
                            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                    input.trigger('fileselect', [numFiles, label]);
                  });

                  $(document).ready(function () {
                    $('.btn-file :file').on('fileselect', function (event, numFiles, label) {
                      console.log(numFiles);
                      console.log(label);
                    });
                  });
    </script>
  </body>
</html>