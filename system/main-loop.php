<?php
	/*
    *	This the main layout page or parent page which defines how the  page looks according to URL.
	* 	Its for including all the Class, Lib and Component (header,footer, sidebar, database connection,
	*	session for user login/	logout check ).
	
	* 	Its identified which page load by getting value from the "page_name" parameters from the URL.
	*	Layout of the page is defind by the "layout" parameters from the URL.
	*   Which Css file will be include defind by the "style" parameters from the URL.
	
	*/

	require_once("functions_loader.php");
	include("html_headers.php");

    // ******************************************************************************
	// get which page have to render as per the user (or prior) request
	// the page to render, can be referred to (right now) by either the page_name or the dict_id 
	// if dict_id is not blank we will use it.
	
	$page_name = $_GET['page_name'];
	$dict_id = $_GET['dict_id'];

	log_event($_GET['page_name'],'page view');   //log event  (event logging)

	$page_layout = $_GET['layout'];
	
	$style = $_GET['style'];  // get the css file name for the current page




	// NOTE ***>>  below may be incorrect 
	// the comment above implies we are looking for the tab value (tab number / component order 
	// but the code below implies that we are saving/retrieving table_alias
	
	if (isset($_GET['dict_id']))
	{
		$_SESSION['dict_id'] = $_GET['dict_id'];
	} elseif (isset($_GET['table_alias']) || !empty($_GET['table_alias'])) 
		{
			$_SESSION['table_alias'] = $_GET['table_alias'];
	} else {
		$_SESSION['table_alias'] = '';
		unset($_SESSION['table_alias']);
	}

	unset($_SESSION['popup_menu_array']);
	
	
	

	// ******************************************************************************
	// Navigation Block
	// formats navigation bars AND other nav menus that might be
	// displayed in the body (or sidebar columns) in the page
	// also display extra FD content - if specified 
	//
	// Executes/formats first ... before any "body" content
	// 
	// These are Navigation MENUS that are constructed from the Navigation (NAV) Table
	// for the current page_name (or ALL)
	// Note - Nav menus can appear on the top/heading rows, footer, AND in the Body (content areas) - and right/left sidebar columns
	// Also
	// Note - Nav menus are different than the "tabs" or "serial" menues (that allow the user to navigate DD components on the page
	//
	// ******************************************************************************

	// TOP NAVIGATION MENUS
	Navigation($page_name,'header1');    //nav_functions.php
	Navigation($page_name,'header2');    //nav_functions.php



    // FORMAT PAGE (COLUMNS, OTHER PAGE-WIDE FORMATTING
	$haveParallax = false;           // formats html for page-wide parallax view 
	ShowComponentTypeParallaxBanner($page_name,$haveParallax);    // component_display_functions.php 

	/* if this is the page = "home" then include the slider
	this will be deprecated eventually and there will be no special handling of the "home" page
	once we revise the general "slider' component 	*/

	if ($page_name == 'home') {include("../system/home-slider.php");}

?>



<div class="container main-content-container">
<?php

