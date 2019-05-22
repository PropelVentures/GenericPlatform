<?php
function renderListView($isExistFilter,$isExistField,$row,$tbQry,$list,$qry,$list_pagination,$tab_anchor){
	$con = connect();
	//styling of data table
	//*
	//*
	if(!isset($list_pagination['pageselector']) || strtoupper($list_pagination['pageselector'])=='ON'){
		$list_pagination['paging'] = 'true';
	}else{
		$list_pagination['paging'] = 'false';
	}

	if(!isset($list_pagination['hscroll']) || strtoupper($list_pagination['hscroll'])=='ON'){
		$list_pagination['scrollX'] = 'true';
	}else{
		$list_pagination['scrollX'] = 'false';
	}

	if(!isset($list_pagination['vscrollbar']) || strtoupper($list_pagination['vscrollbar'])=='ON'){
		$list_pagination['scrollY'] = '500';
		$list_pagination['scrollCollapse'] = 'true';
	}else{
		$list_pagination['scrollY'] = 'false';
		$list_pagination['scrollCollapse'] = 'false';
	}
	if(!isset($list_pagination['searchbox']) || strtoupper($list_pagination['searchbox'])=='ON'){
		$list_searching= 'true';
	}else{
		$list_searching = 'false';
	}

	if(!isset($list_pagination['pagedropdown']) || strtoupper($list_pagination['pagedropdown'])=='ON'){
		$list_pagination['lengthChange'] = 'true';
	}else{
		$list_pagination['lengthChange'] = 'false';
	}
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
    $keyfield = firstFieldName($row['database_table_name']);
    $table_type = trim($row['table_type']);
    $table_name = trim($row['database_table_name']);
    $list_fields = trim($row['list_fields']);
    $dict_id = $row['dict_id'];
	$display_page = $row['display_page'];
	$display_id ='#'.$display_page . $dict_id.' .clearFunction';
	$list_select_arr = getListSelectParams($list_select);
	$column_widths_array = [];
	$column_widths_array_with_name = [];
	$stripTags = isStripHtmlTags($row['list_extra_options']);
	?>

	<input type='button' onclick='clearFunction()' id='test' value='X' class='clearFunction'>
	<!--Code Changes for Task 5.4.77 Start-->
	<!--<table id='table_<?php //echo $dict_id;?>' class='display nowrap compact' cellspacing='0' width='100%'>-->
	<table id='table_<?php echo $dict_id;?>' class='display nowrap compact clear1 <?=$dd_css_class ?>' cellspacing='0' width='100%' style="table-layout: fixed !important;
    word-wrap:break-word;">
	<!--Code Changes for Task 5.4.77 End-->
		<thead>
			<tr class='tr-heading'>
				<th class='tbl-action'><span style='visibility:hidden;'>12<span></th>
				<?php $tbRs = $con->query($tbQry);
				$sort_index = 0;
				$count = 0;
				while ($tbRow = $tbRs->fetch_assoc()) {
					if(itemHasVisibility($tbRow['visibility']) && itemHasPrivilege($tbRow['privilege_level']) && $tbRow['format_type'] != 'list_fragment'){
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

					  ?>
						<th> <?= $tbRow['field_label_name']; ?></th>
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
			if ($list->num_rows > 0) {
				$i=0;
				$count = 1;

				preg_match_all('!\d+!', $list_pagination['totalpages'], $limitPage);
				$no_of_pages = $limitPage[0][0];
				$limit = $limitPage[0][0] * $list_pagination['itemsperpage'];

				if(isset($list_pagination['totalitems']) && !empty(trim($list_pagination['totalitems']))){
					$limit = trim($list_pagination['totalitems']);
				}
				$list_pagination['totalpages'] = '#' . ceil($list_pagination['totalitems']/ $list_pagination['itemsperpage']);

				while ($listRecord = $list->fetch_assoc()) {
					if($count > $limit){
						break;
					}

					if(!isFileExistFilterFullFillTheRule($listRecord,$isExistFilter,$isExistField)){
						break;
					}

					$_SESSION['list_pagination'] = array($list_pagination[0],$no_of_pages);
					$rs = $con->query($qry);
					if (!empty($list_select) || $table_type == 'child') {
						if (strpos($list_select, '()')) {
							exit('function calls');
						} elseif (strpos($list_select, '.php')) {
							exit('php file has been called');
						} else {
							$nav = $con->query("SELECT * FROM navigation where target_display_page='$_GET[display]'");
							$navList = $nav->fetch_assoc();
							/// Extracting action ,when user click on edit button or on list
							if (isset($list_select_arr[0]) && !empty($list_select_arr[0])) {
								if (count($list_select_arr[0]) == 2) {
									$target_url = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&ta=" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;
									/// add button url
									$_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
								} else {
									$target_url = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&ta=" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;
									/// add button url
									$_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
								}
							}
							/// Extracting action, when user click on boxView Image of list
							if (isset($list_select_arr[1][0]) && !empty($list_select_arr[1][0])) {
								if (count($list_select_arr[1]) == 2) {
									$target_url2 = BASE_URL_SYSTEM . $navList['item_target'] . "?display=" . $list_select_arr[1][2] . "&tab=" . $list_select_arr[1][0] . "&ta=" . $list_select_arr[1][0] . "&tabNum=" . $list_select_arr[1][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['item_style'] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&edit=true&fnc=onepage";
								} else {
									$target_url2 = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[1][2] . "&tab=" . $list_select_arr[1][0] . "&ta=" . $list_select_arr[1][0] . "&tabNum=" . $list_select_arr[1][1] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&edit=true&fnc=onepage";
								}
							} ?>
							<tr id="<?php echo $target_url.'&edit=true#'.$tab_anchor; ?>" class="boxview-tr">
								<td class='dt-body-center'>
									<?php $checkbox_id = $listRecord[$_SESSION['update_table']['keyfield']];
									/*
									 * displaying checkboxes
									 * checking in database if checklist is there
									 */
									if ($list_views['checklist'] == 'true') { ?>
										<span class='span-checkbox'><input type='checkbox'  name='list[]'  value='<?= $checkbox_id; ?>' class='list-checkbox tabholdEvent' style='margin:right:6px;'/></span>
										<input type='hidden' name='dict_id[]' value='<?php echo $dict_id; ?>'>
									<?php } ?>
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
						while ($row = $rs->fetch_assoc()) {
							//Code Change for Task 5.4.22 Start
								$flag = false;
								if($row['ignore_in_lists'] != 1){
									$flag = true;
									$fieldValue = $listRecord[$row[generic_field_name]];
								}else{
									$flag = false;
								}
								//$fieldValue = $listRecord[$row[generic_field_name]];
							//Code Change for Task 5.4.22 End
							if (!empty($row[dropdown_alias])) {
								$fieldValue = dropdown($row, $urow = 'list_display', $fieldValue);
							}
							if($stripTags){
								$fieldValue = strip_tags($fieldValue);
							}
							//truncating the lengths of data
							$fieldValue =  truncateLongDataAsPerAvailableWidth($fieldValue,$$column_widths_array_with_name[$row[generic_field_name]]);
							//Code Change for Task 5.4.22 Start
							if($flag == true){
							//Code Change for Task 5.4.22 End
							if(itemHasVisibility($row['visibility']) && itemHasPrivilege($row['privilege_level']) && $row['format_type'] != 'list_fragment'){ ?>

								<td> <?php echo $fieldValue; ?></td>
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
					}/// end of mainIF ?>
							</tr>
				<?php
				$count++;
				}
			} ?>
		</tbody>
	</table>
	<?php
		global $popup_menu;
		if ($popup_menu['popupmenu'] == 'true') {
			$popup_menu['popup_menu_id'] = "popup_menu_$dict_id";
			$_SESSION['popup_munu_array'][] = $popup_menu;
		}


	?>
	<script>
	<?php $page_no = $list_pagination['itemsperpage']; ?>
		$('#table_<?php echo $dict_id;?>').DataTable({
			pageLength: <?php echo $page_no; ?>,
  		scrollX: <?php echo $list_pagination['scrollX'];?>,
			paging: <?php echo $list_pagination['paging'];?>,
			scrollY:<?php echo $list_pagination['scrollY'];?>,
			scrollCollapse: <?php echo $list_pagination['scrollCollapse'];?>,
			// sScrollX: "100%",
			searching: <?php echo $list_searching;?>,
			lengthChange: <?php echo $list_pagination['lengthChange'];?>,
			pagingType: 'full_numbers',
			lengthMenu: <?php if($page_no!='ALL') { ?> [[<?php if(!empty($page_no)){echo $page_no.','.(2*$page_no).','.(3*$page_no).','.(4*$page_no);}else{ echo "10,25,50,100";}?>],[<?php if(!empty($page_no)){echo $page_no.','.(2*$page_no).','.(3*$page_no).','.(4*$page_no);}else{ echo "10,25,50,100,'ALL'";}?>]] <?php }else { ?> [ [10, 25, 50, -1], [10, 25, 50, "ALL"] ] <?php } ?>,
			order: [<?php echo $sort_index; ?>, <?php echo "'" . $sort_order . "'"; ?>],
			columnDefs:[
				<?php foreach ($column_widths_array as $key => $value) { ?>
						{ "width": <?=$value?>, "targets": [<?=$key?>] },
				<?php }?>
			]
			// bStateSave: true,
		});

		//Fixing the bug for default pagination values for the datatable//
		<?php $page_no = empty($page_no)?10:$page_no; ?>

		setTimeout(function(){;
			$('select[name="table_<?php echo $dict_id;?>_length"]').val(<?= "'".($page_no=="ALL"?'-1':$page_no)."'" ?>);
			$("select[name=table_<?php echo $dict_id;?>_length]").trigger('change');
		}, 1000);

		/* Sorting function on SORT button click */
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
} ?>
