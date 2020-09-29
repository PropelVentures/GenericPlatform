<?php
// Root directory

/*

class FlashMessage
  public static function render()
  public static function add($message)

function imageUpload($fileDetails)

function getAllCategories()

function emailAlreadyExists($email, $userTblArray)
function userNameAlreadyExists($uname, $userTblArray)

function fileUploadCare($uploadCareURL, $imageName, $src)

function create_recovery_password()
function send_mail_to($to, $subject, $message_to, $headers)
public function likeUser()

function userHasPrivilege()
function itemHasVisibility($visibility)
function itemHasPrivilege($privilege)
function itemEditable($editable)


function getSliderImages($description, $dict_id)
function getImages($description)
function getBannerImages($description)

function getIframeUrl($description)
function dislayUserNameSelector($selector)
function setUserDataInSession($con, $user)

function log_event($page_name, $action, $senderId=false, $reciverId=false)
function log_notification($type, $displayPage, $action, $senderId, $reciverId, $notification_subject, $notification_message)
function send_notification_alert($type, $displayPage, $action, $senderId, $reciverId,
function sendEmailNotification($page, $action, $senderId, $reciverId, $notification_subject,
function sendMessageAsEmail($to, $messageText)
function sendMessageAndAddLog()

function getUserData($userId)
function getSliderInterval($extra_options)
function setBoxStyles($listExtraOptions)
function issetStyleForBox($array, $filter)
function fetchStyleConfigs($category_styles)
function findAndSetCategoryStyles($con, $category_styles)

*/

// *****************************************************************************

class FlashMessage
{
    public static function render()
    {
        if (!isset($_SESSION['messages'])) {
            return null;
        }
        $messages = $_SESSION['messages'];
        unset($_SESSION['messages']);
        return implode('<br/>', $messages);
    }

    public static function add($message)
    {
        if (!isset($_SESSION['messages'])) {
            $_SESSION['messages'] = array();
        }
        $_SESSION['messages'][] = $message;
    }
}

/* * *****Image Upload function starts here********* */

/*
* "imageUpload" is responsible to upload the image in server in given path.
* It's check extension valid image fomate by checking the extension of image (valid extension "gif", "jpeg", "jpg", "png", "JPEG", "JPG")
*
* This function also responsible for making the thumbnail of uplaod image.
* For making the Thum it used the resizeImage lib.
* flag: not used any where in the code.
*/

function imageUpload($fileDetails) {
    $allowedExts = array("gif", "jpeg", "jpg", "png", "JPEG", "JPG");
    $temp = explode(".", $fileDetails["projectImage"]["name"]);
    $extension = end($temp);
    $randName = rand(124, 1000);

    $filen = $randName . $fileDetails["projectImage"]['name'];

    $doc_root = $_SERVER['DOCUMENT_ROOT'] . '/generic/';
    $chk_dir = "img";

    /* "is_dir" function
    * Check the particluer directoy is exist or notin the specific location.
    */

    if (!is_dir($chk_dir)) {
        @mkdir($chk_dir, 0700);
    }

    if ($fileDetails["projectImage"]["error"] > 0) {
        echo "Return Code: " . $fileDetails["projectImage"]["error"] . "<br>";
        exit();
        return "false";
    } else {
        /**
         * This function check the files is exist by the same name or not.
         * if exist then return false.
         * if not then create the file on the specifice location with the same file
         * name
         */
        if (file_exists("img/" . $filen)) {
            echo $filen . " already exists. ";
            exit();
            return "false";
        } else {
            /* "move_upload_file" fucntion
            * This function checks to ensure that the file designated by filename is a
            * valid upload file (meaning that it was uploaded via PHP's HTTP POST
            * upload
            * mechanism). If the file is valid, it will be moved to the filename given
            * by destination.
            */
            if (move_uploaded_file($fileDetails["projectImage"]["tmp_name"], $doc_root . "img/" . $filen)) {
                //Resize the image
                include_once('resizeImage.php');
                $image = new ResizeImage();
                $wk_img_wt = '';
                $wk_img_ht = '';
                $imgpath = "img/" . $filen;
                $thumb_imgpath = "img/thumb_" . $filen;
                list($wk_img_wt, $wk_img_ht) = getimagesize($imgpath);
                if ($wk_img_wt > $wk_img_ht && $wk_img_wt > 800) {
                    $image->load($imgpath);
                    $image->setImgDim($wk_img_wt, $wk_img_ht);
                    $image->resizeToWidth(800);
                    $image->save($imgpath);
                }
                if ($wk_img_ht > $wk_img_wt && $wk_img_ht > 450) {
                    $image->load($imgpath);
                    $image->setImgDim($wk_img_wt, $wk_img_ht);
                    $image->resizeToHeight(450);
                    $image->save($imgpath);
                }

                // For Thumb
                if ($wk_img_wt > $wk_img_ht && $wk_img_wt > 200) {
                    $image->load($imgpath);
                    $image->setImgDim($wk_img_wt, $wk_img_ht);
                    $image->resizeToWidth(200);
                    $image->save($thumb_imgpath);
                }

                if ($wk_img_ht > $wk_img_wt && $wk_img_ht > 130) {
                    $image->load($imgpath);
                    $image->setImgDim($wk_img_wt, $wk_img_ht);
                    $image->resizeToHeight(130);
                    $image->save($thumb_imgpath);
                }
                return $filen;
            } else {
                echo "file not uploaded ";
                exit();
                return "false";
            }
        }
    }
}