// FORMATS RIGHT/LEFT SIDEBARS, CENTER-BODY AREA ... 
// DISPLAYs any Navigation Menus that appear in each ...

	/* CHECKING NAV HAS VISIBILITY  START
	*  navHasVisibility from  nav_functions.php file, 
	* CHECKS TO SEE IF THE CURRENT PAGE IS ALLOWED TO BE SEEN by the current user priv level and if the nav item has visibility ...
	*/

	if (navHasVisibility()) {
		//  echo "<br><br><br>LEFT CONTENT<br><br><br>";exit();
		//  NOT JUST NAV CONTENT - ALL Left content
		// FORMATS the ENTIRE PAGE
	
		// FD CONTENT Above Tabs	
		//		Determines IF there are any FD fields  that are supposed to be displayed ABOVE the "Tab"/Content  area?? 
		//		(ie right below the naviagation menus)


		$rs = $con->query("SELECT component_column FROM data_dictionary where page_name='$page_name'");

		$right_sidebar = $left_sidebar = '';
		$left_sidebar_width = $right_sidebar_width = 0;
	
		
		while ($row = $rs->fetch_assoc()) {
			$r1 = explode('w', trim($row['component_column']));
			
			// if the component_column also has a wNN after the column number, then NN is the column WIDTH
			// the "w" here is all about a custom width for the right or left column
			
			if (!empty($r1[1])) {
				if ($r1[0] == 'R1') {
					$right_sidebar_width = $r1[1];
				} else {
					$left_sidebar_width = $r1[1];
				}
			}
			if ($r1[0] == 'R1') {
				$right_sidebar = 'right';
			}
			if ($r1[0] == 'L1') {
				$left_sidebar = 'left';
			}
		}
		
		
		// Nav Body-Left or Body-right Code Start
		// are there any NAV items/menus that are supposed to be displayed in tne BODY area - left/right or center?

		$navBodyLeftQuery = $con->query("SELECT * FROM navigation where (page_name='$page_name' OR page_name='ALL' ) AND (menu_location='body-left') AND nav_id > 0 AND loginRequired='1' AND (item_number LIKE '%.0' OR item_number REGEXP '^[0-9]$') ORDER BY item_number ASC");
		if($navBodyLeftQuery->num_rows){
			if($left_sidebar ==''){
				$left_sidebar = 'left';
			}
		}
		$navBodyRightQuery = $con->query("SELECT * FROM navigation where (page_name='$page_name' OR page_name='ALL' ) AND (menu_location='body-right') AND nav_id > 0 AND loginRequired='1' AND (item_number LIKE '%.0' OR item_number REGEXP '^[0-9]$') ORDER BY item_number ASC");
		if($navBodyRightQuery->num_rows){
			if($right_sidebar ==''){
				$right_sidebar = 'right';
			}
		}


		/* Tab TTl1  or Tl2 Start */
		$tabLeftExist = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND page_layout LIKE 'S-L%'");
		if($tabLeftExist->num_rows){
			if($left_sidebar ==''){
				$left_sidebar = 'left';
			}
		}
		$tabRightExist = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND page_layout LIKE 'S-R%'");
		if($tabRightExist->num_rows){
			if($right_sidebar ==''){
				$right_sidebar = 'right';
			}
		}
		/* Tab TTl1 or Tl2 End */
		if ($left_sidebar == 'left' && $right_sidebar == 'right') {
			$both_sidebar = 'both';
		}
		
		
		
		/*
		 * Check If Center_Body content exist
		 * If not exist then check the width of aone and asign the other
		 * or if width not exist then divide 50% each
		 */
		$Center_Body_Content_Exist = true;
		$checkCenter_BodyContentQuery = $con->query("SELECT component_order FROM data_dictionary where page_name='$page_name'  and component_order REGEXP '^[0-9]+$' AND component_order >'0'");
		if($checkCenter_BodyContentQuery->num_rows == 0 ) {
			$Center_Body_Content_Exist = false;
			if (!empty($right_sidebar_width) && !empty($left_sidebar_width)) {
				// do nothing
			} else if (!empty($right_sidebar_width) && empty($left_sidebar_width)) {
				$left_sidebar_width = 100 - $right_sidebar_width;
			} else if (empty($right_sidebar_width) && !empty($left_sidebar_width)) {
				$right_sidebar_width = 100 - $left_sidebar_width;
			} else {
				if ($both_sidebar == 'both') {
					$left_sidebar_width = $right_sidebar_width = 50;
				} else if ($both_sidebar != 'both' && ( $right_sidebar == 'right' || $left_sidebar == 'left' )) {
					$left_sidebar_width = $right_sidebar_width = 50;
				} else {
					$left_sidebar_width = $right_sidebar_width = 0;
				}
			}
		}

		// ***************************************************************************************************
		// left sidebar code		
		// ***************************************************************************************************
		sidebar($left_sidebar, $both_sidebar, $page_name, $left_sidebar_width);
		
		
		// ***************************************************************************************************
		// Center_Body content area - FORMATTING ...
		// ***************************************************************************************************

		// $total_width = 0;

		if ($_GET['child_list_active'] == 'isSet')
		echo "<a href='#' class='goBackToParent'>click me</a>";


		/**
		 * Calculate the width of center body content
		 * 
		 * If width for left and/or right sidebar is specified,
		 * then center will be calculated accordingly
		 * 
		 * If no width specified for sidebars:
		 * If both sidebars exist, center body = col-lg-8
		 * If left/right sidebar exists, center body = col-lg-9
		 * If no sidebars, center body = col-lg-12
		 */
		if ($Center_Body_Content_Exist) {
			if (!empty($right_sidebar_width) && !empty($left_sidebar_width)) {
				$total_width = 100 - ( $right_sidebar_width + $left_sidebar_width );
				echo "<div class='center-container' style='width:$total_width%;float:left;' >";
			} else if (!empty($right_sidebar_width) && empty($left_sidebar_width)) {
				$total_width = 100 - $right_sidebar_width;
				echo "<div class='center-container content-manual' style='width:$total_width%;float:left;'>";
			} else if (empty($right_sidebar_width) && !empty($left_sidebar_width)) {
				$total_width = 100 - $left_sidebar_width;
				echo "<div class='center-container' style='width:$total_width%;float:left;'>";
			} else {
				if ($both_sidebar == 'both') {
					echo "<div class='col-lg-8 center-container'>";
				} else if ($both_sidebar != 'both' && ( $right_sidebar == 'right' || $left_sidebar == 'left' )) {
					echo "<div class='col-9 col-sm-9 col-lg-9 center-container' >";
				} else {
					echo "<div class='col-12 col-sm-12 col-lg-12 center-container'>";
				}
			}
		}

		//if( $both_sidebar == 'false' &&  $right_sidebar == 'false' && $left_sidebar == 'false'  )
				
		// ******************************************************************************
		// END NAVIGATION BLOCK 
		// ******************************************************************************
				

		// ******************************************************************************
		// CENTER Page CONTENT Area (Body) - Check to see if page is 'Tabbed' or 'Serial' 
		// ******************************************************************************	
				
		// This query below .. for now searches for "component_order=0 
		// (which dictates Page-level layout directives) ...
		// later, we might change this to be located inside the Nav table
			
		$rs = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' and (component_order='0' OR page_layout like 'S-%') ");
		$row = $rs->fetch_assoc();

		/**
		 * Determine the layout
		 * If row exists, then the layout is serial, otherwise tabbed
		 */
		$tab_style = !empty($row) ? 'serial' : 'tabbed';
		$_SESSION['temp_page_name'] = $tab_style == 'serial' ? $page_name : '';

		/**
		 * Render side bar navigation
		 * applicable for all layout (serial and tabbed)
		 */
		GetSideBarNavigation($page_name, 'body-center');  // in nav_functions.php

		/**
		 * Display fffr icons
		 */
		fffr_icons($page_name);

		/**
		 * Render header and sub header
		 */
		headersAndSubHeaders($page_name);  // in component_display_functions.php
		// SERIAL LAYOUT
		if ($tab_style == 'serial') {
			
			// SERIAL Tab Navigation (on top or sides)
			Get_Serial_Tab_Links($page_name, 'center');  // in component_display_functions.php

			if($Center_Body_Content_Exist){
				if ($list_sort!='') {
					$field_str = getListSortingValue($list_sort);   // list_functions.php
					$field_str = rtrim($field_str,',');
				} else {
					$field_str = 'component_order';
				}
				component_display_loop('', $tab, $page_name, $tab_style, $field_str);
			}
		} else {
			Get_Top_Tab_Links($page_name);   // in component_display_functions.php
			
			global $table_alias;
			if ($Center_Body_Content_Exist) {
				component_display_loop('', (isset($_SESSION['table_alias']) ? $_SESSION['table_alias'] : $table_alias), $page_name, $tab_style);
			}
		}
			
		// component_order else ends here

		// ********************************************************************************
		// RIGHT SIDEBAR content
		// ********************************************************************************

		sidebar($right_sidebar, $both_sidebar, $page_name, $right_sidebar_width);
		// WHAT ABOUT LEFT SIDEBAR?????
	
	} else { ?>
		<div class="center-body-message-box">
			<h2><?php echo ERROR_NOT_ENOUGH_PRIVILEGE_LEVEL; ?></h2>
		</div>
	<?php
	}
	/* CHECKING NAV HAS VISIBILITY  END*/
