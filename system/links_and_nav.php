<?php
/*
 * function Get_Links($display_page)
 *
 * ****Creating TABS
 *
 * ****************
 *
 * function Navigation($page, $menu_location = 'header')
 */


/*
 *   * *********************
 *
 * Getting tabs name for display_page
 *   * *********************************************
 *
 */

function Get_Links($display_page) {
	/* Check From table_type == header1 or header2 Start */
	// ShowTableTypeHeaderContent($display_page);
	/* Check For table_type == header1 or header2 End */
    $_SESSION['display'] = $display_page;
    global $tab;
    $con = connect();
    $rs = $con->query("SELECT * FROM  data_dictionary DD INNER JOIN navigation ON(navigation.target_display_page=DD.display_page) where DD.display_page = '$display_page' AND DD.table_type NOT REGEXP 'header|banner|slider|content|url|text|subheader|image|icon' AND DD.tab_num REGEXP '^[0-9]+$' AND DD.tab_num >'0' GROUP BY DD.dict_id order by DD.tab_num ");
    $i = 1;

    /* *********
     * *************
     * *******************Adding FFFR ICONS HERE
     * *********************************************
     * ******************
     * *********************************************
     */


    if ($_GET['edit'] == 'true')
        fffr_icons();


    /////////////////////////////
    //////////////////////////////////////////////
    ///////////////////////////////////////////////////////////

	if($rs->num_rows){
		echo "<ul class='center-tab' role='tablist' >";
		while ($row = $rs->fetch_assoc()) {
			if($row['loginRequired']== 'true' && !itemHasVisibility($row['dd_visibility'])){
				continue;
			}
			//pr($row);

			$tab_name = explode("/", $row['tab_name']);

			$row['tab_name'] = trim($tab_name[0]);
			$dd_css_class = $row['dd_css_class'];
      $css_style = $row['dd_css_code'];
			if ($i == 1 && !( isset($_SESSION['tab']) )) {
				$tab = $row['table_alias'];

				$_SESSION['tab'] = $tab;


				//echo "<li class='active'><a href=?display=$display_page&tab=$row[table_alias]&tabNum=$row[tab_num] class='tab-class' id='$list_style'>$row[tab_name]</a></li>";
				if($row['tab_name']){
					echo "<li class='active'><a href=?display=$display_page&tab=$row[table_alias]&tabNum=$row[tab_num] class='tab-class'>$row[tab_name]</a></li>";
				}
			} else if ($_SESSION['tab'] == $row['table_alias'] && $_GET['tabNum'] == $row['tab_num']) {

				//echo "<li class='active'><a href=?display=$display_page&tab=$row[table_alias]&tabNum=$row[tab_num] class='tab-class' id='$list_style'>$row[tab_name]</a></li>";
				if($row['tab_name']){
					echo "<li class='active'><a href=?display=$display_page&tab=$row[table_alias]&tabNum=$row[tab_num] class='tab-class'>$row[tab_name]</a></li>";
				}
			} else {

				//echo "<li><a href=?display=$display_page&tab=$row[table_alias]&tabNum=$row[tab_num]&search_id=$_GET[search_id] class='tab-class' id='$list_style'>$row[tab_name]</a></li>";
				if($row['tab_name']){
					echo "<li><a href=?display=$display_page&tab=$row[table_alias]&tabNum=$row[tab_num]&search_id=$_GET[search_id] class='tab-class'>$row[tab_name]</a></li>";
				}
			}
			$i++;
		}

		echo "</ul>";
	}

	/* Check From table_type == subheader1 or subheader2 Start */
	// ShowTableTypeSubHeaderContent($display_page);
	/* Check For table_type == subheader1 or subheader2 End */

	ShowTableTypeSlider($display_page);

	ShowTableTypeBanner($display_page);

	ShowTableTypeContent($display_page);

	ShowTableTypeImage($display_page);
}

