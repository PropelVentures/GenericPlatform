<?php 
function renderListView($row,$tbQry,$list,$qry,$list_pagination,$tab_anchor){
	$con = connect();
	$list_select = trim($row['list_select']);
	$list_style = $row['list_style'];
    $keyfield = firstFieldName($row['database_table_name']);
    $table_type = trim($row['table_type']);
    $table_name = trim($row['database_table_name']);
    $list_fields = trim($row['list_fields']);
    $dict_id = $row['dict_id'];
	$list_select_arr = array();
	$list_select_sep = explode(';', $list_select);
	foreach ($list_select_sep as $listArray) {
		$list_select_arr[] = explode(",", $listArray);
	}
	?>
	<input type='button' onclick='clearFunction()' id='test' value='X' class='clearFunction'>
	<table id='example' class='display nowrap compact' cellspacing='0' width='100%'>
		<thead>
			<tr class='tr-heading'>
				<th class='tbl-action'><span style='visibility:hidden;'>12<span></th>
				<?php $tbRs = $con->query($tbQry);
				while ($tbRow = $tbRs->fetch_assoc()) {
					if(itemHasVisibility($tbRow['visibility']) && itemHasPrivilege($tbRow['privilege_level']) && $tbRow['format_type'] != 'list_fragment'){ ?>
						<th><?php echo $tbRow['field_label_name']; ?></th>
					<?php
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
									 * checking in database if checklest is there
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
							$fieldValue = $listRecord[$row[generic_field_name]];
							if (!empty($row[dropdown_alias])) {
								$fieldValue = dropdown($row, $urow = 'list_display', $fieldValue);
							}
							//will temprory truncate
							$fieldValue = substr($fieldValue, 0, 30);
							if(itemHasVisibility($row['visibility']) && itemHasPrivilege($row['privilege_level']) && $row['format_type'] != 'list_fragment'){ ?>
								<td> <?php echo $fieldValue; ?></td>
							<?php 
							}
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
} ?>