<?php
/*

function generateFacebookButton($facebook_array){
function generateGoogleButton($google_array){
function generateLinkedinButton($linkedin_array){
function callToLogin(){
function callToSignup(){
function forgotPassword(){
function resetPassword(){
function changePassword(){
function sendResetLinkEmail($data,$email){
function sendVerificationEmail($data){
function facebookLogin(){
function linkedInLogin(){
function importProfileFromLinkedIn(){
function verifyEmail(){
function redirectToResetPassword(){

*/			
	
	
function generateFacebookButton($facebook_array){
	$facebookButton = "<a onclick='fbLogin();' class='btn btn-primary update-btn " . $facebook_array['style'] . "'>
					<span class='fa fa-facebook'></span>
					".$facebook_array['value']."
				</a> &nbsp;"; ?>
	<script>
	window.fbAsyncInit = function() {
		// FB JavaScript SDK configuration and setup
		FB.init({
			appId      : '<?php echo FACEBOOK_APP_ID; ?>', // FB App ID
			cookie     : true,  // enable cookies to allow the server to access the session
			xfbml      : true,  // parse social plugins on this page
			version    : 'v2.8' // use graph api version 2.8
		});

		// Check whether the user already logged in
		/* FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
				//display user data
				//getFbUserData();
			}
		}); */
	};

	// Load the JavaScript SDK asynchronously
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));

	// Facebook login with JavaScript SDK
	function fbLogin() {
		FB.login(function (response) {
			if (response.authResponse) {
				// Get and display the user profile data
				getFbUserData();
			} else {
				alert('User cancelled signup or did not fully authorize.');
			}
		}, {scope: 'email'});
	}

	// Fetch the user profile data from facebook
	function getFbUserData(){
		FB.api('/me', {locale: 'en_US', fields: 'id,first_name,last_name,email,link,gender,locale,picture'},
		function (response) {
			$.ajax({
				type: 'POST',
				url: '?action=update&table_type=facebookLogin',
				dataType: 'json',
				data: response,
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
		});
	}
	</script>
	<?php
	return $facebookButton;
}


function generateGoogleButton($google_array){
	$googleButton = "<a id='googleSignup' class='btn btn-primary update-btn " . $google_array['style'] . "'>
							<span class='fa fa-google'></span>
							".$google_array['value']."
						</a> &nbsp;"; ?>
	<script src="https://apis.google.com/js/api:client.js"></script>
	<script>
		var googleUser = {};
		var googleSignup = function() {
			gapi.load('auth2', function(){
				// Retrieve the singleton for the GoogleAuth library and set up the client.
				auth2 = gapi.auth2.init({
					client_id: '<?php echo GOOGLE_CLIENT_ID; ?>',
					cookiepolicy: 'single_host_origin',
					//callback : 'googleCallback',
					scope: 'profile email',
					//approvalprompt:'force',
					//scope : 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read'
				});
				attachSignin(document.getElementById('googleSignup'));
			});
		};

		function attachSignin(element) {
			auth2.attachClickHandler(element, {},
				function(googleUser) {
					$.ajax({
						type: 'POST',
						url: '?action=update&table_type=googleLogin',
						dataType: 'json',
						data: { email : googleUser.getBasicProfile().getEmail() , name : googleUser.getBasicProfile().getName() },
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
				}, function(error) {
					//JSON.stringify(error.error, undefined, 2)
					alert(error.error);
				}
			);
		}

		googleSignup();
		</script>
	<?php
	return $googleButton;
}

function generateLinkedinButton($linkedin_array){
	$linkedinButton = "<a onclick='onLinkedInLoad()' class='btn btn-primary update-btn " . $linkedin_array['style'] . "'>
							<span class='fa fa-linkedin'></span>
							".$linkedin_array['value']."
						</a> &nbsp;"; ?>
	<script type="text/javascript" src="//platform.linkedin.com/in.js">
		api_key		: <?php echo LINKEDIN_APP_ID. PHP_EOL; ?>
		authorize	: true
		scope		: r_basicprofile r_emailaddress
	</script>
	<script>
		function onLinkedInLoad() {
			IN.UI.Authorize().place();
			<?php if (stripos($_SERVER['HTTP_USER_AGENT'], 'Edge/') !== false) {  ?>
				IN.Event.on(IN, "auth", getProfileData());
			<?php } else {  ?>
				IN.Event.on(IN, "auth", getProfileData);
			<?php } ?>
		}
		function getProfileData() {
			//IN.API.Raw("/people/~").result(displayProfileData).error(onError);
			IN.API.Profile("me").fields("id", "first-name", "last-name", "headline", "location", "picture-url", "public-profile-url", "email-address","summary").result(displayProfileData).error(onError);
		}
		function displayProfileData(data){
			var user = data.values[0];
			/* document.getElementById("picture").innerHTML = '<img src="'+user.pictureUrl+'" />';
			document.getElementById("name").innerHTML = user.firstName+' '+user.lastName;
			document.getElementById("intro").innerHTML = user.headline;
			document.getElementById("email").innerHTML = user.emailAddress;
			document.getElementById("location").innerHTML = user.location.name;
			document.getElementById("link").innerHTML = '<a href="'+user.publicProfileUrl+'" target="_blank">Visit profile</a>';
			document.getElementById('profileData').style.display = 'block'; */
			saveUserData(user);
		}

		// Save user data to the database
		function saveUserData(userData){
			$.ajax({
				type: 'POST',
				url: '?action=update&table_type=linkedinLogin',
				dataType: 'json',
				//data: { email : userData.getBasicProfile().getEmail() , name : googleUser.getBasicProfile().getName() },
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
	return $linkedinButton;
}


function callToLogin(){
	$table_name = $_SESSION['update_table2']['table_name'];
  $primaryKey = $_SESSION['update_table2']['keyfield'];
  $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$query = "SELECT * FROM $table_name WHERE ";
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
			$query .= " AND $_SESSION[user_field_password] = '$passValue'" ;
			$userQuery = $con->query($query) OR $message = mysqli_error($con);
			if($userQuery->num_rows > 0){
				$user = $userQuery->fetch_assoc();
				if($user['isActive'] == 1){
					$_SESSION['uid'] = $user[$primaryKey];
                    setUserDataInSession($con,$user);
					$message = '';
					//$message = PROFILE_COMPLETE_MESSAGE;
					$returnUrl = BASE_URL."index.php";
				} else {
					$message = "Your account is Inactive. Please contact to administrator";
				}
			} else {
			    $message = resetPasswordIfFlagIsSet($con);
                if($message === false){
                  $message = "Invalid Username/Email or Password";
                }else{
                  $returnUrl = BASE_URL."index.php";
                }
			}
		}
	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
  log_event('login','login');
	echo "<script>window.location='".$returnUrl."';</script>";
}
/*
 * // To Do: Login Function functionality
 *
 */
 

function callToSignup(){

  log_event($_GET['page_name'],'signup');

	$table_name = $_SESSION['update_table2']['table_name'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$emailField = $_SESSION['user_field_email'];
		$passField = $_SESSION['user_field_password'];
		$unameField = $_SESSION['user_field_uname'];

		$emailValue = trim($_POST[$_SESSION['user_field_email']]);
		$passValue = trim($_POST[$_SESSION['user_field_password']]);
		$unameValue = trim($_POST[$_SESSION['user_field_uname']]);
		if(!empty($emailValue) && !empty($passValue) && !empty($unameValue)){
			$checkEmailUnique = $con->query("SELECT * FROM $table_name WHERE $emailField = '$emailValue'");
			if($checkEmailUnique->num_rows > 0){
				$message = "Email address already exist.";
				echo "<script>alert('$message');</script>";
				echo "<script>window.location='".$returnUrl."';</script>";
				return;
			}
			$checkUsernameUnique = $con->query("SELECT * FROM $table_name WHERE $unameField = '$unameValue'");
			if($checkUsernameUnique->num_rows > 0){
				$message = "Username already exist.";
				echo "<script>alert('$message');</script>";
				echo "<script>window.location='".$returnUrl."';</script>";
				return;
			}
			$_POST = array_map("trim", $_POST);
			$data = $_POST;
			$data[$passField] = md5($data[$passField]);
			$data['user_privilege_level'] = 5;
			$data['isActive'] = 0;
			$data['token'] = uniqid();
			$data['timeout'] = date('Y-m-d H:i:s',strtotime('+1 day'));
			$user_id = insert($table_name,$data);
			$status = sendVerificationEmail($data);
			if($status == true){
				$message = "Please verify your email address sent to your email";
				$returnUrl = BASE_URL."index.php";
			} else {
				$message = $status;
			}
		}

	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}

/*
 * // To Do: forgotPassword Function
 *
 */
function forgotPassword(){
	$table_name = $_SESSION['update_table2']['table_name'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$emailField = $_SESSION['user_field_email'];
		$emailValue = trim($_POST[$_SESSION['user_field_email']]);
		if(!empty($emailValue)){
			$checkEmail = $con->query("SELECT * FROM $table_name WHERE $emailField = '$emailValue'");
			if($checkEmail->num_rows == 0){
				$message = "Email address is not registered with us.";
				echo "<script>alert('$message');</script>";
				echo "<script>window.location='".$returnUrl."';</script>";
				return;
			}
			$user = $checkEmail->fetch_assoc();
			if($user['isActive'] == 1){
				$data['token'] = uniqid();
				$data['timeout'] = date('Y-m-d H:i:s',strtotime('+1 day'));
				$where = array($emailField => $emailValue);
				update($table_name,$data,$where);
				$status = sendResetLinkEmail($data,$emailValue);
				if($status == true){
					$message = "Please check your email with reset password link.";
					$returnUrl = BASE_URL."index.php";
				} else {
					$message = $status;
				}
			} else {
				$message = "Your account is Inactive. Please contact to administrator";
			}
		}

	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}

/*
 * // To Do: resetPassword Function
 *
 */
function resetPassword(){
	$table_name = $_SESSION['update_table2']['table_name'];
	$token = $_SESSION['reset_token'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$checkToken = $con->query("SELECT * FROM $table_name WHERE token = '$token' AND timeout > '".date('Y-m-d H:i:s')."'");
		if($checkToken->num_rows > 0){
			$passwordField = $_SESSION['user_field_password'];
			$confirmPasswordField = $_SESSION['user_field_confirm_password'];
			$passwordValue = trim($_POST[$passwordField]);
			$confirmPasswordValue = trim($_POST[$confirmPasswordField]);
			if(empty($passwordValue)){
				$message = "Please enter new password.";
			} elseif(empty($confirmPasswordValue)){
				$message = "Please confirm your password.";
			} elseif($passwordValue != $confirmPasswordValue){
				$message = "Your passwords do not match. Please type carefully.";
			} else {
				$user = $checkToken->fetch_assoc();
				$passwordValue = md5($passwordValue);
				$update = $con->query("UPDATE $table_name SET $passwordField='$passwordValue' , token=NULL ,timeout=NULL WHERE token = '$token'");
				unset($_SESSION['reset_token']);
        $_SESSION['uid'] = $user[$_SESSION['update_table2']['keyfield']];
        setUserDataInSession($con,$user);
				$message = "Your password has been changed. Please login.";
				$returnUrl = BASE_URL."index.php";
			}
		} else {
			$returnUrl = BASE_URL."index.php";
			$message = "Invalid or expired token. Please check your email or try again.";
		}
	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}

/*
 * // To Do: change Password Function
 *
 */
function changePassword(){
	$table_name = $_SESSION['update_table2']['table_name'];
	$token = $_SESSION['reset_token'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $user_id = $_SESSION['uid'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$userQuery = $con->query("SELECT * FROM $table_name WHERE $primaryKey = '$user_id'");
		if($userQuery->num_rows > 0){
			$oldPasswordField = $_SESSION['user_field_old_password'];
			$passwordField = $_SESSION['user_field_password'];
			$confirmPasswordField = $_SESSION['user_field_confirm_password'];
			$oldPasswordValue = trim($_POST[$oldPasswordField]);
			$passwordValue = trim($_POST[$passwordField]);
			$confirmPasswordValue = trim($_POST[$confirmPasswordField]);
			if(empty($oldPasswordValue)){
				$message = "Please enter old password.";
			} elseif(empty($passwordValue)){
				$message = "Please enter your new password.";
			} elseif(empty($confirmPasswordValue)){
				$message = "Please confirm your password.";
			} elseif($passwordValue != $confirmPasswordValue){
				$message = "Your passwords do not match. Please type carefully.";
			} else {
				$user = $userQuery->fetch_assoc();
				if(md5($oldPasswordValue) != $user[$passwordField]){
					$message = "Your old password is incorrect. Please try again.";
				} else {
					$passwordValue = md5($passwordValue);
					$update = $con->query("UPDATE $table_name SET $passwordField='$passwordValue' WHERE $primaryKey = '$user_id'");
					$message = "Your password has been changed.";
					$returnUrl = BASE_URL."index.php";
				}
			}
		} else {
			$returnUrl = BASE_URL."index.php";
			$message = "Invalid user. Please check your email or try again.";
		}
	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}


function sendResetLinkEmail($data,$email){
	$returnUrl = $_SESSION['return_url2'];
	$to = $email;
	$token = $data['token'];
	$table_name = $_SESSION['update_table2']['table_name'];
	$url = BASE_URL_SYSTEM."main-loop.php?action=reset_email&table=$table_name&token=$token";
	$subject = "Reset Password | Generic Platform";
	$message = "<html><head><title>Reset Password</title></head><body>";
	$message .= "Hi,<br/>";
	$message .= "Please click <a href='".$url."'>here</a> to reset your password or visit the below link.</br>";
	$message .= "$url";
	$message .= "<br/><br/>Regards,<br>Generic Platform";
	$message .= "</body></html>";

	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: Generic Platform<noreply@genericplatform.com>' . "\r\n";
	//$headers .= 'Cc: myboss@example.com' . "\r\n";

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

function sendVerificationEmail($data){
	$returnUrl = $_SESSION['return_url2'];
	$to = $data[$_SESSION['user_field_email']];
	$token = $data['token'];
	$table_name = $_SESSION['update_table2']['table_name'];
	$url = BASE_URL_SYSTEM."main-loop.php?action=verify_registration_email&table=$table_name&token=$token";

     /*Code Change for Task 8.5.102 Start*/
    $event_log_code = get('event_log_codes',"event_name='SIGNUP'");
    $notification_subject = $event_log_code['notification_subject'];
    $notification_message = $event_log_code['notification_message'];
    /*Code Change for Task 8.5.102 End*/

	/*$subject = "Email Verification | Generic Platform";
	$message = "<html><head><title>Email Verification</title></head><body>";
	$message .= "Hi,<br/>";
	$message .= "Please click <a href='".$url."'>here</a> to verify your email address or visit the below link.</br>";
	$message .= "$url";
	$message .= "<br/><br/>Regards,<br>Generic Platform";
	$message .= "</body></html>";*/

    /*Code Change for Task 8.5.102 Start*/
    $notification_message = str_replace("verification_url", $url, $notification_message);
    $subject = $notification_subject;
    $message = $notification_message;
    /*Code Change for Task 8.5.102 End*/


	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: Generic Platform<noreply@genericplatform.com>' . "\r\n";
	//$headers .= 'Cc: myboss@example.com' . "\r\n";

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
/*
 * // To Do: Login Function functionality
 *
 */
function facebookLogin(){
	$table_name = $_SESSION['update_table2']['table_name'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$facebookIdValue = $_POST['id'];
		$emailField = $_SESSION['user_field_email'];
		$unameField = $_SESSION['user_field_uname'];
		$emailValue = $_POST['email'];
		$passField = $_SESSION['user_field_password'];
		if(!empty($emailValue)){
			// Check If email already exist. If exist then create a new session of that user
			$check = "SELECT * FROM $table_name WHERE $emailField = '$emailValue'";
			$checkQuery = $con->query($check);
			$data[$emailField] = $emailValue;
			$data['oauth_provider'] = 'Facebook';
			$data['facebook_account'] = $facebookIdValue;
			if($checkQuery->num_rows > 0){
				$user = $checkQuery->fetch_assoc();
				$where = array($primaryKey => $user[$primaryKey]);
				update($table_name,$data,$where);
				$user = get($table_name,"$primaryKey=$user[$primaryKey]");
			} else {
				// Insert new record in users table
				$uname = explode("@",$emailValue)[0];
				$data[$unameField] = $uname.time();
				$data['user_privilege_level'] = 5;
				$user_id = insert($table_name,$data);
				$user = get($table_name,"$primaryKey=$user_id");
			}
			if($user['isActive'] == 1){
				$_SESSION['uid'] = $user[$primaryKey];
                setUserDataInSession($con,$user);
				$message = '';
				$returnUrl = BASE_URL."index.php";
			} else {
				$message = "Your account is Inactive. Please contact to administrator";
			}
		}
	}
	return [
		'message' => $message,
		'returnUrl' =>$returnUrl
	];
}

/*
 * // To Do: Login Function functionality
 *
 */
function linkedInLogin(){
	$table_name = $_SESSION['update_table2']['table_name'];
    $primaryKey = $_SESSION['update_table2']['keyfield'];
    $con = connect();
	$message = "Please enter required fields";
	$returnUrl = $_SESSION['return_url2'];
	if(!empty($_POST)){
		$linkedInIdValue = $_POST['id'];
		$emailField = $_SESSION['user_field_email'];
		$unameField = $_SESSION['user_field_uname'];
		$emailValue = $_POST['emailAddress'];
		$passField = $_SESSION['user_field_password'];
		if(!empty($emailValue)){
			// Check If email already exist. If exist then create a new session of that user
			$check = "SELECT * FROM $table_name WHERE $emailField = '$emailValue'";
			$checkQuery = $con->query($check);
			$data[$emailField] = $emailValue;
			$data['oauth_provider'] = 'linkedIn';
			if($checkQuery->num_rows > 0){
				$user = $checkQuery->fetch_assoc();
				$where = array($primaryKey => $user[$primaryKey]);
				update($table_name,$data,$where);
				$user = get($table_name,"$primaryKey=$user[$primaryKey]");
			} else {
				// Insert new record in users table
				$uname = explode("@",$emailValue)[0];
				$data[$unameField] = $uname.time();
				$data['user_privilege_level'] = 5;
				$user_id = insert($table,$data);
				$user = get($table_name,"$primaryKey=$user_id");
			}
			if($user['isActive'] == 1){
				$_SESSION['uid'] = $user[$primaryKey];
                setUserDataInSession($con,$user);
				$message = '';
				$returnUrl = BASE_URL."index.php";
			} else {
				$message = "Your account is Inactive. Please contact to administrator";
			}
		}
	}
	return [
		'message' => $message,
		'returnUrl' =>$returnUrl
	];
}

/*
 * // To Do: Import Profile From LinkedIn
 *
 */
function importProfileFromLinkedIn(){
	$page_name = $_GET['page_name'];
	$returnUrl = BASE_URL."index.php";
	$message = "";
	$con = connect();
	$query = "SELECT * FROM field_dictionary
					INNER JOIN data_dictionary ON (data_dictionary.`table_alias` = field_dictionary.`table_alias`)
					where data_dictionary.table_alias = '$page_name' ORDER BY field_dictionary.field_order";
	$ddQuery = $con->query($query);
	if($ddQuery->num_rows > 0){

		$data = array();
		while($ddRecord = $ddQuery->fetch_assoc()){
			$table_name = $ddRecord['table_name'];
			$primaryKey = $ddRecord['keyfield'];
			$fieldIdentifier = trim($ddRecord['field_identifier']);
			switch($fieldIdentifier){
				case 'firstname':
					$data[$ddRecord['generic_field_name']] = $_POST['firstName'];
					break;

				case 'lastname':
					$data[$ddRecord['generic_field_name']] = $_POST['lastName'];
					break;

				case 'country':
					$countryCode = strtolower($_POST['location']['country']['code']);
					$countryDetails = file_get_contents("https://restcountries.eu/rest/v2/alpha/$countryCode");
					$countryDetails = json_decode($countryDetails,true);
					if(!empty($countryDetails)){
						$data[$ddRecord['generic_field_name']] = $countryDetails['name'];
					}
					break;

				case 'about':
					$data[$ddRecord['generic_field_name']] = $_POST['headline'];
					break;

				case 'profileimage':
					if(isset($_POST['pictureUrl']) && !empty($_POST['pictureUrl'])){
						$content = file_get_contents($_POST['pictureUrl']);
						//Store in the filesystem.
						$image_name = uniqid().".jpg";
						$fp = fopen(USER_UPLOADS.$image_name, "w");
						fwrite($fp, $content);
						fclose($fp);
						$data[$ddRecord['generic_field_name']] = $image_name;
					}
					break;

				case 'description':
					$data[$ddRecord['generic_field_name']] = $_POST['summary'];
					break;
			}
		}
		$user = get($table_name,"$primaryKey=$_SESSION[uid]");
		if(!empty($user) && $user['oauth_provider'] == 'linkedIn'){
			if(!empty($data)){
				$where = array($primaryKey => $_SESSION['uid']);
				update($table_name,$data,$where);
				$message = 'Profie Imported successfully.';
			}
		} else {
			$message = 'Your account is not signed up through LinkedIn.';
		}
	}
	return [
		'message' => $message,
		'returnUrl' =>$returnUrl
	];
}


function verifyEmail(){
	$con = connect();
	$returnUrl = BASE_URL."index.php";
	$token = trim($_GET['token']);
	$table_name = trim($_GET['table_name']);
	$checkToken = $con->query("SELECT * FROM $table_name WHERE token = '$token' AND timeout > '".date('Y-m-d H:i:s')."'");
	if($checkToken->num_rows > 0){
		$update = $con->query("UPDATE $table_name SET token=NULL ,timeout=NULL,isActive=1 WHERE token = '$token'");
		$message = "Your email has been verified.";
	} else {
		$message = "Invalid or expired token. Please check your email or try again.";
	}

	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'reset_email'){
	redirectToResetPassword();
}

function redirectToResetPassword(){
	$con = connect();
	$returnUrl = BASE_URL."index.php";
	$token = trim($_GET['token']);
	$table_name = trim($_GET['table_name']);
	$checkToken = $con->query("SELECT * FROM $table_name WHERE token = '$token' AND timeout > '".date('Y-m-d H:i:s')."'");
	if($checkToken->num_rows > 0){
		$dataDictionary = get('data_dictionary',"component_type='reset_password'");
		$page_name = $dataDictionary['page_name'];
		$navigation = get('navigation',"target_page_name='$page_name'");
		$layout = "";
		if(!empty($navigation)){
			$layout = "";   
			$itemStyle = $navigation['nav_css_class'];
		}
		$_SESSION['reset_token'] = $token;
		$returnUrl = BASE_URL_SYSTEM."main-loop.php?page_name=$page_name&layout=$layout&style=$itemStyle";
	} else {
		$message = "Invalid or expired token. Please check your email or try again.";
	}
	if($message){
		echo "<script>alert('$message');</script>";
	}
	echo "<script>window.location='".$returnUrl."';</script>";
}