/* * *****Image Upload class ends here********* */

/* * ************Get All Categories*********** */

function getAllCategories() {
    $query = "select * from category";
    $result = mysql_query($query);
    $categories = mysql_fetch_array($result);
    return $categories;
}

/**
 * Check if email already exists in user table
 * 
 * @param string $email Email to check
 * @param array $userTblArray Associative array with required fields to check email, `table_name` and `email_fld` keys must exist in the array
 * 
 * @author ph
 * 
 * @return bool True if email exists, false otherwise
 */
function emailAlreadyExists($email, $userTblArray) {
    $email = mysql_real_escape_string($email);
    $query = "SELECT * FROM {$userTblArray['table_name']} WHERE {$userTblArray['email_fld']}='" . $email . "'";
    $result = mysql_query($query);
    $count = mysql_num_rows($result);
    return $count >= 1;
}

/**
 * Check if username already exists in user table
 * 
 * @param string $uname Username to check
 * @param array $userTblArray Associative array with required fields to check username, `table_name` and `uname_fld` keys must exist in the array
 * 
 * @author ph
 * 
 * @return bool True if username exists, false otherwise
 */
function userNameAlreadyExists($uname, $userTblArray) {
    $email = mysql_real_escape_string($uname);
    $query = "SELECT * FROM {$userTblArray['table_name']} WHERE {$userTblArray['uname_fld']} = '" . $uname . "'";
    $result = mysql_query($query);
    $count = mysql_num_rows($result);
    return $count >= 1;
}

/* * ************CHECK USERNAME ALREADY EXITS*********** */

/* * ************UPLOAD CARE FUNCTION*********** */
/*
* "fileUploadCare" fucntion is used for upalod the image of the give specific
*  location.
* It's take 3 params
*    - Image url
*    - Image name
*    - Location wher it wil be stored
* It also validation that file should be only image.
* It's also make the Thumbnail for the image going to upload in server
*/

