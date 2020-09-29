<?php
/*
function getNavItems($page,$menu_location,$overRide= false){

function navHasVisibility(){
function itemHasEnable($enable){
function getSeperator($label=''){	
function getNavTarget($row){
function getNavItemIcon($item_icon,$class,$style){
function getDDUrl($list_select){


function GetSideBarNavigation($page_name,$menu_location){
function getSideBarNavItems($page,$menu_location,$overRide= false){	
function generateSideBarNavigation($navItems,$menu){
	
function generateTopNavigation($navItems,$loginRequired){
	
MAIN NAVIGATION:	
function Navigation($page, $menu_location = 'header1') 
	
	
*/


/**
 * Get navigation items for a specific page and location
 * @param string $page Page name to get the nav items for
 * @param string $menu_location Location of the navigation to get the items for
 * @param bool $overRide Flag to determine whether to get the nav items for the specific page
 * 
 * @author ph
 * 
 * @return array List of navigation items
 */
function getNavItems(string $page, string $menu_location, bool $overRide = false): array {
	
	$con = connect();

	// Determine based on authentication
	$notThis = isUserLoggedin() ? '2' : '1';
	
	if ($overRide) {
		$rs = $con->query("SELECT * FROM navigation where page_name='$page' and menu_location='$menu_location' AND item_number > 0 AND loginRequired != $notThis ORDER BY item_number ASC");
	} else {
		$rs = $con->query("SELECT * FROM navigation where  page_name='ALL'  and menu_location='$menu_location' AND nav_id > 0 AND loginRequired != $notThis ORDER BY item_number ASC");
	}

	$navItems = array();
	$arr = array();
	$i = 0;
	while ($row = $rs->fetch_assoc()) {
		if ( strpos($row['item_number'], ".") && !strpos($row['item_number'],".0") ) {
			$navItems[floor($row['item_number'])]['children'][] = $row;
		} else {
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		}
	}
	return $navItems;
}




/**
 * Detect whether navigation can be displayed based on current page and user privilege
 * 
 * @author ph
 * 
 * @return bool true if navigation can be displayed, false otherwise
 */
function navHasVisibility() {
	$con = connect();
	$page_name = $_GET['page_name'];
	$nav = $con->query("SELECT * FROM navigation WHERE target_page_name='$page_name' LIMIT 1") or die($con->error);
	$navigation = $nav->fetch_assoc();
	if(empty($navigation) || $navigation['loginRequired'] == '2'){
		return true;
	}
	return itemHasVisibility($navigation['item_visibility']);
}

/**
 * Check whether user has requried privilege
 * 
 * @param int $enable Required privilege
 * 
 * @author ph
 * 
 * @return boolean
 */
function itemHasEnable($enable){
	if(!defined("USER_PRIVILEGE")){
		define("USER_PRIVILEGE",'NO');
	}
	if( (USER_PRIVILEGE == 'YES' && $_SESSION['user_privilege'] >= $enable) || (USER_PRIVILEGE == 'NO' && $enable > 0 ) ){
		return true;
	}
	return false;
}

// *****************************************************************************
function getSeperator($label=''){
	$hash_start=strpos($label,'#');
	$hash_end=strpos($label,'#',$hash_start+1);
	$saperator_name = "";
	if(!empty($hash_start) && !empty($hash_end)){
		$saperator_name=substr($label,$hash_start+1,$hash_end-$hash_start-1);
	}
	switch($saperator_name){
		case "line":
			$label=str_replace("#$saperator_name#","<div class='saperator_line'></div>",$label);
			break;
		case "LINE":
			$label=str_replace("#$saperator_name#","<div class='saperator_line'></div>",$label);
			break;
		case "break":
			$label=str_replace("#$saperator_name#","<br/>",$label);
			break;
		case "BREAK":
			$label=str_replace("#$saperator_name#","<br/>",$label);
			break;
		case "space":
			$label=str_replace("#$saperator_name#","&nbsp&nbsp",$label);
			break;
		case "SPACE":
			$label=str_replace("#$saperator_name#","&nbsp&nbsp",$label);
			break;
	}
	return $label;

	/* if(!empty($hash_start) && !empty($hash_end)){
		$saperator_name=substr($label,$hash_start+1,$hash_end-$hash_start-1);
	}
	switch(strtolower($saperator_name)){
		case "line":
			$html_saperator="<div class='saperator_li'></div>";
			$label=str_replace("#line#","",strtolower($label));
			break;
		case "break":
			$html_saperator="<br/>";
			$label=str_replace("#break#","",strtolower($label));
			break;
		default:
			$html_saperator = "";
	}
	return [
		"label"=>$label,
		"saperator"=>$html_saperator
	]; */
}

