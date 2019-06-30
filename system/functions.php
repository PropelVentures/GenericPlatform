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
    $imgpath = $RELATIVE . $src . "/" . $randName . $new_filename;
    $thumb_imgpath = $RELATIVE . $src . "/thumbs/" . $randName . $new_filename;

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

function userHasPrivilege(){
	//var_dump(defined("USER_PRIVILEGE"));die;
}

function itemHasVisibility($visibility){
	if(!defined("USER_PRIVILEGE")){
		define("USER_PRIVILEGE",'NO');
	}
	if( (USER_PRIVILEGE == 'YES' && $_SESSION['user_privilege'] >= $visibility) &&  $visibility >0 || (USER_PRIVILEGE == 'NO' && $visibility > 0 ) || (!isset($_SESSION['user_privilege']) && $visibility > 0 ) ){
    return true;
	}
	return false;
}

function itemHasPrivilege($privilege){
	if(!defined("USER_PRIVILEGE")){
		define("USER_PRIVILEGE",'NO');
	}
	if( (USER_PRIVILEGE == 'YES' && $_SESSION['user_privilege'] >= $privilege) || (USER_PRIVILEGE == 'NO' && $privilege > 0 ) ){
		return true;
	}
	return false;
}
function itemEditable($editable){
	if(!defined("USER_PRIVILEGE")){
		define("USER_PRIVILEGE",'NO');
	}
	if( (USER_PRIVILEGE == 'YES' && $_SESSION['user_privilege'] >= $editable) || (USER_PRIVILEGE == 'NO' && $editable > 0 ) ){
		return true;
	}
	return false;
}

function loginNotRequired(){
	$con = connect();
	$display_page = $_GET['display'];
	$nav = $con->query("SELECT * FROM navigation WHERE target_display_page='$display_page' LIMIT 1") or die($con->error);
	$navigation = $nav->fetch_assoc();
	if(!empty($navigation) && strtoupper($navigation['loginRequired']) == '2'){
		return true;
	}
	return false;
}

/* TO DO//
 * Get Nav Items according to Parent & Children
 * For all menu location & loginrequired(true or false)
 */
function getNavItems($page,$menu_location,$overRide= false){
	$con = connect();

  if(isUserLoggedin()){
    $loginRequired = 'true';
    $notThis = '2';
  }else{
    $loginRequired=='false';
    $notThis = '1';
  }
  if($overRide){
    $rs = $con->query("SELECT * FROM navigation where display_page='$page' and menu_location='$menu_location' AND item_number>0 AND loginRequired!=$notThis ORDER BY item_number ASC");
  }else{
    $rs = $con->query("SELECT * FROM navigation where  display_page='ALL'  and menu_location='$menu_location' AND nav_id>0 AND loginRequired!=$notThis ORDER BY item_number ASC");
  }

	$navItems = array();
	$arr = array();
	$i = 0;
	while ($row = $rs->fetch_assoc()) {
		if(strpos($row['item_number'],".0")){
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		} elseif(strpos($row['item_number'],".")){
			$navItems[floor($row['item_number'])]['children'][] = $row;
		} else {
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		}
	}
	return $navItems;
}

function getSideBarNavItems($page,$menu_location,$overRide= false){
	$con = connect();

  if(isUserLoggedin()){
    $loginRequired = 'true';
    $notThis = '2';
  }else{
    $loginRequired=='false';
    $notThis = '1';
  }
  if($overRide){
    $rs = $con->query("SELECT * FROM navigation where display_page='$page' and menu_location='$menu_location' AND item_number>0 AND loginRequired!=$notThis ORDER BY item_number ASC");
  }else{
    $rs = $con->query("SELECT * FROM navigation where  (display_page='$page' OR display_page='ALL' )  and menu_location='$menu_location' AND nav_id>0 AND loginRequired!=$notThis ORDER BY item_number ASC");
  }

	$navItems = array();
	$arr = array();
	$i = 0;
	while ($row = $rs->fetch_assoc()) {
		if(strpos($row['item_number'],".0")){
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		} elseif(strpos($row['item_number'],".")){
			$navItems[floor($row['item_number'])]['children'][] = $row;
		} else {
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		}
	}
	return $navItems;
}


