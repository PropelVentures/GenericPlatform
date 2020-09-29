<?php
/*
 
 Navigation Processing ...

function Get_Top_Tab_Links($page_name)
   * ****Creating TABS Getting tabs name for page_name
  

 function ShowTab($page_name, $rs, $row, $tab, $class=''){
 function generateTabs($page_name,$row,$ulClass='vertical-tab '){
	 
	 
 function Get_Serial_Tab_Links($page_name,$sidebar) {
	 
 function headersAndSubHeaders($page_name){

 function ShowComponentTypeHeaderContent($page_name,$ComponentOrder=''){
 function ShowComponentTypeSubHeaderContent($page_name,$ComponentOrder=''){
 function ShowComponentTypeBanner($page_name,$ComponentOrder=''){
 function ShowComponentTypeParallaxBanner($page_name,&$haveParallax,$ComponentOrder=''){
 function ShowComponentTypeContent($page_name,$ComponentOrder=''){
 function ShowComponentTypeURL($page_name,$ComponentOrder=''){
 function ShowComponentTypeSlider($page_name,$ComponentOrder=''){
 function ShowComponentTypeImage($page_name,$ComponentOrder=''){
 function ShowComponentTypeIcon($page_name,$ComponentOrder=''){
 
 function get_FD_rec_By_DictId($dict_id){
 function get_single_record($db_name, $pkey, $search) { 
 function get_multi_record($db_name, $pkey, $search, $listFilter = 'false', $singleSort = 'false', $listCheck = 'false',&$isExistFilter,&$isExistField) {
 function get_listFragment_record($db_name, $pkey, $listFilter = 'false', $limit = 'false', $fields = 'false') {
 function Select_Data_FieldDictionary_Record($alias) {
function formating_Select($row) {
 
  function is_FFFR_DD($componentType){

function Footer($page, $menu_location = 'footer') {
 */


/**
 * Renders header and subheader section
 * 
 * @param string $page_name Page name
 * 
 * @author ph
 * 
 * @return void
 */
function headersAndSubHeaders(string $page_name) {
	$con = connect();

	$header_query = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order>0 AND component_type LIKE 'header%' ORDER BY component_type ASC");
	$sub_header_query = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order>0 AND component_type LIKE 'subheader%'  ORDER BY component_type ASC");

	if ($header_row = $header_query->fetch_assoc()) {
		ShowComponentTypeHeaderContent($page_name);
	}

	if ($sub_header_row = $sub_header_query->fetch_assoc()) {
		ShowComponentTypeSubHeaderContent($page_name);
	}
}


/**
 * Renders header section for specific page
 * 
 * @param string $page_name Page where the header will be rendered
 * @param string $ComponentOrder Order of the header component, default empty
 * 
 * @author ph
 * 
 * @return void
 */
function ShowComponentTypeHeaderContent(string $page_name, string $ComponentOrder = '') {
	$con = connect();
	if($ComponentOrder){
		$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order='$ComponentOrder' AND component_type LIKE 'header%' ORDER BY component_type ASC");
	} else {
		if (empty($_GET['ComponentOrder'])) {
			$rs = $con->query("SELECT component_order FROM data_dictionary where page_name='$page_name' and component_order REGEXP '^[0-9]+$' AND component_type LIKE 'header%' AND component_order >'0' order by component_order");
			$row = $rs->fetch_assoc();
			$ComponentOrder = $row['component_order'];
		} else {
			$ComponentOrder = $_GET['ComponentOrder'];
		}
		$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name'  AND component_type LIKE 'header%'  ORDER BY component_type ASC");
	}
	if ($componentTypeHeaderQuery->num_rows > 0) {
		While($row = $componentTypeHeaderQuery->fetch_assoc()) {

			if (isAllowedToShowByPrivilegeLevel($row)) {
				$header = $row['description'];
				$dd_css_class = $row['dd_css_class'];
        		$css_style = $row['dd_css_code'];
				$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']); ?>
				<h1 class="<?php echo $dd_css_class; ?>" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>;text-align:<?php echo $align; ?><?= $css_style?>">
					<?php echo $header; ?>
				</h1>
			<?php
			}
		}
	}
}

