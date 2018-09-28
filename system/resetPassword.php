<?php 
require_once("../appConfig/appConfig.php");
include_once("../application/database/db.php");
include("../application/header.php"); 


?>

<style>
.validate-error{
	border-color: #b94a48;
	box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392;
}
</style>

<div class="jumbotron projects">
<div class="container">
	<div class="row">
      <div class="col-lg-12">
		<!--Change password-->
			<div class="login-div sign-in">
			<form class="form-signin" role="form" action="<?=BASE_URL_SYSTEM?>form-actions.php" method="post" onSubmit="return loginFormvalidate()">
			<?php 
			if(isset($_SESSION["messages"])){
				echo "<p style='color:blue'>".FlashMessage::render()."</p>";
			}
			?>
			<input type="password" class="form-control" placeholder="Enter Your Current Password"  autofocus name="current_pass" id="current_password">
			<input type="password" class="form-control" placeholder="New Password" name="new_pass" id="new_pass">
			<input type="password" class="form-control" placeholder="Confirm New Password" name="cnf_new_pass" id="cnf_new_pass">
			<button class="btn btn-lg btn-primary btn-block" type="submit" name="reset_pass" value="reset_pass">Reset Password</button>
			</form>
		</div>
		</div>
	</div>

</div>
</div>

<script>
function loginFormvalidate(){
	var curr_pass = $("#current_password").val();
	var new_pass = $("#new_pass").val();
	var cnf_new_pass = $("#cnf_new_pass").val();
	var username_flag = true;
	var pwd_flag = true;
	var cnf_new_pwd_flag = true;
	
	if(curr_pass == ""){
		$("#current_password").addClass("validate-error");
		username_flag = false;
	}
	else{
		$("#current_password").removeClass("validate-error");
	}
	
	if(new_pass == ""){
		$("#new_pass").addClass("validate-error");
		pwd_flag = false;
	}
	else{
		$("#new_pass").removeClass("validate-error");
	}
	
	if(cnf_new_pass != new_pass){
		$("#cnf_new_pass").addClass("validate-error");
		cnf_new_pwd_flag = false;
	}
	else{
		$("#cnf_new_pass").removeClass("validate-error");
	}
	
	if(username_flag && pwd_flag && cnf_new_pwd_flag){
		return true;
	}
	else{
		return false;
	}
}
</script>

<?php include("../application/footer.php"); ?>