/* TO DO//
 * Generate Top Nav Items
 * For all menu location & loginrequired(true or false)
 */
function generateTopNavigation($navItems,$loginRequired){
  $isUserLoggedIn = true;
  if(isset($_SESSION['user_privilege'])){
    $currentUserPrivilege = $_SESSION['user_privilege'];
  }else{
    $isUserLoggedIn = false;
    $currentUserPrivilege = 0;
  }
	$menu = '';

    echo '<style>
        .disabled_nav {
    pointer-events: none;
    cursor: default;
    opacity: 0.6;
    }
    </style>';

	if(!empty($navItems)){
		foreach($navItems as $parent){
			if($loginRequired && (!itemHasVisibility($parent['item_visibility']) || !isset($parent['nav_id'])) ){
				continue;
			}
      $label = dislayUserNameSelector($parent['item_label']);
			$title = $parent['item_help'];
			$item_style = $parent['nav_css_class'];
      $nav_css_code = $parent['nav_css_code'];
			$item_icon = getNavItemIcon($parent['item_icon'],$item_style,$nav_css_code);
			$navTarget = getNavTarget($parent);
			$target = $navTarget['target'];
			$enable_class=$navTarget['enable_class'];
			$target_blank = $navTarget['target_blank'];
      $disable = '';
      if($currentUserPrivilege < $parent['item_privilege'] ){
        $disable ='disabled_nav';
      }
			if(!empty($parent['children'])){

				switch(strtolower($label)){
						case "#line#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<div class='saperator_line'></div>
									<span class='caret'></span>
								</li>
								<ul class='dropdown-menu'>";
						break;
						case "#break#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<br/>
									<span class='caret'></span>
								</li>
								<ul class='dropdown-menu'>";
						break;
						case "#space#":
						$menu.="<li class='nav_item $item_style' style='$nav_css_code'>
									<div class='margin_bottom_list'></div>
									<span class='caret'></span>
								</li>
								<ul class='dropdown-menu'>";
						break;
						default:
						$menu.="<li class='$enable_class dropdown nav_item $item_style' style='$nav_css_code'>
								<a class='$item_style' style='$nav_css_code' href='#' class='dropdown-toggle' data-toggle='dropdown' title='$title'>
									".$item_icon.getSaperator($label)."
									<span class='caret'></span>
								</a>
								<ul class='dropdown-menu'>";
						break;
					}

				foreach($parent['children'] as $children){
					if($loginRequired && !itemHasVisibility($children['item_visibility'])){
						continue;
					}
					$label = dislayUserNameSelector($children['item_label']);
					$title = $children['item_help'];
					$item_style = $children['nav_css_class'];
          $nav_css_code = $children['nav_css_code'];
					$item_icon = getNavItemIcon($children['item_icon'],$item_style,$nav_css_code);
					$navTarget = getNavTarget($children);
					$target = $navTarget['target'];
					$enable_class=$navTarget['enable_class'];
					$target_blank = $navTarget['target_blank'];
          $disable_child = '';
          if($currentUserPrivilege < $children['item_privilege'] && $isUserLoggedIn){
            $disable_child ='disabled_nav';
          }
					#$label=$label.'#line#';
					switch(strtolower($label)){
						case "#line#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<div class='saperator_line'></div>
								</li>";
						break;
						case "#break#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<br/>
								</li>";
						break;
						case "#space#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<div class='margin_bottom_list'></div>
								</li>";
						break;
						default:
						$menu.="<li class='$enable_class nav_item $item_style' style='$nav_css_code'>
									<a onClick=urlVariables() class='$disable_child $item_style' $target_blank href='$target' title='$title' style='$nav_css_code'>".
										$item_icon.
										getSaperator($label)."
									</a>
								</li>";
						break;
					}
				}
				$menu.= "</ul></li>";
			} else {
				switch(strtolower($label)){
						case "#line#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<div class='saperator_line'></div>
								</li>";
						break;
						case "#break#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<br/>
								</li>";
						break;
						case "#space#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<div class='margin_bottom_list'></div>
								</li>";
						break;
						default:
						$menu.="<li class='nav_item $enable_class $item_style' style='$nav_css_code'>
									<a onClick=urlVariables() class='$disable $item_style' $target_blank href='$target' title='$title' style='$nav_css_code'>
										".$item_icon.getSaperator($label)."
									</a>
								</li>
                                <script>
                                function urlVariables(){
                                    $.ajax({
                                        method: 'GET',
                                        url: 'ajax-actions.php',
                                        data:{ values_to_unset: 'abc' }
                                    })
                                    .done(function (msg) {
                                        // alert(msg);
                                    });
                                }
                                </script>
                                ";
						break;
					}
			}
		}
	}
	return $menu;
}

