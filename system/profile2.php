<?php
////////////////////////
////////////////////////////////////
/////////////////////////////////////////////////////////
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
?>


<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="https://ucarecdn.com/widget/1.3.1/uploadcare/uploadcare-1.3.1.min.js"></script>
<!-- CAPSTONE: Override Uploadcare text -->
<script src="<?php echo BASE_URL ?>ckeditor/ckeditor.js"></script>

<script src="<?= BASE_JS_URL ?>profile.js"></script>
<?php
///// copy these two files for displaying navigation/////


Navigation($display_page);
//////////////
?>


<div class="content">
    <div id="profile">

        <div class="jumbotron search-form profile-complete">
            <div class="container">
                <div class="row">
                    <div class="col-6 col-sm-6 col-lg-3 height2">




                        <form action="form-actions.php" method="post">
                            <div class='left-content'> <span> <img id="user_thumb" src="<?= (($row['image'] != "") && isset($row['image'])) ? "users_uploads/" . $row['image'] : "users_uploads/defaultImageIcon.png" ?>" border="0" width="200" height="200" class="img-thumbnail img-responsive" style="width:100%;" /> </span>
                                <div>
                                    <?php
                                    if (isset($row['image']) && $row['image'] != "") {
                                        echo "<a href='form-actions.php?action=remove_profile_image' class='btn btn-primary update-btn'>" . REMOVE_IMAGE_BUTTON . "</a>";
                                    } else {
                                        ?>
                                        <input type="hidden" role="uploadcare-uploader" name="image" id="file2" data-locale="en" data-tabs="file url facebook gdrive instagram" data-images-only="false" data-path-value="false" data-preview-step="false" data-multiple="false"  value="" data-crop="650x430 minimum"/>
                                        <br />
                                        <input type="hidden" name="uploadcare_image_url" id="uploadcare_image_url" value="" />
                                        <input type="hidden" name="uploadcare_image_name" id="uploadcare_image_name" value="" />
                                        <input type="hidden" name="profile_id" id="profile_id" value="<?= $row["uid"] ?>" />
                                        <div style="margin:5% 0%">
                                            <input type="submit" class="submit btn btn-primary pull-left" name="profile_image_submit" id="login" value="<?= USER_IMAGE_SAVE_BUTTON ?>">
                                            <input type="button" onclick="location.href = '<?= BASE_URL ?>profile.php'" class="submit btn btn-primary  pull-right" name="login" value="<?= USER_IMAGE_CANCEL_BUTTON ?>"/>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </form>


                    </div>
                    <div class="col-6 col-sm-6 col-lg-9">

                    </div>
                </div>
            </div>




            <div class="container">
                <div class="row" >
                    <div class='col-6 col-sm-6 col-lg-3'>
                        <?php
                        /*
                         * Fetching User Profile Image if there is any
                         */


                        $userRow = get("users", "uid=$_SESSION[uid]");
                        ?>
                        <form action="form-actions.php" method="post">
                            <div class='left-content'> <span> <img id="user_thumb" src="<?= (($row['image'] != "") && isset($row['image'])) ? "users_uploads/" . $row['image'] : "users_uploads/defaultImageIcon.png" ?>" border="0" width="200" height="200" class="img-thumbnail img-responsive" style="width:100%;" /> </span>
                                <div>
                                    <?php
                                    if (isset($row['image']) && $row['image'] != "") {
                                        echo "<a href='form-actions.php?action=remove_profile_image' class='btn btn-primary update-btn'>" . REMOVE_IMAGE_BUTTON . "</a>";
                                    } else {
                                        ?>
                                        <input type="hidden" role="uploadcare-uploader" name="image" id="file2" data-locale="en" data-tabs="file url facebook gdrive instagram" data-images-only="false" data-path-value="false" data-preview-step="false" data-multiple="false"  value="" data-crop="650x430 minimum"/>
                                        <br />
                                        <input type="hidden" name="uploadcare_image_url" id="uploadcare_image_url" value="" />
                                        <input type="hidden" name="uploadcare_image_name" id="uploadcare_image_name" value="" />
                                        <input type="hidden" name="profile_id" id="profile_id" value="<?= $row["uid"] ?>" />
                                        <div style="margin:5% 0%">
                                            <input type="submit" class="submit btn btn-primary pull-left" name="profile_image_submit" id="login" value="<?= USER_IMAGE_SAVE_BUTTON ?>">
                                            <input type="button" onclick="location.href = '<?= BASE_URL ?>profile.php'" class="submit btn btn-primary  pull-right" name="login" value="<?= USER_IMAGE_CANCEL_BUTTON ?>"/>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class='col-6 col-sm-6 col-lg-9 right-content user-profile'>

                        <?php
                        if (isset($page_layout_style) && ($page_layout_style == 'serial-layout')) {
                            serial_layout($display_page, $style);
                        } else {
                            ?>

                            <ul class="nav nav-tabs" role="tablist" >

                                <?php
                                echo Get_Links($display_page);

                                global $tab;
                                ?>



                            </ul>



                            <?php
                            if (isset($_SESSION['tab'])) {
                                Get_Data_FieldDictionary_Record($_SESSION['tab'], $display_page);
                            } else {

                                Get_Data_FieldDictionary_Record($tab, $display_page);
                            }
                            ?>





                        <?php }//// page_layout seriel      ?>
                        <script src="http://generic.cjcornell.com/ckeditor/ckeditor.js"></script>
                        <script>
                                                //CKEDITOR.replace('description'); 
                                                CKEDITOR.replace('description', {
                                                    toolbarGroups: [
                                                        {name: 'document', groups: ['mode']}, // Line break - next group will be placed in new line.
                                                        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                                                        {name: 'styles', groups: ['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor']},
                                                        {name: 'insert', groups: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'uploadcare', 'youtube']}
                                                    ]},
                                                {
                                                    allowedContent: true
                                                });</script>            <div style="clear:both"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<?php
global $popup_menu;



/*
 * Popup Menu codes starts here
 */

//print_r($popup_menu);die;

if ($popup_menu['popupmenu'] == 'true') {

    echo "<ul class='custom-menu'>";

    if (isset($popup_menu['popup_delete']) && !empty($popup_menu['popup_delete'])) {



        echo "<li data-action='delete'  class='" . $popup_menu['popup_delete']['style'] . "'>" . $popup_menu['popup_delete']['label'] . "</li>";
    }


    if (isset($popup_menu['popup_copy']) && !empty($popup_menu['popup_copy'])) {



        echo "<li data-action='copy'  class='" . $popup_menu['popup_copy']['style'] . "'>" . $popup_menu['popup_copy']['label'] . "</li>";
    }

    echo "</ul>";
}
?>

<script>


    $(document).ready(function () {


        $('.project-detail:even').css('background-color', '#bbbbff'); //list rows zebra colors



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
        var value;
        jQuery(document).on('click', '.grid-type span', function () {
            var gridType = $(this).attr('id');
//alert(gridType);
            if (gridType == "listView") {
                jQuery(".project-details-wrapper").removeClass().addClass("project-details-wrapper listView");
                jQuery(".project-details-wrapper > div:first-child").removeClass().addClass("col-12 col-sm-12 col-lg-12");
                jQuery(".edit").addClass("invisible");
                jQuery('.project-detail').addClass('project-detail-list');
                jQuery(".list-checkbox").show();
                jQuery('#checklist-div').show();
                jQuery('.list-del').show();
            }

            if (gridType == "boxView") {
                jQuery(".project-details-wrapper").removeClass().addClass("project-details-wrapper boxView");
                //jQuery(".project-details-wrapper .profile-image").hide();
                jQuery(".project-details-wrapper > div:first-child").removeClass().addClass("col-6 col-sm-6 col-lg-3");
                jQuery(".edit").removeClass("invisible");
                jQuery('.project-detail').removeClass('project-detail-list');
                jQuery(".list-checkbox").hide();
                jQuery('#checklist-div').hide();
                jQuery('.list-del').hide();
            }

            if (gridType == "thumbView") {
                jQuery(".project-details-wrapper").removeClass().addClass("project-details-wrapper thumbView");
                jQuery(".project-details-wrapper > div:first-child").removeClass().addClass("col-12 col-sm-12 col-lg-12");
                jQuery(".edit").addClass("invisible");
                jQuery('.project-detail').addClass('project-detail-list');
                jQuery(".list-checkbox").hide();
                jQuery('#checklist-div').hide();
                jQuery('.list-del').hide();
            }
        });
        var def_sort;
<?php
if (!empty($_GET['sort'])) {
    ?>

            def_sort = jQuery("#sort_popular_users li[data-value='<?= $_GET['sort'] ?>'").text();
    <?php
    if ($_GET['orderFlag'] == 'down') {
        
    }
    ?>

            jQuery("#sort_popular_users_value").html(def_sort);
<?php } ?>
        $("#sort_popular_users li").click(function () {

            var text = jQuery(this).text();
            jQuery("#sort_popular_users_value").html(text);
            value = $(this).attr("data-value");
            var order;
            var orderFlag;
            order = ($(this).find("span").attr('class'));
//alert(order);
            switch (order) {
                case 'glyphicon glyphicon-chevron-down':
                    orderFlag = "&orderFlag=down";
                    break;
                case 'glyphicon glyphicon-chevron-up':
                    orderFlag = "&orderFlag=up";
                    break;
                default:
                    orderFlag = "&orderFlag=down";
            }

            var url;
            var split;
            url = window.location + "&sort=" + value + orderFlag;
            split = url.split("&sort=");
//alert(split[0] + "&sort=" + value + orderFlag);
            //    $.get(split[0] + "&sort=" + value + orderFlag);


            window.location.href = split[0] + "&sort=" + value + orderFlag;
        });
        /* when click on dropdown SORT */

        jQuery('#sort_popular_users li').click(function () {


            //  var index = jQuery(this).index();
            // var text = jQuery(this).text();
            // var value = jQuery(this).attr('data-value');	
            //jQuery("#sort_hidden_value").val(value);
            //jQuery("#sort_popular_users_value").html(text);
            //alert('Index is: ' + index + ' , text is ' + text + ' and value is ' + value);
        });
        /* Sorting function on SORT button click */
        jQuery("#sortList").click(function () {
            // var sortby = jQuery('#sort_hidden_value').val();


        });
        $(".action-delete").click(function () {


            $("#checkHidden").val('delete');
            $('#list-form').ajaxForm(function (data) {
                //alert(data);
                location.reload();
            });
        });
        $(".list-del").click(function () {


            if (confirm("Are you sure ,You want to delete the Record!") == true) {


                var del_id = $(this).attr('id');
                $.ajax({
                    method: "GET",
                    url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                    data: {list_delete: del_id, check_action: "delete"}
                })
                        .done(function (msg) {
                            location.reload();
                        });
            }

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
         * it calls when right click on single line list
         */

        var popup_del;
        // Trigger action when the contexmenu is about to be shown
        $(".project-detail").bind("contextmenu", function (event) {

            popup_del = $(this).children().attr('id');
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
                    popup_delete(popup_del);
                    break;
                case "copy":
                    alert("second");
                    break;

            }

            // Hide it AFTER the action was triggered
            $(".custom-menu").hide(100);
        });


        /***** popup DELETE Function ****///

        function popup_delete(del_id) {


            if (confirm("Are you sure ,You want to delete the Record!") == true) {



                $.ajax({
                    method: "GET",
                    url: "<?= BASE_URL_SYSTEM ?>ajax-actions.php",
                    data: {list_delete: del_id, check_action: "delete"}
                })
                        .done(function (msg) {
                            location.reload();
                        });
            }
        }



    });
</script>



<?php include("../application/footer.php"); ?>









<form action="form-actions.php" method="post">
    <div class='left-content'> <span> <img id="user_thumb" src="<?= (($row['image'] != "") && isset($row['image'])) ? "users_uploads/" . $row['image'] : "users_uploads/defaultImageIcon.png" ?>" border="0" width="200" height="200" class="img-thumbnail img-responsive" style="width:100%;" /> </span>
        <div>
            <?php
            if (isset($row['image']) && $row['image'] != "") {
                echo "<a href='form-actions.php?action=remove_profile_image' class='btn btn-primary update-btn'>" . REMOVE_IMAGE_BUTTON . "</a>";
            } else {
                ?>
                <input type="hidden" role="uploadcare-uploader" name="image" id="file2" data-locale="en" data-tabs="file url facebook gdrive instagram" data-images-only="false" data-path-value="false" data-preview-step="false" data-multiple="false"  value="" data-crop="650x430 minimum"/>
                <br />
                <input type="hidden" name="uploadcare_image_url" id="uploadcare_image_url" value="" />
                <input type="hidden" name="uploadcare_image_name" id="uploadcare_image_name" value="" />
                <input type="hidden" name="profile_id" id="profile_id" value="<?= $row["uid"] ?>" />
                <div style="margin:5% 0%">
                    <input type="submit" class="submit btn btn-primary pull-left" name="profile_image_submit" id="login" value="<?= USER_IMAGE_SAVE_BUTTON ?>">
                    <input type="button" onclick="location.href = '<?= BASE_URL ?>profile.php'" class="submit btn btn-primary  pull-right" name="login" value="<?= USER_IMAGE_CANCEL_BUTTON ?>"/>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</form>