/**
 * Renders subheader section for specific page
 * 
 * @param string $page_name Page where the header will be rendered
 * @param string $ComponentOrder Order of the header component, default empty
 * 
 * @author ph
 * 
 * @return void
 */
function ShowComponentTypeSubHeaderContent(string $page_name, string $ComponentOrder = '') {
	$con = connect();
	if($ComponentOrder){
		$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order='$ComponentOrder' AND component_type LIKE 'subheader%' ORDER BY component_type ASC");
	} else {
		/*
		if (empty($_GET['ComponentOrder'])) {
			$rs = $con->query("SELECT component_order FROM data_dictionary where page_name='$page_name' and component_order REGEXP '^[0-9]+$' AND component_type LIKE 'subheader%' AND component_order >'0' order by component_order");
			$row = $rs->fetch_assoc();
			$ComponentOrder = $row['component_order'];
		} else {
			$ComponentOrder = $_GET['ComponentOrder'];
		}
		*/
		$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name'  AND component_type LIKE 'subheader%' ORDER BY component_type ASC");
	}
	if($componentTypeHeaderQuery->num_rows > 0){
		While($row = $componentTypeHeaderQuery->fetch_assoc()) {
			if (isAllowedToShowByPrivilegeLevel($row)) {
				$header = $row['description'];

				$dd_css_class = $row['dd_css_class'];
        $css_style = $row['dd_css_code'];
				$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']); ?>
				<h2 class="<?php echo $dd_css_class; ?>" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>;text-align:<?php echo $align; ?><?= $css_style?>>">
					<?php echo $header; ?>
				</h2>
			<?php
			}
		}
	}
}

/**
 * Extract component order value from passed parameter or from other sources, if parameter is empty
 * 
 * @param string $page_name Page where the component order will be used
 * @param string $ComponentOrder Default passed order, default empty
 * 
 * @author ph
 * 
 * @return string
 */
function extractComponentOrder(string $page_name, string $ComponentOrder = '') {
	if (!empty($ComponentOrder)) {
		return $ComponentOrder;
	}
	if (!empty($_GET['ComponentOrder'])) {
		return $_GET['ComponentOrder'];
	}
	$con = connect();
	$rs = $con->query("SELECT component_order FROM data_dictionary where page_name='$page_name' and component_order REGEXP '^[0-9]+$' AND component_order >'0' order by component_order");
	$row = $rs->fetch_assoc();
	return $row['component_order'];
}

/**
 * Renders banner section for specific page
 * 
 * @param string $page_name Page where the banner will be rendered
 * @param string $ComponentOrder Order of the banner component, default empty
 * 
 * @author ph
 * 
 * @return void
 */
function ShowComponentTypeBanner(string $page_name, string $ComponentOrder = '') {
	$con = connect();
	$ComponentOrder = extractComponentOrder($page_name, $ComponentOrder);
	$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order='$ComponentOrder' AND component_type = 'banner' ORDER BY component_type ASC");
	if ($componentTypeHeaderQuery->num_rows > 0) {
		While($row = $componentTypeHeaderQuery->fetch_assoc()) {
			if (isAllowedToShowByPrivilegeLevel($row)) {
				$banner = getBannerImages($row['description']);
				$dd_css_class = $row['dd_css_class'];
        		$css_style = $row['dd_css_code'];
				$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']);
				if(!empty($banner)) { ?>
					<div style="<?= $css_style ?>"class="<?php echo $divClass.' '.$dd_css_class; ?>">
						<div style="width:<?php echo $width; ?>;">
							<section class='section-sep'>
								<a href="<?php echo $url; ?>"><img style="width:100%;height:<?php echo $height; ?>;" src="<?php echo $banner; ?>"></a>
							</section>
						</div>
					</div>
					<?php
				}
			}
		}
	}
}