/* TO DO//
 * Generate SideBar Nav Items
 * For all menu location & loginrequired(true or false)
 */
function generateSideBarNavigation($navItems,$menu){
	foreach($navItems as $parent){
		if(strtoupper($parent['loginRequired'])== '1' && !itemHasVisibility($parent['item_visibility']) || !isset($parent['nav_id'])){
			continue;
		}
		$label = dislayUserNameSelector($parent['item_label']);
		$title = $parent['item_help'];
		$item_style = $parent['nav_css_class'];
    $nav_css_code = $parent['nav_css_code'];
		$item_icon = getNavItemIcon($parent['item_icon'],$item_style,$nav_css_code);
		$navTarget = getNavTarget($parent);
		$target = $navTarget['target'];
		$enable_class=$navTarget['enable_class'];
		$target_blank = $navTarget['target_blank'];
		if(!empty($parent['children'])){
			switch(strtolower($label)){
				case "#line#":
				$menu.=" <li>
							<div class='saperator_line'></div>
							<span class='caret'></span>
						</li>";
				break;
				case "#break#":
				$menu.=" <li >
							<br/>
							<span class='caret'></span>
						</li>";
				break;
				case "#space#":
				$menu.="<li>
							<div class='margin_bottom_list'></div>
							<span class='caret'></span>
						</li>";
				break;
				default:
				$menu.="<li class='$enable_class dropdown nav_item $item_style' style='$nav_css_code'>
						<a href='#nav_".$parent['nav_id']."' class='dropdown-toggle' data-toggle='collapse' title='$title'>
							".$item_icon.getSaperator($label)."
							<span class='caret'></span>
						</a>";
				break;
			}
			$menu .= "<div id='nav_".$parent['nav_id']."' class='panel-collapse collapse'>
							<div class='panel-body'>
								<ul class='nav navbar-nav'>";

			foreach($parent['children'] as $children){
				if($children['loginRequired']== '1' && !itemHasVisibility($children['item_visibility'])){
					continue;
				}
				$label = dislayUserNameSelector($children['item_label']);
				$title = $children['item_help'];
				$item_style = $children['nav_css_class'];
        $nav_css_code = $children['nav_css_code'];
				$item_icon = getNavItemIcon($children['item_icon'],$item_style,$nav_css_code);
				$navTarget = getNavTarget($children);
				$target = $navTarget['target'];
				$enable_class=$navTarget['enable_class'];
				$target_blank = $navTarget['target_blank'];
				#$label=$label.'#line#';
				switch(strtolower($label)){
					case "#line#":
					$menu.=" <li >
								<div class='saperator_line'></div>
							</li>";
					break;
					case "#break#":
					$menu.=" <li >
								<br/>
							</li>";
					break;
					case "#space#":
					$menu.=" <li >
								<div class='margin_bottom_list'></div>
							</li>";
					break;
					default:
					$menu.="<li class='$enable_class $item_style' style='$nav_css_code'>
								<a $target_blank href='$target' title='$title'>".
									$item_icon.
									getSaperator($label)."
								</a>
							</li>";
					break;
				}
			}
			$menu.= "</ul></div></div>";
		} else {
			switch(strtolower($label)){
				case "#line#":
				$menu.=" <li >
							<div class='saperator_line'></div>
						</li>";
				break;
				case "#break#":
				$menu.=" <li >
							<br/>
						</li>";
				break;
				case "#space#":
				$menu.=" <li >
							<div class='margin_bottom_list'></div>
						</li>";
				break;
				default:
				$menu.="<li class='$enable_class $item_style' style='$nav_css_code'>
							<a $target_blank href='$target' title='$title'>
								".$item_icon.getSaperator($label)."
							</a>
						</li>";
				break;
			}
		}
	}
	return $menu;
}


