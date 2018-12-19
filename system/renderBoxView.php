<?php 
function renderBoxView($row , $tbQry ,$list ,$qry ,$list_pagination, $tab_anchor, $tab_num, $imageField, $ret_array){
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
	<div class='boxViewContainer' id='content<?php echo $tab_num; ?>'>
		<!-- the input fields that will hold the variables we will use -->
		<input type='hidden' class='current_page' />
		<input type='hidden' class='show_per_page' />
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
				$rs = $con->query($qry); ?>
				<div class="boxView  <?php echo (!empty($list_style) ? $list_style : '') ?>" data-scroll-reveal="enter bottom over 1s and move 100px">
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
						/*
						 * @while loop
						 * get FD info and put data into @lisdata array
						 */
						$listData = array();
						while ($row = $rs->fetch_assoc()) {
							if(itemHasVisibility($row['visibility']) && itemHasPrivilege($row['privilege_level'])){
								$listData[] = strip_tags($listRecord[$row['generic_field_name']]);
							}
						}
						/*
						 * @listViews function
						 *
						 * give bOX LIST UI and data inside lists
						 */
						listViews($listData, $table_type, $target_url, $imageField, $listRecord, $keyfield, $target_url2, $tab_anchor, $ret_array['users'], $list_select_arr); ///boxview ends here
					}
				}?>
				</div>
			<?php
			$count++;
			}
			/*
			 *
			 * Pagination Function goes here
			 */
			if(isset($list_pagination[2]) && $list_pagination[2] == 'hscroll'){
				boxViewHscroll($list_pagination, $tab_num, $list_select_arr);
			} else {
				echo boxViewPagination($list_pagination, $tab_num, $list_select_arr);
			}
		} ?>	
<?php
} ?>