function Get_Top_Tab_Links($page_name) {
	/* Check From component_type == header1 or header2 Start */
	// ShowComponentTypeHeaderContent($page_name);
	/* Check For component_type == header1 or header2 End */
	
    $_SESSION['page_name'] = $page_name;
    global $table_alias;
    $con = connect();
    $rs = $con->query("SELECT * FROM  data_dictionary DD INNER JOIN navigation ON(navigation.target_page_name=DD.page_name) where DD.page_name = '$page_name' AND DD.component_type NOT REGEXP 'header|banner|slider|content|url|text|subheader|image|icon' AND DD.component_order REGEXP '^[0-9]+$' AND DD.component_order >'0' GROUP BY DD.dict_id order by DD.component_order ");
    $i = 1;


    if ($_GET['edit'] == 'true')
        fffr_icons($page_name);

	if($rs->num_rows){
        echo "<ul class='center-tab' role='tablist' >";
		while ($row = $rs->fetch_assoc()) {
    		if($row['loginRequired']== '1' && !itemHasVisibility($row['dd_visibility'])){
    			continue;
    		}

    		$component_name = explode("/", $row['component_name']);

    		$row['component_name'] = trim($component_name[0]);
    		$dd_css_class = $row['dd_css_class'];
            $css_style = $row['dd_css_code'];
    		if ($i == 1 && !( isset($_SESSION['table_alias']) )) {
    			$table_alias = $row['table_alias'];

    				$_SESSION['table_alias'] = $table_alias;
                ShowTab($page_name, $rs, $row, $_SESSION['table_alias'], 'active');
                
    		} else if ($_SESSION['table_alias'] == $row['table_alias'] && $_GET['ComponentOrder'] == $row['component_order']) {
    			ShowTab($page_name, $rs, $row, $_SESSION['table_alias'], 'active');
    			
    		} else {

    			//echo "<li><a href=?page_name=$page_name&table_alias=$row[table_alias]&ComponentOrder=$row[component_order]&search_id=$_GET[search_id] class='tab-class' id='$list_style'>$row[component_name]</a></li>";
    			ShowTab($page_name, $rs, $row, $row['table_alias']);
    		}
        		$i++;
        }

    		echo "</ul>";
	}


	/* Check From component_type == subheader1 or subheader2 Start */
	// ShowComponentTypeSubHeaderContent($page_name);
	/* Check For component_type == subheader1 or subheader2 End */

	ShowComponentTypeSlider($page_name);

	ShowComponentTypeBanner($page_name);

  // ShowComponentTypeParallaxBanner($page_name);

	ShowComponentTypeContent($page_name);

	ShowComponentTypeImage($page_name);
}


/**
 * Generates serial tabs of page based on sidebar position
 * @param string $page_name Page name
 * @param string $sidebar Sidebar location (left, right or center), default = center
 * 
 * @author ph
 * 
 * @return void
 */
function Get_Serial_Tab_Links(string $page_name, string $sidebar = 'center') {
	$con = connect();
	$layout = 'S-' . strtoupper($sidebar[0]);
	$ulClass = 'vertical-tab ';
	if ($layout == 'S-C') {
		$ulClass = 'center-tab center-tab-fixed ';
	}
	$rs = $con->query("SELECT * FROM data_dictionary where page_name='{$page_name}' and (page_layout='{$layout}')");
	$row = $rs->fetch_assoc();
	generateTabs($page_name, $row, $ulClass);
}

/**
 * Show tabular navigation on page
 * 
 * @param string $page_name The page where to render the navigation
 * @param mysqli $rs Result from database (TODO: need to clarify)
 * @param array $row Array of data (TODO: need to clarify)
 * @param string $table_alias TODO: where it is used?
 * @param string $class Css class for li
 * 
 * @author ph
 * 
 * @return void
 */
function ShowTab($page_name, $rs, $row, $table_alias, $class = '') {

    if (is_FFFR_DD($row['component_type'])) {
      	return;
    }
    $component_name = trim($row['component_name']);

    if ($rs->num_rows == 1 && $row['component_name']) {
        if ($component_name[0] != '*') {
            echo "<li class='$class'><a href=?page_name=$page_name&table_alias=$row[table_alias]&ComponentOrder=$row[component_order] class='tab-class'>$row[component_name]</a></li>";
        }
    } else if ($rs->num_rows > 1) {
        if (!empty($row['component_order'])) {
            echo "<li class='$class'><a href=?page_name=$page_name&table_alias=$row[table_alias]&ComponentOrder=$row[component_order] class='tab-class'>$row[component_name]</a></li>";
        }
    } elseif ($rs->num_rows == 0 && $row['component_name']) {
		if ($component_name[0] != '*') {
			echo "<li class='$class'><a href=?page_name=$page_name&table_alias=$row[table_alias]&ComponentOrder=$row[component_order] class='tab-class'>$row[component_name]</a></li>";
	   	}
    }
}

