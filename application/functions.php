<?php
// Root directory
function isUserLoggedin()
{
  if (isset($_SESSION['uid']) && $_SESSION['uid'] != "")
  {
    return true;
  }
  else
  {
    return false;
  }
}

function isProjectOwner($pid)
{
  $sql = "SELECT * FROM projects WHERE pid=" . $pid . " AND uid=" . $_SESSION['uid'];
  $result = mysql_query($sql);
  return mysql_num_rows($result);
}

function isAdmin()
{
  if (isset($_SESSION['level']) && $_SESSION['level'] != "")
  {
    return $_SESSION['level'];
  }
  else
  {
    return $_SESSION['level'];
  }
}

function get_user_details($userTblArray)
{
  if (isset($_SESSION) && $_SESSION['uid'] != "")
  {
    $uid = $_SESSION['uid'];
    $sql = "SELECT u.{$userTblArray['uname_fld']},u.{$userTblArray['firstname_fld']},u.{$userTblArray['lastname_fld']}, u.{$userTblArray['user_type_fld']} from {$userTblArray['database_table_name']} as u WHERE u.{$userTblArray['uid_fld']} =" . $uid;
    $query = mysql_query($sql);

/*
       echo "uid =".$uid."<br><br>";
       echo "sql =".$sql."<br><br>";
       echo "query =".$query."<br><br>";

       echo "userTblArray =".$userTblArray."<br><br>";
             print_r($userTblArray);
	                    exit;
*/

    if (mysql_num_rows($query) == 1)
    {
      return $row = mysql_fetch_array($query);
    }
    else
    {
      return FALSE;
    }
  }
  else
  {
    return $_SESSION['level'];
  }
}

function get_client_ip()
{
  $ipaddress = '';
  if (getenv('HTTP_CLIENT_IP'))
    $ipaddress = getenv('HTTP_CLIENT_IP');
  else if (getenv('HTTP_X_FORWARDED_FOR'))
    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  else if (getenv('HTTP_X_FORWARDED'))
    $ipaddress = getenv('HTTP_X_FORWARDED');
  else if (getenv('HTTP_FORWARDED_FOR'))
    $ipaddress = getenv('HTTP_FORWARDED_FOR');
  else if (getenv('HTTP_FORWARDED'))
    $ipaddress = getenv('HTTP_FORWARDED');
  else if (getenv('REMOTE_ADDR'))
    $ipaddress = getenv('REMOTE_ADDR');
  else
    $ipaddress = 'UNKNOWN';
  return $ipaddress;
}

class FlashMessage
{

  public static function render()
  {
    if (!isset($_SESSION['messages']))
    {
      return null;
    }
    $messages = $_SESSION['messages'];
    unset($_SESSION['messages']);
    return implode('<br/>', $messages);
  }

  public static function add($message)
  {
    if (!isset($_SESSION['messages']))
    {
      $_SESSION['messages'] = array();
    }
    $_SESSION['messages'][] = $message;
  }

}

/* * *****Image Upload class starts here********* */