// *****************************************************************************
function getNavTarget($row){
	$target_blank = "";
	if($row['loginRequired'] == '1' && !itemHasPrivilege($row['item_privilege'])){
		$target = "javascript:void(0);";
		$enable_class = "disabled ";
	}else if($row['loginRequired'] == '1' && !itemHasEnable($row['enabled']) ){
		$target = "javascript:void(0);";
		$enable_class = "disabled ";
	} else {
		$enable_class = "enabled ";
		$item_target = trim($row['item_target']);
		if ($item_target == '') {
			$item_target = 'main-loop.php';
		}
		// Remove all illegal characters from a url
		$item_target = filter_var($item_target, FILTER_SANITIZE_URL);
		// If Url is valid then et target as defined in DB
		if (filter_var($item_target, FILTER_VALIDATE_URL)) {
			$target = $item_target;
			$target_blank ="target='_blank'";
		} elseif($item_target == "#" || strpos($row['item_number'],".0")) {
			$target = "javascript:void(0);";
		} else {
			$target = BASE_URL_SYSTEM . $item_target . "?page_name=" . $row['target_page_name'] . "
			&style=" . $row['nav_css_class'];

		}
	}


	return [
		'target' => $target,
		'target_blank' => $target_blank,
		'enable_class'=>$enable_class
	];
}

/**
 * Generate img tag for item icon
 * 
 * @param string $item_icon Filename ofthe icon or specific tag (e.g.: #CURRENT-USER-PROFILE-IMAGE) to identify dynamic image
 * @param string $class Css class to attach to the img element
 * @param string $style Inline style to attach to the img element
 * 
 * @author ph
 * 
 * @return string Generated img tag or empty string
 */
function getNavItemIcon($item_icon, $class, $style) {
	if (empty($item_icon)) {
		return "";
	} elseif (strtoupper($item_icon) == '#CURRENT-USER-PROFILE-IMAGE') {
    	return  "<img class='$class' style='$style' src='".USER_UPLOADS.$_SESSION['current-user-profile-image']."'>  ";
  	}
	if (file_exists($GLOBALS['APP_DIR']."system/system_images/".$item_icon)) {
		return "<img class='$class' style='$style' src='".BASE_IMAGES_URL.$item_icon."'>  ";
	}
	return "";
}

// *****************************************************************************
function getDDUrl($list_select){
	$list_select = trim($list_select);
	if (empty($list_select)) {
		return "";
	}
	 // Remove all illegal characters from a url
	$list_select = filter_var($list_select, FILTER_SANITIZE_URL);
	 // If Url is valid then et target as defined in DB
	 
//	 echo "<br><br> list_select=";echo $list_select; echo "<br><br><br><br>";exit();
	 
	if (filter_var($list_select, FILTER_VALIDATE_URL)) {
		return $list_select;
	} else {
		$con = connect();
		$ddQuery = $con->query("SELECT * FROM data_dictionary where dict_id='$list_select'");
		if($ddQuery->num_rows >0 ){
			$ddRecord = $ddQuery->fetch_assoc();
			$nav = $this->con->query("SELECT * FROM navigation where target_page_name='".$ddRecord['page_name']."'");
			$layout = "";
			if($nav->num_rows > 0){
				$navRecord = $nav->fetch_assoc();
				$itemStyle = $navRecord['nav_css_class'];

}
			return BASE_URL_SYSTEM ."main-loop.php?page_name=" . $ddRecord['page_name'] . "&layout=$layout&style=$itemStyle";
		}
		return "";
	}
}