/**
 * Generate and render tabs for specific page
 * 
 * @param string $page_name Page name
 * @param array $row Row from data dictionary having info on the layout (is this really needed?)
 * @param string $ulClass Css class of the ul element
 * 
 * @author ph
 * 
 * @return void
 */
function generateTabs($page_name, $row, $ulClass = 'vertical-tab ') {
	if (!empty($row)) {
		if ($_GET['edit'] == 'true'){
			fffr_icons($page_name);
		}
		$con = connect();
		$tabQuery = $con->query("SELECT * FROM  data_dictionary where page_name = '$page_name' AND component_type NOT REGEXP 'header|banner|slider|content|url|text|subheader|image|icon' AND component_order REGEXP '^[0-9]+$' AND component_order >'0' order by component_order");
		if ($tabQuery->num_rows > 0) { ?>

			<ul class='<?php echo $ulClass.$sidebar; ?>' role='tablist'>
				<?php
				while ($row = $tabQuery->fetch_assoc()) {
					if (trim($row['component_name'])) {
						$dd_css_class = $row['dd_css_class'];
                        $css_style = $row['dd_css_code'];

						$tab_id = "#".$page_name.$row['dict_id'];
						if(!loginNotRequired() && !itemHasVisibility($row['dd_visibility'])){
							continue;
						} ?>
						<li class="tab-class js_tab <?= $dd_css_class ?>" style="<?= $css_style?>"id="<?php echo $tab_id; ?>">
							<!--<a id="<?php //echo $list_style; ?>" href="javascript:void(0);">
								<?php //echo $row['component_name']; ?>
							</a>-->
							<a href="javascript:void(0);">
								<?php echo $row['component_name']; ?>
							</a>
						</li>
						<?php
					} else if (trim($row['component_name']) && $tabQuery->num_rows > 1 && !empty($row['component_name'])) {
						$list_style = $row['list_style'];
						$tab_id = "#".$page_name.$row['dict_id'];
						if(!loginNotRequired() && !itemHasVisibility($row['dd_visibility'])){
							continue;
						} ?>
						<li class="tab-class js_tab" id="<?php echo $tab_id; ?>">
							<a href="javascript:void(0);">
								<?php echo $row['component_name']; ?>
							</a>
						</li>
						<?php
					}
				}?>
			</ul>
			<script>
			$(document).ready(function(){
				$('.js_tab').click(function(){
					$('.js_tab').removeClass('active');
					$(this).addClass('active');
					$('html, body').animate({
						scrollTop: $($(this).attr('id')).offset().top - 160
					}, 500);
				});
				$('.js_tab').first().addClass('active');
			});
			</script>
	<?php
		}
	}
}