?>
</div>
<?php if($haveParallax){?>
</div>
<?php }

// ******************************************************************************
// END OF MAIN LOOP
// ******************************************************************************



// ******************************************************************************
// JAVASCRIPT BELOW 
// ******************************************************************************


?>



<script src="<?= BASE_URL_SYSTEM ?>ckeditor/ckeditor.js"></script>

<!-- modal view dialog to display  Enlarge image -->
<div id="imgModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"></h4>
			</div>
            <div class="modal-body">
                <img src="" class="img-responsive img-modal">
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- modal view dialog to display  Enlarge image Ends here-->
<!-- Modal For voting Dialog display -->
<div class="modal fade" id="votingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= AlERTBOX ?></h4>
			</div>
            <div class="modal-body votingBody">
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>


<div id="addOptionModel" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"></h4>
						</div>
            <div class="modal-body">
						</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-default" onclick="AddOptionInTable()">Add</button>

			</div>
		</div>
	</div>
</div>

<div id="sendMessageModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Type Message Here..</h4>
						</div>
            <div class="modal-body">
							<textarea style="width:100%" id='sendMessageModalText' rows = "5" >
		            Enter your message
		         </textarea>
						 <input type='hidden' id='message_reciver_id'>
						 <input type='hidden' id='message_log_table'>
						</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-default" onclick="sendMessaeg()">Send</button>

			</div>
		</div>
	</div>