function fileUploadCare($uploadCareURL, $imageName, $src) {
    $uploadcare_image_url = $uploadCareURL;
    $filename = $imageName;
    $ext = pathinfo($filename, PATHINFO_EXTENSION);   //returns the extension
    $allowed_types = array('jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'GIF', 'png', 'PNG', 'bmp');
    $randName = rand(124, 1000);
    $imgInfo = array();

    // If the file extension is allowed
    if (in_array($ext, $allowed_types)) {
        $new_filename = $filename;

        //$new_filepath = $base_path.'upload/orig/'.$new_filename;
        $imgpath = $RELATIVE . $src . "/" . $randName . $new_filename;
        $thumb_imgpath = $RELATIVE . $src . "/thumbs/" . $randName . $new_filename;

        // Attempt to copy the image from Uploadcare to our server
        if (copy($uploadcare_image_url, $imgpath)) {
            //Resize the image
            include_once('resizeImage.php');
            $image = new ResizeImage();
            $wk_img_wt = '';
            $wk_img_ht = '';

            list($wk_img_wt, $wk_img_ht) = getimagesize($imgpath);
            if ($wk_img_wt >= 650 && $wk_img_wt > $wk_img_ht) {
                $image->load($imgpath);
                $image->setImgDim($wk_img_wt, $wk_img_ht);
                $image->resizeToWidth(650);
                $image->save($imgpath);
            }
            if ($wk_img_ht > $wk_img_wt && $wk_img_ht >= 430) {
                $image->load($imgpath);
                $image->setImgDim($wk_img_wt, $wk_img_ht);
                $image->resizeToHeight(430);
                $image->save($imgpath);
            }

            //For Thumb
            if ($wk_img_wt > $wk_img_ht && $wk_img_wt >= 325) {
                $image->load($imgpath);
                $image->setImgDim($wk_img_wt, $wk_img_ht);
                $image->resizeToWidth(325);
                $image->save($thumb_imgpath);
            }

            if ($wk_img_ht > $wk_img_wt && $wk_img_ht > 215) {
                $image->load($imgpath);
                $image->setImgDim($wk_img_wt, $wk_img_ht);
                $image->resizeToHeight(215);
                $image->save($thumb_imgpath);
            }

            $imgInfo['image'] = $randName . $new_filename;
            $imgInfo['thumb_image'] = "thumb_" . $randName . $new_filename;
            return $imgInfo;
        } else {
            return $imgInfo;
        }
    } else {
        return $imgInfo;
    }
}

/* * ************UPLOAD CARE FUNCTION*********** */

/**
 * Generates random string with length of 8 chars. Can be used for passwords but it is not cryptographically secure.
 * 
 * @author ph
 * 
 * @return string Generated string
 */
function create_recovery_password() {
    $recovery_pass = substr(md5(rand(999, 99999)), 0, 8);
    return $recovery_pass;
}

/* * ***Create recovery password*** */


/* * ****Send Email starts here***** */
/*
* "send_mail_to" function is responsbale to send the email as per the parame value
* Its take 4 params :
* to =  a person who receiver  the email.
* subject = subject of email.
* message_to = message.
* headers = Header for email
*/
function send_mail_to($to, $subject, $message_to, $headers) {
    return mail($to, $subject, $message_to, $headers);
}

/* * ****Send Email ends here***** */

/* * ******Relationship management class@starts ********
  class relationship_management{

  protected $action;
  protected $target_user_id;
  protected $user_id;


  public function __construct($action=NULL, $userId=NULL, $targetUid=NULL)
  $this->action = $action;
  $this->target_user_id = $targetUid;
  $this->user_id = $userId;
  }

  //Function to like a user
  public function likeUser()
  $sql = "INSERT INTO user_liked(user_id, target_user_id) VALUES($this->user_id, $this->target_user_id)";
  return mysql_query($sql);
  }
  }

 * ******Relationship management class@ends ******** */

function userHasPrivilege() {
	  // var_dump(defined("USER_PRIVILEGE"));die;
}
/*
* "itemHasVisibility" function check user have the visibility permission or not.
* If yes then it will return ture otherwise false.
* Accordingly that user are able to see the content.
* It's take on param which content the visibility value.
*/
function itemHasVisibility($visibility) {
    // Its check user have defined privilege value true on global veriable

    if (!defined("USER_PRIVILEGE")) {
        define("USER_PRIVILEGE", 'NO');
    }
	
	  if ( 
		    ($visibility = 0  || !isset($_SESSION['user_privilege']) || USER_PRIVILEGE == 'NO' )
		    ||
		    (USER_PRIVILEGE == 'YES' && $_SESSION['user_privilege'] >= $visibility) 
	  ) {
        return true;
	  }
	  return false;
}
/*
* "itemHasPrivilege" function check USER_PRIVILEGE have defined value or not  .
* If yes then it will return ture otherwise false.
*
*/
function itemHasPrivilege($privilege) {
    if (!defined("USER_PRIVILEGE")) {
        define("USER_PRIVILEGE", 'NO');
    }
    if ( (USER_PRIVILEGE == 'YES' && $_SESSION['user_privilege'] >= $privilege) || (USER_PRIVILEGE == 'NO' && $privilege > 0 ) ) {
        return true;
    }
    return false;
}

/*
* "itemEditable" function check USER_PRIVILEGE have defined value or not  .
* If yes then it will return ture otherwise false.
*
*/
function itemEditable($editable) {
    if (!defined("USER_PRIVILEGE")) {
        define("USER_PRIVILEGE", 'NO');
    }
    if ( (USER_PRIVILEGE == 'YES' && $_SESSION['user_privilege'] >= $editable) || (USER_PRIVILEGE == 'NO' && $editable > 0 ) ) {
        return true;
    }
    return false;
}



function getSliderImages($description, $dict_id) {
    $description =  trim($description);
    if (empty($description)) {
        return array();
    }
    $counter = 0;
    $sliders = array();
    $images = explode(';', $description);
    foreach ($images as $image) {
        if (!empty(trim($image))) {
            $configs = explode(',', $image);
            $portion = [];
            if (file_exists($GLOBALS['APP_DIR']."application/banner-images/".$configs[0])) {
                $portion['image'] = BASE_URL_APP."banner-images/".$configs[0];
                $portion['id'] = 'slider_'.$dict_id.'_'.$counter;
                $counter++;
            }
            if (!empty($configs[1])) {
                $portion['url'] = $configs[1];
            }
            $sliders[] = $portion;
        }
    }
    return $sliders;
}

function getImages($description) {
    $description =  trim($description);
    if (empty($description)) {
        return array();
    }
    $sliders = array();
    $images = explode(';', $description);
    foreach ($images as $image) {
        if (file_exists($GLOBALS['APP_DIR']."system/system_images/".$image)) {
            $sliders[] = BASE_URL_SYSTEM."system_images/".$image;
        }
    }
    return $sliders;
}

/**
 * Get url of banner image
 * 
 * @param string $description Banner image file name
 * 
 * @author ph
 * 
 * @return string Url of banner image or emapty if file does not exist
 */
function getBannerImages($description) {
    $description = trim($description);
    if (empty($description)) {
        return '';
    }
    if (file_exists($GLOBALS['APP_DIR']."application/banner-images/".$description)) {
        return BASE_URL_APP."banner-images/".$description;
    }
    return "";
}

function getIframeUrl($description) {
    $description = trim($description);
    if (empty($description)) {
        return '';
    }
    // Remove all illegal characters from a url
    $description = filter_var($description, FILTER_SANITIZE_URL);
    // If Url is valid then et target as defined in DB
    if (filter_var($description, FILTER_VALIDATE_URL)) {
        return $description;
    } else {
        return "";
    }
}

/**
 * This method is to chose in nave-bar that how to show user name in nav bar
 */
function dislayUserNameSelector($selector) {
    $tempSelector = strtoupper($selector);
    if ( in_array($tempSelector, ['#CURRENT-USERNAME', '#CURRENT-USER-FIRSTNAME', '#CURRENT-USER-FIRST-LASTNAME']) ) {
        $sel = str_replace('#', '', $tempSelector);
        return $_SESSION[strtolower($sel)];
    }
    return $selector;
}

/**
 * Set current user data in session
 * 
 * @param mysqli $con Mysqli connection resource
 * @param array $user User data in associative array
 * 
 * @author ph
 * 
 * @return void
 */
function setUserDataInSession($con, $user) {
    $name = 'uname';
    $userNameQuery = "SELECT * FROM field_dictionary WHERE field_identifier='username'";
      $fieldQuery = $con->query($userNameQuery) OR $message = mysqli_error($con);
      if ($fieldQuery->num_rows > 0) {
          $field = $fieldQuery->fetch_assoc();
          $name = $field['generic_field_name'];
      }
    $_SESSION['user_privilege'] = $user['user_privilege_level'];
    $_SESSION['uname'] = $user[$_SESSION['user_field_email']];
    $_SESSION['current-username'] = $user[$name];
    $_SESSION['current-user-email'] = $user['email'];
    $_SESSION['current-user-firstname'] = $user['firstname'];
    $_SESSION['current-user-first-lastname'] = $user['firstname'].' '.$user['lastname'];
    $_SESSION['current-user-profile-image'] = $user['image'];
}

// Code Change for Task 8.5.101 Start
function log_event($page_name, $action, $senderId = false, $reciverId=false) {
    if (EVENT_LOGGING_ON !=='ON') {
       return;
    }
    $action = strtoupper(trim($action));
    $event_log_code = get('event_log_codes', "event_name='$action'");
    $event_action_default = false;
    $notification_alert_type = false;
    if (!empty($event_log_code)) {
        $event_action_default = $event_log_code['event_action_default'];
        $notification_alert_type = $event_log_code['notification_alert_type'];
        $notification_subject = $event_log_code['notification_subject'];
        $notification_message = $event_log_code['notification_message'];
    }

    if ($senderId!== false) {
        $userId = $senderId;
        $targetUserId = $reciverId;
    } else {
        $userId = $_SESSION['uid'];
        $targetUserId = $_SESSION['uid'];
    }
    if ($event_action_default ) {
        $data = [];
        $data['page_name'] = $page_name;
        $data['action_taken'] = $action;
        $data['user_id'] = $userId;
        $data['target_user_id'] = $targetUserId;
        $data['event_log_code'] = $event_log_code['event_log_code_id'];
        $data['notification_type'] = $notification_alert_type;
        $log_id =insert('event_log', $data);
        log_notification($notification_alert_type, $page_name, $action, $userId, $targetUserId, $notification_subject, $notification_message);
    }
}

function log_notification($type, $displayPage, $action, $senderId, $reciverId, $notification_subject, $notification_message) {
    if (NOTIFICATION_ALERTS_ON !=='ON') {
        return;
    }
    $data = [];
    $data['notification_type'] = $type;
    $data['user_id'] = $senderId;
    $data['target_user_id'] = $reciverId;
    $data['response_type'] = 0;
    $data['alert_type'] = 0;
    $notif_id = insert('notification_log', $data);
    if ($type=='Email' || $type=='Alert 1') {
        send_notification_alert($type, $displayPage, $action, $senderId, $reciverId, $notification_subject, $notification_message);
    }
}

function send_notification_alert($type, $displayPage, $action, $senderId, $reciverId, $notification_subject, $notification_message) {
    if ($type=='Email') {
        sendEmailNotification($displayPage, $action, $senderId, $reciverId, $notification_subject, $notification_message);
    } else if ($type=='Alert 1') {
      //push notification
    }
}
//Code Change for Task 8.5.101 End

function sendEmailNotification($page, $action, $senderId, $reciverId, $notification_subject, $notification_message) {
    $str =  $_SERVER['HTTP_HOST'];
    $sender = getUserData($senderId);
    $reciver = getUserData($reciverId);
    $to = $reciver['email'];

    /*$subject = "Action Notification | Generic Platform";
    $message = "<html><head><title>Notification</title></head><body>";
    $message .= "Hi ".$reciver['name'].",<br/>";
    $message .= "An Action '".$action."' has occured at '".$page."' page";

    if ($senderId != $reciverId) {
      $message .=" by ".$sender['name'].".";
    }
    $message .= "<br/>";
    $message .= "<br/><br/>Regards,<br>Generic Platform";
    $message .= "</body></html>";*/

    if ($senderId != $reciverId) {
        $message .=" by ".$sender['name'].".";
        $notification_message = str_replace("sender_name", $sender['name'].'.', $notification_message);
    } else {
        $notification_message = str_replace(' by sender_name', '', $notification_message);
    }

    $notification_message = str_replace("user_name", $reciver['name'], $notification_message);
    $notification_message = str_replace("action_name", "'".$action."'", $notification_message);
    $notification_message = str_replace("page_name", "'".$page."'", $notification_message);

    $subject = $notification_subject;
    $message = $notification_message;

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // More headers
    //$headers .= 'From: Generic Platform<noreply@genericplatform.com>' . "\r\n";
    //Code Change for Task 8.4.104 Start
    $headers .= 'From: Generic Platform<noreply@'.$str.'>' . "\r\n";
    //Code Change for Task 8.4.104 End
    try {
        $sent = mail($to, $subject, $message, $headers);
        if ($sent) {
            return true;
        }
        return "Unable to send email";
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

/**
 * Send email notification
 * 
 * @param string $to Email address to send message to
 * @param string $messageText The message
 * 
 * @author ph
 * 
 * @return bool|string True if succeed, or error message
 */
function sendMessageAsEmail($to, $messageText) {
    $subject = "Message Notification | Generic Platform";
    $message = "<html><head><title>Message</title></head><body>";
    $message .= "Hi,<br/>";
    $message .= "User ".$_SESSION['current-user-first-lastname']." send you you a message.which is : <br> ".$messageText." <br/>";
    $message .= "<br/><br/>Regards,<br>Generic Platform";
    $message .= "</body></html>";
    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // More headers
    $headers .= 'From: Generic Platform<noreply@genericplatform.com>' . "\r\n";
    try {
        $sent = mail($to, $subject, $message, $headers);
        if ($sent) {
            return true;
        }
        return "Unable to send email";
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function sendMessageAndAddLog() {
    $reciverId = $_GET['reciverid'];
    $table_name = $_GET['table_name'];
    $message = trim($_GET['message']);
    $user = get("user", "user_id='$reciverId'");
    $email = trim($user['email']);
    sendMessageAsEmail($email, $message);
    $data = [
        'sender' => $_SESSION['uid'],
        'reciver' => $reciverId,
        'message' => $message,
    ];
    insert($table_name, $data);
}

/**
 * Get user data from session or database
 * 
 * @param int $userId Id of the user to get data for
 * 
 * @author ph
 * 
 * @return array Associative array of user details
 */
function getUserData($userId) {
    $userData = [];
    if ($userId == $_SESSION['uid']) {
        $userData['id'] = $_SESSION['uid'];
        $userData['email'] = $_SESSION['current-user-email'];
        $userData['name'] = $_SESSION['current-user-first-lastname'];
    } else {
        $data = get('user', "user_id='$userId'");
        $userData['id'] = $data['user_id'];
        $userData['email'] = $data['email'];
        $userData['name'] = $data['firstname'].' '.$data['lastname'];
    }
    return $userData;
}

/**
 * 
 */
function getSliderInterval($extra_options) {
    if (strpos($extra_options, 'slider_delay') !== false) {
        $all_options = explode(";", trim($extra_options));
        foreach ($all_options as $key => $option) {
            if (strpos($option, 'slider_delay') !==false) {
                $configs = explode(":", trim($option));
                if (trim($configs[0])=='slider_delay' && !empty(trim($configs[1]))) {
                    $result = (double)(trim($configs[1]));
                    return $result*1000;
                }
            }
        }
    }
    return 3000;
}

function setBoxStyles($listExtraOptions) {
    $dataSet = [];
    if (strpos($listExtraOptions, 'boxstyles')!==false) {
        $category_styles = trim(get_string_between($listExtraOptions, 'boxstyles[',']'));
        if (!empty($category_styles)) {
            $categories =   explode(',', $category_styles);
            if (!empty(trim($categories[0])) && !empty(trim($categories[1]))) {
                $dataSet['table'] = trim($categories[0]);
                $dataSet['field'] = trim($categories[1]);
                return $dataSet;
            }
        }
    }
    return false;
}

/**
 * Get generated style rule for inline style attribute
 * 
 * @param array $array Associative array of styles, e.g.: ['color' => 'red']
 * @param string $filter Property e.g.: color
 * 
 * @author ph
 * 
 * @return string Style rule or empty if property does not exist in list
 */
function issetStyleForBox($array, $filter) {
    if (isset($array[$filter]) && !empty($array[$filter])) {
        return $filter.':'.$array[$filter].';';
    }
    return '';
}

function fetchStyleConfigs($category_styles) {
    $category_styles  = trim($category_styles);
    $dataSet = [];
    if (empty($category_styles)) {
        return false;
    }
    $categories =   explode(',', $category_styles);
    if (empty(trim($categories[0])) ||  empty(trim($categories[1]))) {
        return false;
    }
    $dataSet['table'] = trim($categories[0]);
    $dataSet['field'] = trim($categories[1]);
    return $dataSet;
}


function findAndSetCategoryStyles($con, $category_styles) {
    $style_table = trim($category_styles['table']);
    $style_refrence_id = trim($category_styles['field']);
    $allStyles = $con->query("SELECT * FROM $style_table");

    while ($style = $allStyles->fetch_assoc()) {
        $dataSet[$style[$style_refrence_id]]['class'] =$style['css_class'];
        $dataSet[$style[$style_refrence_id]]['code'] =$style['css_code'];
        $dataSet[$style[$style_refrence_id]]['icon'] =$style['map_icon'];
    }
    return $dataSet;
}