function ShowComponentTypeParallaxBanner($page_name,&$haveParallax,$ComponentOrder=''){
	$con = connect();
  	$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_type = 'p_banner'");
	// if($ComponentOrder){
	// 	$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order='$ComponentOrder' AND component_type = 'p_banner' ORDER BY component_type ASC");
	// } else {
	// 	if (empty($_GET['ComponentOrder'])) {
	// 		$rs = $con->query("SELECT component_order FROM data_dictionary where page_name='$page_name' and component_order REGEXP '^[0-9]+$' AND component_order >'0' order by component_order");
	// 		$row = $rs->fetch_assoc();
	// 		$ComponentOrder = $row['component_order'];
	// 	} else {
	// 		$ComponentOrder = $_GET['ComponentOrder'];
	// 	}
	// 	$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order='$ComponentOrder' AND component_type = 'p_banner' ORDER BY component_type ASC");
	// }
	if($componentTypeHeaderQuery->num_rows > 0){
		While($row = $componentTypeHeaderQuery->fetch_assoc()) {
			if (isAllowedToShowByPrivilegeLevel($row)) {
				$banner = getBannerImages($row['description']);

				$dd_css_class = $row['dd_css_class'];
        $css_style = $row['dd_css_code'];
				$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']);
				if(!empty($banner)) {
          $haveParallax = true;?>

          <script src="<?= BASE_URL_SYSTEM ?>js/parallax.min.js"></script>


          <div class="parallax-window" data-parallax="scroll" data-image-src="<?=$banner?>">

          <!-- Font Awesome -->
          <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.6/css/mdb.min.css" rel="stylesheet"> -->

          <!-- JQuery -->
<!-- Bootstrap tooltips -->
<!-- Bootstrap core JavaScript -->
<!-- MDB core JavaScript -->
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<!-- Bootstrap tooltips -->
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js"></script> -->
<!-- Bootstrap core JavaScript -->
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script> -->

<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.6/js/mdb.min.js"></script> -->

          <!-- <style>
          .parallax {
            /* The image used */
            background-image: url("<?=$banner?>");

            /* Full height */
            height: 200px;

            /* Create the parallax scrolling effect */
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            }
          </style>
          <div class="parallax"></div><div style="height:100%"> -->
                <!-- <div class="parallax"></div>
                  <div style="height:1000px;background-color:red;font-size:36px">
                    Scroll Up and Down this page to see the parallax scrolling effect.
                    This div is just here to enable scrolling.
                    Tip: Try to remove the background-attachment property to remove the scrolling effect.
                  </div>
                <div class="parallax"></div> -->
                <!-- <div class="jarallax">
  Your content here...
</div>
							</section>
						</div>
					</div> -->

          <!-- <script type="text/javascript">

          $(document).ready(function(){
            setTimeout(function(){
              // object-fit polyfill run
              objectFitImages();

              /* init Jarallax */
              jarallax(document.querySelectorAll('.jarallax'));

              jarallax(document.querySelectorAll('.jarallax-keep-img'), {
                keepImg: true,
              });
           }, 500);
          });

          </script> -->
				<?php }
			}
		}
	}
}

/**
 * Renders content section for specific page
 * 
 * @param string $page_name Page where the content will be rendered
 * @param string $ComponentOrder Order of the content component, default empty
 * 
 * @author ph
 * 
 * @return void
 */
function ShowComponentTypeContent(string $page_name, string $ComponentOrder = '') {
	$con = connect();
	$ComponentOrder = extractComponentOrder($page_name, $ComponentOrder);
	$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order='$ComponentOrder' AND component_type = 'content' ORDER BY component_type ASC");
	if ($componentTypeHeaderQuery->num_rows > 0) {
		While($row = $componentTypeHeaderQuery->fetch_assoc()) {
			if (isAllowedToShowByPrivilegeLevel($row)) {
				$content = $row['description'];
				$listStyle = $row['list_style'];
				$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']);
				if (!empty($content)) { ?>
                    <div class="<?php echo $divClass; ?>">
						<div id="<?php echo $listStyle; ?>" style="width:<?php echo $width; ?>;">
							<?php echo $content ?>
						</div>
					</div>
					<?php
				}
			}
		}
	}
}

/**
 * Renders url section for specific page
 * 
 * @param string $page_name Page where the url will be rendered
 * @param string $ComponentOrder Order of the url component, default empty
 * 
 * @author ph
 * 
 * @return void
 */
function ShowComponentTypeURL(string $page_name, string $ComponentOrder = '') {
	$con = connect();
	$ComponentOrder = extractComponentOrder($page_name, $ComponentOrder);
	$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order='$ComponentOrder' AND component_type = 'url' ORDER BY component_type ASC");
	if($componentTypeHeaderQuery->num_rows > 0){
		While($row = $componentTypeHeaderQuery->fetch_assoc()) {
			if (isAllowedToShowByPrivilegeLevel($row)) {
				$iframeUrl = getIframeUrl($row['description']);
				$dd_css_class = $row['dd_css_class'];
        $css_style = $row['dd_css_code'];
				$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']);
				if(!empty($iframeUrl)) { ?>
					<div class="<?php echo $divClass; ?><?= $dd_css_class?>" style="<?= $css_style?>">
						<iframe align="<?php echo $align; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" src="<?php echo $iframeUrl; ?>"></iframe>
					</div>
					<?php
				}
			}
		}
	}
}	


/**
 * Renders slider section for specific page
 * 
 * @param string $page_name Page where the slider will be rendered
 * @param string $ComponentOrder Order of the slider component, default empty
 * 
 * @author ph
 * 
 * @return void
 */
