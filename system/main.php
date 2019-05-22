<?php
	require_once("functions_loader.php");
	include("header.php");

	$display_page = $_GET['display'];

	//log event
	log_event($_GET['display'],'page view');

	$page_layout_style = $_GET['layout'];
	$style = $_GET['style'];
	if (isset($_GET['tab']) || !empty($_GET['tab'])) {
			$_SESSION['tab'] = $_GET['tab'];
	} else {
		$_SESSION['tab'] = '';
		unset($_SESSION['tab']);
	}
	unset($_SESSION['popup_munu_array']);
	//echo($_SESSION['return_url']) . "<br>";
	//exit( $_SESSION['add_url_list']);
	///// copy these two files for displaying navigation/////

	Navigation($display_page,'header');
	Navigation($display_page,'header2');

	$haveParalax = false;
	ShowTableTypeParallaxBanner($display_page,$haveParalax);

	//////////////
	if ($display_page == 'home') {
		include("../system/home-slider.php");
	}

?>
<div class="container main-content-container">
	<?php
		/* CHECKING NAV HAS VISIBILITY  START*/
		if(navHasVisibility()){ ?>
		<!-- Left sidebar content Area -->
		<?php
			/*
				* Finding page layout by DD->tab_num
			*/
			$con = connect();
			Get_Data_FieldDictionary_Record('above',$_SESSION['tab'], $display_page, 'true');
			$rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page'");
			$right_sidebar = $left_sidebar = '';
			$left_sidebar_width = $right_sidebar_width = 0;
			while ($row = $rs->fetch_assoc()) {
				$r1 = explode('w', trim($row['tab_num']));
				if (!empty($r1[1])) {
					if ($r1[0] == 'R1')
					$right_sidebar_width = $r1[1];
					else
					$left_sidebar_width = $r1[1];
				}
				if ($r1[0] == 'R1') {
					$right_sidebar = 'right';
				}
				if ($r1[0] == 'L1') {
					$left_sidebar = 'left';
				}
			}
			/* Nav Body-Left or Body-right Code Start*/
			$navBodyLeftQuery = $con->query("SELECT * FROM navigation where (display_page='$display_page' OR display_page='ALL' ) AND (menu_location='body-left') AND nav_id > 0 AND loginRequired='1' AND (item_number LIKE '%.0' OR item_number REGEXP '^[0-9]$') ORDER BY item_number ASC");
			if($navBodyLeftQuery->num_rows){
				if($left_sidebar ==''){
					$left_sidebar = 'left';
				}
			}
			$navBodyRightQuery = $con->query("SELECT * FROM navigation where (display_page='$display_page' OR display_page='ALL' ) AND (menu_location='body-right') AND nav_id > 0 AND loginRequired='1' AND (item_number LIKE '%.0' OR item_number REGEXP '^[0-9]$') ORDER BY item_number ASC");
			if($navBodyRightQuery->num_rows){
				if($right_sidebar ==''){
					$right_sidebar = 'right';
				}
			}
			/* Nav Body-Left or Body-right Code End*/
			/* Tab TTl1 or Tl2 Start */
			$tabLeftExist = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num LIKE 'S-L%'");
			if($tabLeftExist->num_rows){
				if($left_sidebar ==''){
					$left_sidebar = 'left';
				}
			}
			$tabRightExist = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' AND tab_num LIKE 'S-R%'");
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
			 * Check If middle content exist
			 * If not exist then check the width of aone and asign the other
			 * or if width not exist then divide 50% each
			 */
			$middleContentExist = true;
			$checkMiddleContentQuery = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page'  and tab_num REGEXP '^[0-9]+$' AND tab_num >'0'");
			if($checkMiddleContentQuery->num_rows == 0 ){
				$middleContentExist = false;
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

			/*
			* left sidebar code
			*/
			sidebar($left_sidebar, $both_sidebar, $display_page, $left_sidebar_width);
			/*
				* displaying tab area
			*/
			// $total_width = 0;
			if ($_GET['child_list_active'] == 'isSet')
			echo "<a href='#' class='goBackToParent'>click me</a>";

			if($middleContentExist){
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
		?>
		<!-- Tab Content area .. -->
		<?php
			if (isset($page_layout_style) && ($page_layout_style == 'serial-layout')) {
				serial_layout($display_page, $style);

			} else {
				$rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' and (tab_num='0' OR tab_num ='S-0' OR tab_num ='S-L' OR tab_num='S-R' OR tab_num ='S-C')");
				$row = $rs->fetch_assoc();
				if (!empty($row)) {
					$tab_status = 'true';
					$_SESSION['display2'] = $display_page;
					/* Side Bar Navigation Start*/

					 GetSideBarNavigation($display_page,'body-center');
					/* Side Bar Navigation End*/
					fffr_icons($display_page);

					headersAndSubHeaders($display_page);

					/* Tab Navigation Start*/
					Get_Tab_Links($display_page,'center');

					/* Tab Navigation End*/
					if($middleContentExist){
						// renderHeadersAndSubheaders($display_page);
						Get_Data_FieldDictionary_Record('',$tab, $display_page, $tab_status);
					}
				} else {
					$_SESSION['display2'] = '';
					unset($_SESSION['display2']);
					/* Side Bar Navigation Start*/
					GetSideBarNavigation($display_page,'body-center');
					/* Side Bar Navigation End*/
					fffr_icons($display_page);

					headersAndSubHeaders($display_page);

					echo Get_Links($display_page);
					global $tab;
					$tab_status = 'false';
					if($middleContentExist){
						// renderHeadersAndSubheaders($display_page);
						if (isset($_SESSION['tab'])) {
							Get_Data_FieldDictionary_Record('',$_SESSION['tab'], $display_page, $tab_status);
						} else {
							Get_Data_FieldDictionary_Record('',$tab, $display_page, $tab_status);
						}
						}
				}/// tab_num else ends here
			}//// page_layout



		?>
	<?php if($middleContentExist){ ?>
	<div style="clear:both"></div>
	</div>
	<?php } ?>
	<!-- Right sidebar content Area -->
	<?php
		/*
		* Right sidebar code
		*/
		sidebar($right_sidebar, $both_sidebar, $display_page, $right_sidebar_width);
	} else { ?>
		<div class="center-body-message-box">
			<h2><?php echo ERROR_403; ?></h2>
		</div>
	<?php }
	/* CHECKING NAV HAS VISIBILITY  END*/
?>
</div>
<?php if($haveParalax){?>
</div>
<?php } ?>
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
	if(!empty($_SESSION['popup_munu_array'])){
		foreach($_SESSION['popup_munu_array'] as $popup_menu){
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
				data: {childID: del_id, check_action: "openChild", dict_id: dict_id, display: "<?= $_GET['display']; ?>"}
			})
			.done(function (child_url) {
				if(child_url=='false'){
					alert('Permission Denied');
				}else{
					window.location = child_url;
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
			var tab_name = '<?= $_GET['tab'] ?>';
			var tab_num = '<?= $_GET['tabNum'] ?>';
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {tab_check: "true", tab_name: tab_name, tab_num: tab_num}
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
					// window.location = $(this).attr('href');
					window.location = document.referrer;
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
			class_holder = this;
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {action: "friend_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id')}
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
			class_holder = this;
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {action: "follow_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id')}
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
			// $(this).css('color','red');
			class_holder = this;
			var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {action: "favorite_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id')}
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
			// class_holder = this;
			var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {action: "rate_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id'), value: value}
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
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {action: "rate_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id'), value: 'clear'}
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
			// class_holder = this;
			var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
			var value = $(this).siblings(".fffr-input").val();
			$.ajax({
				method: "GET",
				url: "ajax-actions.php",
				data: {action: "rate_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id'), value: value,ta:'<?= $_GET[tab]?>',tabNum:'<?= $_GET[tabNum]?>'}
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
			var display = '<?= $_GET['display'] ?>';
			var ta = '<?= $_GET['ta'] ?>';
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
<?php include("footer.php"); ?>