function navHasVisibility(){
	$con = connect();
	$display_page = $_GET['display'];
	$nav = $con->query("SELECT * FROM navigation WHERE target_display_page='$display_page' LIMIT 1") or die($con->error);
	$navigation = $nav->fetch_assoc();
	if(empty($navigation) || $navigation['loginRequired'] == '2'){
		return true;
	}
	return itemHasVisibility($navigation['item_visibility']);
}

function itemHasEnable($enable){
	if(!defined("USER_PRIVILEGE")){
		define("USER_PRIVILEGE",'NO');
	}
	if( (USER_PRIVILEGE == 'YES' && $_SESSION['user_privilege'] >= $enable) || (USER_PRIVILEGE == 'NO' && $enable > 0 ) ){
		return true;
	}
	return false;
}

function getSaperator($label=''){
	$hash_start=strpos($label,'#');
	$hash_end=strpos($label,'#',$hash_start+1);
	$saperator_name = "";
	if(!empty($hash_start) && !empty($hash_end)){
		$saperator_name=substr($label,$hash_start+1,$hash_end-$hash_start-1);
	}
	switch($saperator_name){
		case "line":
			$label=str_replace("#$saperator_name#","<div class='saperator_line'></div>",$label);
			break;
		case "LINE":
			$label=str_replace("#$saperator_name#","<div class='saperator_line'></div>",$label);
			break;
		case "break":
			$label=str_replace("#$saperator_name#","<br/>",$label);
			break;
		case "BREAK":
			$label=str_replace("#$saperator_name#","<br/>",$label);
			break;
		case "space":
			$label=str_replace("#$saperator_name#","&nbsp&nbsp",$label);
			break;
		case "SPACE":
			$label=str_replace("#$saperator_name#","&nbsp&nbsp",$label);
			break;
	}
	return $label;

	/* if(!empty($hash_start) && !empty($hash_end)){
		$saperator_name=substr($label,$hash_start+1,$hash_end-$hash_start-1);
	}
	switch(strtolower($saperator_name)){
		case "line":
			$html_saperator="<div class='saperator_li'></div>";
			$label=str_replace("#line#","",strtolower($label));
			break;
		case "break":
			$html_saperator="<br/>";
			$label=str_replace("#break#","",strtolower($label));
			break;
		default:
			$html_saperator = "";
	}
	return [
		"label"=>$label,
		"saperator"=>$html_saperator
	]; */
}

