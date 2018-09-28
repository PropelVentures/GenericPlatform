<?php
include("../application/header.php");
$relevant_data = array("DAY" => "Today", "WEEK" => "Last Week", "MONTH" => "Last Month");
?>

<script src="<?php echo BASE_JS_URL ?>jquery.min.js"></script>
<script>

  jQuery.noConflict();

  jQuery(document).ready(function () {

    jQuery('#select_joined li').click(function () {
      var index = jQuery(this).index();
      var text = jQuery(this).text();
      var value = jQuery(this).attr('data-value');
      jQuery("#relevant").val(value);
      jQuery("#selected_joined_value").html(text);
      //alert('Index is: ' + index + ' , text is ' + text + ' and value is ' + value);
    });

    jQuery("#reset_search_popular_users").click(function () {

      $('#selected_joined_value').html("------<?php echo JOINED ?>------");
      jQuery("#relevant").val("");
    });

    jQuery('#sort_popular_users li').click(function () {

      var index = jQuery(this).index();

      var text = jQuery(this).text();

      var value = jQuery(this).attr('data-value');

      jQuery("#sort_hidden_value").val(value);
      jQuery("#sort_popular_users_value").html(text);

      //alert('Index is: ' + index + ' , text is ' + text + ' and value is ' + value);

    });

    /* Sorting function on SORT button click */
    jQuery("#sortList").click(function () {

      var sortby = jQuery('#sort_hidden_value').val();
      if (sortby == "Alpha") {
        $('#sortList').data("sortKey", ".project-info h3");				//setup sort attributes
        sortUsingNestedText(jQuery('#popular_users'), "a", jQuery(this).data("sortKey"));
      }
      if (sortby == "Date") {
        $('#sortList').data("sortKey", ".project-info .date");			//setup sort attributes
        sortUsingNestedDate(jQuery('#popular_users'), "a", jQuery(this).data("sortKey"));
      }

    });

    /******Alphabetically Sorting function starts here******/
    function sortUsingNestedText(parent, childSelector, keySelector) {
      var items = parent.children(childSelector).sort(function (a, b) {
        var vA = jQuery(keySelector, a).text().toLowerCase();
        var vB = jQuery(keySelector, b).text().toLowerCase();
        return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
      });
      parent.append(items);
    }

    /******Date Sorting function starts here******/
    function sortUsingNestedDate(parent, childSelector, keySelector) {
      var items = parent.children(childSelector).sort(function (a, b) {
        var firstDate = new Date(jQuery(keySelector, a).text());
        var secondDate = new Date(jQuery(keySelector, b).text());
        return (firstDate < secondDate) ? -1 : (firstDate > secondDate) ? 1 : 0;
      });
      parent.append(items);
    }

  });

</script>

<div class="slider">
  <div id="myCarousel" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>

    <div class="carousel-inner">
      <div class="item active"> <img src="<?php echo BASE_IMAGES_URL ?>slide1.jpg" alt="" class="img-responsive">
        <div class="container">
          <div class="carousel-caption slide1">
            <h1><?php echo HOME_SLIDER_TITLE1 ?></h1>
            <p><?php echo HOME_SLIDER_CONTENT1 ?></p>
            <p><a class="btn btn-lg btn-primary" href="#" role="button"><?php echo HOME_SLIDER_BUTTON_TEXT1 ?></a></p>
          </div>
        </div>
      </div>
      <div class="item"> <img src="<?php echo BASE_IMAGES_URL ?>slide2.jpg" alt="" class="img-responsive">
        <div class="container">
          <div class="carousel-caption slide2">
            <h1><?php echo HOME_SLIDER_TITLE2 ?></h1>
            <p><?php echo HOME_SLIDER_CONTENT2 ?></p>
            <p><a class="btn btn-lg btn-primary" href="#" role="button"><?php echo HOME_SLIDER_BUTTON_TEXT2 ?></a></p>
          </div>
        </div>
      </div>

      <div class="item"> <img src="<?php echo BASE_IMAGES_URL ?>slide3.jpg" alt="" class="img-responsive">
        <div class="container">
          <div class="carousel-caption slide3">
            <h1><?php echo HOME_SLIDER_TITLE3 ?></h1>
            <p><?php echo HOME_SLIDER_CONTENT3 ?></p>
            <p><a class="btn btn-lg btn-primary" href="#" role="button"><?php echo HOME_SLIDER_BUTTON_TEXT3 ?></a></p>
          </div>
        </div>
      </div>
    </div>

    <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left"></span></a> <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right"></span></a> </div>

  <!-- /.carousel -->
