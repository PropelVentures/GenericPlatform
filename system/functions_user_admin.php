<?php

/*

function isUserLoggedin()
function isProjectOwner($pid)
function isAdmin()
function get_user_details($userTblArray)
function get_client_ip()
function profileCompletion($users, $userTblArray)
function loginNotRequired(){
function resetPasswordIfFlagIsSet($con){	
	
*/





// Root directory

/*
* "isUserLoggedIn" function is played an important role to check
* Is user logged or not by checking the keyword  "UID" is set and not blank
* return => true if uid set in session
*           flase if not set or blank
*/

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


function loginNotRequired(){
	$con = connect();
	$page_name = $_GET['page_name'];
	$nav = $con->query("SELECT * FROM navigation WHERE target_page_name='$page_name' LIMIT 1") or die($con->error);
	$navigation = $nav->fetch_assoc();
	if(!empty($navigation) && strtoupper($navigation['loginRequired']) == '2'){
		return true;
	}
	return false;
}


/*
* "isProjectOwner" function is played an important role to check
* Is logged user is project owner or not.
* return => 1 (true) if the user exist in the table.
*           0 (false) if the user didn't exist in the table
*
*  take one params "project id"
*  flag: not used any where in the code.
*/

function isProjectOwner($pid)
{
  $sql = "SELECT * FROM projects WHERE pid=" . $pid . " AND uid=" . $_SESSION['uid'];
  $result = mysql_query($sql);
  return mysql_num_rows($result);
}

/*
* "isAmin" function is played an important role to check
* Is logged user is Admin user or not by checking the "level" value in the session
* flag: not used any where in the code.
*/


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

/*
* "get_user_details" function is used to get the information of user.
*
*
* flag: not used any where in the code.
*/

function get_user_details($userTblArray)
{
  if (isset($_SESSION) && $_SESSION['uid'] != "")
  {
    $uid = $_SESSION['uid'];
    $sql = "SELECT u.{$userTblArray['uname_fld']},u.{$userTblArray['firstname_fld']},u.{$userTblArray['lastname_fld']}, u.{$userTblArray['user_type_fld']} from {$userTblArray['table_name']} as u WHERE u.{$userTblArray['uid_fld']} =" . $uid;
    $query = mysql_query($sql);
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

/*
* "get_client_ip" function is used to get the ip address of enduser.
* "getenv" Gets the value of an environment variable
*
* flag: not used any where in the code.
*/


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



/**
 * a temporary method to reset user password if its reset_password flag is on
 */
function resetPasswordIfFlagIsSet($con){

  $table_name = $_SESSION['update_table2']['table_name'];
  $primaryKey = $_SESSION['update_table2']['keyfield'];

  $query = "SELECT * FROM $table WHERE ";
  $emailValue = trim($_POST[$_SESSION['user_field_email']]);
  $usernameValue = trim($_POST[$_SESSION['user_field_uname']]);
  $passValue = trim($_POST[$_SESSION['user_field_password']]);

  if( (!empty($emailValue) || !empty($usernameValue) ) && !empty($passValue)){
    $passValue = md5($passValue);
    if($emailValue){
      $query .= "$_SESSION[user_field_email] = '$emailValue'" ;
    } elseif($usernameValue){
      $query .= "$_SESSION[user_field_uname] = '$usernameValue'" ;
    }
  }
  $userQuery = $con->query($query) OR $message = mysqli_error($con);
  if($userQuery->num_rows > 0){
    $user = $userQuery->fetch_assoc();
    if(empty($user['password']) && ($user['reset_password'] || $user['reset_password_flag'])){
      $data = array('password' => $passValue,'reset','reset_password'=>0,'reset_password_flag'=>0);
      $where = array('user_id' => $user['user_id']);
      $result = update($table_name,$data,$where);
      $_SESSION['uid'] = $user[$primaryKey];
      setUserDataInSession($con,$user);
      return '';
    }else{
      return false;
    }
  }
  return "Invalid Username/Email or Password";
}

?>