function getNavTarget($row){
	$target_blank = "";
	if($row['loginRequired'] == '1' && !itemHasPrivilege($row['item_privilege'])){
		$target = "javascript:void(0);";
		$enable_class = "disabled ";
	}else if($row['loginRequired'] == '1' && !itemHasEnable($row['enabled']) ){
		$target = "javascript:void(0);";
		$enable_class = "disabled ";
	} else {
		$enable_class = "enabled ";
		$item_target = trim($row['item_target']);
		if ($item_target == '') {
			$item_target = 'main.php';
		}
		// Remove all illegal characters from a url
		$item_target = filter_var($item_target, FILTER_SANITIZE_URL);
		// If Url is valid then et target as defined in DB
		if (filter_var($item_target, FILTER_VALIDATE_URL)) {
			$target = $item_target;
			$target_blank ="target='_blank'";
		} elseif($item_target == "#" || strpos($row['item_number'],".0")) {
			$target = "javascript:void(0);";
		} else {
			$target = BASE_URL_SYSTEM . $item_target . "?display=" . $row['target_display_page'] . "&layout=" . $row['page_layout_style'] . "&style=" . $row['page_layout_style'];
		}
	}

	return [
		'target' => $target,
		'target_blank' => $target_blank,
		'enable_class'=>$enable_class
	];
}

function getNavItemIcon($item_icon,$class,$style){
	if(empty($item_icon)){
		return "";
	}elseif(strtoupper($item_icon)=='#CURRENT-USER-PROFILE-IMAGE'){
    //     return  "<img width='16' height='16' src='".USER_UPLOADS.$_SESSION['current-user-profile-image']."'>  ";
    return  "<img class='$class' style='$style' src='".USER_UPLOADS.$_SESSION['current-user-profile-image']."'>  ";
  }
	if(file_exists($GLOBALS['APP_DIR']."system/system_images/".$item_icon)){
		return "<img class='$class' style='$style' src='".BASE_IMAGES_URL.$item_icon."'>  ";
	}
	return "";

}

function getDDUrl($list_select){
	$list_select = trim($list_select);
	if (empty($list_select)) {
		return "";
	}
	 // Remove all illegal characters from a url
	$list_select = filter_var($list_select, FILTER_SANITIZE_URL);
	 // If Url is valid then et target as defined in DB
	if (filter_var($list_select, FILTER_VALIDATE_URL)) {
		return $list_select;
	} else {
		$con = connect();
		$ddQuery = $con->query("SELECT * FROM data_dictionary where dict_id='$list_select'");
		if($ddQuery->num_rows >0 ){
			$ddRecord = $ddQuery->fetch_assoc();
			$nav = $this->con->query("SELECT * FROM navigation where target_display_page='".$ddRecord['display_page']."'");
			$layout = $itemStyle = "";
			if($nav->num_rows > 0){
				$navRecord = $nav->fetch_assoc();
				$layout = $navRecord['page_layout_style'];
				$itemStyle = $navRecord['nav_css_class'];
			}
			return BASE_URL_SYSTEM ."main.php?display=" . $ddRecord['display_page'] . "&layout=$layout&style=$itemStyle";
		}
		return "";
	}
}

function parseListExtraOption($listExtraOptions,$inPx=false){
	$height = "";
	$width = "100%";
	$align = 'left';
	$divClass = 'left_cont';
	$listExtraOptions = trim($listExtraOptions);
	if(!empty($listExtraOptions)){
		$params = explode(";",$listExtraOptions);
		if(!empty($params)){
			foreach($params as $param){
				list($key,$value) = explode("=",$param);
				switch($key){
					case 'height':
						$height = $value."px";
						break;
					case 'width':
						$width = $value.($inPx ? 'px':"%");
						break;
					case 'align':
						if($value == 'left'){
							$divClass = 'left_cont';
						} elseif($value == 'right'){
							$divClass = 'right_cont';
						} elseif($value == 'center'){
							$divClass = 'center_cont';
						}
						$align = $value;
						break;
					default:
						break;
				}
			}
		}
	}
	return [$height,$width,$align,$divClass];
}

function getSliderImages($description,$dict_id){
	$description =  trim($description);
	if(empty($description)){
		return array();
	}
  $counter = 0;
	$sliders = array();
	$images = explode(';',$description);
	foreach($images as $image){
    if(!empty(trim($image))){
      $configs = explode(',',$image);
      $portion = [];
  		if(file_exists($GLOBALS['APP_DIR']."application/banner-images/".$configs[0])){
        $portion['image'] = BASE_URL_APP."banner-images/".$configs[0];
        $portion['id'] = 'slider_'.$dict_id.'_'.$counter;
        $counter++;
  		}
      if(!empty($configs[1])){
        $portion['url'] = $configs[1];
      }
      $sliders[] = $portion;
    }
	}
	return $sliders;
}

