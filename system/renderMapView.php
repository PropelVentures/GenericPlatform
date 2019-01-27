<?php 
function renderMapView($row,$tbQry,$list,$qry,$list_pagination,$tab_anchor,$tab_num, $imageField, $ret_array, $mapAddress){
	$con = connect();
	$list_select = trim($row['list_select']);
	$list_style = $row['list_style'];
    $keyfield = firstFieldName($row['database_table_name']);
    $table_type = trim($row['table_type']);
    $table_name = trim($row['database_table_name']);
    $list_fields = trim($row['list_fields']);
    $dict_id = $row['dict_id'];
	$list_select_arr = getListSelectParams($list_select);
	$mapData= array();
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
					if(!empty($tempLatLong['lat']) && !empty($tempLatLong['lng'])){
						$tempLatLong['list_data'] = array_filter($listData);
						$tempLatLong['target_url'] = $target_url."&edit=true#$tab_anchor";
						$tempLatLong['target_url2'] = $target_url2;
						$mapData[] = $tempLatLong;
					}
				}
			}/// end of mainIF ?>
		<?php
		$count++;
		}
		} else { ?>
		<div> No record found!</div>
	<?php } ?>
	<?php if(!empty($mapData)) { ?>
		<div id="map_<?php echo $dict_id; ?>" class="mapView <?php echo (!empty($list_style) ? $list_style : '') ?>" ></div>
		<script>
		function initMap<?php echo $dict_id; ?>() {
			var markers = [];
			var markersForBound = [];
			var map = new google.maps.Map(document.getElementById("<?php echo 'map_'.$dict_id; ?>"), {
				zoom: <?php echo MAP_ZOOM; ?>,
				center: {lat: <?php echo MAP_CENTER_LATITUDE; ?>, lng: <?php echo MAP_CENTER_LONGITUDE; ?>}
			});
			
			<?php foreach($mapData as $key=>$data){ ?>
				var listData = '<?php echo substr(implode('<br>',$data['list_data']), 0, 200); ?>';
				var marker_obj_<?php echo $key; ?> = new google.maps.Marker({
					position:new google.maps.LatLng(<?php echo $data['lat']; ?>,<?php echo $data['lng']; ?>),
					id: '<?php echo $key ?>',
					title: "<?php echo ucwords(@$data['list_data'][0]); ?>",
					label: "<?php echo substr(@$data['list_data'][0],0,1); ?>",
				});
				marker_obj_<?php echo $key; ?>.setMap(map);
				
				markers.push(marker_obj_<?php echo $key; ?>); //creating array for cluster
				
				markersForBound.push(new google.maps.LatLng(<?php echo $data['lat']; ?>,<?php echo $data['lng']; ?>));
				
				var window_<?php echo $key; ?> = new google.maps.InfoWindow({
				  content:"<div>"+listData+"</div>"
				});
					
				/*For manage click event start*/
				google.maps.event.addListener(marker_obj_<?php echo $key; ?>, 'click', function() {
					window_<?php echo $key; ?>.open(map,marker_obj_<?php echo $key; ?>);
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
			$.each(markersForBound,function(index,value){
				bounds.extend(value);
			});
			map.fitBounds(bounds);
		
   
			var options = {
				imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
			};

			var markerCluster = new MarkerClusterer(map, markers, options);
		}
		
		$(function(){
		   $(window).load(function(){
				initMap<?php echo $dict_id; ?>();
		   });
		});
		</script>
		
	<?php } else { ?>
		<h3 style='color:red;margin:15px;'> No record present </h3>
	<?php } ?>
<?php
} ?>