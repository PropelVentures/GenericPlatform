<?php 
include("application/header.php");
//include_once(INCLUDE_APP_DIR."functions.php");
if(!isUserLoggedin()){
	echo '<META http-equiv="refresh" content="0;URL=index.php">';
	exit();
}

if(isset($_POST["profile_update"]) && $_POST["profile_update"] != ""){
	$uid = mysql_real_escape_string($_POST["uid"]);
	$uname = mysql_real_escape_string($_POST["uname"]);
	$country = mysql_real_escape_string($_POST["country"]);
	$email = mysql_real_escape_string($_POST["email"]);
	$aboutme = mysql_real_escape_string($_POST["aboutme"]);
	$interests = mysql_real_escape_string($_POST["interests"]);
	$skills = mysql_real_escape_string($_POST["skills"]);
	$company = mysql_real_escape_string($_POST["company"]);
	$city = mysql_real_escape_string($_POST["city"]);
	$zip = mysql_real_escape_string($_POST["zip"]);
	$state = mysql_real_escape_string($_POST["state"]);
	$description = mysql_real_escape_string($_POST["description"]);
	$twitterAccount = mysql_real_escape_string($_POST["twitterAccnount"]);
	$gplusAccount = mysql_real_escape_string($_POST["gplusAccount"]);
	$fbAccount = mysql_real_escape_string($_POST["fbAccount"]);
	
	$query = "UPDATE users SET uname='".$uname."', email = '".$email."', aboutme = '".$aboutme."', interests = '".$interests."', skills = '".$skills."', country = '".$country."', company='".$company."', city='".$city."', zip='".$zip."', state='".$state."', description='".$description."', twitter_account = '".$twitterAccount."', googleplus_account = '".$gplusAccount."', facebook_account = '".$fbAccount."' where uid = $uid";
	$result = mysql_query($query);
	if($result){
		FlashMessage::add(PROFILE_UPDATE_SUCCESS);
		echo '<META http-equiv="refresh" content="0;URL=profile.php">';
		exit();
	}
	else{
		FlashMessage::add(PROFILE_UPDATE_NOT_SUCCESS);
		echo '<META http-equiv="refresh" content="0;URL=profile.php">';
		exit();
	}
}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="https://ucarecdn.com/widget/1.3.1/uploadcare/uploadcare-1.3.1.min.js"></script>
<!-- CAPSTONE: Override Uploadcare text -->
<script src="<?=BASE_JS_URL?>profile.js"></script>
<script>
$( "#myprojects_tab" ).mouseover(function() {	
	  $( this).addClass("open");	 
});
$( "#myprojects_tab" ).mouseout(function() {	
	  $( this).removeClass("open");	 
});
</script>

