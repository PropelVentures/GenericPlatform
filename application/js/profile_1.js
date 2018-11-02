$(document).ready(function () {


    $(document).on("click", ".uploadcare-widget-button-open", function () {

        //var editCheck = $(this).parents("#user_profile_form").find(".edit-btn").attr('id');
        /*
         if (editCheck) {
         alert('Click on Edit button first');
         // uploadcare.closeDialog();
         location.reload();
         } else {*/



        var widget1 = uploadcare.Widget($(this).parents(".img-holder").find(".file2"));
        var uploadcare_field = $($(this).parents(".img-holder").find(".file2"));
        var old_value = uploadcare_field.val();
        var value_changed = false;


        var test = $(this).parents(".img-holder").find(".user_thumb").attr("alt");

        widget1.onUploadComplete(function (info) {
            console.log(info);
            $("#" + test + "").attr("src", info.cdnUrl);


        });



        function track_value_change()
        {
            if (!value_changed && uploadcare_field.val() != old_value)
            {
                value_changed = true;

                var file = uploadcare.fileFrom('uploaded', uploadcare_field.val());
                widget1.value(file);
            }
        }
        setInterval(function () {
            track_value_change()
        }, 100);



        $(this).parents(".img-holder").find(".user_thumb").attr("id", test);

        // }

    });



    $(document).on("click", ".remove-img-btn", function () {

        /*
         var editCheck = $(this).parents("#user_profile_form").find(".edit-btn").attr('id');
         
         if (editCheck) {
         alert('Click on Edit button first');
         
         } else {*/

        var fieldName = $(this).attr('id');

        var profile_img = $(this).attr("name");

        $.ajax({
            method: "GET",
            url: "ajax-actions.php",
            data: {fieldName: fieldName, check_action: "image_delete", profile_img: profile_img}
        })
                .done(function (msg) {
//alert(msg);

                    if ($.trim(msg) == 'Deleted') {
                        location.reload();
                    }
                    else {
                        alert("image not Deleted. Please try Again!")
                    }
                });
        //  }
    });

    $(document).on("click", ".login-btn", function () {



        var widget1 = uploadcare.Widget($(this).parents(".img-holder").find(".file2"));
        var uploadcare_field = $($(this).parents(".img-holder").find(".file2"));

        var fieldName = $(this).parents(".img-holder").find(".field_name").val();

        var file = uploadcare.fileFrom('uploaded', uploadcare_field.val());

        var profile_img = $(this).attr("name");

        widget1.value(file);



        widget1.onUploadComplete(function (info) {
            //console.log(info);

            $(".user_thumb").attr("src", info.cdnUrl);

            $.ajax({
                method: "GET",
                url: "ajax-actions.php",
                data: {cdnUrl: info.cdnUrl, check_action: "image_submit", imgName: info.name, fieldName: fieldName, profile_img: profile_img}
            })
                    .done(function (msg) {

                        if ($.trim(msg) != 'notSaved') {
                            location.reload();
                        }
                        else {
                            alert("image not uploaded. Please try Again!")
                        }
                    });
        });


    });



    /*
     * 
     * Image pop up code goes here
     */


    var imgHolder;

   // var old_imgHolder = '';
    // Trigger action when the contexmenu is about to be shown
    $(".left-content").on("click", function (event) {

        imgHolder = this;
/*
        if (old_imgHolder == '') {
            old_imgHolder = imgHolder;
        }*/
       // console.log(imgHolder);

        // Avoid the real one
        event.preventDefault();
        // Show contextmenu
        $(".image-menu").finish().toggle(100).
                // In the right position (the mouse)
                css({
                    top: event.pageY + "px",
                    left: event.pageX + "px"
                });
    });
// If the document is clicked somewhere
    $(document).bind("mousedown", function (e) {

        // If the clicked element is not the menu
        if (!$(e.target).parents(".image-menu").length > 0) {

            // Hide it
            $(".image-menu").hide(100);
        }
    });
// If the menu element is clicked
    $(".image-menu li").on("click", function () {

        ////for revert function

/*

        var fieldName = $(imgHolder).find(".user_thumb").attr("alt");


        var old_fieldName = $(old_imgHolder).find(".user_thumb").attr("alt");

        if (fieldName != old_fieldName) {

            console.log('change');
            old_imgHolder = imgHolder;
        }




         console.log(old_imgHolder);*/

        ///end revert function use-case

        // This is the triggered action name
        switch ($(this).attr("data-action")) {

            // A case for each action. Your actions here
            case "upload":
                upload_img(imgHolder);
                break;
            case "remove":
                remove_img(imgHolder);
                break;

            case "enlarge":
                enlarge_img(imgHolder);
                break;

            case "revertt":
                revert_img(imgHolder);
                break;

        }

        // Hide it AFTER the action was triggered
        $(".image-menu").hide(100);
    });

    function upload_img(imgHolder) {

        var fieldName = $(imgHolder).find(".user_thumb").attr("alt");

        var oldValue = $(imgHolder).find("." + fieldName).val();

//console.log(oldValue);
        var dialog = uploadcare.openDialog(null, {
            imagesOnly: true,
            crop: "650x430 minimum"});

        dialog.done(function (file) {
            file.done(function (fileInfo) {

                $(imgHolder).find(".user_thumb").attr("src", fileInfo.cdnUrl);

                if (oldValue != '') {

                    $(imgHolder).find(".img-extra").html("<input type='hidden' name='" + fieldName + "[img_extra]' value='" + oldValue + "'/>");

                }

                $(imgHolder).find("." + fieldName).val(fileInfo.name);

                $(imgHolder).find("#" + fieldName).val(fileInfo.cdnUrl);

            });
        });


    }



    function remove_img(imgHolder) {


        $(imgHolder).find(".user_thumb").attr("src", "../users_uploads/NO-IMAGE-AVAILABLE-ICON.jpg");

        var fieldName = $(imgHolder).find(".user_thumb").attr("alt");

        var oldValue = $(imgHolder).find("." + fieldName).val();


        if (oldValue != '') {

            $(imgHolder).find(".img-extra").html("<input type='hidden' name='" + fieldName + "[img_extra]' value='" + oldValue + "'/>");
        }

        // $(imgHolder).find("." + fieldName).val('');

    }




    function enlarge_img(imgHolder) {

       var fieldName = $(imgHolder).parents(".new_form").find("label").html();
       
        var img_src = $(imgHolder).find(".user_thumb").attr("src");
        
        $("#imgModal").find(".modal-title").html(fieldName);
        
        $("#imgModal").find(".img-modal").attr("src", img_src);

       $("#imgModal").modal('show');

    }


    function revert_img(imgHolder, oldHolder) {
        // fetching field name
        var fieldName = $(imgHolder).find(".user_thumb").attr("alt");
        //////
        console.log(oldHolder);
        ////fetchign old values
        var imgSrc = $(oldHolder).find(".user_thumb").attr("src");
        //  console.log(imgSrc);
        var firstInput = $(oldHolder).find("." + fieldName).val();

        var secInput = $(oldHolder).find("#" + fieldName).val();

        var imgExtra = $(oldHolder).find(".img-extra").html();


        //// replacing new values with old values ;)
        $(imgHolder).find(".user_thumb").attr("src", imgSrc);

        $(imgHolder).find("." + fieldName).val(firstInput);

        $(imgHolder).find("#" + fieldName).val(secInput);

        $(imgHolder).find(".img-extra").html(imgExtra);
    }



/*
 * Tooltip Code goes here
 */


  // Tooltip only Text
        $('.masterTooltip').hover(function(){
                // Hover over code
                var title = $(this).attr('title');
                $(this).data('tipText', title).removeAttr('title');
                $('<p id="tooltip"></p>')
                .text(title)
                .appendTo('body')
                .fadeIn('slow');
        }, function() {
                // Hover out code
                $(this).attr('title', $(this).data('tipText'));
                $('#tooltip').remove();
        }).mousemove(function(e) {
                var mousex = e.pageX + 20; //Get X coordinates
                var mousey = e.pageY + 10; //Get Y coordinates
                $('#tooltip')
                .css({ top: mousey, left: mousex })
        });
});
