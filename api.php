<?php
/**
 * Created by PhpStorm.
 * User: abhinav
 * Date: 07/08/18
 * Time: 3:24 PM
 */

require_once("appConfig/appConfig.php");
include_once("application/database/db.php");
require_once 'application/config.php';



//check parameters
if(
    (!isset($_GET["log_email"]) || trim($_GET["log_email"]) == '') ||
    (!isset($_GET["log_pwd"]) || trim($_GET["log_pwd"]) == '') ||
    (!isset($_GET["did"]) || trim($_GET["did"]) == '')
){
    echo 'Invalid parameters';
    return false;
}


//check login details

$data['email'] = $_GET["log_email"];
$data['password'] = $_GET["log_pwd"];

$ws = " email='" . $data['email'] . "' or uname='" . $data['email'] . "'";
$result = get('user', $ws);
if(empty($result)){
    echo 'Invalid Login';
    return false;
}
elseif($result['password'] != $data['password']){
    echo 'Invalid Login';
    return false;
}
else{
    $loginArray = [
        'did' => $_GET['did'],
        'layout' => $_GET['layout'],
        'style' => $_GET['style'],
        'uid' => $result['user_id'],
        'uname' => $result['uname'],
        'email' => $result['email'],
        'country' => $result['country'],
        'user_privilege' => $result['user_privilege_level'],
        'source' => 'api',
    ];

    $urlParameters = http_build_query($loginArray);

    $apiURl = BASE_URL.'system/profile.php?'.$urlParameters;

}

// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $apiURl,
    CURLOPT_USERAGENT => 'GPE API REQUEST'
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
// Close request to clear up some resources
curl_close($curl);

echo $resp;