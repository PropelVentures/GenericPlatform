<?php 
	include("application/header.php"); 
	if(!isUserLoggedin()){
		echo '<META http-equiv="refresh" content="0;URL=index.php">';
		exit();
	}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<div id="projectWrapper">
  <div class="contentWrapper">
    <div class="jumbotron search-form">
      <div class="container">
        <div class="row row-offcanvas row-offcanvas-right">
            <div class="col-6 height2"></div>
			<?php 
			if(isset($_SESSION["messages"])){
				echo "<div class='alert alert-info'>";
				echo " <a href='#' class='close' data-dismiss='alert'>&times;</a>";
				echo "<p>".FlashMessage::render()."</p>";
				echo "</div>";
			}
			?>
          <div class="col-6 col-sm-12 col-lg-12 right-content user-profile" id="searchProjects">
            <!--<div class="userDetailsWrapper">-->
            
            <!-- PUT START HERE -->
            
          	 <div id="wrapper-my-proj">
              <div class="">
                <div class="container">
                  <!--	<div class="search1">Search Project</div> -->
                  <br/>
                  <?php
					if($_SESSION['messages']!=""){
						echo "<div class='message'>".FlashMessage::render()."</div>";
					}
					?>
                  <div class="row" id="pdwrapper">
				  <?php
					include("grid_widget/grid.php");
					?>
                  </div>
                </div>
              </div>
            </div>
          
          </div>
        </div>
      </div>
    </div>
    <div id="wrapper-all-tabs"> </div>
  </div>
</div>
<?php include("application/footer.php"); ?>