function imageUpload($fileDetails)
{
  //print_r($fileDetails);

  $allowedExts = array("gif", "jpeg", "jpg", "png", "JPEG", "JPG");
  $temp = explode(".", $fileDetails["projectImage"]["name"]);
  $extension = end($temp);
  $randName = rand(124, 1000);

  $filen = $randName . $fileDetails["projectImage"]['name'];

  $doc_root = $_SERVER['DOCUMENT_ROOT'] . '/generic/';
  $chk_dir = "img";
  if (!is_dir($chk_dir))
  {

    @mkdir($chk_dir, 0700);
  }

  if ($fileDetails["projectImage"]["error"] > 0)
  {
    echo "Return Code: " . $fileDetails["projectImage"]["error"] . "<br>";
    exit();
    return "false";
  }
  else
  {
    if (file_exists("img/" . $filen))
    {
      echo $filen . " already exists. ";
      exit();
      return "false";
    }
    else
    {
      if (move_uploaded_file($fileDetails["projectImage"]["tmp_name"], $doc_root . "img/" . $filen))
      {
        //Resize the image
        include_once('resizeImage.php');
        $image = new ResizeImage();
        $wk_img_wt = '';
        $wk_img_ht = '';
        $imgpath = "img/" . $filen;
        $thumb_imgpath = "img/thumb_" . $filen;
        list($wk_img_wt, $wk_img_ht) = getimagesize($imgpath);
        if ($wk_img_wt > $wk_img_ht && $wk_img_wt > 800)
        {

          $image->load($imgpath);

          $image->setImgDim($wk_img_wt, $wk_img_ht);

          $image->resizeToWidth(800);

          $image->save($imgpath);
        }
        if ($wk_img_ht > $wk_img_wt && $wk_img_ht > 450)
        {

          $image->load($imgpath);

          $image->setImgDim($wk_img_wt, $wk_img_ht);

          $image->resizeToHeight(450);

          $image->save($imgpath);
        }



        //For Thumb

        if ($wk_img_wt > $wk_img_ht && $wk_img_wt > 200)
        {

          $image->load($imgpath);

          $image->setImgDim($wk_img_wt, $wk_img_ht);

          $image->resizeToWidth(200);

          $image->save($thumb_imgpath);
        }



        if ($wk_img_ht > $wk_img_wt && $wk_img_ht > 130)
        {

          $image->load($imgpath);

          $image->setImgDim($wk_img_wt, $wk_img_ht);

          $image->resizeToHeight(130);

          $image->save($thumb_imgpath);
        }
        return $filen;
      }
      else
      {
        echo "file not uploaded ";
        exit();
        return "false";
      }
    }
  }
}

/* * *****Image Upload class ends here********* */


/* * *****Profile completion function ******** */

function profileCompletion($users, $userTblArray)
{
  $mandatoryFields = array($userTblArray['uname_fld'], $userTblArray['email_fld'], $userTblArray['image_fld'], $userTblArray['company_fld'], $userTblArray['city_fld'],
    $userTblArray['state_fld'], $userTblArray['zip_fld'], $userTblArray['country_fld']);
  $countMandatoryFields = count($mandatoryFields);
  $countEmptyFields = 0;


  for ($j = 0; $j < $countMandatoryFields; $j++)
  {
    $key = $mandatoryFields[$j];
    if ($users[$key] != NULL || $users[$key] != "")
    {
      $countEmptyFields++;
    }
  }

  $profileComplete = ($countEmptyFields * 100) / $countMandatoryFields;
  return $profileComplete;
}

/* * *****Profile completion function ******** */

/* * ************Get All Categories*********** */

function getAllCategories()
{
  $query = "select * from category";
  $result = mysql_query($query);
  $categories = mysql_fetch_array($result);
  return $categories;
}

/* * ************Get All Categories*********** */

/* * ************CHECK EMAIL ALREADY EXITS*********** */

function emailAlreadyExists($email, $userTblArray)
{
  $email = mysql_real_escape_string($email);

  $query = "SELECT * FROM {$userTblArray['database_table_name']} WHERE {$userTblArray['email_fld']}='" . $email . "'";
  $result = mysql_query($query);
  $count = mysql_num_rows($result);
  if ($count == 1)
  {
    return true;
  }
  else
  {
    return false;
  }
}

/* * ************CHECK EMAIL ALREADY EXITS*********** */

/* * *********CHECK USERNAME ALREADY EXITS*********** */

function userNameAlreadyExists($uname, $userTblArray)
{
  $email = mysql_real_escape_string($uname);

  $query = "SELECT * FROM {$userTblArray['database_table_name']} WHERE {$userTblArray['uname_fld']} = '" . $uname . "'";
  $result = mysql_query($query);
  $count = mysql_num_rows($result);
  if ($count == 1)
  {
    return true;
  }
  else
  {
    return false;
  }
}