function ShowComponentTypeSlider(string $page_name, string $ComponentOrder = '') {
	$con = connect();
	$ComponentOrder = extractComponentOrder($page_name, $ComponentOrder);
	$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order='$ComponentOrder' AND component_type='slider' ORDER BY component_type ASC");
	if($componentTypeHeaderQuery->num_rows > 0){
		While($row = $componentTypeHeaderQuery->fetch_assoc()) {

			if (isAllowedToShowByPrivilegeLevel($row)) {
				$sliders = getSliderImages($row['description'],$row['dict_id']);
				$slider_interval = 0;
				$slider_interval = getSliderInterval($row['list_extra_options']);
				$dd_css_class = $row['dd_css_class'];
        		$css_style = $row['dd_css_code'];
				//$url = getDDUrl($row['list_select']);
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options']);
				if(!empty($sliders)){ ?>
					<div class="<?php echo $divClass; ?><?=$dd_css_class ?>" style="<?=$css_style?>">
						<div  style="width:<?php echo $width; ?>;" class="slider">
							<div id="myCarousel" style="height:<?php echo $height; ?>;" data-interval="<?php echo $slider_interval; ?>" class="carousel slide" data-ride="carousel" >
								<!-- Indicators -->
								<ol class="carousel-indicators">
									<?php foreach($sliders as $key=>$slider){ ?>
										<li data-target="#myCarousel" data-slide-to="<?php echo $key; ?>" class="<?php echo ($key==0 ?'active':''); ?>"></li>
									<?php } ?>
								</ol>
								<div class="carousel-inner">
									<?php foreach($sliders as $key=>$slider){ ?>
										<div style="height:<?php echo $height; ?>;" class="item <?php echo ($key==0 ?'active':''); ?>">
											<img data-url="<?php echo $slider['url']; ?>" id="<?php echo $slider['id']; ?>" style="height:<?php echo $height; ?>;" src="<?php echo $slider['image']; ?>" alt="" class="img-responsive">
											<!--<div class="container">
												<div class="carousel-caption slide2">
													<h1><?php //echo HOME_SLIDER_TITLE2 ?></h1>
													<p><?php //echo HOME_SLIDER_CONTENT2 ?></p>
													<p><a class="btn btn-lg btn-primary" href="#" role="button"><?php //echo HOME_SLIDER_BUTTON_TEXT2 ?></a></p>
												</div>
											</div>-->
										</div>
									<?php } ?>
								</div>
								<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left"></span></a> <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right"></span></a>
							</div>
							<!-- /.carousel -->
						</div>
					</div>
					<script>
					<?php foreach ($sliders as $key => $slider) { ?>
					var image_id = "#"+"<?php echo $slider['id']; ?>";
					$(image_id).on("click", function(){
						var image_url  = $(this).data('url');
						if (image_url !== '') {
							var win = window.open(image_url, '_blank');
							win.focus();
						}
					});
					<?php } ?>
					</script>
					<?php
				}
			}
		}
	}
}

/**
 * Renders image component for specific page
 * 
 * @param string $page_name Page where the image will be rendered
 * @param string $ComponentOrder Order of the image component, default empty
 * 
 * @author ph
 * 
 * @return void
 */
function ShowComponentTypeImage(string $page_name, string $ComponentOrder = '') {
	$con = connect();
	$ComponentOrder = extractComponentOrder($page_name, $ComponentOrder);
	$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order='$ComponentOrder' AND (component_type='image') ORDER BY component_type ASC");
	if($componentTypeHeaderQuery->num_rows > 0){
		While($row = $componentTypeHeaderQuery->fetch_assoc()) {
			if (isAllowedToShowByPrivilegeLevel($row)) {
				$images = getImages($row['description']);
				$url = getDDUrl($row['list_select']);
				$dd_css_class = $row['dd_css_class'];
        		$css_style = $row['dd_css_code'];
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options'],true);
				if(!empty($images)){ ?>
				<div class="<?php echo $divClass; ?><?= $dd_css_class?>" style="<?= $css_style?>">
					<?php foreach($images as $key=>$image){ ?>
						<a href="<?php echo $url; ?>"><img src="<?php echo $image; ?>" style="margin:10px;height:<?php echo $height; ?>;width:<?php echo $width; ?>;"></a></li>
					<?php } ?>
				</div>
				<?php
				}
			}
		}
	}
}