function getImages($description){
	$description =  trim($description);
	if(empty($description)){
		return array();
	}
	$sliders = array();
	$images = explode(';',$description);
	foreach($images as $image){
		if(file_exists($GLOBALS['APP_DIR']."system/system_images/".$image)){
			$sliders[] = BASE_URL_SYSTEM."system_images/".$image;
		}
	}
	return $sliders;
}

function getBannerImages($description){
	$description =  trim($description);
	if(empty($description)){
		return '';
	}
	if(file_exists($GLOBALS['APP_DIR']."application/banner-images/".$description)){
		return BASE_URL_APP."banner-images/".$description;
	}
	return "";
}

function getIframeUrl($description){
	$description =  trim($description);
	if(empty($description)){
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
function dislayUserNameSelector($selector){
  $tempSelector = strtoupper($selector);
  if($tempSelector=='#CURRENT-USERNAME'){
    return $_SESSION['current-username'];
  }elseif($tempSelector=='#CURRENT-USER-FIRSTNAME'){
    return $_SESSION['current-user-firstname'];
  }elseif($tempSelector=='#CURRENT-USER-FIRST-LASTNAME'){
    return $_SESSION['current-user-first-lastname'];
  }else{
    return $selector;
  }
}

function setUserDataInSession($con,$user){
    $name = 'uname';
    $userNameQuery = "SELECT * FROM field_dictionary WHERE field_identifier='username'";
      $fieldQuery = $con->query($userNameQuery) OR $message = mysqli_error($con);
      if($fieldQuery->num_rows > 0){
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

function log_event($display_page,$action,$senderId=false,$reciverId=false){
  if(EVENT_LOGGING_ON !=='ON'){
    return;
  }
  $action = strtoupper(trim($action));
  $event_log_code = get('event_log_codes',"event_name='$action'");
  $event_action_default = false;
  $event_notification_alert_type_default = false;
  if(!empty($event_log_code)){
    $event_action_default = $event_log_code['event_action_default'];
    $event_notification_alert_type_default = $event_log_code['event_notification_alert_type_default'];
  }

  if($senderId!== false){
    $userId = $senderId;
    $targetUserId = $reciverId;
  }else{
    $userId = $_SESSION['uid'];
    $targetUserId = $_SESSION['uid'];
  }
  if($event_action_default && $event_action_default>0){
    $data = [];
    $data['display_page'] = $display_page;
    $data['action_taken'] = $action;
    $data['user_id'] = $userId;
    $data['target_user_id'] = $targetUserId;
    $data['event_log_code'] = $event_log_code['event_log_code_id'];
    $data['notification_type'] = $event_notification_alert_type_default;
    $log_id =insert('event_log',$data);
    log_notification($event_notification_alert_type_default,$display_page,$action,$userId,$targetUserId);
  }
}

function log_notification($type,$displayPage,$action,$senderId,$reciverId){
  if(NOTIFICATION_ALERTS_ON !=='ON'){
    return;
  }
  $data = [];
  $data['notification_type'] = $type;
  $data['user_id'] = $senderId;
  $data['target_user_id'] = $reciverId;
  $data['response_type'] = 0;
  $data['alert_type'] = 0;
  $notif_id = insert('notification_log',$data);
  if($type>1){
    send_notification_alert($type,$displayPage,$action,$senderId,$reciverId);
  }
}

function send_notification_alert($type,$displayPage,$action,$senderId,$reciverId){
  if($type=='2'){
    sendEmailNotification($displayPage,$action,$senderId,$reciverId);
  }else if($type=='3'){
    //push notification
  }
}

function sendEmailNotification($page,$action,$senderId,$reciverId){
  $sender = getUserData($senderId);
  $reciver = getUserData($reciverId);
	$to = $reciver['email'];
	$subject = "Action Notification | Generic Platform";
	$message = "<html><head><title>Notification</title></head><body>";
	$message .= "Hi ".$reciver['name'].",<br/>";
  $message .= "An Action '".$action."' has occured at '".$page."' page";
  if($senderId != $reciverId){
    $message .=" by ".$sender['name'].".";
  }
  $message .= "<br/>";
	$message .= "<br/><br/>Regards,<br>Generic Platform";
	$message .= "</body></html>";
	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	// More headers
	$headers .= 'From: Generic Platform<noreply@genericplatform.com>' . "\r\n";
	try{
		$sent = mail($to,$subject,$message,$headers);
		if($sent){
			return true;
		}
		return "Unable to send email";
	} catch(Exception $e){
		return $e->getMessage();
	}
}

function sendMessageAsEmail($to,$messageText){
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
	try{
		$sent = mail($to,$subject,$message,$headers);
		if($sent){
			return true;
		}
		return "Unable to send email";
	} catch(Exception $e){
		return $e->getMessage();
	}
}

function sendMessageAndAddLog(){
  $reciverId = $_GET['reciverid'];
  $table = $_GET['table'];
  $message = trim($_GET['message']);
  $user = get("user","user_id='$reciverId'");
  $email = trim($user['email']);
  sendMessageAsEmail($email,$message);
  $data= [];
  $data['sender'] = $_SESSION['uid'];
  $data['reciver'] = $reciverId;
  $data['message'] = $message;
  insert($table,$data);
}

function getUserData($userId){
  $userData = [];
  if(false){
    $userData['id'] = $_SESSION['uid'];
    $userData['email'] = $_SESSION['current-user-email'];
    $userData['name'] = $_SESSION['current-user-first-lastname'];
  }else{
    $data = get('user',"user_id='$userId'");
    $userData['id'] =$data['user_id'];
    $userData['email'] = $data['email'];
    $userData['name'] = $data['firstname'].' '.$data['lastname'];
  }
  return $userData;
}

function getSliderInterval($extra_options){
  if(strpos($extra_options,'slider_delay') !== false){
     $all_options = explode(";", trim($extra_options));
     foreach ($all_options as $key => $option) {
       if(strpos($option,'slider_delay') !==false){
         $configs = explode(":",trim($option));
         if(trim($configs[0])=='slider_delay' && !empty(trim($configs[1]))){
           $result = (double)(trim($configs[1]));
           return $result*1000;
         }
       }
     }
  }
  return 3000;
function setBoxStyles($listExtraOptions){
  $dataSet = [];
  if(strpos($listExtraOptions,'boxstyles')!==false){
    $category_styles = trim(get_string_between($listExtraOptions,'boxstyles[',']'));
    if(!empty($category_styles)){
      $categories =   explode(',',$category_styles);
      if(!empty(trim($categories[0])) && !empty(trim($categories[1]))){
        $dataSet['table'] = trim($categories[0]);
        $dataSet['field'] = trim($categories[1]);
        return $dataSet;
      }
    }
  }
  return false;
}

function issetStyleForBox($array,$filter){
  if(isset($array[$filter]) && !is_null($array[$filter])  && !empty($array[$filter])){
    return $filter.':'.$array[$filter].';';
  }
  return '';
}

function fetchStyleConfigs($category_styles){
  $category_styles  = trim($category_styles);
  $dataSet = [];
  if(empty($category_styles)){
    return false;
  }
  $categories =   explode(',',$category_styles);
  if(empty(trim($categories[0])) ||  empty(trim($categories[1]))){
    return false;
  }

  $dataSet['table'] = trim($categories[0]);
  $dataSet['field'] = trim($categories[1]);

  return $dataSet;
}
function findAndSetCategoryStyles($con,$category_styles){


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
?>