</div>

<div class="jumbotron search-form">
  <div class="container">
    <div class="row row-offcanvas row-offcanvas-right">
      <div class="col-12 col-sm-12 col-lg-12">
        <h3><?php echo SEARCH_USERS ?>:</h3>
       	<form action="">
          <input type="text" placeholder="Username" name="username" id="username" class="input-bg form-control" value="" />
          <input type="text" placeholder="Location" name="location" id="location"  class="input-bg form-control"  value="" />
          <input type="text" placeholder="State" name="state" id="state" class="input-bg form-control"  value="">
          <input type="text" placeholder="Country" name="country" id="country" class="input-bg form-control"  value="">
          <input type="hidden"  name="relevant" id="relevant" class="input-bg form-control"  value="">
          <div class="btn-group select">
            <button type="button" class="btn btn-danger main-select" id="selected_joined_value">------<?php echo JOINED ?>------</button>
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
            <ul class="dropdown-menu" role="menu" id="select_joined">
              <li data-value="DAY"><a><?php echo TODAY ?></a></li>
              <li data-value="WEEK"><a><?php echo LAST_WEEK ?></a></li>
              <li data-value="MONTH"><a><?php echo LAST_MONTH ?></a></li>
            </ul>
          </div>
          <input type="button" value="<?php echo SEARCH_BUTTON ?>" name="submitSearch" class="btn btn-primary" id="search_popular_users">
          <input type="reset" value="<?php echo RESET ?>" name="resetSearch" class="btn btn-primary" id="reset_search_popular_users">
        </form>
      </div>
    </div>
    <!--/row-->

  </div>
  <!--/.container-->
</div>

<div class="jumbotron projects">
  <div class="container">
    <div class="row">
      <div class="col-6 col-sm-6 col-lg-6 sortby">
        <h3><?php echo SORT_BY ?></h3>
        <span>
          <div class="btn-group select2">
            <button type="button" class="btn btn-danger main-select2" id="sort_popular_users_value">---Select----</button>
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
            <ul class="dropdown-menu" role="menu" id="sort_popular_users">
              <li data-value="Alpha"><a><?php echo ALPHABETICALL ?></a></li>
              <li data-value="Date"><a><?php echo DATE_JOINED ?></a></li>
              <li data-value="Relevance"><a><?php echo RELEVANCE ?></a></li>
            </ul>
          </div>
        </span> <span>
          <input type="hidden"  name="sort_hidden_value" id="sort_hidden_value" class="input-bg form-control"  value="">
          <input type="button" id="sortList" value="<?php echo SORT_BUTTON ?>" name="sortList" class="btn btn-primary">
        </span> </div>
      <div class="col-6 col-sm-6 col-lg-6 grid-type">

        <span id="listView" class="glyphicon glyphicon-align-right"></span>

        <span id="boxView" class="glyphicon glyphicon-th-large"></span>

        <span id="thumbView" class="glyphicon glyphicon-th-list"></span>

        <span></span> </div>



    </div>
    <!--Loading Popular Users from ajax call -->
    <div class="row" id="popular_users" ></div>
    <div id="pagination_div" style="float: left;text-align: center;"></div>

  </div>
</div>

