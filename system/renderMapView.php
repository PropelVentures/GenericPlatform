<?php
function renderMapView($isExistFilter,$isExistField,$row,$tbQry,$list,$qry,$list_pagination,$tab_anchor,$tab_num, $imageField, $ret_array, $mapAddress){
	$con = connect();
	$showImageIcon = isHaveToShowImage($row['list_extra_options']);
	$list_select = trim($row['list_select']);
	$dd_css_class = $row['dd_css_class'];
	$css_style = trim($row['dd_css_code']);
  $keyfield = firstFieldName($row['database_table_name']);
  $table_type = trim($row['table_type']);
  $table_name = trim($row['database_table_name']);
  $list_fields = trim($row['list_fields']);
  $dict_id = $row['dict_id'];
	$style_refrence_configs = false;
	$category_styles = false;
	$style_refrence_configs = setBoxStyles($row['list_extra_options']);
	if($style_refrence_configs !== flase){
		$category_styles = findAndSetCategoryStyles($con,$style_refrence_configs);
	}
	$list_select_arr = getListSelectParams($list_select);
	$mapData= array();
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
			$haveToShowImageIcon = 'NO';
			$thisUserImage = false;
			if($showImageIcon !== false){
				$haveToShowImageIcon = 'YES';
				if(!empty($listRecord[$showImageIcon]) && file_exists(USER_UPLOADS.$listRecord[$showImageIcon])){
					$thisUserImage = USER_UPLOADS.$listRecord[$showImageIcon];
				}else{
					$category_image = '';
					if($style_refrence_configs !== false){
						$category_image = $category_styles[$listRecord[$style_refrence_configs['field']]]['icon'];
					}
					if(!empty($category_image) && file_exists(USER_UPLOADS.$category_image)){
							$thisUserImage = USER_UPLOADS.$category_image;
					}else{
							$thisUserImage = USER_UPLOADS . "NO-IMAGE-AVAILABLE-ICON.jpg";
					}
				}
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
							$target_url = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['nav_css_class'] . "&ta=" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;
							/// add button url
							$_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
						} else {
							$target_url = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&ta=" . $list_select_arr[0][0] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&table_type=" . $table_type;
							/// add button url
							$_SESSION['add_url_list'] = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[0][2] . "&tab=" . $list_select_arr[0][0] . "&tabNum=" . $list_select_arr[0][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['nav_css_class'] . "&addFlag=true&checkFlag=true&ta=" . $list_select_arr[0][0] . "&table_type=" . $table_type;
						}
					}
					/// Extracting action, when user click on boxView Image of list
					if (isset($list_select_arr[1][0]) && !empty($list_select_arr[1][0])) {
						if (count($list_select_arr[1]) == 2) {
							$target_url2 = BASE_URL_SYSTEM . $navList['item_target'] . "?display=" . $list_select_arr[1][2] . "&tab=" . $list_select_arr[1][0] . "&ta=" . $list_select_arr[1][0] . "&tabNum=" . $list_select_arr[1][1] . "&layout=" . $navList['page_layout_style'] . "&style=" . $navList['nav_css_class'] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&edit=true&fnc=onepage";
						} else {
							$target_url2 = BASE_URL_SYSTEM . "main.php?display=" . $list_select_arr[1][2] . "&tab=" . $list_select_arr[1][0] . "&ta=" . $list_select_arr[1][0] . "&tabNum=" . $list_select_arr[1][1] . "&search_id=" . $listRecord[$keyfield] . "&checkFlag=true&edit=true&fnc=onepage";
						}
					}
					/*
					 * @while loop
					 * get FD info and put data into @lisdata array
					 */
					$listData = array();

					$tempLatLong['lat'] = '';
					$tempLatLong['lng'] = '';
					while ($row = $rs->fetch_assoc()) {
						$row['generic_field_name'] =  trim($row['generic_field_name']);
						$row['format_type'] =  trim(strtolower($row['format_type']));
						$row['field_identifier'] =  trim(strtolower($row['field_identifier']));
						if(itemHasVisibility($row['visibility']) && itemHasPrivilege($row['privilege_level'])){
							// For mapAddress base latitude and longitude
							if($mapAddress){
								if($row['format_type'] == 'base_latitude' || $row['field_identifier'] == 'base_latitude'){
									$tempLatLong['lat'] = $listRecord[$row['generic_field_name']];
								}elseif($row['format_type'] == 'base_longitude' || $row['field_identifier'] == 'base_longitude'){
									$tempLatLong['lng'] = $listRecord[$row['generic_field_name']];
								} else {
									$listData[] = strip_tags(trim(preg_replace('/\s\s+/', ' ', $listRecord[$row['generic_field_name']])));
								}
							} else {
								if($row['format_type'] == 'gps_latitude' || $row['field_identifier'] == 'gps_latitude'){
									$tempLatLong['lat'] = $listRecord[$row['generic_field_name']];
								}elseif($row['format_type'] == 'gps_longitude' || $row['field_identifier'] == 'gps_longitude'){
									$tempLatLong['lng'] = $listRecord[$row['generic_field_name']];
								} else {
									$listData[] = strip_tags(trim(preg_replace('/\s\s+/', ' ', $listRecord[$row['generic_field_name']])));
								}
							}
						}
					}
					$recordDbId = $listRecord[$keyfield];
					if(!empty($tempLatLong['lat']) && !empty($tempLatLong['lng'])){
						$tempLatLong['userImage'] = $thisUserImage;
						$tempLatLong['list_data'] = array_filter($listData);
						$tempLatLong['target_url'] = $target_url."&edit=true#$tab_anchor";
						$tempLatLong['target_url2'] = $target_url2;
						$tempLatLong['record_id'] = $recordDbId;
						$mapData[] = $tempLatLong;
					}
				}
			}/// end of mainIF ?>
		<?php
		$count++;
			global $popup_menu;
			if ($popup_menu['popupmenu'] == 'true') {
				$popup_menu['popup_menu_id'] = "popup_menu_$dict_id";
				$_SESSION['popup_munu_array'][] = $popup_menu;
			}
		}
		} else { ?>
		<div> No record found!</div>
	<?php } ?>
	<?php if(!empty($mapData)) {
		$button_id ="reset_state".$dict_id; ?>
		<button  style='margin:10px' type='button' class="btn btn-danger" id="<?=$button_id?>">Reset Map</button>
		<div id="map_<?php echo $dict_id; ?>" class="mapView <?php echo (!empty($dd_css_class) ? $dd_css_class : '') ?>" style="$css_style"></div>
		<script>
		function initMap<?php echo $dict_id; ?>() {
			var markers = [];
			var markersForBound = [];
			var latlng = new google.maps.LatLng(<?php echo MAP_CENTER_LATITUDE; ?>, <?php echo MAP_CENTER_LONGITUDE; ?>);
			var mapOptions = {
        zoom: <?php echo MAP_ZOOM; ?>,
        center: latlng,
		 }

		 var haveToShowImageIcon = "<?= $haveToShowImageIcon ?>";
			var map = new google.maps.Map(document.getElementById("<?php echo 'map_'.$dict_id; ?>"), mapOptions);
			<?php foreach($mapData as $key=>$data){ ?>
				var listData = '<?php echo substr(implode('<br>',$data['list_data']), 0, 200); ?>';
					if(haveToShowImageIcon == 'YES'){
						var image = {
          		url:"<?php echo $data['userImage'] ?>",
          		scaledSize: new google.maps.Size(40, 40),
          		origin: new google.maps.Point(0, 0),
          		anchor: new google.maps.Point(0, 0)
        		};
						var marker_obj_<?php echo $key; ?> = new google.maps.Marker({
							position:new google.maps.LatLng(<?php echo $data['lat']; ?>,<?php echo $data['lng']; ?>),
							id: '<?php echo $key ?>',
							title: "<?php echo ucwords(@$data['list_data'][0]); ?>",
							icon: image
						});
					}else{
						var marker_obj_<?php echo $key; ?> = new google.maps.Marker({
							position:new google.maps.LatLng(<?php echo $data['lat']; ?>,<?php echo $data['lng']; ?>),
							id: '<?php echo $key ?>',
							title: "<?php echo ucwords(@$data['list_data'][0]); ?>",
							label: "<?php echo substr(@$data['list_data'][0],0,1); ?>"
						});
					}

				marker_obj_<?php echo $key; ?>.setMap(map);

				markers.push(marker_obj_<?php echo $key; ?>); //creating array for cluster

				markersForBound.push(new google.maps.LatLng(<?php echo $data['lat']; ?>,<?php echo $data['lng']; ?>));

				var window_<?php echo $key; ?> = new google.maps.InfoWindow({
				  content:"<div>"+listData+"</div>"
				});

				/*For manage click event start*/
				google.maps.event.addListener(marker_obj_<?php echo $key; ?>, 'click', function() {
						window.location.href = '<?php echo $data['target_url']; ?>';
				});

				/*For manage right click event start*/
				google.maps.event.addListener(marker_obj_<?php echo $key; ?>, 'rightclick', function() {
						dict_id = "<?php echo $dict_id; ?>";
						popup_del = '<?php echo $data['record_id']; ?>';
						event.preventDefault();
						// Show contextmenu
						$("#popup_menu_<?php echo $dict_id?>.custom-menu").finish().toggle(100).
						// In the right position (the mouse)
						css({
							top: event.pageY + "px",
							left: event.pageX + "px"
						});

				});

				/*For manage click event end*/

				/*For mouseover event start*/
				google.maps.event.addListener(marker_obj_<?php echo $key; ?>, 'mouseover', function() {
					window_<?php echo $key; ?>.open(map,marker_obj_<?php echo $key; ?>);
				});
				/*For mouseover event end*/

				/*For mouseout event start*/
				google.maps.event.addListener(marker_obj_<?php echo $key; ?>, 'mouseout', function() {
					window_<?php echo $key; ?>.close();
				});
				/*For mouseout event end*/

				/*For manage dblclick event start*/
				google.maps.event.addListener(marker_obj_<?php echo $key; ?>, "dblclick", function (e) {
					window.location.href = '<?php echo $data['target_url']; ?>';
				});
				/*For manage dblclick event end*/

				/*For manage rightclick event start*/
				/* google.maps.event.addListener(marker_obj_<?php echo $key; ?>, "rightclick", function(event) {
					$(".custom-menu").finish().toggle(100)
				}); */
				/*For manage rightclick event start*/

			<?php
			} ?>

			/* var bounds = new google.maps.LatLngBounds();
			var markers = locations.map(function(location, i) {
				bounds.extend(location);
				map.fitBounds(bounds);
				return new google.maps.Marker({
					position: location,
					label: labels[i % labels.length]
				});
			}); */

			var bounds = new google.maps.LatLngBounds();
			// $.each(markersForBound,function(index,value){
			// 	// bounds.extend(value);
			// });
			// map.fitBounds(bounds);


			var options = {
				imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
			};

			var markerCluster = new MarkerClusterer(map, markers, options);
			return [map,bounds];
		}

		$(function(){
			var thismap;
		   $(window).load(function(){
				thismap = initMap<?php echo $dict_id; ?>();
				setTimeout(function(){
						$(".gm-style img[src*='/application/']").each(function (i, el) {
		         $(el).css('border-radius','50%');
		     });
			 }, 500);
		   });
			 $("#reset_state"+"<?php echo $dict_id; ?>").click(function() {
				  // thismap[0].fitBounds(thismap[1]);
					thismap[0].setCenter(new google.maps.LatLng(<?php echo MAP_CENTER_LATITUDE; ?>, <?php echo MAP_CENTER_LONGITUDE; ?>));
				})
		});
		</script>

	<?php } else { ?>
		<h3 style='color:red;margin:15px;'> No record present </h3>
	<?php } ?>
<?php
} ?>