</div>


<!-- Modal For Transaction Dialog display -->
<div class="modal fade" id="transModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= transTitle ?></h4>
			</div>
            <div class="modal-body transBody">
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<ul class='image-menu'>
    <li data-action='upload'  class='popup-class'>Upload/Replace Image</li>
    <li data-action='enlarge'  class='popup-class'>View Image</li>
    <li data-action='remove'  class='popup-class'>Remove</li>
    <li data-action='revert'  class='popup-class'>Revert Changes</li>
</ul>
<ul class='pdf-menu'>
    <li data-action='upload'  class='popup-class'>Upload/Replace PDF FILE</li>
    <li data-action='enlarge'  class='popup-class'>View PDF FILE</li>
    <li data-action='remove'  class='popup-class'>Remove</li>
    <li data-action='revert'  class='popup-class'>Revert Changes</li>
</ul>
<?php
	/*
	* Popup Menu codes starts here
	*/
	if(!empty($_SESSION['popup_menu_array'])){
		foreach($_SESSION['popup_menu_array'] as $popup_menu){
			if ($popup_menu['popupmenu'] == 'true') {
				echo "<ul id='".$popup_menu['popup_menu_id']."' class='custom-menu'>";
				if (isset($popup_menu['popup_delete']) && !empty($popup_menu['popup_delete'])) {
					echo "<li data-action='delete'  class='" . $popup_menu['popup_delete']['style'] . "'>" . $popup_menu['popup_delete']['label'] . "</li>";
				}
				if (isset($popup_menu['popup_add']) && !empty($popup_menu['popup_add'])) {
					echo "<li data-action='add'  class='" . $popup_menu['popup_add']['style'] . "'>" . $popup_menu['popup_add']['label'] . "</li>";
				}
				if (isset($popup_menu['popup_copy']) && !empty($popup_menu['popup_copy'])) {
					echo "<li data-action='copy'  class='" . $popup_menu['popup_copy']['style'] . "'>" . $popup_menu['popup_copy']['label'] . "</li>";
				}
				if (isset($popup_menu['popup_openChild']) && !empty($popup_menu['popup_openChild'])) {
					echo "<li data-action='openChild'  class='" . $popup_menu['popup_openChild']['style'] . "'>" . $popup_menu['popup_openChild']['label'] . "</li>";
				}
				echo "</ul>";
			}
		}
	}
