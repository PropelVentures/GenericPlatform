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

    $_SESSION['display'] = $display_page;
    global $tab;
//$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $con = connect();

    $rs = $con->query("SELECT * FROM  data_dictionary where display_page = '$display_page' and (tab_num !='0' AND tab_num != 'S-0' AND tab_num != 'S-L' AND tab_num != 'S-R' AND tab_num != 'S-C')  and tab_name != 'fffr_icon' order by tab_num");
    $i = 1;

    /*     * ********
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


    echo "<ul class='center-tab' role='tablist' >";
    while ($row = $rs->fetch_assoc()) {
		if(!itemHasVisibility($row['dd_visibility'])){
			continue;
		}
		//pr($row);

        $tab_name = explode("/", $row['tab_name']);

        $row['tab_name'] = trim($tab_name[0]);

        if ($i == 1 && !( isset($_SESSION['tab']) )) {
//$_SESSION['first_tab'] = $row[table_alias];
//            if (!isset($_SESSION['first_tab']))
//                echo "<script>window.location='$actual_link';
            $tab = $row[table_alias];
            $_SESSION['tab'] = $tab;


            echo "<li class='active'><a href=?display=$display_page&tab=$row[table_alias]&tabNum=$row[tab_num] class='tab-class'>$row[tab_name]</a></li>";
        } else if ($_SESSION['tab'] == $row[table_alias] && $_GET['tabNum'] == $row['tab_num']) {

            echo "<li class='active'><a href=?display=$display_page&tab=$row[table_alias]&tabNum=$row[tab_num] class='tab-class'>$row[tab_name]</a></li>";
        } else {

            echo "<li><a href=?display=$display_page&tab=$row[table_alias]&tabNum=$row[tab_num]&search_id=$_GET[search_id] class='tab-class'>$row[tab_name]</a></li>";
        }
        $i++;
    }

    echo "</ul>";
}

function Get_Tab_Links($display_page,$sidebar) {
	$con = connect();
	if($sidebar == 'left'){
		$rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' and (tab_num ='S-L')");
	} elseif($sidebar == 'right'){
		$rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' and (tab_num='S-R')");
	} else {
		$rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' and (tab_num='S-C')");
	}
	$row = $rs->fetch_assoc();
	if (!empty($row)) {
		if ($_GET['edit'] == 'true'){
			fffr_icons();
		}
		$tabQuery = $con->query("SELECT * FROM  data_dictionary where display_page = '$display_page' and (tab_num !='0' AND tab_num != 'S-0' AND tab_num != 'S-L' AND tab_num != 'S-R' AND tab_num != 'S-C')  and tab_name != 'fffr_icon' order by tab_num");
		if($tabQuery->num_rows > 0) { ?>
			<ul class='vertical-tab <?php echo $sidebar; ?>' role='tablist'>
				<?php 
				while ($row = $tabQuery->fetch_assoc()) {
					$tab_id = "#".$display_page.$row['dict_id'];
					if(!itemHasVisibility($row['dd_visibility'])){
						continue;
					} ?>
					<li class="tab-class js_tab" id="<?php echo $tab_id; ?>">
						<a href="javascript:void(0);">
							<?php echo $row['tab_name']; ?>
						</a>
					</li>
				<?php 
				} ?>
			</ul>
			<script>
			$(document).ready(function(){
				
				$('.js_tab').click(function(){
					$('.js_tab').removeClass('active');
					$(this).addClass('active');
					$('html, body').animate({
						scrollTop: $($(this).attr('id')).offset().top - 70
					}, 500);
				});
				$('.js_tab').first().addClass('active');
			});
			</script>
	<?php 
		}
	} ?>
<?php
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
     *  //echo "<br><br><br><br><br><br>";
      // print_r($row);
     * Checking whether user have access to current page or not
     */

    if ($row['loginRequired'] == 'true' && !isset($_SESSION['uid'])) {

        $_SESSION['callBackPage'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


        FlashMessage::add('Login required to view the current page!');

        echo "<META http-equiv='refresh' content='0;URL=" . BASE_URL_SYSTEM . "login.php'>";
        exit();
    }
    $item_style = $row['item_style'];

    $rs = $con->query("SELECT * FROM navigation where (display_page='$page' OR display_page='ALL' ) and menu_location='$menu_location' AND nav_id>0 AND loginRequired='true' ORDER BY item_number ASC");

    $navItems = array();
    $arr = array();
    $i = 0;
    while ($row = $rs->fetch_assoc()) {
		if(strpos($row['item_number'],".0")){
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		} elseif(strpos($row['item_number'],".")){
			$navItems[floor($row['item_number'])]['children'][] = $row;
		} else {
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		}
		/* if(!itemHasVisibility($row['item_visibility'])){
			continue;
		}
        $arr[$i] = $row;
        $i++; */
    }
	//pr($navItems);die;

    /*for ($j = 0; count($arr) > $j; $j++) {
        if(strtoupper($arr[$j]['display_page'])==strtoupper($_GET['display']) && empty($_GET['search_id'])){
            if($arr[$j]['item_number']==0){
            $pagename = $arr[$j]['display_page'];
            $action = 'only1';

            }
            elseif( $arr[$j]['item_number']!=0 && explode('.',$arr[$j]['item_number'])[1]==0){
            $pagename = $arr[$j]['display_page'];
            $action = 'showall';
            }
            }
            if( $arr[$j]['item_number']==0 && $arr[$j]['display_page']=='ALL'){
            $itemlable = $arr[$j]['item_label'];
            }
     }*/

//////html version of navigation will be displayed....
    ?>
    <script type='text/javascript'>
        /*$(document).ready(function() {
            <?php if($action=='only1') { ?>
            $('.<?= $pagename ?>').show();
            $('.ALL').hide();
            <?php } ?>
            $("li:contains('<?= $itemlable ?>')").remove();
        });*/
    </script>

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
            <ul class="nav navbar-nav navbar-right" id="<?= $item_style; ?>">

                <?php
                if (isUserLoggedin()) {
					$menu = "";
					if(!empty($navItems)){
						foreach($navItems as $parent){
							if(!itemHasVisibility($parent['item_visibility']) || !isset($parent['nav_id'])){
								continue;
							}
							$label = ucwords($parent['item_label'] =='CURRENTUSERNAME' ?  $_SESSION['uname'] : $parent['item_label']);
							$title = $parent['item_help'];
							$item_style = $parent['item_style'];
							$item_icon = getNavItemIcon($parent['item_icon']);
							$navTarget = getNavTarget($parent);
							$target = $navTarget['target'];
							$enable_class=$navTarget['enable_class'];
							$target_blank = $navTarget['target_blank'];
							if(!empty($parent['children'])){
								
								switch(strtolower($label)){
										case "#line#":
										$menu.=" <li >
													<div class='saperator_line'></div>
													<span class='caret'></span>
												</li>
												<ul class='dropdown-menu'>";
										break;
										case "#break#":
										$menu.=" <li >
													<br/>
													<span class='caret'></span>
												</li>
												<ul class='dropdown-menu'>";
										break;
										case "#space#":
										$menu.="<li >
													<div class='margin_bottom_list'></div>
													<span class='caret'></span>
												</li>
												<ul class='dropdown-menu'>";
										break;
										default:
										$menu.="<li class='$enable_class dropdown newone' id='$item_style'>
												<a href='#' class='dropdown-toggle' data-toggle='dropdown' title='$title'>
													".$item_icon.getSaperator($label)."
													<span class='caret'></span>
												</a>
												<ul class='dropdown-menu'>";
										break;
									}
								
								foreach($parent['children'] as $children){
									if(!itemHasVisibility($children['item_visibility'])){
										continue;
									}
									$label = ucwords($children['item_label'] =='CURRENTUSERNAME' ?  $_SESSION['uname'] : $children['item_label']);
									$title = $children['item_help'];
									$item_style = $children['item_style'];
									$item_icon = getNavItemIcon($children['item_icon']);
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
										$menu.="<li class='$enable_class' id='$item_style'>
													<a $target_blank href='$target' title='$title'>".
														$item_icon.
														getSaperator($label)."
													</a>
												</li>";
										break;
									}
								}
								$menu.= "</ul></li>";
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
										$menu.="<li class='$enable_class' id='$sub_item_style'>
													<a $target_blank href='$target' title='$title'>
														".$item_icon.getSaperator($label)."
													</a>
												</li>";
										break;
									}
							}
						}
					}
					echo $menu;
                    /*for ($i = 0; count($arr) > $i; $i++) {
						$label = $arr[$i]['item_label'];
                        if(!itemHasPrivilege($arr[$i]['item_privilege'])){
							$target = "javascript:void(0);";
						} else {
							$item_target = trim($arr[$i]['item_target']);
							if ($item_target == '') {
								$item_target = 'main.php';
							}
							// Remove all illegal characters from a url
							$item_target = filter_var($item_target, FILTER_SANITIZE_URL);
							// If Url is valid then et target as defined in DB
							if (filter_var($item_target, FILTER_VALIDATE_URL)) {
								$target = $item_target;
							} elseif($item_target == "#" || strpos($arr[$i]['item_number'],".0")) {
								$target = "javascript:void(0);";
							} else {
								$target = BASE_URL_SYSTEM . $item_target . "?display=" . $arr[$i]['target_display_page'] . "&layout=" . $arr[$i]['page_layout_style'] . "&style=" . $arr[$i]['page_layout_style'];
							}
						}
						
						
                        $curr_item_number = explode('.', $arr[$i]['item_number']);

                        $next_item_number = explode('.', $arr[$i + 1]['item_number']);


                        if ($arr[$i]['enabled'] > 0) {
                            $enabled = 'enabled';
                        } else
                            $enabled = 'disabled';

                        $visibility = $arr[$i]['display_page'];

                        /*
                          if ($arr[$i]['admin_level'] <= 0) {
                          $admin_enabled = 'enabled';
                          } else
                          $admin_enabled = 'disabled'; */
                        //using for now
                        //$admin_enabled = 'enabled';

                        /* $title = $arr[$i]['item_help'];

                        $sub_item_style = $arr[$i]['item_style'];

                        /*
                         * Fetching Userprivilege first to match
                         */

                        // $userRec = get($_SESSION['select_table']['database_table_name'], $_SESSION['select_table']['keyfield'] . '=' . $_SESSION['uid']);
                        ///Checking item privilege with user privilege
						
                        /* if ($_SESSION['user_privilege'] < $arr[$i]['item_privilege'] && $_SESSION['user_privilege'] <= 9) {

                            $userPrivilege = 'inactiveLink';
                        } else {

                            $userPrivilege = '';
                        } */

                        //not displaying menu if user dont have admin privileges

                        /* $adminPrivilege = false;

                        $menuAdmin = false;

                        if ($arr[$i]['admin_level'] > 0) {

                            $menuAdmin = true;

                            if ($_SESSION['user_privilege'] >= 9) {

                                $adminPrivilege = true;
                            }
                        }

                        if (( $menuAdmin === false && $adminPrivilege === false ) || ( $menuAdmin === true && $adminPrivilege === true )) {
                            $showMenu = true;
                        } else {
                            $showMenu = false;
                        } */
						/*$showMenu = true;

                        if (($curr_item_number[0] == $next_item_number[0]) && ( $curr_item_number[1] == 0 && $next_item_number[1] == 1 )) {
                        /// Menu name have sub menu

                            if ($showMenu === true) {
                                echo "<li class='dropdown newone $enabled $visibility  $userPrivilege' id='$sub_item_style'> <a href='#' class='dropdown-toggle' data-toggle='dropdown' title='$title'> ";
                                if ($label == 'CURRENTUSERNAME') {
                                    echo $_SESSION[uname];
                                } else
                                    echo $label;
                                echo "<span class='caret'></span></a>
                                    <ul class='dropdown-menu'>";
                            }
                        } else
                        if (isset($curr_item_number[0]) && isset($curr_item_number[1]) && ( $curr_item_number[1] > 0 ) && ( $next_item_number[1] > 0 )) {
                            /// Child names
                            if ($showMenu === true) {
                                echo " <li class='$enabled $visibility $userPrivilege' id='$sub_item_style'>
                                        <a href='$target' title='$title'>$label</a>
                                      </li>";
                            }
                        } else
                        if (isset($curr_item_number[0]) && isset($curr_item_number[1]) && ( $curr_item_number[1] > 0 ) && !($next_item_number[1] > 0 )) {
                            /// last child
                            if ($showMenu === true) {
                                echo "<li class='$enabled $visibility $userPrivilege' id='$sub_item_style'>
                                            <a href='$target' title='$title'>$label</a>
                                          </li>
                                        </ul>
                                      </li>";
                            }
                        } else
                        if (($curr_item_number[0] != $next_item_number[0]) && ( $curr_item_number[1] == 0 && $next_item_number[1] != 1 )) {
                            /// Menu name which have no childs
                            if ($showMenu === true) {
                                echo "<li class='$enabled $visibility $userPrivilege' id='$sub_item_style'> <a href='$target' title='$title'>";
                                if ($label == 'CURRENTUSERNAME') {
                                    echo $_SESSION[uname];
                                } elseif($arr[$i]['item_number'] != 0){
									echo $label;
								}
                                   
                                echo "</a></li>";
                            }
                        }
                    }/////////// for loop ends here///
					*/
                } else {
                ?>



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


            <?php } ///////else if ends here                                                                                           ?>

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
	$con = connect();
    $rs = $con->query("SELECT * FROM navigation where (display_page='$display_page' OR display_page='ALL' ) and menu_location='$menu_location' AND nav_id>0 AND loginRequired='true' ORDER BY item_number ASC");
    $navItems = array();
    $arr = array();
    $i = 0;
    while ($row = $rs->fetch_assoc()) {
		if(strpos($row['item_number'],".0")){
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		} elseif(strpos($row['item_number'],".")){
			$navItems[floor($row['item_number'])]['children'][] = $row;
		} else {
			$row['children'] = array();
			$navItems[floor($row['item_number'])] = $row;
		}
    }
	//pr($navItems);
	if (isUserLoggedin()) {
		$menu = "";
		if(!empty($navItems)){ ?>
			<!-- Menu -->
			<div class="side-menu <?php echo $menu_location; ?>">
				<nav class="navbar navbar-default" role="navigation">
					<!-- Main Menu -->
					<div class="side-menu-container">
						<ul class="nav navbar-nav <?php echo $menu_location; ?>">
						<?php foreach($navItems as $parent){
							if(!itemHasVisibility($parent['item_visibility']) || !isset($parent['nav_id'])){
								continue;
							}
							$label = ucwords($parent['item_label'] =='CURRENTUSERNAME' ?  $_SESSION['uname'] : $parent['item_label']);
							$title = $parent['item_help'];
							$item_style = $parent['item_style'];
							$item_icon = getNavItemIcon($parent['item_icon']);
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
									$menu.="<li class='$enable_class dropdown newone' id='$item_style'>
											<a href='#nav_".$parent['nav_id']."' class='dropdown-toggle' data-toggle='collapse' title='$title'>
												".$item_icon.getSaperator($label)."
												<span class='caret'></span>
											</a>";
									break;
								}
								$menu .= "<div id='nav_".$parent['nav_id']."' class='panel-collapse collapse'>
												<div class='panel-body'>
													<ul class='nav navbar-nav'>";
								
								foreach($parent['children'] as $children){
									if(!itemHasVisibility($children['item_visibility'])){
										continue;
									}
									$label = ucwords($children['item_label'] =='CURRENTUSERNAME' ?  $_SESSION['uname'] : $children['item_label']);
									$title = $children['item_help'];
									$item_style = $children['item_style'];
									$item_icon = getNavItemIcon($children['item_icon']);
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
										$menu.="<li class='$enable_class' id='$item_style'>
													<a $target_blank href='$target' title='$title'>".
														$item_icon.
														getSaperator($label)."
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
									$menu.="<li class='$enable_class' id='$sub_item_style'>
												<a $target_blank href='$target' title='$title'>
													".$item_icon.getSaperator($label)."
												</a>
											</li>";
									break;
								}
							}
						} 
						echo $menu;  
						?>
						</ul>
					</div><!-- /.navbar-collapse -->
				</nav>
			</div>
<?php 	}
	}
}
?>