// ******************************************************************************************
function GetSideBarNavigation($page_name,$menu_location){

	$navItems = getSideBarNavItems($page_name,$menu_location);

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


// *****************************************************************************
function getSideBarNavItems($page,$menu_location,$overRide= false){
	$con = connect();

	if (isUserLoggedin()) {
		$loginRequired = 'true';
		$notThis = '2';
	} else {
		$loginRequired=='false';
		$notThis = '1';
	}
	if ($overRide) {
		$rs = $con->query("SELECT * FROM navigation where page_name='$page' and menu_location='$menu_location' AND item_number>0 AND loginRequired!=$notThis ORDER BY item_number ASC");
	} else {
		$rs = $con->query("SELECT * FROM navigation where  (page_name='$page' OR page_name='ALL' )  and menu_location='$menu_location' AND nav_id>0 AND loginRequired!=$notThis ORDER BY item_number ASC");
	}

	$navItems = array();
	$arr = array();
	$i = 0;
	while ($row = $rs->fetch_assoc()) {
		if (strpos($row['item_number'],".0")) {
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		} elseif (strpos($row['item_number'],".")) {
			$navItems[floor($row['item_number'])]['children'][] = $row;
		} else {
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		}
	}
	return $navItems;
}


/* TO DO//
 * Generate Top Nav Items
 * For all menu location & loginrequired(true or false)
 */
 



/* 
 * Get Nav Items according to Parent & Children
 * For all menu location & loginrequired(true or false)
 */
 
// *****************************************************************************
/* Generate SideBar Nav Items
 * For all menu location & loginrequired(true or false)
 */
function generateSideBarNavigation($navItems,$menu){
	foreach($navItems as $parent){
		if(strtoupper($parent['loginRequired'])== '1' && !itemHasVisibility($parent['item_visibility']) || !isset($parent['nav_id'])){
			continue;
		}
		$label = dislayUserNameSelector($parent['item_label']);
		$title = $parent['item_help'];
		$item_style = $parent['nav_css_class'];
    	$nav_css_code = $parent['nav_css_code'];
		$item_icon = getNavItemIcon($parent['item_icon'],$item_style,$nav_css_code);
		$navTarget = getNavTarget($parent);
		$target = $navTarget['target'];
		$enable_class=$navTarget['enable_class'];
		$target_blank = $navTarget['target_blank'];
		if(!empty($parent['children'])){
			switch(strtolower($label)){
				case "#line#":
				$menu.=" <li>
							<div class='saperator_line'></div>
							<span class='caret'></span>
						</li>";
				break;
				case "#break#":
				$menu.=" <li >
							<br/>
							<span class='caret'></span>
						</li>";
				break;
				case "#space#":
				$menu.="<li>
							<div class='margin_bottom_list'></div>
							<span class='caret'></span>
						</li>";
				break;
				default:
				$menu.="<li class='$enable_class dropdown nav_item $item_style' style='$nav_css_code'>
						<a href='#nav_".$parent['nav_id']."' class='dropdown-toggle' data-toggle='collapse' title='$title'>
							".$item_icon.getSeperator($label)."
							<span class='caret'></span>
						</a>";
				break;
			}
			$menu .= "<div id='nav_".$parent['nav_id']."' class='panel-collapse collapse'>
							<div class='panel-body'>
								<ul class='nav navbar-nav'>";

			foreach($parent['children'] as $children){
				if($children['loginRequired']== '1' && !itemHasVisibility($children['item_visibility'])){
					continue;
				}
				$label = dislayUserNameSelector($children['item_label']);
				$title = $children['item_help'];
				$item_style = $children['nav_css_class'];
        		$nav_css_code = $children['nav_css_code'];
				$item_icon = getNavItemIcon($children['item_icon'],$item_style,$nav_css_code);
				$navTarget = getNavTarget($children);
				$target = $navTarget['target'];
				$enable_class=$navTarget['enable_class'];
				$target_blank = $navTarget['target_blank'];
				#$label=$label.'#line#';
				switch(strtolower($label)){
					case "#line#":
					$menu.=" <li >
								<div class='saperator_line'></div>
							</li>";
					break;
					case "#break#":
					$menu.=" <li >
								<br/>
							</li>";
					break;
					case "#space#":
					$menu.=" <li >
								<div class='margin_bottom_list'></div>
							</li>";
					break;
					default:
					$menu.="<li class='$enable_class $item_style' style='$nav_css_code'>
								<a $target_blank href='$target' title='$title'>".
									$item_icon.
									getSeperator($label)."
								</a>
							</li>";
					break;
				}
			}
			$menu.= "</ul></div></div>";
		} else {
			switch(strtolower($label)){
				case "#line#":
				$menu.=" <li >
							<div class='saperator_line'></div>
						</li>";
				break;
				case "#break#":
				$menu.=" <li >
							<br/>
						</li>";
				break;
				case "#space#":
				$menu.=" <li >
							<div class='margin_bottom_list'></div>
						</li>";
				break;
				default:
				$menu.="<li class='$enable_class $item_style' style='$nav_css_code'>
							<a $target_blank href='$target' title='$title'>
								".$item_icon.getSeperator($label)."
							</a>
						</li>";
				break;
			}
		}
	}
	return $menu;
}


// ******************************************************************************************
function generateTopNavigation($navItems,$loginRequired){
	$isUserLoggedIn = true;
	if (isset($_SESSION['user_privilege'])) {
		$currentUserPrivilege = $_SESSION['user_privilege'];
	} else {
		$isUserLoggedIn = false;
		$currentUserPrivilege = 0;
	}
	$menu = '';

    echo '<style>
        .disabled_nav {
    pointer-events: none;
    cursor: default;
    opacity: 0.6;
    }
    </style>';

	if (!empty($navItems)) {
		foreach ($navItems as $parent) {
			if ($loginRequired && (!itemHasVisibility($parent['item_visibility']) || !isset($parent['nav_id'])) ) {
				continue;
			}
      		$label = dislayUserNameSelector($parent['item_label']);
			$title = $parent['item_help'];
			$item_style = $parent['nav_css_class'];
      		$nav_css_code = $parent['nav_css_code'];
			$item_icon = getNavItemIcon($parent['item_icon'],$item_style,$nav_css_code);
			$navTarget = getNavTarget($parent);
			$target = $navTarget['target'];
			$enable_class=$navTarget['enable_class'];
			$target_blank = $navTarget['target_blank'];
			$disable = '';
			if ($currentUserPrivilege < $parent['item_privilege'] ) {
				$disable ='disabled_nav';
			}
			if (!empty($parent['children'])) {

				switch (strtolower($label)) {
					case "#line#":
					$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
								<div class='saperator_line'></div>
								<span class='caret'></span>
							</li>
							<ul class='dropdown-menu'>";
					break;
					case "#break#":
					$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
								<br/>
								<span class='caret'></span>
							</li>
							<ul class='dropdown-menu'>";
					break;
					case "#space#":
					$menu.="<li class='nav_item $item_style' style='$nav_css_code'>
								<div class='margin_bottom_list'></div>
								<span class='caret'></span>
							</li>
							<ul class='dropdown-menu'>";
					break;
					default:
					$menu.="<li class='$enable_class dropdown nav_item $item_style' style='$nav_css_code'>
							<a class='$item_style' style='$nav_css_code' href='#' class='dropdown-toggle' data-toggle='dropdown' title='$title'>
								".$item_icon.getSeperator($label)."
								<span class='caret'></span>
							</a>
							<ul class='dropdown-menu'>";
					break;
				}

				foreach ($parent['children'] as $children) {
					if ($loginRequired && !itemHasVisibility($children['item_visibility'])) {
						continue;
					}
					$label = dislayUserNameSelector($children['item_label']);
					$title = $children['item_help'];
					$item_style = $children['nav_css_class'];
          			$nav_css_code = $children['nav_css_code'];
					$item_icon = getNavItemIcon($children['item_icon'],$item_style,$nav_css_code);
					$navTarget = getNavTarget($children);
					$target = $navTarget['target'];
					$enable_class=$navTarget['enable_class'];
					$target_blank = $navTarget['target_blank'];
					$disable_child = '';
					if ($currentUserPrivilege < $children['item_privilege'] && $isUserLoggedIn) {
						$disable_child ='disabled_nav';
					}
					#$label=$label.'#line#';
					switch (strtolower($label)) {
						case "#line#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<div class='saperator_line'></div>
								</li>";
						break;
						case "#break#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<br/>
								</li>";
						break;
						case "#space#":
						$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
									<div class='margin_bottom_list'></div>
								</li>";
						break;
						default:
						$menu.="<li class='$enable_class nav_item $item_style' style='$nav_css_code'>
									<a onClick=urlVariables(this) class='$disable_child $item_style' $target_blank data-link='$target' href='javascript:;' title='$title' style='$nav_css_code'>".
										$item_icon.
										getSeperator($label)."
									</a>
								</li>";
						break;
					}
				}
				$menu.= "</ul></li>";
			} else {
				switch (strtolower($label)) {
					case "#line#":
					$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
								<div class='saperator_line'></div>
							</li>";
					break;
					case "#break#":
					$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
								<br/>
							</li>";
					break;
					case "#space#":
					$menu.=" <li class='nav_item $item_style' style='$nav_css_code'>
								<div class='margin_bottom_list'></div>
							</li>";
					break;
					default:
					$menu.="<li class='nav_item $enable_class $item_style' style='$nav_css_code'>
								<a onClick=urlVariables(this) class='$disable $item_style' $target_blank data-link='$target' href='javascript:;' title='$title' style='$nav_css_code'>
									".$item_icon.getSeperator($label)."
								</a>
							</li>
							<script>
							function urlVariables(e){
								var target = $(e).data('link');
								if(target != '' && target != null){
								$.ajax({
									method: 'GET',
									url: 'ajax-actions.php',
									data:{ values_to_unset: 'abc' }
								})
								.done(function (msg) {
									console.log(target)
									window.location = target;
								});
								}
							}
							</script>
							";
					break;
				}
			}
		}
	}
	return $menu;
}



// ******************************************************************************************
function Navigation($page, $menu_location = 'header1') {
    $con = connect();
    $rs = $con->query("SELECT * FROM navigation where page_name='$page' and item_number=0 and menu_location='$menu_location' AND item_target='override'");
	// the above is to detect any "inherited" parameters for the entire Nav menu for that page_name

    $classForNavBr2 = '';
    if($menu_location=='header2'){
      $classForNavBr2 = 'navbar-lower';
      echo "<style>
      .navbar-lower{
        margin-top:75px;
        z-index:800;
      }

      </style>";
    }
    /*
     *
     * Checking whether user have access to current page or not
     */
     $overRide = false;
    if ($rs->num_rows > 0) {
         $overRide = true;
        // $_SESSION['callBackPage'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // FlashMessage::add('Login required to view the current page!');
        // echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "login.php'>";
        // exit();
    }
  	$navItems = getNavItems($page,$menu_location,$overRide);
    if($menu_location!=='header1' && count($navItems)==0){
      return;
    }
    ?>
    <!-- Navigation starts here -->
    <div class="navbar navbar-default navbar-fixed-top <?=$classForNavBr2 ?>">
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

                $rs = $con->query("SELECT * FROM navigation where (page_name='$page' OR page_name='ALL' ) and menu_location LIKE 'LOGO%' order by item_number");
                $row = $rs->fetch_assoc();

                if ($row) {
                    $nav_menu_location = strtoupper($row['menu_location']);
                    $logo_image = BASE_IMAGES_URL . $row['item_target'];
                    $logo_text = $row['item_label'];
                    $logo_class = $row['nav_css_class'];
                    $logo_code = $row['nav_css_code'];


                    if ($nav_menu_location == 'LOGO-CENTER') {
                        $logo_position = 'center';
                    }
                }
            ?>
            <?php if ($nav_menu_location != 'LOGO-RIGHT') {
              if($menu_location=='header1'){ ?>
                <a class="navbar-brand logo <?php echo $logo_position ?>" href="<?php echo $logo_link ?>">
                    <?php

                        if ($logo_image != '') {
                            echo "<img src='$logo_image' alt='$logo_text' class='$logo_class' style='$logo_code'>";
                        }
                        echo $logo_text;
                    ?>
                </a>
            <?php } }?>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right <?= $item_style;?>">
                <?php
                if (isUserLoggedin()) {
					             $loginRequired = true;
                } else {
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
                            echo "<img src='$logo_image' alt='$logo_text' class='$logo_class' style='$logo_code'>";
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
// ******************************************************************************************

?>