/* * ************CHECK USERNAME ALREADY EXITS*********** */

/* * ************UPLOAD CARE FUNCTION*********** */

function fileUploadCare($uploadCareURL, $imageName, $src)
{

  $uploadcare_image_url = $uploadCareURL;
  $filename = $imageName;
  $ext = pathinfo($filename, PATHINFO_EXTENSION);   //returns the extension
  $allowed_types = array('jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'GIF', 'png', 'PNG', 'bmp');
  $randName = rand(124, 1000);
  $imgInfo = array();

  // If the file extension is allowed
  if (in_array($ext, $allowed_types))
  {
    $new_filename = $filename;

    //$new_filepath = $base_path.'upload/orig/'.$new_filename;
    $imgpath = $MYPATH . $src . "/" . $randName . $new_filename;
    $thumb_imgpath = $MYPATH . $src . "/thumbs/" . $randName . $new_filename;

    // Attempt to copy the image from Uploadcare to our server
    if (copy($uploadcare_image_url, $imgpath))
    {
      //Resize the image
      include_once('resizeImage.php');
      $image = new ResizeImage();
      $wk_img_wt = '';
      $wk_img_ht = '';

      list($wk_img_wt, $wk_img_ht) = getimagesize($imgpath);
      if ($wk_img_wt >= 650 && $wk_img_wt > $wk_img_ht)
      {
        $image->load($imgpath);
        $image->setImgDim($wk_img_wt, $wk_img_ht);
        $image->resizeToWidth(650);
        $image->save($imgpath);
      }
      if ($wk_img_ht > $wk_img_wt && $wk_img_ht >= 430)
      {
        $image->load($imgpath);
        $image->setImgDim($wk_img_wt, $wk_img_ht);
        $image->resizeToHeight(430);
        $image->save($imgpath);
      }

      //For Thumb
      if ($wk_img_wt > $wk_img_ht && $wk_img_wt >= 325)
      {
        $image->load($imgpath);
        $image->setImgDim($wk_img_wt, $wk_img_ht);
        $image->resizeToWidth(325);
        $image->save($thumb_imgpath);
      }

      if ($wk_img_ht > $wk_img_wt && $wk_img_ht > 215)
      {
        $image->load($imgpath);
        $image->setImgDim($wk_img_wt, $wk_img_ht);
        $image->resizeToHeight(215);
        $image->save($thumb_imgpath);
      }

      $imgInfo['image'] = $randName . $new_filename;
      $imgInfo['thumb_image'] = "thumb_" . $randName . $new_filename;
      return $imgInfo;
    }
    else
    {
      return $imgInfo;
    }
  }
  else
  {
    return $imgInfo;
  }
}

/* * ************UPLOAD CARE FUNCTION*********** */

/* * ***Create recovery password*** */

function create_recovery_password()
{
  $recovery_pass = substr(md5(rand(999, 99999)), 0, 8);
  return $recovery_pass;
}

/* * ***Create recovery password*** */


/* * ****Send Email starts here***** */

function send_mail_to($to, $subject, $message_to, $headers)
{
  if (mail($to, $subject, $message_to, $headers))
  {
    return true;
  }
  else
  {
    return false;
  }
}

/* * ****Send Email ends here***** */

/* * ******Relationship management class@starts ********
  class relationship_management{

  protected $action;
  protected $target_user_id;
  protected $user_id;


  public function __construct($action=NULL, $userId=NULL, $targetUid=NULL){
  $this->action = $action;
  $this->target_user_id = $targetUid;
  $this->user_id = $userId;
  }

  //Function to like a user
  public function likeUser(){
  $sql = "INSERT INTO user_liked(user_id, target_user_id) VALUES($this->user_id, $this->target_user_id)";
  return mysql_query($sql);
  }
  }

 * ******Relationship management class@ends ******** */
?>
