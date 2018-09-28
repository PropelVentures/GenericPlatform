<?php 
include("application/header.php");
if(isset($_GET['pid']) && !empty($_GET['pid'])){
	$sql = "SELECT * FROM projects as p LEFT JOIN project_categories ON p.cid = project_categories.project_category_id WHERE p.pid=".$_GET['pid'];
	$result = mysql_query($sql);
	$project = mysql_fetch_array($result);
}
?>

<div class="jumbotron search-form" >
	<div class="container">
		<div class="row">
			<div class="col-6 height2"></div>
			<div class="row top-add-tab">
				<div class="col-md-6 col-lg-8 col-sm-6 add-tab">
					<div class="row">
						<div class="row top-add-tab">
							<!--<div class="col-md-10 col-lg-12 col-sm-10 add-tab">-->
							<?php  
							if($_SESSION['messages']!=""){				
								echo "<div class='alert alert-info'>";				
								echo " <a href='#' class='close' data-dismiss='alert'>&times;</a>";				
								echo "<p>".FlashMessage::render()."</p>";				
								echo "</div>";				
							}				
							?>
							<!--	</div>-->
							<div class="col-md-10 col-lg-12 col-sm-10 add-tab">
								<div class="left-content" style="float:left; width:25%;"> <img class="img-thumbnail" src="<?=BASE_URL?>project_uploads/<?=($project['projectImage']!="")?("thumbs/".$project['projectImage']):'defaultImageIcon.png'?>"> </div>
								<div class="right-content" style="float:left; width:75%; padding-left:5px;">
									<h2>
										<?=isset($project['pname'])?$project['pname']:'Project Name'?>
									</h2>
									<span>(
									<?=isset($project['project_categeory_name'])?$project['project_categeory_name']:'No Categories for this Project.'?>
									)</span> </div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="row top-add-tab">
							<div class="col-md-10 col-lg-12 col-sm-10 add-tab">
								<ul class="nav nav-tabs">
									<li class="active"><a href="#hello" data-toggle="tab"> <i class="fa fa-file-text-o"></i><br />
										<span>Description</span> </a></li>
									<li><a href="#empty" data-toggle="tab"> <i class="fa fa-tags"></i><br />
										<span>tag</span> </a></li>
									<li><a href="#templates" data-toggle="tab"> <i class="fa fa-file-text-o"></i><br />
										<span>Templates</span> </a></li>
									<li><a href="#bluths" data-toggle="tab"> <i class="fa fa-file-text-o"></i><br />
										<span>Bluths</span> </a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="hello">
										<div class="col-lg-12 right-content user-profile set-img" >
											<?=isset($project['description'])?stripslashes($project['description']):'Project Does Not Have Any Description'?>
											<br/>
										</div>
									</div>
									<!-- @end #hello -->
									<div class="tab-pane" id="empty">
										<div class="col-lg-12 right-content user-profile set-img">
											<div>TAGS</div>
											<div class="displayTags"> </div>
										</div>
									</div>
									<!-- @end #empty -->
									<div class="tab-pane" id="templates">
										<div class="col-lg-12 right-content user-profile set-img">
											<h3>Some Cool Templates</h3>
											<div class="span6"></div>
											<div class="span6"></div>
										</div>
									</div>
									<!-- @end #templates -->
									<div class="tab-pane" id="bluths">
										<div class="col-lg-12 right-content user-profile set-img">
											<h3>It's <a href="http://bluthipsum.com/">Bluth Ipsum</a>.</h3>
											<p>She keeps saying that God is going to show me a sign. The? something of my ways. Wisdom? It's probably wisdom.</p>
											<p>Annhog's coming? I'd go to Craft Service, get some raw veggies, bacon, Cup-A-Soup... baby, I got a stew goin'. Did you enjoy your lunch, mom? You drank it fast enough.</p>
											<p>No, I was ashamed to be SEEN with you. I like being WITH you. I've used one adjective to describe myself. What is it? Professional.</p>
											<hr>
											<p>Can't a guy call his mother pretty without it seeming strange? Amen. And how about that little piece of tail on her? Cute! I've been in the film business for a while but I just cant seem to get one in the can.</p>
											<p>Aren't you the sweetest thing, spending time with what's left of your uncle. A group of British builders operating outside the O.C. contacted me for a partnership to build homes overseas. I did not know they meant Iraq.</p>
											<p>Steve Holt? The moron jock? We need a name. Maybe 'Operation Hot Mother'.</p>
										</div>
									</div>
									<!-- @end #bluths -->
								</div>
								<!-- @end .tab-content -->
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6 col-lg-4 col-sm-6 p-owner">
					<div class="project-detail new-trans" >
						<?php
						$tsql = "SELECT t.*, u.uid, u.uname FROM transactions as t LEFT JOIN users as u ON t.user_id=u.uid LEFT JOIN transaction_type as tt ON t.transactionType = tt.transaction_type_name WHERE t.transaction_id=".$_GET['tid'];
								
						$tresult = mysql_query($tsql);			
						$transactions = mysql_fetch_array($tresult);			
						?>
						<div class="bootom-cont">
							<h2>Transaction Details</h2>
							<ul class="trans-wrap">
								<li><span>Transaction Id :</span> <span>
									<?=$transactions['transaction_id']?>
									</span>
									<div class="clear"></div>
								</li>
								<li><span>User :</span> <span>
									<?=$transactions['uname']?>
									</span>
									<div class="clear"></div>
								</li>
								<li><span>Amount :</span> <span>
									<?=$transactions['amount']?>
									</span>
									<div class="clear"></div>
								</li>
								<li><span>Date & Time :</span> <span>
									<?=$transactions['transaction_datetime']?>
									</span>
									<div class="clear"></div>
								</li>
								<li><span>Status :</span> <span>
									<?=$transactions['status']?>
									</span>
									<div class="clear"></div>
								</li>
								<div class="clear"></div>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("application/footer.php"); ?>
