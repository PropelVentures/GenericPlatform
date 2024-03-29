<?php
function renderBoxView($isExistFilter,$isExistField,$row , $tbQry ,$list ,$qry ,$list_pagination, $tab_anchor, $component_order, $imageField, $ret_array){
	$con = connect();
	$list_select = trim($row['list_select']);
	$dd_css_class = $row['dd_css_class'];
	$css_style = trim($row['dd_css_code']);
	$keyfield = firstFieldName($row['table_name']);
	$component_type = trim($row['component_type']);
	$table_type = trim($row['table_type']);
	$table_name = trim($row['table_name']);
	$list_fields = trim($row['list_fields']);
	$dict_id = $row['dict_id'];
	$style_refrence_configs = false;
	$category_styles = false;
	$style_refrence_configs = setBoxStyles($row['list_extra_options']);
	if($style_refrence_configs !== false){
		$category_styles = findAndSetCategoryStyles($con,$style_refrence_configs);
	}

	/*Rashid Code Start*/

	$list_sort = $row['list_sort'];
	$list_sort = array_filter( array_map('trim', explode(',', $row['list_sort'])) );

	/*Rashid End*/

	$list_select_arr = getListSelectParams($list_select);
	
	?>
	<div class="boxViewContainer <?php echo (!empty($dd_css_class) ? $dd_css_class : '') ?>" id='content<?php echo $component_order; ?>'>
		<!-- the input fields that will hold the variables we will use -->
		<input type='hidden' class='current_page' />
		<input type='hidden' class='show_per_page' />
		<?php
		if ($list->num_rows > 0) {
			$i=0;
			$count = 1;
			preg_match_all('!\d+!', $list_pagination['totalpages'], $limitPage);
			$no_of_pages = $limitPage[0][0];
			$limit = $limitPage[0][0] * $list_pagination['itemsperpage'];

			if(isset($list_pagination['totalitems']) && !empty(trim($list_pagination['totalitems']))){
				$limit = trim($list_pagination['totalitems']);
			}
			if(!isset($list_pagination['totalpages']) || isset($list_pagination['totalitems'])){
				$list_pagination['totalpages'] = '#' . ceil($list_pagination['totalitems']/ $list_pagination['itemsperpage']);
			}
			while ($listRecord = $list->fetch_assoc()) {
				$itemId = $listRecord[$row['generic_field_name']];

				if($count > $limit){
					break;
				}

				if(!isFileExistFilterFullFillTheRule($listRecord,$isExistFilter,$isExistField)){
					continue; //break;
				}

				$_SESSION['list_pagination'] = array($list_pagination[0],$no_of_pages);
				$rs = $con->query($qry);
				$boxStyleClass = '';$boxStyleCode = '';
				if($category_styles!==false && isset($category_styles[$listRecord[$style_refrence_configs['field']]])){
					$boxStyleClass = $category_styles[$listRecord[$style_refrence_configs['field']]]['class'];
					$boxStyleCode = $category_styles[$listRecord[$style_refrence_configs['field']]]['code'];
				}
				$data = "";
				if (is_array($list_sort) && !empty($list_sort) ) {
					/*echo "<div class='hidden-sorts'>";*/
					foreach( $list_sort as $lsort ){
						if( ltrim($lsort,"-") != "" ) {
							//$data .= "<input type='hidden' class='".$lsort."' value='".$listRecord[$lsort]."' > ";
							$data .= "data-".$lsort." = ".$listRecord[$lsort]." ";
						}
					}
					/*echo "</div>";*/
				}
				?>
				<div style="<?=  $boxStyleCode.$css_style ?>" class="boxView <?php echo (!empty($dd_css_class) ? $dd_css_class : '') ?> $boxStyleClass" data-scroll-reveal="enter bottom over 1s and move 100px" <?php echo $data; ?> >
						<input type='hidden' id='<?php echo $itemId; ?>' name='<?php echo $dict_id; ?>' class='list-del' />
					<?php

					if (!empty($list_select) || $table_type == 'child') {
						if (strpos($list_select, '()')) {
							exit('function calls');
						} elseif (strpos($list_select, '.php')) {
							exit('php file has been called');
						} else {
							$nav = $con->query("SELECT * FROM navigation where target_page_name='$_GET[page_name]'");
							$navList = $nav->fetch_assoc();
							/// Extracting action ,when user click on edit button or on list
							if (isset($list_select_arr[0]) && !empty($list_select_arr[0])) {
								if (count($list_select_arr[0]) == 2) {
									$target_url = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&table_alias" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type."&component_type=" . $component_type;
									/// add button url
									$_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias" . $list_select_arr[0][0] . "&table_type=" . $table_type."&component_type=" . $component_type;
								} else {
									$target_url = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&table_alias" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type."&component_type=" . $component_type;
									/// add button url
									$_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&table_alias=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias" . $list_select_arr[0][0] . "&table_type=" . $table_type."&component_type=" . $component_type;
									
								}
							}
							/// Extracting action, when user click on boxView Image of list
							if (isset($list_select_arr[1][0]) && !empty($list_select_arr[1][0])) {
								if (count($list_select_arr[1]) == 2) {
									$target_url2 = BASE_URL_SYSTEM . $navList['item_target'] . "?page_name=" . $list_select_arr[1][2] . "&table_alias=" . $list_select_arr[1][0] . "&table_alias" . $list_select_arr[1][0] . "&ComponentOrder=" . $list_select_arr[1][1] . "&style=" . $navList['nav_css_class'] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&edit=true&fnc=onepage";
								} else {
									$target_url2 = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[1][2] . "&table_alias=" . $list_select_arr[1][0] . "&table_alias" . $list_select_arr[1][0] . "&ComponentOrder=" . $list_select_arr[1][1] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&edit=true&fnc=onepage";
								}
							}
						}
					}

					/*
					 * @while loop
					 * get FD info and put data into @lisdata array
					 */
					$listData = array();
					while ($row = $rs->fetch_assoc()) {
						//Code Change for Task 5.4.22 Palak Start
						$flag = false;
								if($row['ignore_in_lists'] != 1){
									$flag = true;
									$row['generic_field_name'] =  trim($row['generic_field_name']);
								}else{
									$flag = false;
								}
						//$row['generic_field_name'] =  trim($row['generic_field_name']);
						if($flag == true){
						//Code Change for Task 5.4.22 Palak End
						$row['generic_field_name'] =  trim($row['generic_field_name']);
						if(itemHasVisibility($row['visibility']) && itemHasPrivilege($row['privilege_level'])){
							$colWidth = listColumnWidth($row);
							$tmpData = array();
							$tmpData['data_length']  = $colWidth;
							$tmpData['field_style'] = $row['field_style'];
							$tmpData['field_name'] = $row['generic_field_name'];
							$tmpData['field_value'] = strip_tags($listRecord[$row['generic_field_name']]);
							if(!empty($tmpData['field_value'])){
								$listData[] = $tmpData;
							}
						}
						 //Code Change for Task 5.4.22 Palak Start
						}
						//Code Change for Task 5.4.22 Palak End
					}
					/*
					 * @listViews function
					 *
					 * give bOX LIST UI and data inside lists
					 */
					listViews($boxStyleCode,$boxStyleClass,$listData, $component_type, $table_type, $target_url, $imageField, $listRecord, $keyfield, $target_url2, $tab_anchor, $ret_array['users'], $list_select_arr); ///boxview ends here
					?>
				</div>
			<?php
			$count++;
			}
			/*
			 *
			 * Pagination Function goes here
			 */
			 if(isset($list_pagination['hscroll']) && strtoupper($list_pagination['hscroll']) == 'ON'){
 				boxViewHscroll($list_pagination, $component_order, $list_select_arr);
 			} else {
 				echo boxViewPagination($list_pagination, $component_order, $list_select_arr);
 			}
			global $popup_menu;
			if ($popup_menu['popupmenu'] == 'true') {
				$popup_menu['popup_menu_id'] = "popup_menu_$dict_id";
				$_SESSION['popup_menu_array'][] = $popup_menu;
			}?>
			<script>
			if (mobileDetector().any()) {
				$(".boxViewContainer#content<?php echo $component_order;?>").on("taphold", '.boxView', function (event) {
					// alert('X: ' + holdCords.holdX + ' Y: ' + holdCords.holdY );
					var xPos = event.originalEvent.touches[0].pageX;
					var yPos = event.originalEvent.touches[0].pageY;
					$(this).addClass('tabholdEvent');
					popup_del = $(this).find('.list-del').attr('id');
					dict_id = $(this).find('.list-del').attr('name');
					//alert(popup_del);
					// Avoid the real one
					event.preventDefault();
					// Show contextmenu
					$("#popup_menu_<?php echo $dict_id?>.custom-menu").finish().toggle(100).
					// In the right position (the mouse)
					css({
						top: yPos + "px",
						left: xPos + "px"
					});
				});
			} else {
				$(".boxViewContainer#content<?php echo $component_order;?>").on("contextmenu", '.boxView', function (event) {
					popup_del = $(this).find('.list-del').attr('id');
					dict_id = $(this).find('.list-del').attr('name');
					//console.log(dict_id);
					// Avoid the real one
					event.preventDefault();
					// Show contextmenu
					$("#popup_menu_<?php echo $dict_id?>.custom-menu").finish().toggle(100).
					// In the right position (the mouse)
					css({
						top: event.pageY + "px",
						left: event.pageX + "px"
					});
				});
			}

			if($(".boxview-sort.sorting-<?php echo $dict_id;?> .sorting-li").length > 0) {
				$(".boxview-sort.sorting-<?php echo $dict_id;?> .sorting-li").click(function() {
					var dict_id = $(this).data("dict");
					var sort_param = $(this).data("value");
					$.ajax({
						method: "POST",
						url: "ajax-actions.php",
						data: {check_action: 'sort_boxview', dict_id: dict_id, sort_param: sort_param}
					}).done(function (msg) {
						console.log(msg);
					});
				});
			}
		</script>
		<?php } else { ?>
	</div>
		<?php
	}
} ?>