/**
 * Renders icon component for specific page
 * 
 * @param string $page_name Page where the icon will be rendered
 * @param string $ComponentOrder Order of the icon component, default empty
 * 
 * @author ph
 * 
 * @return void
 */
function ShowComponentTypeIcon(string $page_name, string $ComponentOrder = '') {
	$con = connect();
	$ComponentOrder = extractComponentOrder($page_name, $ComponentOrder);
	$componentTypeHeaderQuery = $con->query("SELECT * FROM data_dictionary where page_name='$page_name' AND component_order='$ComponentOrder' AND (component_type='icon') ORDER BY component_type ASC");
	if($componentTypeHeaderQuery->num_rows > 0){
		While($row = $componentTypeHeaderQuery->fetch_assoc()) {
			if (isAllowedToShowByPrivilegeLevel($row)) {
				$images = getImages($row['description']);
				$url = getDDUrl($row['list_select']);
				$listStyle = $row['dd_css_class'];
				list($height,$width,$align,$divClass) =  parseListExtraOption($row['list_extra_options'],true);
				if(!empty($images)){ ?>
				<!--div class="<?php echo $divClass; ?>"-->
					<?php foreach($images as $key=>$image){ ?>
						<a href="<?php echo $url; ?>"><img src="<?php echo $image; ?>" style="margin:10px;height:<?php echo $height; ?>;width:<?php echo $width; ?>;"></a></li>
					<?php } ?>
				<!--/div-->
				<?php
				}
			}
		}
	}
}


/*
 * GET Data FD Record
 */
function get_FD_rec_By_DictId($dict_id){
	$con = connect();
	$ddFDRecord = array();
	$ddFdQuery = $con->query("SELECT * FROM data_dictionary DD INNER JOIN field_dictionary FD ON(DD.table_alias = FD.table_alias) WHERE DD.dict_id ='$dict_id' ORDER BY FD.field_order");
	if($ddFdQuery->num_rows >0){
		while($row = $ddFdQuery->fetch_assoc()){
			$ddFDRecord[] = $row;
		}
	}
	return $ddFDRecord;
}

function get_single_record($db_name, $pkey, $search) {

    $_SESSION['update_table']['search'] = $search;

    $con = connect();
    $user = $con->query("select * from $db_name where $pkey='$search'");
    //print "select * from $db_name where $pkey='$search'";

	if($user>num_rows){
		return $user->fetch_assoc();
	}
	return array();

}

/**
 * Get multi records for list display
 *
 * @param type $db_name
 * @param type $pkey
 * @param type $search
 * @param mixed $listFilter string if no parent-> child relationship of DD.table_type='child' then array('list_filter'=>DD.list_filter, 'child_filter'=>"'DD.table_name'.'DD.keyfield'=$search")
 * @param type $singleSort
 * @param type $listCheck
 * @return type
 */
 
 
function get_multi_record($db_name, $pkey, $search, $listFilter = 'false', $singleSort = 'false', $listCheck = 'false',&$isExistFilter,&$isExistField) {
    $_SESSION['update_table']['search'] = $search;
    $con = connect();

    if ($listFilter != 'false')
        $clause = listFilter($listFilter, $search,$isExistFilter,$isExistField);

    // exit("select * from $db_name $clause");
    if (!empty(trim($clause))){
      $clause ='WHERE ' . $clause;
    }

    if($singleSort !=='false' && !is_array( $singleSort ) ){
      $temp = strtoupper($singleSort);
      if($temp==='RANDOM'){
        $clause = $clause .' order by rand()';
      }else{
        $clause = $clause .' order by '.$singleSort;
      }
    }

	/*else if($singleSort !=='false' && is_array( $singleSort ) ){
		$key = 0;
		foreach($singleSort as $sorter) {
			if( $sorter ) {
				if( $key == 0 ) {
					$clause = $clause .' order by '.$sorter; $key++;
				}
				else {
					$clause = $clause .', '.$sorter; $key++;
					$key++;
				}
			}
		}
	}*/
	//echo "SELECT * FROM $db_name $clause ";
    $user = $con->query("SELECT * FROM $db_name $clause ");
    //print "SELECT * FROM $db_name $clause";
    return $user;
}

