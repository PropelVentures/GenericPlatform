<?php
function renderListView($isExistFilter, $isExistField, $row, $tbQry, $list,$qry,$list_pagination,$tab_anchor)
{
	// $row['list_select'] = 'http://genericsandbox8.cjcornell.net/system/main-loop.php?dict_id=31&checkFlag=true&table_type=parent&edit=true&page_name=myproduct&ComponentOrder=1&table_alias=products&search_id=76#Products;product_child,1,product_child_page';// 'products,1,myproduct;product_child,1,product_child_page' http://genericsandbox8.cjcornell.net/system/main-loop.php?dict_id=31&checkFlag=true&table_type=parent&edit=true&page_name=myproduct&ComponentOrder=1&table_alias=products&search_id=76#Products
	// var_dump($row);
	$con = connect();
	
	/**
	 * Default values for datatable rendering
	 * These values will render paginated datatable
	 * and enble searching on the datatable
	 * 
	 * These defaults represent values from `list_pagination column `data_dictionary`
	 */
	$list_pagination = array_merge([
		'itemsperpage' => 9,
		'pageselector' => 'ON',
		'hscroll' => 'ON',
		'vscrollbar' => 'ON',
		'searchbox' => 'ON',
		'pagedropdown' => 'ON',
	], $list_pagination);
	
	//styling of data table
	//*
	//*
	$list_pagination['paging'] = hasUppercaseValue($list_pagination['pageselector'], 'ON') ? 'true' : 'false';
	$list_pagination['scrollX'] = hasUppercaseValue($list_pagination['hscroll'], 'ON') ? 'true' : 'false';
	$list_pagination['scrollY'] = hasUppercaseValue($list_pagination['vscrollbar'], 'ON') ? '500' : 'false';
	$list_pagination['scrollCollapse'] = hasUppercaseValue($list_pagination['vscrollbar'], 'ON') ? 'true' : 'false';
	$list_pagination['lengthChange'] = hasUppercaseValue($list_pagination['pagedropdown'], 'ON') ? 'true' : 'false';
	$list_searching = hasUppercaseValue($list_pagination['searchbox'], 'ON') ? 'true' : 'false';

	$scroll_both = 'false';
	if(isset($list_pagination['hscrollbar']) && strtoupper($list_pagination['hscrollbar'])=='TOP'){
		echo "<style>
		#table_".$row['dict_id']."_wrapper .dataTables_scrollBody {
	    transform:rotateX(180deg);
	}";
	echo "#table_".$row['dict_id']."_wrapper .dataTables_scrollBody table {
	    transform:rotateX(180deg);
	}</style>";
	}elseif(isset($list_pagination['hscrollbar']) && strtoupper($list_pagination['hscrollbar'])=='BOTH'){
		$scroll_both = 'true';
		echo "<style>
		#table_".$row['dict_id']."_wrapper .dataTables_scrollBody {
	overflow-y: visible !important;
	overflow-x: initial !important;
	}</style>";
	}

	// for checklist in listviews
	$listView = trim($row['list_views']);
	$list_views = listvalues($row['list_views']);

	$list_sort = trim($row['list_sort']);
	$list_select = trim($row['list_select']);
	$dd_css_class = $row['dd_css_class'];
    $keyfield = firstFieldName($row['table_name']);
    $table_type = trim($row['table_type']);
	$component_type = trim($row['component_type']);
    $table_name = trim($row['table_name']);
    $list_fields = trim($row['list_fields']);
    $dict_id = $row['dict_id'];
	$page_name = $row['page_name'];
	$display_id ='#'.$page_name . $dict_id.' .clearFunction';
	$list_select_arr = getListSelectParams($list_select);
	$column_widths_array = [];
	$column_widths_array_with_name = [];
	$stripTags = isStripHtmlTags($row['list_extra_options']);

	$targetUrlParts = parse_url(BASE_URL_SYSTEM . 'main-loop.php');

	// Params for target url which is triggered when a row is clicked
	$targetUrlParts['query'] = [
		'dict_id' => $row['dict_id'],
		'checkFlag' => 'true',
		'table_type' => $table_type,
		'edit' => 'true',
	];

	$targetUrlParts['query']['page_name'] = $list_select_arr[0][2];
	$targetUrlParts['query']['ComponentOrder'] = $list_select_arr[0][1];
	$targetUrlParts['query']['table_alias'] = $list_select_arr[0][0];
	
	$dd_row = $row;

	$list_sort = array_filter( array_map('trim', explode(',', $dd_row['list_sort'])) );
	if (count($list_sort) >= 1 && !empty($row['list_sort'])) {
		$show_sort = false;
		$tbl = $row['table_alias'];
		foreach ($list_sort as $val) {
			$q = $con->query("select field_label_name from field_dictionary where generic_field_name='$val' and table_alias='$tbl'");
			$fdField = $q->fetch_assoc();
			if( $fdField[field_label_name] != "" ) {
				$show_sort = true;
				break;
			}
		}
		if($show_sort) {
        ?>
		<h3>Sort by </h3>
		<span>
			<div class="btn-group select2 listview">
				<button type="button" class="select_btn btn btn-danger main-select2 dropdown-toggle" data-toggle="dropdown" id="sort_popular_users_value">	---Select----</button>
				<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
					<span class="sr-only">Toggle navigation</span>
				</button>
				<ul class="dropdown-menu" role="menu" id="sort_popular_users">
					<?php
					$tbl = $row['table_alias'];
					if (!empty($_GET['sort']) && empty($_GET['orderFlag'])) {
						$orderFlag = 'down';
					} else if (!empty($_GET['sort']) && $_GET['orderFlag'] == 'down') {
						$orderFlag = 'up';
					} else if (!empty($_GET['sort']) && $_GET['orderFlag'] == 'up') {
						$orderFlag = 'down';
					}
					$order = "<span class='glyphicon glyphicon-chevron-up'></span>";
					foreach ($list_sort as $val) {
						if ($val != $_GET['sort']) {
							$order = "<span class='glyphicon'></span>";
						} else {

							switch ($orderFlag) {
								case 'up':
									$order = "<span class='glyphicon glyphicon-chevron-up'></span>";
									break;
								case 'down':
									$order = "<span class='glyphicon glyphicon-chevron-down'></span>";
									break;
								default:
									$order = "<span class='glyphicon'></span>";
							}
						}
						$q = $con->query("select field_label_name from field_dictionary where generic_field_name='$val' and table_alias='$tbl'");

						$fdField = $q->fetch_assoc();
						if( $fdField[field_label_name] != "" ) {
							echo "<li id='sort-li' class='sorting-li' data-value='$val'>
									<a>$fdField[field_label_name]$order</a>
								</li>";
						}
					}
					?>
				</ul>
			</div>
		</span>
		<?php
		}
    }

	?>

	<input type='button' onclick='clearFunction()' id='test' value='X' class='clearFunction'>
	<!--<table id='table_<?php //echo $dict_id;?>' class='display nowrap compact' cellspacing='0' width='100%'>-->
	<div class="table-responsive">
		<table id='table_<?php echo $dict_id;?>' class='display nowrap compact clear1 <?=$dd_css_class ?>' cellspacing='0' width='100%'>
		<!--Code Changes for Task 5.4.77 End-->
			<thead>
				<tr class='tr-heading'>
					<th class='tbl-action'><span style='visibility:hidden;'>12<span></th>
					<?php $tbRs = $con->query($tbQry);
					$sort_index = 0;
					$count = 0;
					while ($tbRow = $tbRs->fetch_assoc())
					{
						// if ($tbRow['ignore_in_lists'] == 1 || $tbRow['format_type'] == 'line_divider' || $tbRow['format_type'] == 'new_line') {
						// 	continue;
						// }
						if (itemHasVisibility($tbRow['visibility']) && itemHasPrivilege($tbRow['privilege_level']) && $tbRow['format_type'] != 'list_fragment'){
							//Code Change for Task 5.4.22 Start
							if($tbRow['ignore_in_lists'] != 1){
								$count++;
								if(isset($row['list_sort']) && !empty($row['list_sort'])){
									$list_sort = explode('-',$row['list_sort']);
									if(isset($list_sort[0]) && !empty($list_sort[0])){
										$sort_parameter = $list_sort[0];
										$sort_order = 'asc';
									}else{
										$sort_parameter = $list_sort[1];
										$sort_order = 'desc';
									}
									if($tbRow['generic_field_name'] == $sort_parameter){
											$sort_index=$count;
									}
								}
								// $colStyle = '';
								// if(!empty(trim($tbRow['format_length']))){
								// 		$colWidth = explode(',',trim($tbRow['format_length']));
								// 		$colWidth = $colWidth[0];
								// 		if(!empty($colWidth) &&  $colWidth>100){
								// 			$colWidth=$colWidth.'px';
								// 			$colStyle = "style='width:$colWidth'";
								// 		}else{
								// 			// $colStyle = "style='width:100px'";
								// 		}
								// }
								$colWidth = listColumnWidth($tbRow);
								$$column_widths_array_with_name[$tbRow['generic_field_name']] = $colWidth;
								$column_widths_array[$count] ='"'.$colWidth.'px"';
								//Code Change for Task 5.4.22 End
								/*Rashid Format Length Start*/
								$fieldValue = format_field_value_length($tbRow, $tbRow['field_label_name'] );
								$fieldValue = $fieldValue['fieldValue'];
								/* Rashid Format Length Over */
							?>
								<th class="<?= $tbRow['generic_field_name']; ?>"> <?= $fieldValue; ?></th>
							<?php
							//Code Change for Task 5.4.22 Start
							}
							//Code Change for Task 5.4.22 End
						}
					} ?>
				</tr>
			</thead>
			<tbody>
				<?php
				
					if ($list->num_rows > 0)
					{
						$i = 0;
						$count = 1;

						preg_match_all('!\d+!', $list_pagination['totalpages'], $limitPage);
						$no_of_pages = $limitPage[0][0];
						$limit = $limitPage[0][0] * $list_pagination['itemsperpage'];

						if (isset($list_pagination['totalitems']) && !empty(trim($list_pagination['totalitems'])))
						{
							$limit = trim($list_pagination['totalitems']);
						}
						$list_pagination['totalpages'] = '#' . ceil($list_pagination['totalitems']/ $list_pagination['itemsperpage']);

						while ($listRecord = $list->fetch_assoc())
						{
							if ($count > $limit)
							{
								break;
							}

							if (!isFileExistFilterFullFillTheRule($listRecord,$isExistFilter,$isExistField))
							{
								continue; //break;
							}

							$_SESSION['list_pagination'] = array($list_pagination[0],$no_of_pages);
							$rs = $con->query($qry);

							if(!empty($list_select) || $table_type == 'child') {
								if (strpos($list_select, '()')) {
									exit('function calls');
								// } else if (strpos($list_select, '.php')) { // TODO: Is this needed?
									// exit('php file has been called');
								} else {
									$nav = $con->query("SELECT * FROM navigation where target_page_name='$_GET[page_name]'");
									$navList = $nav->fetch_assoc();

									//  DD OVERHAUL 2-18-2020 ... Here is where we can get into trouble replacing table_type with component_type
									//  CJ:  I added component type in the blocks below instead of replacing table type
									/// Extracting action ,when user click on edit button or on list
									
									// TODO: Is this condition block needed anymore?
									if (isset($list_select_arr[0]) && !empty($list_select_arr[0])) {
										if (count($list_select_arr[0]) == 2) {
											//$target_url = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&dict_id=" . $dict_id . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&table_alias=" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;   // &"component_type=" . $component_type;
											$target_url = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&dict_id=" . $row['dict_id'] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&table_alias=" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;   // &"component_type=" . $component_type;
											// add button url
											//$_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&dict_id=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias" . $list_select_arr[0][0] . "&table_type=" . $table_type;    // &"component_type=" . $component_type;
											$_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&dict_id=" . $row['dict_id'] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias" . $list_select_arr[0][0] . "&table_type=" . $table_type;    // &"component_type=" . $component_type;
										} else {
											//$target_url = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&dict_id=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&table_alias=" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;   // &"component_type=" . $component_type;
											$target_url = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&dict_id=" . $row['dict_id'] . "&ComponentOrder=" . $list_select_arr[0][1] . "&table_alias=" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;   // &"component_type=" . $component_type;
											/// add button url
											//$_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&dict_id=" . $list_select_arr[0][0] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias" . $list_select_arr[0][0] . "&table_type=" . $table_type;  // &"component_type=" . $component_type;
											$_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main-loop.php?page_name=" . $list_select_arr[0][2] . "&dict_id=" . $row['dict_id'] . "&ComponentOrder=" . $list_select_arr[0][1] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&table_alias" . $list_select_arr[0][0] . "&table_type=" . $table_type;  // &"component_type=" . $component_type;
										}
									}
									
									/// Extracting action, when user click on boxView Image of list
									if (isset($list_select_arr[1][0]) && !empty($list_select_arr[1][0])) {
										if (count($list_select_arr[1]) == 2) {
											// TODO: Where is this nedded?
											$target_url2 = BASE_URL_SYSTEM 
											. $navList['item_target'] 
											. "?page_name=" 
											
											. $list_select_arr[1][2] 
											. "&dict_id=" 
											. $list_select_arr[1][0] 
											. "&table_alias" 
											. $list_select_arr[1][0] 
											. "&ComponentOrder=" 
											. $list_select_arr[1][1] 

											. "&style=" 
											. $navList['nav_css_class'] 
											. "&search_id=" 
											. $listRecord[$keyfield] 
											. "&checkFlag=true&edit=true&fnc=onepage";
 											//. "&layout=". $navList['page_layout_style'] 											
										} else {
											// TODO: Where is this nedded?
											$target_url2 = BASE_URL_SYSTEM 
											. "main-loop.php?page_name=" 
											. $list_select_arr[1][2] 
											. "&dict_id=" 
											. $list_select_arr[1][0] 
											. "&table_alias" 
											. $list_select_arr[1][0] 
											. "&ComponentOrder=" 
											. $list_select_arr[1][1] 
											. "&search_id=" 
											. $listRecord[$keyfield] 
											. "&checkFlag=true&edit=true&fnc=onepage";
										}
									}
									$targetUrlParts['query']['search_id'] = $listRecord[$keyfield];
									$targetUrlParts['fragment'] = $tab_anchor;
									$target_url = buildUrl($targetUrlParts);
									?>
									<tr id="<?php echo $target_url; ?>" class="boxview-tr">
										<td class='dt-body-center'>
											<?php
												$checkbox_id = $listRecord[$_SESSION['update_table']['keyfield']];
												/*
												 * displaying checkboxes
												 * checking in database if checklist is there
												 */
												if ($list_views['checklist'] == 'true')
												{
													?>
													<span class='span-checkbox'><input type='checkbox'  name='list[]'  value='<?= $checkbox_id; ?>' class='list-checkbox tabholdEvent' style='margin:right:6px;'/></span>
													<input type='hidden' name='dict_id[]' value='<?php echo $dict_id; ?>'>
													<?php
												}
											?>
											<span class='list-del' id='<?php echo $checkbox_id; ?>' name='<?php echo $dict_id; ?>'></span>
										</td>
										<?php
								}
											/*
											 *
											 *
											 * ******
											 * *************************
											 * ******************************
											 * ***********************************FETCHING DATA AND PUTING IN CORRESPONDING TDS
											 * **************
											 * *************************
											 * ******************************************************************
											 *
											 */
											/////table view starts here
											//fetching data from corresponding table
											while ($row12 = $rs->fetch_assoc())
											{

												// Code Change for Task 5.4.22 Start
												// if ($row12['ignore_in_lists'] == 1 || $row12['format_type'] == 'line_divider' || $row12['format_type'] == 'new_line') {
												// 	continue;
												// }
												$flag = false;
												if ($row12['ignore_in_lists'] != 1) {
													$flag = true;
													$fieldValue = $listRecord[$row12[generic_field_name]];
												} else {
													$flag = false;
												}
													//$fieldValue = $listRecord[$row[generic_field_name]];
													//Code Change for Task 5.4.22 End
												if (!empty($row12[dropdown_alias])) {
													$fieldValue = dropdown($row12, $urow = 'list_display', $fieldValue);
												}
												if ($stripTags) {
													$fieldValue = strip_tags($fieldValue);
												}
													//edit by Akshay S (2019-Aug-04 19:00 IST)
													//function 'truncateLongDataAsPerAvailableWidth' commented to fix truncation of text fields (Task ID: 8.3.404)

													//truncating the lengths of data
													//truncateLongDataAsPerAvailableWidth($fieldValue,$$column_widths_array_with_name[$row[generic_field_name]]);
													//Code Change for Task 5.4.22 Start


												/*Rashid Format Length Start*/
												$fieldValue = format_field_value_length($tbRow, $fieldValue );
												$fieldValue = $fieldValue['fieldValue'];
												/* Rashid Format Length Over */
												if ($flag == true) {
														//Code Change for Task 5.4.22 End
													if (itemHasVisibility($row12['visibility']) && itemHasPrivilege($row12['privilege_level']) && $row12['format_type'] != 'list_fragment') {
														?>
														<td><?php echo $fieldValue; ?></td>
														<?php
													}
														//Code Change for Task 5.4.22 Start
												}
													//Code Change for Task 5.4.22 End
											}
											
											if ($table_type == 'child') {
												$_SESSION['child_return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
											} else {
												$_SESSION['return_url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
											}
										} /// end of mainIF
										?>
									</tr>
									<?php
										$count++;
						}
					}
				?>
			</tbody>
		</table>
	</div>
	<?php

		global $popup_menu;
		if ($popup_menu['popupmenu'] == 'true') {
			$popup_menu['popup_menu_id'] = "popup_menu_$dict_id";
			$_SESSION['popup_menu_array'][] = $popup_menu;
		}


	?>
	<script>
	<?php $page_no = $list_pagination['itemsperpage']; ?>
		var dtble = $('#table_<?php echo $dict_id;?>').DataTable({
			pageLength: <?php echo $page_no; ?>,
  			scrollX: <?php echo $list_pagination['scrollX'];?>, // true
			paging: <?php echo $list_pagination['paging'];?>, // true
			scrollY: <?php echo $list_pagination['scrollY'];?>, // 500
			scrollCollapse: <?php echo $list_pagination['scrollCollapse'];?>, // true
			sScrollX: "100%",
			searching: <?php echo $list_searching;?>, // true
			lengthChange: <?php echo $list_pagination['lengthChange'];?>, // true
			pagingType: 'full_numbers',
			lengthMenu: <?php if($page_no!='ALL') { ?> [[<?php if(!empty($page_no)){echo $page_no.','.(2*$page_no).','.(3*$page_no).','.(4*$page_no);}else{ echo "10,25,50,100";}?>],[<?php if(!empty($page_no)){echo $page_no.','.(2*$page_no).','.(3*$page_no).','.(4*$page_no);}else{ echo "10,25,50,100,'ALL'";}?>]] <?php }else { ?> [ [10, 25, 50, -1], [10, 25, 50, "ALL"] ] <?php } ?>,
			order: [<?php echo $sort_index; ?>, <?php echo "'" . $sort_order . "'"; ?>],
			columnDefs:[
				<?php foreach ($column_widths_array as $key => $value) { ?>
					{ "width": <?php echo $value?>, "targets": "<?php echo $key?>" },
				<?php } ?>
			],
			"bInfo" : false
		});

		//Fixing the bug for default pagination values for the datatable//
		<?php $page_no = empty($page_no)?10:$page_no; ?>

		setTimeout(function() {;
			$('select[name="table_<?php echo $dict_id;?>_length"]').val(<?= "'".($page_no=="ALL"?'-1':$page_no)."'" ?>);
			$("select[name=table_<?php echo $dict_id;?>_length]").trigger('change');
		}, 1000);

		/* Sorting function on SORT button click */

		$(".listview .sorting-li").click(function() {

			var sorting_var = $(this).data("value");
			var clicked_element = $(this);
			var table = $(this).parents(".start_render_view").find(".dataTables_scrollHead table .tr-heading");
			var index = 0;

			table.find("th").each(function() {
				//  console.log(sorting_var);
				if( $(this).hasClass(sorting_var) ) {
					var ordertype = "asc";
					if( $(this).hasClass('asc') ) {
						$(this).removeClass('asc');
						ordertype = "desc";
						clicked_element.parents(".listview").removeClass("sorted-asc");
					}
					else {
						$('.asc').removeClass('asc');
						$(this).addClass('asc');
						ordertype = "asc";
						clicked_element.parents(".listview").addClass("sorted-asc");
					}
					dtble.order([index, ordertype]).draw();

					clicked_element.parents(".listview").find(".select_btn").text(clicked_element.text());

					return;
				}
				index++;
			});
			//dtble.order([2, "desc"]).draw();
		});

		/*if( $(".listview").length > 0) {
			if(  ) {

			}
		}*/


		$(".listview .sorting-li:first").click();
		/* action perform by list_select button on click */
		/*
			* *****
			* *************
			* ******************MAKING TR CLICKABLE AND TAKIGN TO EDIT PAGE........
			* ........................
			* ............................................
			* .......................................................
		*/
		$('#table_<?php echo $dict_id;?> tbody').on('click', 'tr td:not(:first-child)', function () {
			event.stopImmediatePropagation();
			if ($(this).hasClass('tabholdEvent')) {
				return false;
				} else {
				window.location = $(this).parent().attr('id');
			}
		});
		if(!(<?php echo $list_searching ?>)){
			$(' <?php echo $display_id;?>').hide();
		}

		if(<?php echo $scroll_both?>){
			$('body').find('.dataTables_scrollBody').wrap('');
			$('#scroll_div').doubleScroll();
		}
		/* $('#table_<?php echo $dict_id;?>').on('click', 'tbody tr td:not(:first-child)', function () {
			if ($(this).hasClass('tabholdEvent')) {
			return false;
			} else {
			window.location = $(this).attr('id');
			}
		}); */
		/*
			* it calls when right click on single line list
		*/
		var popup_del;
		var dict_id;
		// Trigger action when the contexmenu is about to be shown
		/// it will be shown for boxView
		if (mobileDetector().any()) {
			$("#table_<?php echo $dict_id;?> tbody").on("taphold", 'tr', function (event) {
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
			////context MEnu will be shown for TableView
			$("#table_<?php echo $dict_id;?> tbody").on("contextmenu", 'tr', function (event) {
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
	</script>
<?php
}

function hasUppercaseValue($holder, $value) {
	if (!isset($holder)) {
		return false;
	}
	return strtoupper($holder) == $value;
}

function buildUrl(array $elements) {
    $e = $elements;
    return
        (isset($e['host']) ? (
            (isset($e['scheme']) ? "$e[scheme]://" : '//') .
            (isset($e['user']) ? $e['user'] . (isset($e['pass']) ? ":$e[pass]" : '') . '@' : '') .
            $e['host'] .
            (isset($e['port']) ? ":$e[port]" : '')
        ) : '') .
        (isset($e['path']) ? $e['path'] : '/') .
        (isset($e['query']) ? '?' . (is_array($e['query']) ? http_build_query($e['query'], '', '&') : $e['query']) : '') .
        (isset($e['fragment']) ? "#$e[fragment]" : '')
    ;
}