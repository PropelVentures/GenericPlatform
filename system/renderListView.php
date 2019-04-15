<?php
function renderListView($row,$tbQry,$list,$qry,$list_pagination,$tab_anchor){

	$con = connect();
// for checklist in listviews
	$listView = trim($row['list_views']);
	$list_views = listvalues($row['list_views']);

	$list_select = trim($row['list_select']);
	$dd_css_class = $row['dd_css_class'];
    $keyfield = firstFieldName($row['database_table_name']);
    $table_type = trim($row['table_type']);
    $table_name = trim($row['database_table_name']);
    $list_fields = trim($row['list_fields']);
    $dict_id = $row['dict_id'];
	$list_select_arr = getListSelectParams($list_select);
	?>
	<input type='button' onclick='clearFunction()' id='test' value='X' class='clearFunction'>
	<!--Code Changes for Task 5.4.77 Start-->
	<!--<table id='table_<?php //echo $dict_id;?>' class='display nowrap compact' cellspacing='0' width='100%'>-->
	<table id='table_<?php echo $dict_id;?>' class='display nowrap compact clear1 <?=$dd_css_class ?>' cellspacing='0' width='100%'>
	<!--Code Changes for Task 5.4.77 End-->
		<thead>
			<tr class='tr-heading'>
				<th class='tbl-action'><span style='visibility:hidden;'>12<span></th>
				<?php $tbRs = $con->query($tbQry);
				while ($tbRow = $tbRs->fetch_assoc()) {
					if(itemHasVisibility($tbRow['visibility']) && itemHasPrivilege($tbRow['privilege_level']) && $tbRow['format_type'] != 'list_fragment'){
					    //Code Change for Task 5.4.22 Start
						if($tbRow['ignore_in_lists'] != 1){
						//Code Change for Task 5.4.22 End
					  ?>
						<th><?php echo $tbRow['field_label_name']; ?></th>
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
				preg_match_all('!\d+!', $list_pagination[1], $limitPage);
				$no_of_pages = $limitPage[0][0];
				$limit = $limitPage[0][0] * $list_pagination[0];
				while ($listRecord = $list->fetch_assoc()) {
					if($count > $limit){
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
										<span class='span-checkbox'><input type='checkbox'  name='list[]'  value='<?php $checkbox_id ;?>' class='list-checkbox tabholdEvent' style='margin:right:6px;'/></span>
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
							//will temprory truncate
							$fieldValue = substr($fieldValue, 0, 30);
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
		<?php $page_no = $list_pagination[0]; ?>
		$('#table_<?php echo $dict_id;?>').DataTable({
			"pageLength": <?php echo $page_no; ?>,
            "scrollX": true,
			"pagingType": 'full_numbers',
			"lengthMenu": <?php if($page_no!='ALL') { ?> [[<?php if(!empty($page_no)){echo $page_no.','.(2*$page_no).','.(3*$page_no).','.(4*$page_no);}else{ echo "10,25,50,100";}?>],[<?php if(!empty($page_no)){echo $page_no.','.(2*$page_no).','.(3*$page_no).','.(4*$page_no);}else{ echo "10,25,50,100,'ALL'";}?>]] <?php }else { ?> [ [10, 25, 50, -1], [10, 25, 50, "ALL"] ] <?php } ?>,
			"bStateSave": true,
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