?>
<a href="#" class="scrollToTop">Scroll To Top</a>
<!-- Css To Fix center tab on top -->
<style>.center_tab_fix{position:fixed; top:75px; z-index:9; border-bottom:1px solid #e7e7e7; left:0; padding-left:calc(50% - 550px); width:100%; background:#fff;}</style>




<script type="text/javascript">
	<!-- Script To Fix center tab on top -->
	$(window).scroll(function() {
		var scroll = $(window).scrollTop();
		if (scroll >= 30) {
			$(".center-tab-fixed").addClass("center_tab_fix");
		} else {
			$(".center-tab-fixed").removeClass("center_tab_fix");
		}
	});
	/*Code Changes by Palak*/
	$(document).ready(function(){
	//$(document).ready(function () {
	/*Code Changes by Palak*/
	   /*
		**
		*
		* Selecting all checkboxes
		*
		*/
        $('#selectAll').click(function (event) {  //on click
            if (this.checked) { // check select status
                $('.list-checkbox').each(function () { //loop through each checkbox
                    this.checked = true; //select all checkboxes with class "checkbox1"
				});
				} else {
                $('.list-checkbox').each(function () { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"
				});
			}
		});
		///when click on delete button////
        $(".action-delete").on('click', function (event) {
						var dict_id = $(this).data('id');
			event.preventDefault();
            if (confirm("<?= deleteConfirm ?>") == true) {
                $("#checkHidden").val('delete');
								var selected = [];
								$('#list-form input:checked').each(function() {
										selected.push($(this).val());
								});
								$.ajax({
										method: "POST",
										url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
										data: {checkHidden: 'delete',list:selected,dict_id:dict_id}
								})
								.done(function (returnUrl) {
										location.reload();
								});
				} else {
                $(this).parents('#list-form').attr('action', '');
			}
		});
        ///// when click on delete icon
        $(".list-del").click(function (event) {
			event.preventDefault();
            if (confirm("<?= deleteConfirm ?>") == true) {
                var del_id = $(this).attr('id');
                var dict_id = $(this).attr('name');
                var fnc = $(this).attr('fnc');
                $.ajax({
                    method: "GET",
                    url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                    data: {list_delete: del_id, check_action: "delete", dict_id: dict_id, fnc: fnc}
				})
				.done(function (returnUrl) {
					window.location.href=returnUrl;
					location.reload();
				});
			} else {
                event.stopImmediatePropagation();
			}
		});
		///copy button .. multi select
        $(".action-copy").on('click', function (event) {
					var dict_id = $(this).data('id');
			event.preventDefault();
            if (confirm("<?= copyConfirm ?>") == true) {
                $("#checkHidden").val('copy');
								var selected = [];
								$('#list-form input:checked').each(function() {
										selected.push($(this).val());
								});
								$.ajax({
										method: "POST",
										url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
										data: {checkHidden: 'copy',list:selected,dict_id:dict_id}
								})
								.done(function (returnUrl) {
									  location.reload();
								});
				} else {
                $(this).parents('#list-form').attr('action', '');
			}
		});
        //// single copy icon
        $(".list-copy").click(function (event) {
			event.preventDefault();
            if (confirm("Are you sure ,You want to Copy the Record!") == true) {
                var del_id = $(this).attr('id');
                var dict_id = $(this).attr('name');
				var fnc = $(this).attr('fnc');
                $.ajax({
                    method: "GET",
                    url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                    data: {list_copy: del_id, check_action: "copy", dict_id: dict_id, fnc: fnc}
				})
				.done(function (returnUrl) {
					window.location.href=returnUrl;
				});
			}
		});
		/******************* ADD BUTTON CODE **********************************/
		$(".action-add").click(function (event) {
			event.preventDefault();
			//window.location.href = '<?= $_SESSION['add_url_list'] ?>';
		});
		/*
			var test = 'something ';
			$(".span-checkbox").click(function(){
			test = test.concat($(this).html());
			alert(test);
			});
		*/

		// If the document is clicked somewhere
		$(document).bind("mousedown", function (e) {
			// If the clicked element is not the menu
			if (!$(e.target).parents(".custom-menu").length > 0) {
				// Hide it
				$(".custom-menu").hide(100);
			}
		});
		// If the menu element is clicked
		$(".custom-menu li").click(function () {
			// This is the triggered action name
			switch ($(this).attr("data-action")) {
				// A case for each action. Your actions here
				case "delete":
				popup_delete(popup_del, dict_id);
				break;
				case "copy":
				popup_copy(popup_del);
				break;
				case "add":
				popup_add(dict_id);
				break;
				case "openChild":
				popup_openChild(popup_del, dict_id);
				break;
			}
			// Hide it AFTER the action was triggered
			$(".custom-menu").hide(100);
		});
		/***** popup DELETE Function ****/
		function popup_delete(del_id, dict_id) {
			if (confirm("Are you sure ,You want to delete the Record!") == true) {
				$.ajax({
					method: "GET",
					url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
					data: {list_delete: del_id, check_action: "delete", dict_id: dict_id}
				})
				.done(function (msg) {
					if(msg=='false'){
							alert('Permission Denied');
					}else{
							location.reload();
					}
				});
			}
		}

		/***** popup COPY Function ****/
		function popup_copy(del_id) {
			if (confirm("Are you sure ,You want to copy the Record!") == true) {
				$.ajax({
					method: "GET",
					url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
					data: {list_copy: del_id, check_action: "copy"}
				})
				.done(function (msg) {
					if(msg=='false'){
							alert('Permission Denied');
					}else{
							location.reload();
					}
				});
			}
		}
		/***************************** POPUP ADD***************************/
		function popup_add(dict_id) {
			if (confirm("Are you sure ,You want to go to ADD Record!") == true) {
				window.location = '<?= $_SESSION['add_url_list'] ?>';
				//###THIS ACTION NEEDS TO BE REMOVED FROM ajax-actions.php######################
				//                $.ajax({
				//                    method: "GET",
				//                    url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
				//                    data: {list_add: dict_id, check_action: "add", url: window.location.href}
				//                })
				//                .done(function (msg) {
				//                    // console.log(msg);
				//                    window.location = msg;
				//                });
			}
		}
		/***** popup openChild Function ****/
		function popup_openChild(del_id, dict_id) {
			// if (confirm("Are you sure ,You want to go to Child list Record!") == true) {
			$.ajax({
				method: "GET",
				url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
				data: {childID: del_id, check_action: "openChild", dict_id: dict_id, display: "<?= $_GET['page_name']; ?>"}
			})
			.done(function (child_url) {
				console.log(child_url);
				console.log(window.location);
				// return
				if(child_url=='false'){
					alert('Permission Denied');
				}else{
					window.open(child_url);
					//window.location = child_url;
				}
				// window.open(msg,'','width=800,height=768,left=300');
			});
			//}
		}
		///IMAGE FIELD CANCEL BUTTON ACTion
		$(".img-cancel").click(function () {
			var profile_img = $(this).attr("name");
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {img_cancel: "img-cancel", profile_img: profile_img}
			})
			.done(function () {
				//alert(msg);
				location.reload();
			});
		});
		$(".tab-class").click(function () {
			var component_name = '<?= $_GET['table_alias'] ?>';
			var component_order = '<?= $_GET['ComponentOrder'] ?>';
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {tab_check: "true", component_name: component_name, component_order: component_order}
			})
			//.done(function (msg) {
			//});
		});
		$(".remove-audio-btn").click(function () {
			//audio_code = $(this).prev(".audio-css").html();
			var field_name = $(this).attr("id");
			$(this).siblings(".audio-css").fadeOut("slow");
			$(this).fadeOut("slow");
			$(this).next(".audio-placing").html("<input type='file' name='" + field_name + "' class='form-control'><input type='button' class='btn btn-primary update-btn pull-left audio-cancel rem-img-size'  value='CANCEL'/>");
		});
		$(".audio-placing").on("click", ".audio-cancel", function () {
			$(this).parents(".audio-placing").siblings(".audio-css").fadeIn("slow");
			$(this).parents(".audio-placing").siblings(".remove-audio-btn").fadeIn("slow");
			$(this).parents(".audio-placing").siblings(".remove-audio-btn").after("<div class='audio-placing'></div>");
			$(this).parents(".audio-placing").remove();
		});
		/*******************Back to top js*/
		//Check to see if the window is top if not then display button
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('.scrollToTop').fadeIn();
				} else {
				$('.scrollToTop').fadeOut();
			}
		});
		//Click event to scroll to top
		$('.scrollToTop').click(function () {
			$('html, body').animate({scrollTop: 0}, 800);
			return false;
		});
		/*
			* **************************
			* **********************************************
			* **************************************************BACK TO LSIT
			* ***************
			* **********************************
			*
		*/
		var form_edit = '';
		$('form:not(.profile_page) :input').change(function () {
			form_edit = 'changed';
		});
		$(".back-to-list").click(function (event) {
			if( $(this).parents('#user_profile_form').hasClass('profile_page') ){
				window.location = $(this).attr('href');
				}else{
				if (form_edit == 'changed') {
					event.preventDefault();
					window.location = $(this).attr('href');
					//window.location = document.referrer;
				}
			}
		});
		/******************************************************/
		/************** Enabling submit and cancel button *******/
		/********** *****************************************/
		$(".edit-btn").click(function () {
			var id = $(this).attr('id');
			/*Code Changes by Palak*/
			//alert(id);
			//var form_edit = 'changed';
			/*Code Changes by Palak*/
			$.ajax({
				method: "GET",
				url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
				data: {id: id, check_action: "enable_edit", form_edit_conf: form_edit}
			})
			.done(function (msg) {
				if ($.trim(msg) == 'active') {
					alert('<?= editBtnAlertMsg ?>');
					} else {
					location.reload();
				}
			});
		});
		/*
		*
		* Friend ICONS CODE GOES HERE****************
		* ************************************
		* *****************************************************
		* ********************************************************************
		*/
		var class_holder;
		$(".friend_me_icon").click(function () {
			var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
			var page_name = '<?= $_GET['page_name'] ?>';
			class_holder = this;
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {page_name:page_name,action: "friend_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id')}
			})
			.done(function (msg) {
				setStyleOfFFFRafterAction(class_holder,msg,'friend_me_icon');
			});
		});
		/*
		*
		* Follow me ICONS CODE GOES HERE****************
		* ************************************
		* *****************************************************
		* ********************************************************************
		*/
		$(".follow_me_icon").click(function () {
			var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
			var page_name = '<?= $_GET['page_name'] ?>';
			class_holder = this;
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {page_name:page_name,action: "follow_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id')}
			})
			.done(function (msg) {
				setStyleOfFFFRafterAction(class_holder,msg,'follow_me_icon');
			});
		});

		/*
		*
		* Favorite me ICONS CODE GOES HERE****************
		* ************************************
		* *****************************************************
		* ********************************************************************
		*/
		$(".favorite_me_icon").click(function () {
			var page_name = '<?= $_GET['page_name'] ?>';
			// $(this).css('color','red');
			class_holder = this;
			var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {page_name:page_name,action: "favorite_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id')}
			})
			.done(function (msg) {
				setStyleOfFFFRafterAction(class_holder,msg,'favorite_me_icon');
			});
		});
		/*
		*
		* Rate me ICONS CODE GOES HERE****************
		* ************************************
		* *****************************************************
		* ********************************************************************
		*/
		$('.rate_me').on('rating.change', function (event, value, caption) {
			var page_name = '<?= $_GET['page_name'] ?>';
			// class_holder = this;
			var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {page_name:page_name,action: "rate_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id'), value: value}
			})
			.done(function (msg) {
				if (msg != '' && msg != 'deleted') {
					$('.votingBody').html(msg);
					$('#votingModal').modal('show');
				}
			});
		});
		///////////when rating is reset////////////
		$('.rate_me').on('rating.clear', function (event) {
			var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
			var page_name = '<?= $_GET['page_name'] ?>';
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {page_name:page_name,action: "rate_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id'), value: 'clear'}
			})
		});
		/*
		*
		* Voting Number CODE GOES HERE****************
		* ************************************
		* *****************************************************
		* ********************************************************************
		*/
		$('.voting-number:not(.disabled)').on('click', function () {
			var page_name = '<?= $_GET['page_name'] ?>';
			// class_holder = this;
			var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
			var value = $(this).siblings(".fffr-input").val();
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {page_name:page_name,action: "rate_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id'), value: value,ta:'<?= $_GET[tab]?>',ComponentOrder:'<?= $_GET[ComponentOrder]?>'}
			})
			.done(function (msg) {
				console.log(msg);
				if (msg == 'deleted') {
					$('.votingBody').html("<?= voteInserted ?>");
					$('#votingModal').modal('show');
					setTimeout(function () {
						$('#votingModal').modal('hide');
					}, 3000);
					} else {
					$('.votingBody').html(msg);
					$('#votingModal').modal('show');
				}
			});
		});
		/*******
		*
		* ********
		* ***********
		* ************************HIDING UPDATE/CANCEL BUTTON WHEN FFFR PRESENT
		* *********
		* ************************
		*/
		if ($(".user-profile").find(".fffr").length) {
			$(".user-profile").find(".form-footer").hide();
		}
		$(".goBackToParent").click(function () {
			window.top.close();
		});
		////////////stop the anchor tag action when prev/next buttons are disabled
		$(".editPagePagination").children(".disabled").click(function (event) {
			event.preventDefault();
		});
		/******
		* **********
		* *******************Transaction Js code goes here
		* *******
		* *************************
		*
		*/
		$('.transaction_execute').on('click', function () {
			// class_holder = this;
			var trans_id = $(this).parents("#user_profile_form").find("select").val();
			var project_id = '<?= $_GET['search_id'] ?>';
			var display = '<?= $_GET['page_name'] ?>';
			var ta = '<?= $_GET['table_alias'] ?>';
			var dd_id = $(this).attr('id');
			$.ajax({
				method: "GET",
				url: "../application/custom-functions.php",
				dataType: 'json',
				data: {action: "execute_trans", project_id: project_id, display: display, ta: ta, trans_id: trans_id, dd_id: dd_id}
			})
			.done(function (msg) {
				var text_msg = '';
				$.each(msg, function (index, value) {
					text_msg = text_msg + value;
				});
				// console.log(text_msg);
				$('.transBody').html(text_msg);
				$('#transModal').modal('show');
			});//transaction_cancel
		});
		/******
		* **********
		* *******************Transaction Action ,when user Confirms the Transaction
		* *******
		* *************************
		*
		*/
		$(document).on('click', '.transaction_confirmation', function () {
			var impData = $(this).parents('#transModal').find(".insertRecord").val();
			$.ajax({
					method: "GET",
					url: "../application/custom-functions.php",
					data: {action: "confirm_trans", impData: impData}
			})
			.done(function (msg) {
				if (msg == 'inserted') {
					$('.transBody').html("<p class='transSuccess'><?= transSuccess ?></p>");
					setTimeout(function () {
						$('#transModal').modal('hide');
					}, 2000);
					}else{
					$('.transBody').html("<p class='transFail'><?= transFail ?></p>");
					setTimeout(function () {
						$('#transModal').modal('hide');
					}, 2000);
				}
			});
		});
	});
	function limitIsFull(){
		alert("Maximum records limit reach, You can not add more records");
	}

	function listFilterChange(e){
		dict_id = $(e).data('dd');
		value = $(e).val();
		$.ajax({
			method: "GET",
			url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
			data: {dict_id_to_apply_filter: dict_id, selected_filter: value,check_action: "set_list_filter"}
		})
		.done(function (msg) {
				location.reload();
		});
	}

	function listViewChange(e){
		dict_id = $(e).attr('name');
		value = $(e).val();
		$.ajax({
			method: "GET",
			url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
			data: {dict_id_to_apply_filter: dict_id, selected_filter: value,check_action: "set_list_view"}
		})
		.done(function (msg) {
				location.reload();
		});
	}

	function addnewOption(e){
		if($(e).find('option:selected').val()!='Add NEW'){
			return;
		}
		var name = $(e).attr('name');
		var table = $(e).data('table');
		var label = $(e).data('label');
		var keyField = $(e).data('key');
		var primaryvalue = $(e).data('primaryvalue');
		var inputFieldes = $(e).data('inputfields');
		$('#addOptionModel .modal-header .modal-title').html('');
		$('#addOptionModel .modal-header .modal-title').html('Add '+label);

		inputFieldes = inputFieldes.split(',');
		$('#addOptionModel .modal-body').html('');
		inputFieldes.forEach (function(value){
			if(value!==''){
				$('#addOptionModel .modal-body').append($('<input>', {type: 'text',name:value,placeholder:value}));
			}
		});
		$('#addOptionModel .modal-body').append($('<input>', {type: 'hidden',id: 'selectedTable',value:table}));
		$('#addOptionModel .modal-body').append($('<input>', {type: 'hidden',id: 'selectedKeyField',value:keyField}));
		$('#addOptionModel .modal-body').append($('<input>', {type: 'hidden',id: 'selectedinputFieldes',value:inputFieldes}));
		$('#addOptionModel .modal-body').append($('<input>', {type: 'hidden',id: 'selectedprimaryvalue',value:primaryvalue}));
		 $('#addOptionModel .modal-body').append($('<input>', {type: 'hidden',id: 'selectedName',value:name}));
		$('#addOptionModel').modal('show');

	}

	function AddOptionInTable(){
		var table = $('#selectedTable').val();
		var selectedKeyField = $('#selectedKeyField').val();
		var selectedinputFieldes = $('#selectedinputFieldes').val();
		var selectedprimaryvalue = $('#selectedprimaryvalue').val();
		var name = $('#selectedName').val();
		var data ={};
		$("#addOptionModel .modal-body input[type=text]").each(function(){
				 data[this.name] = this.value;
		});
		$.ajax({
				method: "GET",
				url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
				data: {table: table, selectedKeyField: selectedKeyField, selectedprimaryvalue: selectedprimaryvalue, data: data,'check_action':'adding_new_options'}
}).done(function (returnUrl) {
	var mystring = '';
	if(returnUrl > 0){
		Object.keys(data).forEach(function(key) {mystring = mystring+ data[key]+' ';});
		$("[name='"+name+"']").append($('<option>', {
		    value: returnUrl,
		    text:mystring
		}));
			$('#addOptionModel').modal('hide');
	}else{
		alert('Some Error accourd');
	}
});
	}
	function sendMessaeg(){
		var reciverId = 	$('#message_reciver_id').val();
		var message = 	$('#sendMessageModalText').val();
		var table  = $('#message_log_table').val();
		$.ajax({
				method: "GET",
				url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
				data: {reciverid: reciverId, message: message,table:table,check_action:'contact_me'}
		}).done(function (returnUrl) {
				$('#sendMessageModal').modal('hide');
		});
	}
	/*
	*
	* Contact me ICONS CODE GOES HERE****************
	* ************************************
	* *****************************************************
	* ********************************************************************
	*/
	$(".contact_me_icon").click(function (e) {
		var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
		$('#message_reciver_id').val(fffr_search_id);
		$('#message_log_table').val($(this).data('table'));
		$('#sendMessageModalText').text('');
		$('#sendMessageModal').modal('show');
	});

	function setStyleOfFFFRafterAction(element,result,type){
		var action_type = $('#'+type+'_type').val();
		var selected = $('#'+type+'_selected').val();
		var unselected = $('#'+type+'_unselected').val();

		if(action_type=='text'){
			if (result == 'deleted') {
				$(element).text(selected);
				} else {
				$(element).text(unselected);
			}
		}else{
			if (result == 'deleted') {
				$(element).removeClass(unselected);
				$(element).addClass(selected);
			}else{
				$(element).removeClass(selected);
				$(element).addClass(unselected);
			}
		}

	}
</script>
<?php

  echo "<div style='height:25px'></div>";
	Footer($page_name,'footer2');
?>
