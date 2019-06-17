<?php
function renderBoxView($isExistFilter,$isExistField,$row , $tbQry ,$list ,$qry ,$list_pagination, $tab_anchor, $tab_num, $imageField, $ret_array){
	$con = connect();
	$list_select = trim($row['list_select']);
	$dd_css_class = $row['dd_css_class'];
	$css_style = trim($row['dd_css_code']);
    $keyfield = firstFieldName($row['database_table_name']);
    $table_type = trim($row['table_type']);
    $table_name = trim($row['database_table_name']);
    $list_fields = trim($row['list_fields']);
    $dict_id = $row['dict_id'];
	$list_select_arr = getListSelectParams($list_select);
	?>
	<div class="boxViewContainer <?php echo (!empty($dd_css_class) ? $dd_css_class : '') ?>" id='content<?php echo $tab_num; ?>'>
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
				if($count > $limit){
					break;
				}
				
				if(!isFileExistFilterFullFillTheRule($listRecord,$isExistFilter,$isExistField)){
					break;
				}

				$_SESSION['list_pagination'] = array($list_pagination[0],$no_of_pages);
				$rs = $con->query($qry); ?>
				<div style="<?= $css_style ?>" class="boxView  <?php echo (!empty($dd_css_class) ? $dd_css_class : '') ?>" data-scroll-reveal="enter bottom over 1s and move 100px">
					<?php
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
					listViews($listData, $table_type, $target_url, $imageField, $listRecord, $keyfield, $target_url2, $tab_anchor, $ret_array['users'], $list_select_arr); ///boxview ends here
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
 				boxViewHscroll($list_pagination, $tab_num, $list_select_arr);
 			} else {
 				echo boxViewPagination($list_pagination, $tab_num, $list_select_arr);
 			}
		} else { ?>
	</div>
		<?php
	}
} ?>
