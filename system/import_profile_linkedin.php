<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("functions_loader.php");
include("header.php");

function makeCurlRequest($url, $params, $post) 
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60000);
    if($post){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["application/x-www-form-urlencoded"]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function getInfo($accessToken)
    {
        $url = "https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams))&oauth2_access_token=" . $accessToken;
        $params = [];
        return makeCurlRequest($url, $params, false);
    }

if (isset($_GET['state']) && isset($_GET['code']) && isset($_SESSION['linkedInCSRF']) && $_GET['state'] == $_SESSION['linkedInCSRF']) {
    $url = "https://www.linkedin.com/oauth/v2/accessToken";
    $params = [
        'client_id' => LINKEDIN_APP_ID,
        'client_secret' => LINKEDIN_APP_SECRET,
        'redirect_uri' => LINKEDIN_APP_REDIRECT_URL,
        'code' => $_GET['code'],
        'grant_type' => 'authorization_code',
    ];
    $response = makeCurlRequest($url, $params, true);
    $arrayResponse = json_decode($response, true);
    $stdProfile = getInfo($arrayResponse['access_token']);
    $arrayProfile = json_decode($stdProfile, true);
    $languageCountry = array_keys($arrayProfile['firstName']['localized'])[0];
    echo "First Name: ".$arrayProfile['firstName']['localized'][$languageCountry]."<br>";
    echo "Last Name: ".$arrayProfile['lastName']['localized'][$languageCountry]."<br>";
    echo "Profile Image: ".$arrayProfile['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier']."<br>";
    echo "<img src='".$arrayProfile['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier']."'/><br>";
} else {
    $csrf = bin2hex(openssl_random_pseudo_bytes(24));
    $_SESSION['linkedInCSRF'] = $csrf;
    $authUrl = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=" . LINKEDIN_APP_ID . "&redirect_uri=" . LINKEDIN_APP_REDIRECT_URL . "&state=" . $csrf . "&scope=" . LINKEDIN_APP_PERMISSIONS;
    ?>
    <button class="btn btn-lg btn-primary btn-block" name="signInLinkedIn" onclick="window.location.href = '<?php echo $authUrl; ?>'">Sign In With LinkedIn</button>
    <?php
}
?>
<?php

/* require_once("functions_loader.php");
  include("header.php");
  /*
  $conn = connect();
  $nav = $conn->query("SELECT * FROM navigation where item_target='".BASE_URL_SYSTEM."import_profile_linkedin.php'");
  if($nav->num_rows > 0){
  $navRecord = $nav->fetch_assoc();
  $displayPage = $navRecord['target_display_page']; ?>
  <script type="text/javascript" src="//platform.linkedin.com/in.js">
  api_key		: <?php echo LINKEDIN_APP_ID; ?>
  authorize	: true
  //scope		: r_basicprofile r_emailaddress
  lang            : US
  onLoad		: onLinkedInLoad
  </script>
  <script>
  // Setup an event listener to make an API call once auth is complete
  function onLinkedInLoad() {
  IN.UI.Authorize().place();
  IN.Event.on(IN, "auth", getProfileData);
  }
  // Use the API call wrapper to request the member's profile data
  function getProfileData() {
  IN.API.Profile("me").fields("id", "first-name", "last-name", "headline", "location", "picture-url", "public-profile-url", "email-address","summary").result(displayProfileData).error(onError);
  }
  // Handle the successful return from the API call
  function displayProfileData(data){
  var user = data.values[0];
  // Save user data
  saveUserData(user);
  }

  // Save user data to the database
  function saveUserData(userData){
  $.ajax({
  type: 'POST',
  url: '?action=update&table_type=linkedinimportprofile&display_page=<?php echo $displayPage; ?>',
  dataType: 'json',
  data: userData,
  beforeSend: function(xhr) {

  },
  success: function(response){
  if(response.message){
  alert(response.message);
  window.location.href = response.returnUrl;
  } else {
  window.location.href = response.returnUrl;
  }
  },
  error: function(xhr, status, error) {
  alert("Something went wrong. Please try again.");
  }
  });
  }

  // Handle an error response from the API call
  function onError(error) {
  console.log(error);
  }

  // Destroy the session of linkedin
  function logout(){
  IN.User.logout(removeProfileData);
  }

  // Remove profile data from page
  function removeProfileData(){
  //document.getElementById('profileData').remove();
  }
  </script>

  <?php
  }
  ?>
  <h2 style="text-align:center;width:100%;">Loading...</h2>







  <?php
  /*require_once("functions_loader.php");
  include("header.php");

  if (isset($_GET['state']) && isset($_GET['code']) && isset($_SESSION['linkedInCSRF']) && $_GET['state'] == $_SESSION['linkedInCSRF']) {
  $url = "https://www.linkedin.com/oauth/v2/accessToken";
  $params = [
  'client_id' => LINKEDIN_APP_ID,
  'client_secret' => LINKEDIN_APP_SECRET,
  'redirect_uri' => LINKEDIN_APP_REDIRECT_URL,
  'code' => $_GET['code'],
  'grant_type' => 'authorization_code',
  ];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 60000);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ["application/x-www-form-urlencoded"]);
  $response = curl_exec($ch);
  curl_close($ch);
  $accessToken = json_decode($response);
  var_dump($accessToken);
  } else {
  $csrf = bin2hex(openssl_random_pseudo_bytes(24));
  $_SESSION['linkedInCSRF'] = $csrf;
  $authUrl = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=" . LINKEDIN_APP_ID . "&redirect_uri=" . LINKEDIN_APP_REDIRECT_URL . "&state=" . $csrf . "&scope=" . LINKEDIN_APP_PERMISSIONS;
  ?>
  <button class="btn btn-lg btn-primary btn-block" name="signInLinkedIn" onclick="window.location.href = '<?php echo $authUrl; ?>'">Sign In With LinkedIn</button>
  <?php
  }
  ?>
  <?php
  include("footer.php");
  //$conn = connect();
  //Change 4 and http to 9 and https in navigation table for the query below to work
  //$nav = $conn->query("SELECT * FROM navigation where item_target='".BASE_URL_SYSTEM."import_profile_linkedin.php'");
  /* $url = "https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams))&oauth2_access_token=XfrQ5g1mCy7DLPAE";
  $params = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 60000);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ["application/x-www-form-urlencoded"]);
  $response = curl_exec($ch);
  curl_close($ch);
  $person = json_decode($response);
  var_dump($person);


  if($nav->num_rows > 0){
  $navRecord = $nav->fetch_assoc();
  $displayPage = $navRecord['target_display_page']; ?>
  <script type="text/javascript" src="//platform.linkedin.com/in.js">
  api_key		: <?php echo LINKEDIN_APP_ID. PHP_EOL; ?>
  authorize	: true
  scope		: r_basicprofile r_emailaddress
  onLoad		: onLinkedInLoad
  </script>
  <script>
  // Setup an event listener to make an API call once auth is complete
  function onLinkedInLoad() {
  IN.UI.Authorize().place();
  IN.Event.on(IN, "auth", getProfileData);
  }
  // Use the API call wrapper to request the member's profile data
  function getProfileData() {
  var x = IN.API.Profile("me").fields("id", "first-name", "last-name", "headline", "location", "picture-url", "public-profile-url", "email-address","summary").result(displayProfileData).error(onError);
  console.log(x);
  }
  // Handle the successful return from the API call
  function displayProfileData(data){
  var user = data.values[0];
  // Save user data
  saveUserData(user);
  }

  // Save user data to the database
  function saveUserData(userData){
  $.ajax({
  type: 'POST',
  url: '?action=update&table_type=linkedinimportprofile&display_page=<?php echo $displayPage; ?>',
  dataType: 'json',
  data: userData,
  beforeSend: function(xhr) {

  },
  success: function(response){
  if(response.message){
  alert(response.message);
  window.location.href = response.returnUrl;
  } else {
  window.location.href = response.returnUrl;
  }
  },
  error: function(xhr, status, error) {
  alert("Something went wrong. Please try again.");
  }
  });
  }

  // Handle an error response from the API call
  function onError(error) {
  console.log(error);
  }

  // Destroy the session of linkedin
  function logout(){
  IN.User.logout(removeProfileData);
  }

  // Remove profile data from page
  function removeProfileData(){
  //document.getElementById('profileData').remove();
  }
  onLinkedInLoad();
  </script>

  <?php
  }
  ?>
  <h2 style="text-align:center;width:100%;">Loading...</h2> */
?>