function generateTabs($display_page,$row,$ulClass='vertical-tab '){
	if (!empty($row)) {
		if ($_GET['edit'] == 'true'){
			fffr_icons();
		}
		$con = connect();
		$tabQuery = $con->query("SELECT * FROM  data_dictionary where display_page = '$display_page' AND table_type NOT REGEXP 'header|banner|slider|content|url|text|subheader|image|icon' AND tab_num REGEXP '^[0-9]+$' AND tab_num >'0' order by tab_num");
		if($tabQuery->num_rows > 0) { ?>
			<ul class='<?php echo $ulClass.$sidebar; ?>' role='tablist'>
				<?php
				while ($row = $tabQuery->fetch_assoc()) {
					if(trim($row['tab_name'])){
						$dd_css_class = $row['dd_css_class'];
            $css_style = $row['dd_css_code'];

						$tab_id = "#".$display_page.$row['dict_id'];
						if(!loginNotRequired() && !itemHasVisibility($row['dd_visibility'])){
							continue;
						} ?>
						<li class="tab-class js_tab <?= $dd_css_class ?>" style="<?= $css_style?>"id="<?php echo $tab_id; ?>">
							<!--<a id="<?php //echo $list_style; ?>" href="javascript:void(0);">
								<?php //echo $row['tab_name']; ?>
							</a>-->
							<a href="javascript:void(0);">
								<?php echo $row['tab_name']; ?>
							</a>
						</li>
						<?php
					}
				}?>
			</ul>
			<script>
			$(document).ready(function(){
				$('.js_tab').click(function(){
					$('.js_tab').removeClass('active');
					$(this).addClass('active');
					$('html, body').animate({
						scrollTop: $($(this).attr('id')).offset().top - 160
					}, 500);
				});
				$('.js_tab').first().addClass('active');
			});
			</script>
	<?php
		}
	}
}

function Get_Tab_Links($display_page,$sidebar) {
	$con = connect();
	if($sidebar == 'left'){
		$rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' and (tab_num ='S-L')");
		$row = $rs->fetch_assoc();
		generateTabs($display_page,$row);
	} elseif($sidebar == 'right'){
		$rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' and (tab_num='S-R')");
		$row = $rs->fetch_assoc();
		generateTabs($display_page,$row);
	} else {
		$rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' and (tab_num='S-C')");
		$row = $rs->fetch_assoc();
		generateTabs($display_page,$row,$ulClass='center-tab center-tab-fixed');
	}
}


function headersAndSubHeaders($display_page){
	$con = connect();

	$header_query = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num>0 AND table_type LIKE 'header%' ORDER BY table_type ASC");
	$sub_header_query = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num>0 AND table_type LIKE 'subheader%'  ORDER BY table_type ASC");

	if ($header_row = $header_query->fetch_assoc()) {
		ShowTableTypeHeaderContent($display_page);
	}

	if ($sub_header_row = $sub_header_query->fetch_assoc()) {
    // pr($sub_header_row);

		ShowTableTypeSubHeaderContent($display_page);
	}
}


/////////// get_links() ends here
//////////////////////////////
///////////////////////
/*
 *
 * Navigations fucntions starts here
 */

