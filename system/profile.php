<?php
////////////////////////
////////////////////////////////////
/////////////////////////////////////////////////////////


require_once("../appConfig/appConfig.php");
include_once("../application/database/db.php");
include("../application/header.php");


$display_page = $_GET['display'];

$page_layout_style = $_GET['layout'];

$style = $_GET['style'];


if (isset($_GET['tab']) || !empty($_GET['tab'])) {
    $_SESSION['tab'] = $_GET['tab'];
} else {
    $_SESSION['tab'] = '';

    unset($_SESSION['tab']);
}


//echo($_SESSION['return_url']) . "<br>";
//exit( $_SESSION['add_url_list']);
///// copy these two files for displaying navigation/////


Navigation($display_page);
//////////////


if ($display_page == 'home') {
    ?>

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
                            <h1>
                                <?php echo HOME_SLIDER_TITLE1 ?>
                            </h1>
                            <p>
                                <?php echo HOME_SLIDER_CONTENT1 ?>
                            </p>
                            <p><a class="btn btn-lg btn-primary" href="#" role="button">
                                    <?php echo HOME_SLIDER_BUTTON_TEXT1 ?>
                                </a></p>
                        </div>
                    </div>
                </div>
                <div class="item"> <img src="<?php echo BASE_IMAGES_URL ?>slide2.jpg" alt="" class="img-responsive">
                    <div class="container">
                        <div class="carousel-caption slide2">
                            <h1>
                                <?php echo HOME_SLIDER_TITLE2 ?>
                            </h1>
                            <p>
                                <?php echo HOME_SLIDER_CONTENT2 ?>
                            </p>
                            <p><a class="btn btn-lg btn-primary" href="#" role="button">
                                    <?php echo HOME_SLIDER_BUTTON_TEXT2 ?>
                                </a></p>
                        </div>
                    </div>
                </div>
                <div class="item"> <img src="<?php echo BASE_IMAGES_URL ?>slide3.jpg" alt="" class="img-responsive">
                    <div class="container">
                        <div class="carousel-caption slide3">
                            <h1>
                                <?php echo HOME_SLIDER_TITLE3 ?>
                            </h1>
                            <p>
                                <?php echo HOME_SLIDER_CONTENT3 ?>
                            </p>
                            <p><a class="btn btn-lg btn-primary" href="#" role="button">
                                    <?php echo HOME_SLIDER_BUTTON_TEXT3 ?>
                                </a></p>
                        </div>
                    </div>
                </div>
            </div>
            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left"></span></a> <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right"></span></a> </div>
        <!-- /.carousel -->
    </div>



<?php } ?>


<div class="container main-content-container">





    <!-- Left sidebar content Area --> 
    <?php
    /*
     * Finding page layout by DD->tab_num
     */

    $con = connect();

    $rs = $con->query("SELECT tab_num FROM data_dictionary where display_page='$display_page'");

//$right_sidebar = $left_sidebar = 'false';

    while ($row = $rs->fetch_assoc()) {


        $r1 = explode('w', trim($row['tab_num']));
       
        if (!empty($r1[1])) {

            if ($r1[0] == 'R1')
                $right_sidebar_width = $r1[1];
            else
                $left_sidebar_width = $r1[1];
        }


        if ($r1[0] == 'R1') {

            $right_sidebar = 'right';
        }

        if ($r1[0] == 'L1') {

            $left_sidebar = 'left';
        }
    }

    if ($left_sidebar == 'left' && $right_sidebar == 'right') {

        $both_sidebar = 'both';
    }



    /*
     * left sidebar code
     */

    sidebar($left_sidebar, $both_sidebar, $display_page, $right_sidebar_width);




    /*
     * displaying tab area
     */

    // $total_width = 0;

    if ($_GET['child_list_active'] == 'isSet')
        echo "<a href='#' class='goBackToParent'>click me</a>";

    if (!empty($right_sidebar_width) && !empty($left_sidebar_width)) {

        $total_width = 100 - ( $right_sidebar_width + $left_sidebar );

        echo "<div class='center-container' style='width:$total_width%;float:left;' >";
    } else if (!empty($right_sidebar_width) && empty($left_sidebar_width)) {

        $total_width = 100 - $right_sidebar_width;

        echo "<div class='center-container content-manual' style='width:$total_width%;float:left;'>";
    } else if (empty($right_sidebar_width) && !empty($left_sidebar_width)) {

        $total_width = 100 - $left_sidebar;

        echo "<div class='center-container' style='width:$total_width%;float:left;'>";
    } else {
        if ($both_sidebar == 'both') {
            echo "<div class='col-lg-8 center-container'>";
        } else if ($both_sidebar != 'both' && ( $right_sidebar == 'right' || $left_sidebar == 'left' )) {

            echo "<div class='col-9 col-sm-9 col-lg-9 center-container' >";
        } else {
            echo "<div class='col-12 col-sm-12 col-lg-12 center-container'>";
        }
    }
