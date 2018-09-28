<?php
ob_start();
session_start();
include("../kint/Kint.class.php");

require_once("../appConfig/appConfig.php");
include_once("../application/database/db.php");
require_once '../application/config.php';
include_once($GLOBALS['APP_DIR'] . "application/functions.php");
//include $GLOBALS['APP_DIR'] . "system/special_config.php";

if (isset($_SESSION['lang'])) {
    include_once($GLOBALS['LANGUAGE_APP_DIR'] . $_SESSION['lang'] . ".php");
} else {
    include_once($GLOBALS['LANGUAGE_APP_DIR'] . "en.php");
}


/* * ********************************* */
/* * ********Register***************** */
/* * ********************************* */
//echo "<br><br><br><br><br><br>++++++++++++++++++++++++++++++ <pre>";
//print_r($_SESSION["datadictionary"]);
//exit;
if (isset($_POST["reg_submit"]) && $_POST["reg_submit"] != "") {

    $data['uname'] = $_POST["uname"];
    $data['password'] = $_POST["password"];
    $data['email'] = $_POST["email"];
    $data['user_type'] = $_POST["user_type"];
    $data['date_added'] = date("Y-m-d");
    //echo "rocess start";
    /* $emailtest = emailAlreadyExists($email, $userTblArray);
      if ($emailtest)
      {

      //echo "Email TEST";
      FlashMessage::add(EMAIL_ALREADY_EXISTS);
      echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "register.php'>";
      exit;
      }
      else
      { */


    $data['last_login'] = date("Y-m-d");


    //print_r($data);die;

    $query = insert("users", $data);


    if ($query) {


        $_SESSION['uid'] = $query;
        $_SESSION['uname'] = $data['uname'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['country'] = $data['country'];

        if (isset($_SESSION['callBackPage'])) {


            echo "<META http-equiv='refresh' content='0;URL=" . $_SESSION['callBackPage'] . "'>";

            unset($_SESSION['callBackPage']);
            exit();
        } else {

            FlashMessage::add(PROFILE_COMPLETE_MESSAGE);
            echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL . "index.php'>";
            exit();
        }
    } else {
        FlashMessage::add(REGISTRATION_NOT_SUCCESS);
        echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "register.php'>";
        exit();
    }
    //}
}

/* * ********************************* */
/* * ********Login Authentication***** */
/* * ********************************* */
if (isset($_POST["log_submit"]) && $_POST["log_submit"] != "") {

    $data['email'] = $_POST["log_email"];
    $data['password'] = $_POST["log_pwd"];
    $remember = $_POST["remember_me"];


    $ws = " email='" . $data['email'] . "' or uname='" . $data['email'] . "'";

    $result = get('users', $ws);

//print_r($result);die;

    if (!empty($result) && $result['reset_password_flag'] == 0) {



        if ($result['password'] == $data['password']) {

            $_SESSION['uid'] = $result['uid'];
            $_SESSION['uname'] = $result['uname'];
            $_SESSION['email'] = $result['email'];
            $_SESSION['country'] = $result['country'];


            // header("location:" . BASE_URL . "index.php");

            if (isset($_SESSION['callBackPage'])) {


                echo "<META http-equiv='refresh' content='0;URL=" . $_SESSION['callBackPage'] . "'>";

                unset($_SESSION['callBackPage']);
                exit();
            } else {

                FlashMessage::add(PROFILE_COMPLETE_MESSAGE);
                echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL . "index.php'>";
                exit();
            }
        } else {
            FlashMessage::add('UserName or Password Incorrect.');
            header("location:" . BASE_URL_SYSTEM . "login.php");
        }
    } else if (!empty($result) && $result['reset_password_flag'] == 1) {


        if ($result['reset_password'] == $data['password']) {

            $_SESSION['uid'] = $result['uid'];
            $_SESSION['uname'] = $result['uname'];
            $_SESSION['email'] = $result['email'];
            $_SESSION['country'] = $result['country'];

            FlashMessage::add('Reset your password please.');
            echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "resetPassword.php'>";
            exit();
        } else {
            FlashMessage::add('UserName or Password Incorrect.');
            header("location:" . BASE_URL_SYSTEM . "login.php");
        }
    } else {
        FlashMessage::add('Login Failed or  User does not exit');
        header("location:" . BASE_URL_SYSTEM . "login.php");
    }
}///////// login post ends here
//} else if ($count > 1) {
//    FlashMessage::add('Multiple user exists.');
//    header("location:" . BASE_URL_SYSTEM . "login.php");
//} else {
//    FlashMessage::add('Login Failed. Try Again.');
//    header("location:" . BASE_URL_SYSTEM . "login.php");
//}
//// post login ends here////

/* * ********************************* */
/* * ********Adimn Login Authentication***** */
/* * ********************************* */
if (isset($_POST["admin_log_submit"]) && $_POST["admin_log_submit"] != "") {
    $email = $_POST["log_email"];
    $password = $_POST["log_pwd"];

    $query = sprintf("SELECT * FROM {$userTblArray['database_table_name']} WHERE  {$userTblArray['email_fld']}='%s' and {$userTblArray['password_fld']}='%s'", mysql_real_escape_string($email), mysql_real_escape_string($password));

    $result = mysql_query($query);
    $users = mysql_fetch_array($result);
    $count = mysql_num_rows($result);
    if ($count == 1) {
        if ($users['isActive'] == 1) {
            $_SESSION['uid'] = $users['uid'];
            $_SESSION['uname'] = $users['uname'];
            $_SESSION['email'] = $users['email'];
            $_SESSION['country'] = $users['country'];
            $_SESSION['level'] = $users['level'];
            header("location:admin/index.php");
        } else {
            FlashMessage::add('Account deactivated, please contact admin.');
            header("location:admin/index.php");
        }
    } else {
        FlashMessage::add('Login Failed. Try Again.');
        header("location:admin/index.php");
    }
}

/* * ********************************* */
/* * ********Logout******************* */
/* * ********************************* */
if (isset($_GET["action"]) && $_GET["action"] == "logout") {

    //unset($_SESSION);
    session_destroy();
    session_unset();
    FlashMessage::add('Logged Out Successful.');
    d("form-actions  .. inside logout");
//Kint::trace();

    header("location:" . BASE_URL . "/index.php");
}



/* * ********************************* */
/* * ********Forgot Password ********* */
/* * ********************************* */

if (isset($_POST['forgot-pass'])) {

    $email = $_POST["log_email"];

    $result = getWhere('users', array('email' => $email));


    //print_r($result);die;

    if ($result) {
        $recovery_pass = create_recovery_password();

        //update the Db flags and recovery_pass

        update('users', array('reset_password_flag' => 1, 'reset_password' => $recovery_pass), array('uid' => $result[0]['uid']));
        //send mail to the user
        $to = $email; //Give user-email id here...
        $subject = 'Password Change request from Generic CjCornell';
        $from = "abdullah.khan8006@gmail.com";
        $headers = "From: $from\r\n";
        $headers .= "Content-type: text/html\r\n";
        $message = 'Hello, <br><br> Password change function test mail. <br><br>
					Login to your account with ' . $recovery_pass . ' temporary password. And change your password immediately after login in.
					Thanks<br> Team Of CjCornell<br> generic.cjcornell.com';

        if (send_mail_to($to, $subject, $message, $headers)) {

            FlashMessage::add('Login with the temporary password that has been emailed to you.');
            header("location:" . BASE_URL_SYSTEM . "login.php");
        }
    } else {

        FlashMessage::add('You are not registered with us. Please Register here.');
        header("location:" . BASE_URL_SYSTEM . "register.php");
    }
}


/* * ********************************* */
/* * ********Reset Password ********* */
/* * ********************************* */
if (isset($_POST['reset_pass'])) {
    $current_pass = $_POST['current_pass'];

    $new['password'] = $_POST['new_pass'];


    $new['reset_password'] = $new['reset_password_flag'] = 0; /// reseting backing to default value
    //check if users Current pass is correct

    $row = get('users', "uid=" . $_SESSION['uid']);

    // print_r($row);die;

    if ($row['reset_password'] == $current_pass) {


        $result2 = update('users', $new, array('uid' => $_SESSION['uid']));


        if ($result2) {
            FlashMessage::add('Your password has been successfully updated.');
            echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL . "index.php'>";
            exit();
        } else {
            FlashMessage::add('Your password couldnt be update please try again.');
            echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "resetPassword.php'>";
            exit();
        }
    } else {
        FlashMessage::add('Your Current password doesnt match. Please try again');
        echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "resetPassword.php'>";
        exit();
    }
}



/* * ******POST A COMMENT****** */
if (isset($_POST['submit_cmt'])) {
    if (isUserLoggedin()) {
        $comment_on_user_id = $_POST['cmnt_on_uid'];
        $comment_by_user_id = $_SESSION['uid'];
        $comment = $_POST['cmnt'];

        $sql = "INSERT INTO comments(comment_on_user_id, comment_by_user_id, date_time, comment) VALUES(" . $comment_on_user_id . ", " . $comment_by_user_id . ", NOW(), '" . $comment . "')";

        if (mysql_query($sql)) {
            $getComment = "SELECT c.*, u.uid, u.uname, u.image FROM comments as c LEFT JOIN users as u ON c.comment_by_user_id = u.uid WHERE c.comment_on_user_id=" . $comment_on_user_id . " AND c.comment_by_user_id=" . $comment_by_user_id . " order by c.comment_id desc LIMIT 1";
            $result = mysql_query($getComment);
            $row = mysql_fetch_array($result);
            if ($row) {
                ?>
                <div class="cmt-container">
                    <div class="userImage">
                        <?php
                        if (isset($row['image']) && $row['image'] != "") {
                            echo '<img src="' . BASE_URL . 'users_uploads/' . $row['image'] . '" alt="User Image">';
                        } else {
                            echo '<img src="' . BASE_URL . 'project_uploads/thumb_defaultImageIcon" alt="No Image">';
                        }
                        ?>
                    </div>
                    <div class="comment-info">
                        <div>
                            <?php echo $row['uname'] ?>
                        </div>
                        <div>
                            <?php echo $row['comment'] ?>
                        </div>
                        <div>
                            <?php
                            $date = date_create($row['date_time']);
                            echo date_format($date, 'g:ia \o\n l jS F Y');
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="cmt-container">
				' . COMMENTS_POST_ERROR . '
			</div>';
        }
    } else {
        echo '<div class="cmt-container">
				' . LOGIN_REQUIRED_MESSAGE_WITH_URL . '
			</div>';
    }
}
?>