<?php
include("../application/header.php");

$display_page = $_GET['display'];

if( isset( $_GET['tab'] )  || !empty( $_GET['tab']) )
{
    $_SESSION['tab']= $_GET['tab'];
    
}
else{
    $_SESSION['tab'] = '';

    unset($_SESSION['tab']);
    
}



?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="https://ucarecdn.com/widget/1.3.1/uploadcare/uploadcare-1.3.1.min.js"></script>
<!-- CAPSTONE: Override Uploadcare text -->
<script src="<?php echo BASE_URL ?>ckeditor/ckeditor.js"></script>


<?php

///// copy these two files for displaying navigation/////
$navi_display='home';

Navigation($navi_display);
//////////////

?>


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
                                <div class='left-content'> <span> <img id="user_thumb" src="../users_uploads/defaultImageIcon.png" border="0" width="200" height="200" class="img-thumbnail img-responsive" style="width:100%;" /> </span>
                                    <div>
                                        <input type="hidden" role="uploadcare-uploader" name="image" id="file2" data-locale="en" data-tabs="file url facebook gdrive instagram" data-images-only="false" data-path-value="false" data-preview-step="false" data-multiple="false"  value="" data-crop="650x430 minimum"/>
                                        <br />
                                        <input type="hidden" name="uploadcare_image_url" id="uploadcare_image_url" value="" />
                                        <input type="hidden" name="uploadcare_image_name" id="uploadcare_image_name" value="" />
                                        <input type="hidden" name="profile_id" id="profile_id" value="16" />
                                        <div style="margin:5% 0%">
                                            <input type="submit" class="submit btn btn-primary pull-left" name="profile_image_submit" id="login" value="SAVE">
                                            <input type="button" onclick="location.href = 'http://generic.cjcornell.com/profile.php'" class="submit btn btn-primary  pull-right" name="login" value="CANCEL"/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class='col-6 col-sm-6 col-lg-9 right-content user-profile'>
                            <ul class="nav nav-tabs" role="tablist" >
                         
                                <?php
                                
                                echo Get_Links($display_page);
                                
                                global $tab;
                                
                                
                                ?>

                                
                                
                            </ul>
                            
                            <form action='?action=update' method='post' id='user_profile_form' enctype='multipart/form-data'>
                                <br>
                                <?php 
                                
                                if( isset($_SESSION['tab'] ))
                                {
                                Get_Data_FieldDictionary_Record($_SESSION['tab'],$display_page);
                                }else{
                                    
                                    Get_Data_FieldDictionary_Record($tab,$display_page);
                                }
                                ?>
                                
                                
                                 
                                 <div style="clear:both"></div>
                            </form>
                        
                            
                        <script src="http://generic.cjcornell.com/ckeditor/ckeditor.js"></script>
                        <script>
                                                //CKEDITOR.replace('description'); 
                                                CKEDITOR.replace('description', {
                                                    toolbarGroups: [
                                                        {name: 'document', groups: ['mode']}, // Line break - next group will be placed in new line.
                                                        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                                                        {name: 'styles', groups: ['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor']},
                                                        {name: 'insert', groups: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'uploadcare', 'youtube']}
                                                    ]},
                                                {
                                                    allowedContent: true
                                                });
                        </script>            <div style="clear:both"></div>
                    </div>
                </div>
            </div>
   
    </div>

  </div>
    
  </div>
    



<?php include("../application/footer.php"); ?>