//if( $both_sidebar == 'false' &&  $right_sidebar == 'false' && $left_sidebar == 'false'  )
    ?>



    <!-- Tab Content area .. -->
    <?php
    if (isset($page_layout_style) && ($page_layout_style == 'serial-layout')) {
        serial_layout($display_page, $style);
    } else {

        $rs = $con->query("SELECT * FROM data_dictionary where display_page='$display_page' and tab_num='0'");

        $row = $rs->fetch_assoc();
        if (!empty($row)) {
            $tab_status = 'true';

            $_SESSION['display2'] = $display_page;

            Get_Data_FieldDictionary_Record($tab, $display_page, $tab_status);
        } else {
			
            $_SESSION['display2'] = '';

            unset($_SESSION['display2']);

            echo Get_Links($display_page); 
 
            global $tab;

            $tab_status = 'false';
			
            if (isset($_SESSION['tab'])) { 
                Get_Data_FieldDictionary_Record($_SESSION['tab'], $display_page, $tab_status);
				
			} else {
                Get_Data_FieldDictionary_Record($tab, $display_page, $tab_status);
            }
        }/// tab_num else ends here
    }//// page_layout           
    ?>
    <div style="clear:both"></div>
</div>

<!-- Right sidebar content Area -->


<?php
/*
 * Right sidebar code
 */

sidebar($right_sidebar, $both_sidebar, $display_page, $left_sidebar_width);
?>

</div>




<script src="<?= BASE_URL ?>/ckeditor/ckeditor.js"></script>




<!-- modal view dialog to display  Enlarge image -->


<div id="imgModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

                <img src="" class="img-responsive img-modal">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- modal view dialog to display  Enlarge image Ends here-->




<!-- Modal For voting Dialog display -->
<div class="modal fade" id="votingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= AlERTBOX ?></h4>
            </div>
            <div class="modal-body votingBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>





<!-- Modal For Transaction Dialog display -->
<div class="modal fade" id="transModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= transTitle ?></h4>
            </div>
            <div class="modal-body transBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>






<ul class='image-menu'>
    <li data-action='upload'  class='popup-class'>Upload/Replace Image</li>
    <li data-action='enlarge'  class='popup-class'>View Image</li>
    <li data-action='remove'  class='popup-class'>Remove</li>
    <li data-action='revert'  class='popup-class'>Revert Changes</li>

</ul>



<ul class='pdf-menu'>
    <li data-action='upload'  class='popup-class'>Upload/Replace PDF FILE</li>
    <li data-action='enlarge'  class='popup-class'>View PDF FILE</li>
    <li data-action='remove'  class='popup-class'>Remove</li>
    <li data-action='revert'  class='popup-class'>Revert Changes</li>

</ul>


<?php
/*
 * Popup Menu codes starts here
 */

global $popup_menu;

if ($popup_menu['popupmenu'] == 'true') {

    echo "<ul class='custom-menu'>";

    if (isset($popup_menu['popup_delete']) && !empty($popup_menu['popup_delete'])) {



        echo "<li data-action='delete'  class='" . $popup_menu['popup_delete']['style'] . "'>" . $popup_menu['popup_delete']['label'] . "</li>";
    }

    if (isset($popup_menu['popup_add']) && !empty($popup_menu['popup_add'])) {



        echo "<li data-action='add'  class='" . $popup_menu['popup_add']['style'] . "'>" . $popup_menu['popup_add']['label'] . "</li>";
    }


    if (isset($popup_menu['popup_copy']) && !empty($popup_menu['popup_copy'])) {



        echo "<li data-action='copy'  class='" . $popup_menu['popup_copy']['style'] . "'>" . $popup_menu['popup_copy']['label'] . "</li>";
    }

    if (isset($popup_menu['popup_copy']) && !empty($popup_menu['popup_copy'])) {



        echo "<li data-action='openChild'  class='" . $popup_menu['popup_openChild']['style'] . "'>" . $popup_menu['popup_openChild']['label'] . "</li>";
    }

    echo "</ul>";
}
?>


