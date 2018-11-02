<?php

if ($_SERVER['HTTP_HOST'] === 'localhost')
{
  include_once($_SERVER['DOCUMENT_ROOT'] . '/models/GenericDBFunctions.php');
   }
else
{
  include_once($_SERVER['SUBDOMAIN_DOCUMENT_ROOT'] . '/models/GenericDBFunctions.php');

$DD_database_table_name = $_SESSION['DD_database_table_name'];
$database_table_name = "database_table_name";
$FD_database_table_name = $_SESSION["FD_database_table_name"];
$session_Array = array();
$session_Array = $_SESSION["datadictionaryArray"];


$userTblArray = array();
$userTblArray['table_alias'] = GenericDBFunctions::get_DD_table_alias(USER_TABLETYPE);


// right now - this gets the ONLY user table - ie  - no parent table ...
// this means I have not yet coded the situation where there can be secondary user table/companion table
// where the primary user table is the parent.

$userTblArray['database_table_name'] = GenericDBFunctions::get_DD_database_table_name(USER_TABLETYPE);

$arrlength=count($internal_field_identifier[USER_TABLETYPE]);
for($x=0;$x<$arrlength;$x++)
  {
$userTblArray[$internal_field_identifier[USER_TABLETYPE][$x]] = $session_Array[$userTblArray['table_alias']][$x][generic_field_name];
  }

$arrlength=count($internal_field_identifier[USER_TABLETYPE]);


// d($internal_field_identifier[USER_TABLETYPE]);

d($session_Array);
  dd($userTblArray);

/*
$userTblArray['uid_fld'] = $session_Array[$userTblArray['table_alias']][0][generic_field_name];
$userTblArray['uname_fld'] = $session_Array[$userTblArray['table_alias']][1][generic_field_name];
$userTblArray['firstname_fld'] = $session_Array[$userTblArray['table_alias']][2][generic_field_name];
$userTblArray['lastname_fld'] = $session_Array[$userTblArray['table_alias']][3][generic_field_name];
$userTblArray['password_fld'] = $session_Array[$userTblArray['table_alias']][4][generic_field_name];
$userTblArray['reset_password_fld'] = $session_Array[$userTblArray['table_alias']][5][generic_field_name];
$userTblArray['reset_password_flag_fld'] = $session_Array[$userTblArray['table_alias']][6][generic_field_name];
$userTblArray['email_fld'] = $session_Array[$userTblArray['table_alias']][7][generic_field_name];
$userTblArray['aboutme_fld'] = $session_Array[$userTblArray['table_alias']][8][generic_field_name];
$userTblArray['interests_fld'] = $session_Array[$userTblArray['table_alias']][9][generic_field_name];
$userTblArray['skills_fld'] = $session_Array[$userTblArray['table_alias']][10][generic_field_name];
$userTblArray['image_fld'] = $session_Array[$userTblArray['table_alias']][11][generic_field_name];
$userTblArray['upload_care_img_url_fld'] = $session_Array[$userTblArray['table_alias']][12][generic_field_name];
$userTblArray['company_fld'] = $session_Array[$userTblArray['table_alias']][13][generic_field_name];
$userTblArray['city_fld'] = $session_Array[$userTblArray['table_alias']][14][generic_field_name];
$userTblArray['state_fld'] = $session_Array[$userTblArray['table_alias']][15][generic_field_name];
$userTblArray['zip_fld'] = $session_Array[$userTblArray['table_alias']][16][generic_field_name];
$userTblArray['country_fld'] = $session_Array[$userTblArray['table_alias']][17][generic_field_name];
$userTblArray['description_fld'] = $session_Array[$userTblArray['table_alias']][18][generic_field_name];
$userTblArray['level_fld'] = $session_Array[$userTblArray['table_alias']][19][generic_field_name];
$userTblArray['date_added_fld'] = $session_Array[$userTblArray['table_alias']][20][generic_field_name];
$userTblArray['login_ip_fld'] = $session_Array[$userTblArray['table_alias']][21][generic_field_name];
$userTblArray['isActive_fld'] = $session_Array[$userTblArray['table_alias']][22][generic_field_name];
$userTblArray['oauth_provider_fld'] = $session_Array[$userTblArray['table_alias']][23][generic_field_name];
$userTblArray['twitter_oauth_token_fld'] = $session_Array[$userTblArray['table_alias']][24][generic_field_name];
$userTblArray['twitter_oauth_token_secret_fld'] = $session_Array[$userTblArray['table_alias']][25][generic_field_name];
$userTblArray['last_login_fld'] = $session_Array[$userTblArray['table_alias']][26][generic_field_name];
$userTblArray['twitter_account_fld'] = $session_Array[$userTblArray['table_alias']][27][generic_field_name];
$userTblArray['facebook_account_fld'] = $session_Array[$userTblArray['table_alias']][28][generic_field_name];
$userTblArray['googleplus_account_fld'] = $session_Array[$userTblArray['table_alias']][29][generic_field_name];
$userTblArray['user_type_fld'] = $session_Array[$userTblArray['table_alias']][30][generic_field_name];
$userTblArray['user_type_status_fld'] = $session_Array[$userTblArray['table_alias']][31][generic_field_name];
$userTblArray['user_privilege_level_fld'] = $session_Array[$userTblArray['table_alias']][32][generic_field_name];
*/

d($userTblArray['table_alias']);
dd($userTblArray);


// ***********  PROJECT TABLE     ************
$projectTblArray = array();
$projectTblArray['table_alias'] = GenericDBFunctions::get_DD_table_alias("project");
$projectTblArray['database_table_name'] = GenericDBFunctions::get_DD_database_table_name("project");

$projectTblArray['pid_fld'] = $session_Array[$projectTblArray['tableAlias']][0][generic_field_name];
$projectTblArray['uid_fld'] = $session_Array[$projectTblArray['tableAlias']][1][generic_field_name];
$projectTblArray['cid_fld'] = $session_Array[$projectTblArray['tableAlias']][2][generic_field_name];
$projectTblArray['pname_fld'] = $session_Array[$projectTblArray['tableAlias']][3][generic_field_name];
$projectTblArray['description_fld'] = $session_Array[$projectTblArray['tableAlias']][4][generic_field_name];
$projectTblArray['projectImage_fld'] = $session_Array[$projectTblArray['tableAlias']][5][generic_field_name];
$projectTblArray['upload_care_img_url_fld'] = $session_Array[$projectTblArray['tableAlias']][6][generic_field_name];
$projectTblArray['create_date_fld'] = $session_Array[$projectTblArray['tableAlias']][7][generic_field_name];
$projectTblArray['expiry_date_fld'] = $session_Array[$projectTblArray['tableAlias']][8][generic_field_name];
$projectTblArray['amount_fld'] = $session_Array[$projectTblArray['tableAlias']][9][generic_field_name];
$projectTblArray['quantity_fld'] = $session_Array[$projectTblArray['tableAlias']][10][generic_field_name];
$projectTblArray['isLive_fld'] = $session_Array[$projectTblArray['tableAlias']][11][generic_field_name];
$projectTblArray['isDraft_fld'] = $session_Array[$projectTblArray['tableAlias']][12][generic_field_name];
$projectTblArray['category_id_1_fld'] = $session_Array[$projectTblArray['tableAlias']][13][generic_field_name];
$projectTblArray['category_id_2_fld'] = $session_Array[$projectTblArray['tableAlias']][14][generic_field_name];
$projectTblArray['category_id_3_fld'] = $session_Array[$projectTblArray['tableAlias']][15][generic_field_name];
$projectTblArray['category_id_4_fld'] = $session_Array[$projectTblArray['tableAlias']][16][generic_field_name];
$projectTblArray['affiliation_id_1_fld'] = $session_Array[$projectTblArray['tableAlias']][17][generic_field_name];
$projectTblArray['affiliation_id_2_fld'] = $session_Array[$projectTblArray['tableAlias']][18][generic_field_name];
$projectTblArray['isBought_fld'] = $session_Array[$projectTblArray['tableAlias']][19][generic_field_name];
$projectTblArray['trans_id_fld'] = $session_Array[$projectTblArray['tableAlias']][20][generic_field_name];
$projectTblArray['project_type_fld'] = $session_Array[$projectTblArray['tableAlias']][21][generic_field_name];
$projectTblArray['privacy_type_fld'] = $session_Array[$projectTblArray['tableAlias']][22][generic_field_name];

d($projectTblArray);


// ***********  CROSS REF TABLE(S)     ************
$statesTblArray = array();
// $statesTblArray['table_alias'] = GenericDBFunctions::get_DD_table_alias("states");
//$statesTblArray['table_alias'] = get_DD_crossref_table_name("states");
//dd($statesTblArray);


/*
// ***********  TRANSACTION REF TABLE(S)     ************
$transTblArray = array();
$transTblArray['table_alias'] = GenericDBFunctions::get_DD_table_alias("transactions");
$transTblArray['database_table_name'] = GenericDBFunctions::get_DD_database_table_name("transactions");

$transTblArray['transaction_id_fld'] = $session_Array[$transTblArray['tableAlias']][0][generic_field_name];
$transTblArray['user_id_fld'] = $session_Array[$transTblArray['tableAlias']][1][generic_field_name];
$transTblArray['owner_id_fld'] = $session_Array[$transTblArray['tableAlias']][2][generic_field_name];
$transTblArray['project_id_fld'] = $session_Array[$transTblArray['tableAlias']][3][generic_field_name];
$transTblArray['transactionType_fld'] = $session_Array[$transTblArray['tableAlias']][4][generic_field_name];
$transTblArray['amount_fld'] = $session_Array[$transTblArray['tableAlias']][5][generic_field_name];
$transTblArray['username_fld'] = $session_Array[$transTblArray['tableAlias']][6][generic_field_name];
$transTblArray['useremail_fld'] = $session_Array[$transTblArray['tableAlias']][7][generic_field_name];
$transTblArray['host_ip_fld'] = $session_Array[$transTblArray['tableAlias']][8][generic_field_name];
$transTblArray['comments_fld'] = $session_Array[$transTblArray['tableAlias']][9][generic_field_name];
$transTblArray['transaction_datetime_fld'] = $session_Array[$transTblArray['tableAlias']][10][generic_field_name];
$transTblArray['wallet_transaction_id_fld'] = $session_Array[$transTblArray['tableAlias']][11][generic_field_name];
$transTblArray['status_fld'] = $session_Array[$transTblArray['tableAlias']][12][generic_field_name];


// Generate Child tables

// ****** User Child Tables
$query = "SELECT `$database_table_name`,`tab_name` FROM $DD_database_table_name where `table_type`='child'
and `parent_table`='{$userTblArray['table_alias']}'
and (visibility not like '0' or visibility is null)
order by `tab_num`";

$result = mysql_query($query);
$userTblChildArray = array();
$userTblChildTABArray = array();
while ($row = mysql_fetch_array($result))
{
  array_push($userTblChildArray, $row[0]);
  array_push($userTblChildTABArray, $row[1]);
  //echo "<br><br><br><br><br>".$row[0];
}


//  ********  Transaction Tables
$query = "SELECT `$database_table_name`,`tab_name` FROM $DD_database_table_name where `table_type`='child'
and `parent_table`='{$transTblArray['tableAlias']}'
and (visibility not like '0' or visibility is null)
order by `tab_num`";

$result = mysql_query($query);
$transTblChildArray = array();
$transTblChildTABArray = array();
while ($row = mysql_fetch_array($result))
{
  array_push($transTblChildArray, $row[0]);
  array_push($transTblChildTABArray, $row[1]);
  //echo "<br><br><br><br><br>".$row[0];
}


// ***** Project Child Tables
$query = "SELECT `$database_table_name`,`tab_name` FROM $DD_database_table_name where `table_type`='child'
and `parent_table`='{$projectTblArray['tableAlias']}'
and (visibility not like '0' or visibility is null)
order by `tab_num`";

$result = mysql_query($query);
$projsTblChildArray = array();
$projsTblChildTABArray = array();
while ($row = mysql_fetch_array($result))
{
  array_push($projsTblChildArray, $row[0]);
  array_push($projsTblChildTABArray, $row[1]);
  //echo "<br><br><br><br><br>".$row[0];
}

$query = "SELECT `$database_table_name`,`tab_name` FROM $DD_database_table_name";
$result = mysql_query($query);
$Tbl_TAB_NAME_Array = array();


while ($row = mysql_fetch_array($result))
{
  if ($row[1] != "" || $row[1] != null)
  {
    $Tbl_TAB_NAME_Array[$row[0]] = $row[1];
  }
  else
  {
    $new = explode("_", $row[0]);
    $newval = "";
    foreach ($new as $k => $v)
    {
      $newval = $newval . " " . $v;
    }

    $Tbl_TAB_NAME_Array[$row[0]] = ucwords($newval);
  }
}

$query = "SELECT `$database_table_name`,`tab_name` FROM $DD_database_table_name where table_type='crossref' and dropdown_fields='dropdown'";
$result = mysql_query($query);
$All_Dropdown_Array = array();
$All_Dropdown_ArrayTAB = array();
while ($row = mysql_fetch_array($result))
{
  $All_Dropdown_Array[$row[0]] = $row[0];
}

$query = "select `generic_field_name`,`field_identifier` from $FD_database_table_name as fd where fd.field_identifier is not null";
$result = mysql_query($query);
while ($row = mysql_fetch_array($result))
{
  $$row['field_identifier'] = $row['generic_field_name'];
}

$projectTblArray['pid_fld'] = $projectid;
$projectTblArray['uid_fld'] = $projectuserid;
$projectTblArray['pname_fld'] = $projectname;
$projectTblArray['description_fld'] = $projectdesc;
$projectTblArray['projectImage_fld'] = $projectimage;
$projectTblArray['isLive_fld'] = $projectislive;
$projectTblArray['isBought_fld'] = $projectisbought;
$projectTblArray['create_date_fld'] = $projectcreatedate;

$userTblArray['upload_care_img_url_fld'] = $useruploadimage;
$userTblArray['image_fld'] = $profile_image;
$userTblArray['last_login_fld'] = $userlastlogin;
$userTblArray['reset_password_fld'] = $userresetpassword;
$userTblArray['reset_password_flag_fld'] = $userresetpasswordflag;
$userTblArray['password_fld'] = $password;
$userTblArray['email_fld'] = $email;
$userTblArray['state_fld'] = $state;
$userTblArray['country_fld'] = $country;
$userTblArray['date_added_fld'] = $userdateadded;
$userTblArray['city_fld'] = $city;
$userTblArray['uname_fld'] = $useruname;
$userTblArray['isActive_fld'] = $userisactive;
$userTblArray['uid_fld'] = $userid;
$userTblArray['user_type_fld'] = $usertype;
$userTblArray['firstname_fld'] = $userfirstname;
$userTblArray['lastname_fld'] = $userlastname;


$list_view[$projectTblArray['tableAlias']]['img'] = $projectTblArray['projectImage_fld'];
$list_view[$projectTblArray['tableAlias']]['pid'] = $projectTblArray['pid_fld'];
$list_view[$projectTblArray['tableAlias']]['pname'] = $projectTblArray['pname_fld'];
$list_view[$projectTblArray['tableAlias']]['created'] = $projectTblArray['create_date_fld'];


$list_view[$userTblArray['table_alias']]['img'] = $userTblArray['image_fld'];
$list_view[$userTblArray['table_alias']]['pid'] = $userTblArray['uid_fld'];
$list_view[$userTblArray['table_alias']]['pname'] = ($userTblArray['uname_fld'] == "") ? $userTblArray['firstname_fld'] . $userTblArray['lastname_fld'] : $userTblArray['uname_fld'];
$list_view[$userTblArray['table_alias']]['created'] = $userTblArray['date_added_fld'];

*/
}