function get_listFragment_record($db_name, $pkey, $listFilter = 'false', $limit = 'false', $fields = 'false') {


    $con = connect();


    $isExistFilter;$isExistField;
    if ($listFilter != 'false')
        $clause = listFilter($listFilter, $search,$isExistFilter,$isExistField);


    // exit("select * from $db_name $clause");

    if (!empty($clause))
        $clause = 'where ' . $clause;

    // exit("select * from $db_name $clause");

    if(!$fields)
        $fields = "*";

    if ($limit)
        $user = $con->query("select $fields from $db_name $clause limit 0, $limit");
    else
        $user = $con->query("select $fields from $db_name $clause");




    return $user;
}



/*
 * USE FOR LOGIN PAGE too ... (instead of just gping to logon.php
 *
 *
 * function Select_Data_FieldDictionary_Record($alias) {
 * *************************************
 *
 * function formating_Select($row) {
 * ************************************
 */

function Select_Data_FieldDictionary_Record($alias) {

    $con = connect();

// ***  DD OVERHAUL 2-18-2020 ... Here is where we can get into trouble replacing table_type with component_type
// ***  CJ:  Also --> here we are looking for table-type=User ... to validate login??

    $rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_alias = '$alias' and table_type='user' order by field_dictionary.field_order");

    $row = $rs->fetch_assoc();

    $_SESSION['select_table']['table_name'] = $row['table_name'];

    $_SESSION['select_table']['keyfield'] = firstFieldName($row['table_name']);


    $rs = $con->query("SELECT * FROM field_dictionary INNER JOIN data_dictionary ON data_dictionary.`table_alias` = field_dictionary.`table_alias` where data_dictionary.table_type='user' and data_dictionary.table_alias = '$alias'  order by field_dictionary.field_order");





    while ($row = $rs->fetch_assoc()) {


        formating_Select($row);
    }//// end of while loop
}

function formating_Select($row) {


    $field = $row[generic_field_name];


    $readonly = '';
    $required = '';
	echo "pre";
	print_r($row);
	die();


    if ($row['editable'] == 'false')
        $readonly = 'readonly';


    if (!empty($row['required']))
        $required = 'required';

    if ($row['format_type'] == 'email') {


        echo "<input type='text' name='$field' value='' $readonly $required title='$row[help_message]' class='form-control' placeholder='Enter your Username or Email'>";
    } else if ($row['format_type'] == 'password') {

        echo "<input type='password' name='$field' value='' $readonly $required title='$row[help_message]' class='form-control' placeholder='Enter your $row[field_label_name]'>";
    } else {

        // echo "<input type='hidden' name='$field' >";
        $_SESSION['select_table']['username'] = $field;
    }
}


function Footer($page, $menu_location = 'footer') {
    $con = connect();
    $rs = $con->query("SELECT * FROM navigation where page_name='$page' and item_number=0 and menu_location='$menu_location' AND item_target='override'");

    $classForNavBr2 = '';
    /*
     *
     * Checking whether user have access to current page or not
     */
     $overRide = false;
    if ($rs->num_rows > 0) {
         $overRide = true;
    }
  	$navItems = getNavItems($page,$menu_location,$overRide);
    if(count($navItems)==0){
      return;
    }
    ?>
    <!-- Navigation starts here -->
    <div class="navbar navbar-default navbar-fixed-bottom <?=$classForNavBr2 ?>">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">
                    <?php echo TOGGLE_NAVIGATION ?>
                </span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right <?= $item_style;?>">
                <?php
                if (isUserLoggedin()) {
					             $loginRequired = true;
                } else {
					$loginRequired = false;
					?>
				<?php
				}
				echo $menu = generateTopNavigation($navItems,$loginRequired);
				?>
            <?php ///////else if ends here                                                                                           ?>



            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>


    <?php
}
////////main navigation function ends here///



function is_FFFR_DD($componentType){
  $componentType = strtolower(trim($componentType));
  if(  $componentType=='friend'
    || $componentType=='follow'
    || $componentType=='favorite'
    || $componentType=='rating'
    || $componentType=='votiing'
    || $componentType=='contact_me'
  ){
    return true;
  }
  return false;
}

?>