function Navigation($page, $menu_location = 'header') {
    $con = connect();
    $rs = $con->query("SELECT * FROM navigation where display_page='$page' and item_number=0 and menu_location='$menu_location' ");
    $row = $rs->fetch_assoc();
    /*
     *
     * Checking whether user have access to current page or not
     */
    if ($row['loginRequired'] == 'true' && !isset($_SESSION['uid'])) {
        $_SESSION['callBackPage'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        FlashMessage::add('Login required to view the current page!');
        echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "login.php'>";
        exit();
    }
    ?>
    <!-- Navigation starts here -->
    <div class="navbar navbar-default navbar-fixed-top">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">
                    <?php echo TOGGLE_NAVIGATION ?>
                </span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
            </button>
            <?php
                /*
                 * Logo
                 */
                $logo_position = BRAND_LOGO_POSITION;
                $logo_link = BRAND_LOGO_LINK;
                $logo_image = BRAND_LOGO_IMAGE;
                $logo_text = BRAND_LOGO;

                $rs = $con->query("SELECT * FROM navigation where (display_page='$page' OR display_page='ALL' ) and menu_location LIKE 'LOGO%' order by item_number");
                $row = $rs->fetch_assoc();

                if ($row) {
                    $nav_menu_location = strtoupper($row['menu_location']);
                    $logo_image = BASE_IMAGES_URL . $row['item_target'];
                    $logo_text = $row['item_label'];
                    $logo_style = $row['item_style'];

                    if ($nav_menu_location == 'LOGO-CENTER') {
                        $logo_position = 'center';
                    }
                }
            ?>
            <?php if ($nav_menu_location != 'LOGO-RIGHT') { ?>
                <a class="navbar-brand logo <?php echo $logo_position ?>" href="<?php echo $logo_link ?>">
                    <?php
                        if ($logo_image != '') {
                            echo "<img src='$logo_image' alt='$logo_text' style='$logo_style'>";
                        }
                        echo $logo_text;
                    ?>
                </a>
            <?php } ?>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right <?= $item_style;?>">
                <?php
                if (isUserLoggedin()) {
					$navItems = getNavItems($page,$menu_location);
					$loginRequired = true;
                } else {
					$navItems = getNavItems($page,$menu_location,'false');
					$loginRequired = false;
					?>
					<!--
					<li>
						<a href="<?php echo BASE_URL_SYSTEM ?>login.php" class="top-btns btn-primary login"><i class="fa fa-sign-in"></i>
							<?php echo LOGIN_MENU ?>
						</a>
					</li>
					<li>
						<a href="<?php echo BASE_URL_SYSTEM ?>register.php" class="top-btns btn-primary"><i class="fa fa-edit"></i>
							<?php echo SIGNUP_MENU ?>
						</a>
					</li>
					--!>

				<?php
				}
				echo $menu = generateTopNavigation($navItems,$loginRequired);
				?>
            <?php ///////else if ends here                                                                                           ?>

            <?php if ($nav_menu_location == 'LOGO-RIGHT') { ?>
                <a class="navbar-brand logo right" href="<?php echo $logo_link ?>">
                    <?php
                        if ($logo_image != '') {
                            echo "<img src='$logo_image' alt='$logo_text' style='$logo_style'>";
                        }
                        echo $logo_text;
                    ?>
                </a>
            <?php } ?>

            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>


    <?php
}
////////main navigation function ends here///

function GetSideBarNavigation($display_page,$menu_location){
	if (isUserLoggedin()) {
		$navItems = getNavItems($display_page,$menu_location);
	} else {
		$navItems = getNavItems($display_page,$menu_location,'false');
	}
	$menu = "";
	if(!empty($navItems)){ ?>
		<!-- Menu -->
		<div class="side-menu <?php echo $menu_location; ?>">
			<nav class="navbar navbar-default" role="navigation">
				<!-- Main Menu -->
				<div class="side-menu-container">
					<ul class="nav navbar-nav <?php echo $menu_location; ?>">
						<?php echo $menu = generateSideBarNavigation($navItems,$menu); ?>
					</ul>
				</div><!-- /.navbar-collapse -->
			</nav>
		</div><?php
	}
}

function ShowTableTypeHeaderContent($display_page,$tabNum=''){
	$con = connect();
	if($tabNum){
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND table_type LIKE 'header%' ORDER BY table_type ASC");
	} else {
		if (empty($_GET['tabNum'])) {
			$rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page' and tab_num REGEXP '^[0-9]+$' AND table_type LIKE 'header%' AND tab_num >'0' order by tab_num");
			$row = $rs->fetch_assoc();
			$tabNum = $row['tab_num'];
		} else {
			$tabNum = $_GET['tabNum'];
		}
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page'  AND table_type LIKE 'header%'  ORDER BY table_type ASC");
	}
	if($tableTypeHeaderQuery->num_rows > 0){
    // pr($tableTypeHeaderQuery->num_rows);
    // pr($display_page);
		While($row = $tableTypeHeaderQuery->fetch_assoc()) {
			$userPrivilege = false;
			if(itemHasPrivilege($row['dd_privilege_level']) && itemHasVisibility($row['dd_visibility'])){
				$userPrivilege = true;
			}
			if(loginNotRequired()){
				$userPrivilege = true;
			}
			if ($userPrivilege === true) {
				$header = $row['description'];
				$dd_css_class = $row['dd_css_class'];
        $css_style = $row['dd_css_code'];
				$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']); ?>
				<h1 class="<?php echo $dd_css_class; ?>" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>;text-align:<?php echo $align; ?><?= $css_style?>">
					<?php echo $header; ?>
				</h1>
			<?php
			}
		}
	}
}

function ShowTableTypeSubHeaderContent($display_page,$tabNum=''){
	$con = connect();
	if($tabNum){
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND table_type LIKE 'subheader%' ORDER BY table_type ASC");
	} else {
		if (empty($_GET['tabNum'])) {

			$rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page' and tab_num REGEXP '^[0-9]+$' AND table_type LIKE 'subheader%' AND tab_num >'0' order by tab_num");
			$row = $rs->fetch_assoc();
			$tabNum = $row['tab_num'];
		} else {
			$tabNum = $_GET['tabNum'];
		}
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page'  AND table_type LIKE 'subheader%' ORDER BY table_type ASC");
	}
	if($tableTypeHeaderQuery->num_rows > 0){
		While($row = $tableTypeHeaderQuery->fetch_assoc()) {
			$userPrivilege = false;
			if(itemHasPrivilege($row['dd_privilege_level']) && itemHasVisibility($row['dd_visibility'])){
				$userPrivilege = true;
			}
			if(loginNotRequired()){
				$userPrivilege = true;
			}
			if ($userPrivilege === true) {
				$header = $row['description'];

				$dd_css_class = $row['dd_css_class'];
        $css_style = $row['dd_css_code'];
				$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']); ?>
				<h2 class="<?php echo $dd_css_class; ?>" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>;text-align:<?php echo $align; ?><?= $css_style?>>">
					<?php echo $header; ?>
				</h2>
			<?php
			}
		}
	}
}

function ShowTableTypeBanner($display_page,$tabNum=''){
	$con = connect();
	if($tabNum){
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND table_type = 'banner' ORDER BY table_type ASC");
	} else {
		if (empty($_GET['tabNum'])) {
			$rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page' and tab_num REGEXP '^[0-9]+$' AND tab_num >'0' order by tab_num");
			$row = $rs->fetch_assoc();
			$tabNum = $row['tab_num'];
		} else {
			$tabNum = $_GET['tabNum'];
		}
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND table_type = 'banner' ORDER BY table_type ASC");
	}
	if($tableTypeHeaderQuery->num_rows > 0){
		While($row = $tableTypeHeaderQuery->fetch_assoc()) {
			$userPrivilege = false;
			if(itemHasPrivilege($row['dd_privilege_level']) && itemHasVisibility($row['dd_visibility'])){
				$userPrivilege = true;
			}
			if(loginNotRequired()){
				$userPrivilege = true;
			}
			if ($userPrivilege === true) {
				$banner = getBannerImages($row['description']);

				$dd_css_class = $row['dd_css_class'];
        $css_style = $row['dd_css_code'];
				$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']);
				if(!empty($banner)) { ?>
					<div style="<?= $css_style ?>"class="<?php echo $divClass.' '.$dd_css_class; ?>">
						<div style="width:<?php echo $width; ?>;">
							<section class='section-sep'>
								<a href="<?php echo $url; ?>"><img style="width:100%;height:<?php echo $height; ?>;" src="<?php echo $banner; ?>"></a>
							</section>
						</div>
					</div>
					<?php
				}
			}
		}
	}
}

function ShowTableTypeContent($display_page,$tabNum=''){
	$con = connect();
	if($tabNum){
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND table_type = 'content' ORDER BY table_type ASC");
	} else {
		if (empty($_GET['tabNum'])) {
			$rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page' and tab_num REGEXP '^[0-9]+$' AND tab_num >'0' order by tab_num");
			$row = $rs->fetch_assoc();
			$tabNum = $row['tab_num'];
		} else {
			$tabNum = $_GET['tabNum'];
		}
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND table_type = 'content' ORDER BY table_type ASC");
	}
	if($tableTypeHeaderQuery->num_rows > 0){
		While($row = $tableTypeHeaderQuery->fetch_assoc()) {
			$userPrivilege = false;
			if(itemHasPrivilege($row['dd_privilege_level']) && itemHasVisibility($row['dd_visibility'])){
				$userPrivilege = true;
			}
			if(loginNotRequired()){
				$userPrivilege = true;
			}
			if ($userPrivilege === true) {
				$iframeUrl = getIframeUrl($row['description']);
				$dd_css_class = $row['dd_css_class'];
        $css_style = $row['dd_css_code'];
				$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']);
				if(!empty($iframeUrl)) { ?>
					<div class="<?php echo $divClass; ?><?= $dd_css_class?>" style="<?= $css_style?>">
						<iframe align="<?php echo $align; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" src="<?php echo $iframeUrl; ?>"></iframe>
					</div>
					<?php
				}
			}
		}
	}
}



function ShowTableTypeSlider($display_page,$tabNum=''){
	$con = connect();
	if($tabNum){
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND table_type='slider' ORDER BY table_type ASC");
	} else {
		if (empty($_GET['tabNum'])) {
			$rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page' and tab_num REGEXP '^[0-9]+$' AND tab_num >'0' order by tab_num");
			$row = $rs->fetch_assoc();
			$tabNum = $row['tab_num'];
		} else {
			$tabNum = $_GET['tabNum'];
		}
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND table_type='slider' ORDER BY table_type ASC");
	}
	if($tableTypeHeaderQuery->num_rows > 0){
		While($row = $tableTypeHeaderQuery->fetch_assoc()) {
			$userPrivilege = false;
			if(itemHasPrivilege($row['dd_privilege_level']) && itemHasVisibility($row['dd_visibility'])){
				$userPrivilege = true;
			}
			if(loginNotRequired()){
				$userPrivilege = true;
			}
			if ($userPrivilege === true) {
				$sliders = getSliderImages($row['description']);
				$dd_css_class = $row['dd_css_class'];
        $css_style = $row['dd_css_code'];
				//$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']);
				if(!empty($sliders)){ ?>
					<div class="<?php echo $divClass; ?><?=$dd_css_class ?>" style="<?=$css_style?>">
						<div  style="width:<?php echo $width; ?>;" class="slider">
							<div id="myCarousel" style="height:<?php echo $height; ?>;" class="carousel slide" data-ride="carousel">
								<!-- Indicators -->
								<ol class="carousel-indicators">
									<?php foreach($sliders as $key=>$slider){ ?>
										<li data-target="#myCarousel" data-slide-to="<?php echo $key; ?>" class="<?php echo ($key==0 ?'active':''); ?>"></li>
									<?php } ?>
								</ol>
								<div class="carousel-inner">
									<?php foreach($sliders as $key=>$slider){ ?>
										<div style="height:<?php echo $height; ?>;" class="item <?php echo ($key==0 ?'active':''); ?>">
											<img style="height:<?php echo $height; ?>;" src="<?php echo $slider; ?>" alt="" class="img-responsive">
											<!--<div class="container">
												<div class="carousel-caption slide2">
													<h1><?php //echo HOME_SLIDER_TITLE2 ?></h1>
													<p><?php //echo HOME_SLIDER_CONTENT2 ?></p>
													<p><a class="btn btn-lg btn-primary" href="#" role="button"><?php //echo HOME_SLIDER_BUTTON_TEXT2 ?></a></p>
												</div>
											</div>-->
										</div>
									<?php } ?>
								</div>
								<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left"></span></a> <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right"></span></a>
							</div>
							<!-- /.carousel -->
						</div>
					</div>
					<?php
				}
			}
		}
	}
}

function ShowTableTypeImage($display_page,$tabNum=''){
	$con = connect();
	if($tabNum){
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND (table_type='image') ORDER BY table_type ASC");
	} else {
		if (empty($_GET['tabNum'])) {
			$rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page' and tab_num REGEXP '^[0-9]+$' AND tab_num >'0' order by tab_num");
			$row = $rs->fetch_assoc();
			$tabNum = $row['tab_num'];
		} else {
			$tabNum = $_GET['tabNum'];
		}
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND (table_type='image') ORDER BY table_type ASC");
	}
	if($tableTypeHeaderQuery->num_rows > 0){
		While($row = $tableTypeHeaderQuery->fetch_assoc()) {
			$userPrivilege = false;
			if(itemHasPrivilege($row['dd_privilege_level']) && itemHasVisibility($row['dd_visibility'])){
				$userPrivilege = true;
			}
			if(loginNotRequired()){
				$userPrivilege = true;
			}
			if ($userPrivilege === true) {
				$images = getImages($row['description']);
				$url = getDDUrl($row['list_select']);
				$dd_css_class = $row['dd_css_class'];
        $css_style = $row['dd_css_code'];
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options'],true);
				if(!empty($images)){ ?>
				<div class="<?php echo $divClass; ?><?= $dd_css_class?>" style="<?= $css_style?>">
					<?php foreach($images as $key=>$image){ ?>
						<a href="<?php echo $url; ?>"><img src="<?php echo $image; ?>" style="margin:10px;height:<?php echo $height; ?>;width:<?php echo $width; ?>;"></a></li>
					<?php } ?>
				</div>
				<?php
				}
			}
		}
	}
}

function ShowTableTypeIcon($display_page,$tabNum=''){
	$con = connect();
	if($tabNum){
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND (table_type='icon') ORDER BY table_type ASC");
	} else {
		if (empty($_GET['tabNum'])) {
			$rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page' and tab_num REGEXP '^[0-9]+$' AND tab_num >'0' order by tab_num");
			$row = $rs->fetch_assoc();
			$tabNum = $row['tab_num'];
		} else {
			$tabNum = $_GET['tabNum'];
		}
		$tableTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num='$tabNum' AND (table_type='icon') ORDER BY table_type ASC");
	}
	if($tableTypeHeaderQuery->num_rows > 0){
		While($row = $tableTypeHeaderQuery->fetch_assoc()) {
			$userPrivilege = false;
			if(itemHasPrivilege($row['dd_privilege_level']) && itemHasVisibility($row['dd_visibility'])){
				$userPrivilege = true;
			}
			if(loginNotRequired()){
				$userPrivilege = true;
			}
			if ($userPrivilege === true) {
				$images = getImages($row['description']);
				$url = getDDUrl($row['list_select']);
				$listStyle = $row['dd_css_class'];
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options'],true);
				if(!empty($images)){ ?>
				<!--div class="<?php echo $divClass; ?>"-->
					<?php foreach($images as $key=>$image){ ?>
						<a href="<?php echo $url; ?>"><img src="<?php echo $image; ?>" style="margin:10px;height:<?php echo $height; ?>;width:<?php echo $width; ?>;"></a></li>
					<?php } ?>
				<!--/div-->
				<?php
				}
			}
		}
	}
}
?>