<div class="jumbotron bottom-area">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h3  data-scroll-reveal="enter top over 1s and move 100px"><?php echo HOME_FOOTER_TITLE ?></h3>
        <p><?php echo HOME_FOOTER_CONTENT ?></p>
        <p  data-scroll-reveal="enter bottom over 1s and move 100px"><a class="btn btn-lg btn-primary" href="#" role="button"><?php echo HOME_FOOTER_BUTTON_TEXT ?></a></p>
      </div>
    </div>
  </div>
</div>

<div id="list-view-hold" style="display: none;"></div>
<script>

</script>
<script>
  jQuery(document).ready(function () {
    jQuery(window).bind("load", function () {
      page = 1;
      limit = 8;
      startpoint = (page * limit) - limit;
      statement = 'records where active = 1';
      query_string = 'action=pagination_users&query=' + statement + '&per_page=' + limit + '&page=' + page;
      jQuery.get('<?php echo BASE_URL_SYSTEM ?>ajax-actions.php', query_string, function (data) {
        var sHTML = data;
        jQuery('#pagination_div').html(sHTML);
      });
      query_string = 'action=pagination_users&query=' + statement + '&startpoint_users=' + startpoint + '&limit=' + limit;
      jQuery.get('<?php echo BASE_URL_SYSTEM ?>ajax-actions.php', query_string, function (data) {
        var sHTML = data;
        jQuery('#popular_users').html(sHTML);
      });
    }); //end load

    jQuery(document).on("click", "ul.pagination li a", function () {
      //alert($(this).attr('title'));
      if (jQuery(this).attr('title') != 'current')
      {
        page = jQuery(this).attr('title');
        limit = 8;
        startpoint = (page * limit) - limit;
        statement = 'records where active = 1';
        query_string = 'action=pagination_users&query=' + statement + '&per_page=' + limit + '&page=' + page;
        jQuery.get('<?php echo BASE_URL_SYSTEM ?>ajax-actions.php', query_string, function (data) {
          var sHTML = data;
          jQuery('#pagination_div').html(sHTML);
        });
        query_string = 'action=pagination_users&query=' + statement + '&startpoint_users=' + startpoint + '&limit=' + limit;
        jQuery.get('<?php echo BASE_URL_SYSTEM ?>ajax-actions.php', query_string, function (data) {
          var sHTML = data;
          jQuery('#popular_users').html(sHTML);
        });
      }
      return false;
    }); //end event

    jQuery(document).on("click", "#search_popular_users", function () {
      //alert($(this).attr('title'));
      var user_name = $("#username").val();
      var location = $("#location").val();
      var state = $("#state").val();
      var country = $("#country").val();
      var joined = $("#relevant").val();
      search_startpoint = 1;
      statement = 'records where active = 1';

      query_string = 'action=search_pagination_users&user_name=' + user_name + '& location=' + location + '&state=' + state + '&country=' + country + '&joined=' + joined + '&search_startpoint=' + search_startpoint + '&limit=' + limit;
      jQuery.get('<?php echo BASE_URL_SYSTEM ?>ajax-actions.php', query_string, function (data) {
        var sHTML = data;
        jQuery('#pagination_div').hide();
        jQuery('#popular_users').html(sHTML);
      });
      return false;
    }); //end event

    jQuery(document).on('click', '.grid-type span', function () {
      var gridType = $(this).attr('id');
      //alert(gridType);
      /*if(gridType == "listView"){
       jQuery(".project-details-wrapper").removeClass().addClass("project-details-wrapper listView");
       jQuery(".project-details-wrapper > div:first-child").removeClass().addClass("col-12 col-sm-12 col-lg-12");
       $("#copy_popular").html($("#popular_users").html());
       //var tablename=$("#listView").attr("rel");
       $("#popular_users").html($("#list-view-hold").html());
       }

       if(gridType == "boxView"){
       *//* if($("#list-view-hold").html().length > 20){
        $("#popular_users").html($("#list-view-hold").html());
        }*//*
         $("#popular_users").html($("#copy_popular").html());
         jQuery(".project-details-wrapper").removeClass().addClass("project-details-wrapper");
         //jQuery(".project-details-wrapper .profile-image").hide();
         jQuery(".project-details-wrapper > div:first-child").removeClass().addClass("col-6 col-sm-6 col-lg-3");

         }

         if(gridType == "thumbView"){
         *//*if($("#list-view-hold").html().length > 20){
          $("#popular_users").html($("#list-view-hold").html());
          }*//*
           $("#popular_users").html($("#copy_popular").html());
           jQuery(".project-details-wrapper").removeClass().addClass("project-details-wrapper thumbView");
           jQuery(".project-details-wrapper > div:first-child").removeClass().addClass("col-12 col-sm-12 col-lg-12");
           }*/
      if (gridType == "listView") {
        /*jQuery(".project-details-wrapper").removeClass().addClass("project-details-wrapper listView");
         jQuery(".project-details-wrapper > div:first-child").removeClass().addClass("col-12 col-sm-12 col-lg-12");
         $("#copy_popular").html($("#popular_users").html());*/
        //var tablename=$("#listView").attr("rel");
        $("#popular_users").html($("#list-view-hold > #tablecontent").html());

      }

      if (gridType == "boxView") {
        /*if($("#list-view-hold").html().length > 20){
         $("#popular_users").html($("#list-view-hold").html());
         }*/
        /*$("#popular_users").html($("#copy_popular").html());
         jQuery(".project-details-wrapper").removeClass().addClass("project-details-wrapper");
         //jQuery(".project-details-wrapper .profile-image").hide();
         jQuery(".project-details-wrapper > div:first-child").removeClass().addClass("col-6 col-sm-6 col-lg-3");		*/
        $("#popular_users").html($("#list-view-hold > #box-view-container").html());
      }

      if (gridType == "thumbView") {
        /*if($("#list-view-hold").html().length > 20){
         $("#popular_users").html($("#list-view-hold").html());
         }*/
        /*$("#popular_users").html($("#copy_popular").html());
         jQuery(".project-details-wrapper").removeClass().addClass("project-details-wrapper thumbView");
         jQuery(".profile-image img").css("height", "none");
         jQuery(".project-details-wrapper > div:first-child").removeClass().addClass("col-12 col-sm-12 col-lg-12");*/
        $("#popular_users").html($("#list-view-hold > #thumb-view-container").html());
      }
      /*$(".rating-container").html("");*/
      $(".rating-container").html("");
    });

    jQuery(document).on('click', '.relationship', function (e) {
      //alert("sure you want to like this fellow?");
      e.preventDefault();
      var currentObject = $(this);
      var userLiked = $(this).attr('data-id');
      var getClass = $(this).attr('class');
      var action = getClass.split(' ')[1];
      if (currentObject.attr('data-active') == 0) {
        $.post(
                '<?php echo BASE_URL_SYSTEM ?>ajax-actions.php',
                {userLiked: userLiked, relationship_action: action},
        function (data) {
          if (data) {
            currentObject.attr('data-active', '1');
            currentObject.children().addClass('user_liked');
          } else {
            alert("Sorry you cant like this user.");
          }
        }
        );
      }
      else {
        $.post(
                '<?php echo BASE_URL_SYSTEM ?>ajax-actions.php',
                {userLiked: userLiked, relationship_action: "unLikeUser"},
        function (data) {
          if (data) {
            currentObject.attr('data-active', '0');
            currentObject.children().removeClass('user_liked');
          } else {
            alert("Sorry you cant like this user.");
          }
        }
        );
      }

    });

  }); //end document ready


  jQuery("#myprojects_tab").click(function () {
    jQuery("#myprojects_tab").toggleClass("open");
  });
</script>
<div id="copy_popular" style="display: none;">
  <?php
  $_REQUEST['tab'] = $userTblArray['table_alias'];
  $_REQUEST['pname'] = $userTblArray['table_alias'];
  include($GLOBALS['APP_DIR']."socprox3.0/views/table.view.php");
  ?>
</div>
<div id="list-view-hold" style="display: none;"></div>
<?php include("../application/footer.php"); ?>