<div class="content">
  <div id="profile">
    <?php
		$query = "SELECT * FROM  users where uid = ".$_SESSION["uid"]." and isActive = 1";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$profCompletion = profileCompletion($row);
		$getStates = "SELECT * FROM  states";
		$states = mysql_query($getStates);
		
		echo "<div class='title'>";
		if(isset($profCompletion)){
			echo "<br/>";
		}
		echo "</div>";
		echo "<div class='contentWrapper'>";
	?>
    <div class="jumbotron search-form profile-complete">
      <div class="container">
        <div class="row">
          <div class="col-6 col-sm-6 col-lg-3 height2">
            <?php 
			if(isset($_SESSION["messages"])){
		  	# start progressbar code by me			
			if(isset($profCompletion)){													
			echo "<br/>";		 
			echo "<div class='progress'>
				  <div class='progress-bar progress-bar-striped active'  role='progressbar' aria-valuenow='45' 
				  aria-valuemin='0' aria-valuemax='100' style='width:".floor($profCompletion)."% '>				
				   </div> <span class='sr-only'>100% Complete</span>					  				
					</div>";
			 echo "<div class='prof_com' >".PROFILE_COMPLETE_LABEL.":".floor($profCompletion)." %</div>";
			}
			?>
          </div>
          <div class="col-6 col-sm-6 col-lg-9">
            <?php             
			# end progressbar code by me
			echo "<div class='alert alert-info'>";
			echo " <a href='#' class='close' data-dismiss='alert'>&times;</a>";
			echo "<p>".FlashMessage::render()."</p>";
			echo "</div>";
			}
			?>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row" >
          <div class="col-6 col-sm-6 col-lg-3">
            <form action="form-actions.php" method="post">
              <div class='left-content'> <span> <img id="user_thumb" src="<?=(($row['image']!="") && isset($row['image']))?"users_uploads/".$row['image']:"users_uploads/defaultImageIcon.png"?>" border="0" width="200" height="200" class="img-thumbnail img-responsive" style="width:100%;" /> </span>
                <div>
                  <?php 
					if(isset($row['image']) && $row['image'] != ""){
						echo "<a href='form-actions.php?action=remove_profile_image' class='btn btn-primary update-btn'>".REMOVE_IMAGE_BUTTON."</a>";
					}
					else{
				  ?>
                  <input type="hidden" role="uploadcare-uploader" name="image" id="file2" data-locale="en" data-tabs="file url facebook gdrive instagram" data-images-only="false" data-path-value="false" data-preview-step="false" data-multiple="false"  value="" data-crop="650x430 minimum"/>
                  <br />
                  <input type="hidden" name="uploadcare_image_url" id="uploadcare_image_url" value="" />
                  <input type="hidden" name="uploadcare_image_name" id="uploadcare_image_name" value="" />
                  <input type="hidden" name="profile_id" id="profile_id" value="<?=$row["uid"]?>" />
                  <div style="margin:5% 0%">
                    <input type="submit" class="submit btn btn-primary pull-left" name="profile_image_submit" id="login" value="<?=USER_IMAGE_SAVE_BUTTON?>">
                    <input type="button" onclick="location.href='<?=BASE_URL?>profile.php'" class="submit btn btn-primary  pull-right" name="login" value="<?=USER_IMAGE_CANCEL_BUTTON?>"/>
                  </div>
                  <?php 
					}
				   ?>
                </div>
              </div>
            </form>
          </div>
          <div class='col-6 col-sm-6 col-lg-9 right-content user-profile'>
            <ul class="nav nav-tabs" role="tablist" <?=($_REQUEST['tab'] != "account")?"style='float:left'":""?>>
              <li <?=(!isset($_REQUEST['tab']) || $_REQUEST['tab'] == "account" )?'class="active"':''?>><a href="profile.php?tab=account"><?=MY_ACCOUNT?></a></li>
              <li <?=($_REQUEST['tab'] == "transactions" )?'class="active"':''?>><a href="profile.php?tab=transactions"><?=MY_TRANSACTIONS?></a></li>
              <li <?=($_REQUEST['tab'] == "otherTransactions" )?'class="active"':''?>><a href="profile.php?tab=otherTransactions"><?=OTHER_TRANSACTIONS?></a></li>
			 
			  <?php if(in_array("user_favorities", $GLOBALS['user_cross_ref_tables'])){ ?>
			  <li <?=($_REQUEST['tab'] == "myFavorites" )?'class="active"':''?>><a href="profile.php?tab=myFavorites"><?=MY_FAVORITES?></a></li>
			  <?php } 
			   if(in_array("user_follow", $GLOBALS['user_cross_ref_tables'])){ ?>
			  <li <?=($_REQUEST['tab'] == "follow" )?'class="active"':''?>><a href="profile.php?tab=follow"><?=USER_FOLLOW?></a></li>
			  <?php } 
			   if(in_array("user_friends", $GLOBALS['user_cross_ref_tables'])){ ?>
			  <li <?=($_REQUEST['tab'] == "friends" )?'class="active"':''?>><a href="profile.php?tab=friends"><?=USER_FRIENDS?></a></li>
			  <?php } 
			   if(in_array("user_liked", $GLOBALS['user_cross_ref_tables'])){ ?>
			  <li <?=($_REQUEST['tab'] == "likes" )?'class="active"':''?>><a href="profile.php?tab=likes"><?=USER_LIKES?></a></li>
			  <?php } ?>
			  
            </ul>
            <?php 
			
				include_once("actions/RelationshipManagement.php");
				$crossRef = new RelationshipManagement();
				if(!isset($_REQUEST['tab']) || $_REQUEST['tab'] == "account" ){
					include_once("tabs/profile.php");
				}
				
				if($_REQUEST['tab'] == "transactions" ){
					include_once("tabs/my_transactions.php");
				}
				
				if($_REQUEST['tab'] == "otherTransactions" ){
					include_once("tabs/other_transactions.php");
				}
				if($_REQUEST['tab'] == "myFavorites" ){						
					include("userChildPages/userChildPages.php");	
				}
				
				if($_REQUEST['tab'] == "follow" ){
					include("userChildPages/userChildPages.php");	
				}
				
				if($_REQUEST['tab'] == "friends" ){
					include("userChildPages/userChildPages.php");	
				}
				if($_REQUEST['tab'] == "liked" ){
					include("userChildPages/userChildPages.php");	
				}
			?>
            <div style="clear:both"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<?php include("application/footer.php"); ?>