<a href="#" class="scrollToTop">Scroll To Top</a>
<?php 
/* $page_no = '';
 $pagination_no = explode(';',$_SESSION['list_pagination']);
 for($j=0;$j<count($pagination_no);$j++)
 {
	$newvar =  explode(',',$pagination_no[$j]);
		//echo strpos(trim($newvar[0]),'pagination')."<br>"; 
		if(strpos(trim($newvar[0]),'pagination')=='0') 
		{
			//echo "hello";
		 $page_no = $newvar[1]; break; 
		}
	 
 } */
 $page_no = $_SESSION['list_pagination'];
 ?>
<script>


    $(document).ready(function () {

        $('.display').DataTable({
            "scrollX": true,
			"pagingType": "full_numbers",
			"lengthMenu": [[<?php if(!empty($page_no)){echo (int)$page_no;}else{ echo 10;}?>, 25, 50, -1], ['<?php if(!empty($page_no)){echo (int)$page_no;}else{ echo 10;}?>', 25, 50, "All"]]
        });
        //// to stop from going to edit screen//

        $('.list-checkbox').on('click', function () {

            event.stopImmediatePropagation();
        });




        /*
         * 
         * Selecting all checkboxes
         * 
         */

        $('#selectAll').click(function (event) {  //on click 
            if (this.checked) { // check select status
                $('.list-checkbox').each(function () { //loop through each checkbox
                    this.checked = true; //select all checkboxes with class "checkbox1"               
                });
            } else {
                $('.list-checkbox').each(function () { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "checkbox1"                       
                });
            }
        });






///when click on delete button////
        $(".action-delete").on('click', function () {

            if (confirm("<?= deleteConfirm ?>") == true) {
                $("#checkHidden").val('delete');
                $('#list-form').ajaxForm(function (data) {
                    // alert(data);
                    // return false;
                    location.reload();
                });

            } else {

                $(this).parents('#list-form').attr('action', '');
            }
        });
        ///// when click on delete icon
        $(".list-del").click(function (event) {


            if (confirm("ok") == true) {


                var del_id = $(this).attr('id');
                var dict_id = $(this).attr('name');
                $.ajax({
                    method: "GET",
                    url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                    data: {list_delete: del_id, check_action: "delete", dict_id: dict_id}
                })
                        .done(function (msg) {

                            location.reload();
                        });
            } else {

                event.stopImmediatePropagation();
            }

        });
///copy button .. multi select

        $(".action-copy").on('click', function () {

            if (confirm("<?= copyConfirm ?>") == true) {
                $("#checkHidden").val('copy');
                $('#list-form').ajaxForm(function (data) {
                    // console.log(data);
                    location.reload();
                });

            } else {

                $(this).parents('#list-form').attr('action', '');
            }
        });
        //// single copy icon
        $(".list-copy").click(function () {


            if (confirm("Are you sure ,You want to Copy the Record!") == true) {


                var del_id = $(this).attr('id');
                var dict_id = $(this).attr('name');
                $.ajax({
                    method: "GET",
                    url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                    data: {list_copy: del_id, check_action: "copy", dict_id: dict_id}
                })
                        .done(function (msg) {
                            location.reload();
                        });
            }

        });
        /******************* ADD BUTTON CODE **********************************/

        $(".action-add").click(function () {


            window.location.href = '<?= $_SESSION['add_url_list'] ?>';
        });
        /*
         
         var test = 'something ';
         $(".span-checkbox").click(function(){
         
         test = test.concat($(this).html());
         
         alert(test);
         
         
         });
         
         */

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


        $('#example tbody').on('click', 'tr', function () {

            if ($(this).hasClass('tabholdEvent')) {
                return false;
            } else {

                window.location = $(this).attr('id');
            }

        });






        /*
         * it calls when right click on single line list
         */

        var popup_del;
        var dict_id;
        // Trigger action when the contexmenu is about to be shown
        /// it will be shown for boxView





        if (mobileDetector().any()) {

            $("#example tbody").on("taphold", 'tr', function (event) {

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
                $(".custom-menu").finish().toggle(100).
                        // In the right position (the mouse)
                        css({
                            top: yPos + "px",
                            left: xPos + "px"
                        });


            });


        } else {

            ////context MEnu will be shown for TableView

            $("#example tbody").on("contextmenu", 'tr', function (event) {


                popup_del = $(this).find('.list-del').attr('id');
                dict_id = $(this).find('.list-del').attr('name');
                //console.log(dict_id);
                // Avoid the real one
                event.preventDefault();
                // Show contextmenu
                $(".custom-menu").finish().toggle(100).
                        // In the right position (the mouse)
                        css({
                            top: event.pageY + "px",
                            left: event.pageX + "px"
                        });


            });


        }




// If the document is clicked somewhere
        $(document).bind("mousedown", function (e) {

            // If the clicked element is not the menu
            if (!$(e.target).parents(".custom-menu").length > 0) {

                // Hide it
                $(".custom-menu").hide(100);


            }
        });
// If the menu element is clicked
        $(".custom-menu li").click(function () {

            // This is the triggered action name
            switch ($(this).attr("data-action")) {

                // A case for each action. Your actions here
                case "delete":
                    popup_delete(popup_del, dict_id);
                    break;
                case "copy":
                    popup_copy(popup_del);
                    break;
                case "add":
                    popup_add(dict_id);
                    break;
                case "openChild":
                    popup_openChild(popup_del, dict_id);
                    break;
            }

            // Hide it AFTER the action was triggered
            $(".custom-menu").hide(100);
        });





        /***** popup DELETE Function ****/

        function popup_delete(del_id, dict_id) {


            if (confirm("Are you sure ,You want to delete the Record!") == true) {



                $.ajax({
                    method: "GET",
                    url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                    data: {list_delete: del_id, check_action: "delete", dict_id: dict_id}
                })
                        .done(function (msg) {
                            location.reload();
                        });
            }
        }


        /***** popup COPY Function ****/

        function popup_copy(del_id) {


            if (confirm("Are you sure ,You want to copy the Record!") == true) {



                $.ajax({
                    method: "GET",
                    url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                    data: {list_copy: del_id, check_action: "copy"}
                })
                        .done(function (msg) {
                            location.reload();
                        });
            }
        }

        /***************************** POPUP ADD***************************/



        function popup_add(dict_id) {

            if (confirm("Are you sure ,You want to go to ADD Record!") == true) {

                $.ajax({
                    method: "GET",
                    url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                    data: {list_add: dict_id, check_action: "add", url: window.location.href}
                })
                        .done(function (msg) {

                            // console.log(msg);
                            window.location = msg;
                        });
            }
        }

        /***** popup openChild Function ****/

        function popup_openChild(del_id, dict_id) {

            // if (confirm("Are you sure ,You want to go to Child list Record!") == true) {



            $.ajax({
                method: "GET",
                url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                data: {childID: del_id, check_action: "openChild", dict_id: dict_id, display: "<?= $_GET['display']; ?>"}

            })
                    .done(function (child_url) {

                        window.location = child_url;
                        // window.open(msg,'','width=800,height=768,left=300');
                    });
            //}
        }




        ///IMAGE FIELD CANCEL BUTTON ACTion

        $(".img-cancel").click(function () {

            var profile_img = $(this).attr("name");
            $.ajax({
                method: "GET",
                url: "ajax-actions.php",
                data: {img_cancel: "img-cancel", profile_img: profile_img}
            })
                    .done(function () {
//alert(msg);
                        location.reload();
                    });
        });
        $(".tab-class").click(function () {

            var tab_name = '<?= $_GET['tab'] ?>';
            var tab_num = '<?= $_GET['tabNum'] ?>';
            $.ajax({
                method: "GET",
                url: "ajax-actions.php",
                data: {tab_check: "true", tab_name: tab_name, tab_num: tab_num}
            })
            //.done(function (msg) {




            //});




        });
        $(".remove-audio-btn").click(function () {

            //audio_code = $(this).prev(".audio-css").html();

            var field_name = $(this).attr("id");
            $(this).siblings(".audio-css").fadeOut("slow");
            $(this).fadeOut("slow");
            $(this).next(".audio-placing").html("<input type='file' name='" + field_name + "' class='form-control'><input type='button' class='btn btn-primary update-btn pull-left audio-cancel rem-img-size'  value='CANCEL'/>");
        });
        $(".audio-placing").on("click", ".audio-cancel", function () {

            $(this).parents(".audio-placing").siblings(".audio-css").fadeIn("slow");
            $(this).parents(".audio-placing").siblings(".remove-audio-btn").fadeIn("slow");
            $(this).parents(".audio-placing").siblings(".remove-audio-btn").after("<div class='audio-placing'></div>");
            $(this).parents(".audio-placing").remove();
        });


        /*****
         * *************Back to top js
         */

//Check to see if the window is top if not then display button
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('.scrollToTop').fadeIn();
            } else {
                $('.scrollToTop').fadeOut();
            }
        });
        //Click event to scroll to top
        $('.scrollToTop').click(function () {
            $('html, body').animate({scrollTop: 0}, 800);
            return false;
        });


        /*
         * **************************
         * **********************************************
         * **************************************************BACK TO LSIT
         * ***************
         * **********************************
         * 
         */

        var form_edit = '';



        $('form:not(.profile_page) :input').change(function () {

            form_edit = 'changed';
        });

        $(".back-to-list").click(function (event) {

  if( $(this).parents('#user_profile_form').hasClass('profile_page') ){
      
       window.location = $(this).attr('href');
        }else{
            if (form_edit == 'changed') {

                event.preventDefault();

                if (confirm("<?= backAlertMsg ?>") == true) {

                    //console.log($(this).attr('href'));
                    window.location = $(this).attr('href');
                }

            }
            
            }

        });



        /*********
         * ******************
         * ************************
         */

        /************** Enabling submit and cancel button *******/


        /*********
         * ******************
         * ************************
         */


        $(".edit-btn").click(function () {


            var id = $(this).attr('id');
            $.ajax({
                method: "GET",
                url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                data: {id: id, check_action: "enable_edit", form_edit_conf: form_edit}
            })
                    .done(function (msg) {
                        if ($.trim(msg) == 'active') {
                            alert('<?= editBtnAlertMsg ?>');
                        } else {
                            location.reload();
                        }
                    });
        });

        /*
         * 
         * Friend ICONS CODE GOES HERE****************
         * ************************************
         * *****************************************************
         * ********************************************************************
         */

        var class_holder;

        $(".friend_me_icon").click(function () {

            var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
            class_holder = this;
            $.ajax({
                method: "GET",
                url: "ajax-actions.php",
                data: {action: "friend_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id')}
            })
                    .done(function (msg) {

                        if (msg == 'deleted') {

                            $(class_holder).text('<?= friendOn ?>');
                        } else {

                            $(class_holder).text('<?= friendOff ?>');
                        }


                    });




        });



        /*
         * 
         * Follow me ICONS CODE GOES HERE****************
         * ************************************
         * *****************************************************
         * ********************************************************************
         */



        $(".follow_me_icon").click(function () {

            var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';
            class_holder = this;

            $.ajax({
                method: "GET",
                url: "ajax-actions.php",
                data: {action: "follow_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id')}
            })
                    .done(function (msg) {


                        if (msg == 'deleted') {

                            $(class_holder).text('<?= followOn ?>');
                        } else {

                            $(class_holder).text('<?= followOff ?>');
                        }


                    });




        });



        /*
         * 
         * Favorite me ICONS CODE GOES HERE****************
         * ************************************
         * *****************************************************
         * ********************************************************************
         */


        $(".favorite_me_icon").click(function () {

            // $(this).css('color','red');

            class_holder = this;
            var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';

            $.ajax({
                method: "GET",
                url: "ajax-actions.php",
                data: {action: "favorite_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id')}
            })
                    .done(function (msg) {


                        if (msg == 'deleted') {

                            $(class_holder).removeClass('favorite_me_icon_selected');

                            $(class_holder).addClass('favorite_me_icon');

                        } else {
                            $(class_holder).removeClass('favorite_me_icon');

                            $(class_holder).addClass('favorite_me_icon_selected');
                        }


                    });




        });


        /*
         * 
         * Rate me ICONS CODE GOES HERE****************
         * ************************************
         * *****************************************************
         * ********************************************************************
         */



        $('.rate_me').on('rating.change', function (event, value, caption) {


            // class_holder = this;
            var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';

            $.ajax({
                method: "GET",
                url: "ajax-actions.php",
                data: {action: "rate_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id'), value: value}
            })

                    .done(function (msg) {

                        if (msg != '' && msg != 'deleted') {


                            $('.votingBody').html(msg);

                            $('#votingModal').modal('show');

                        }
                    });


        });

        ///////////when rating is reset////////////
        $('.rate_me').on('rating.clear', function (event) {

            var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';

            $.ajax({
                method: "GET",
                url: "ajax-actions.php",
                data: {action: "rate_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id'), value: 'clear'}
            })
        });





        /*
         * 
         * Voting Number CODE GOES HERE****************
         * ************************************
         * *****************************************************
         * ********************************************************************
         */



        $('.voting-number:not(.disabled)').on('click', function () {


            // class_holder = this;
            var fffr_search_id = '<?= $_SESSION['fffr_search_id'] ?>';

            var value = $(this).siblings(".fffr-input").val();


            $.ajax({
                method: "GET",
                url: "ajax-actions.php",
                data: {action: "rate_me", fffr_search_id: fffr_search_id, table_name: $(this).attr('id'), value: value,ta:'<?= $_GET[tab]?>',tabNum:'<?= $_GET[tabNum]?>'}
            })

                    .done(function (msg) {

                        console.log(msg);

                        if (msg == 'deleted') {

                            $('.votingBody').html("<?= voteInserted ?>");

                            $('#votingModal').modal('show');


                            setTimeout(function () {
                                $('#votingModal').modal('hide');
                            }, 3000);


                        } else {


                            $('.votingBody').html(msg);

                            $('#votingModal').modal('show');

                        }
                    });


        });



        /*******
         * 
         * ********
         * ***********
         * ************************HIDING UPDATE/CANCEL BUTTON WHEN FFFR PRESENT
         * *********
         * ************************
         */

        if ($(".user-profile").find(".fffr").length) {

            $(".user-profile").find(".form-footer").hide();
        }



        $(".goBackToParent").click(function () {


            window.top.close();
        });



        ////////////stop the anchor tag action when prev/next buttons are disabled

        $(".editPagePagination").children(".disabled").click(function (event) {


            event.preventDefault();
        });

        /******
         * **********
         * *******************Transaction Js code goes here 
         * *******
         * *************************
         * 
         */



        $('.transaction_execute').on('click', function () {


            // class_holder = this;
            var trans_id = $(this).parents("#user_profile_form").find("select").val();


            var project_id = '<?= $_GET['search_id'] ?>';


            var display = '<?= $_GET['display'] ?>';

            var ta = '<?= $_GET['ta'] ?>';

            var dd_id = $(this).attr('id');



            $.ajax({
                method: "GET",
                url: "../appConfig/custom-functions.php",
                dataType: 'json',
                data: {action: "execute_trans", project_id: project_id, display: display, ta: ta, trans_id: trans_id, dd_id: dd_id}
            })

                    .done(function (msg) {


                        var text_msg = '';
                        $.each(msg, function (index, value) {

                            text_msg = text_msg + value;

                        });


                        // console.log(text_msg);
                        $('.transBody').html(text_msg);

                        $('#transModal').modal('show');

                    });//transaction_cancel


        });




        /******
         * **********
         * *******************Transaction Action ,when user Confirms the Transaction
         * *******
         * *************************
         * 
         */



        $(document).on('click', '.transaction_confirmation', function () {




            var impData = $(this).parents('#transModal').find(".insertRecord").val();


            $.ajax({
                method: "GET",
                url: "../appConfig/custom-functions.php",
                data: {action: "confirm_trans", impData: impData}
            })

                    .done(function (msg) {


                        if (msg == 'inserted') {

                            $('.transBody').html("<p class='transSuccess'><?= transSuccess ?></p>");

                            setTimeout(function () {
                                $('#transModal').modal('hide');
                            }, 2000);

                        }else{
                            
                              $('.transBody').html("<p class='transFail'><?= transFail ?></p>");

                            setTimeout(function () {
                                $('#transModal').modal('hide');
                            }, 2000);
                            }






                    });

        });


    });

</script>






<?php include("../application/footer.php"); ?>