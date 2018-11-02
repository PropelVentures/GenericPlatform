$(document).ready(function () {
   

    /*
     * 
     * Image pop up code goes here
     */


    var imgHolder;

   // var old_imgHolder = '';
    // Trigger action when the contexmenu is about to be shown
    $(".pdf-content").on("click", function (event) {

        imgHolder = this;

        // Avoid the real one
        event.preventDefault();
        // Show contextmenu
        $(".pdf-menu").finish().toggle(100).
                // In the right position (the mouse)
                css({
                    top: event.pageY + "px",
                    left: event.pageX + "px"
                });
    });
// If the document is clicked somewhere
    $(document).bind("mousedown", function (e) {

        // If the clicked element is not the menu
        if (!$(e.target).parents(".pdf-menu").length > 0) {

            // Hide it
            $(".pdf-menu").hide(100);
        }
    });
// If the menu element is clicked
    $(".pdf-menu li").on("click", function () {

        // This is the triggered action name
        switch ($(this).attr("data-action")) {

            // A case for each action. Your actions here
            case "upload":
                upload_pdf(imgHolder);
                break;
            case "remove":
                remove_pdf(imgHolder);
                break;

            case "enlarge":
                enlarge_pdf(imgHolder);
                break;

            case "revert":
                revert_pdf(imgHolder);
                break;

        }

        // Hide it AFTER the action was triggered
        $(".pdf-menu").hide(100);
    });

    function upload_pdf(imgHolder) {

        var fieldName = $(imgHolder).find(".user_thumb").attr("alt");

        var oldValue = $(imgHolder).find("." + fieldName).val();

//console.log(oldValue);
        var dialog = uploadcare.openDialog();

        dialog.done(function (file) {
            
            file.done(function (fileInfo) {


var fileNameByUser = prompt("Please enter file name", "");

if( fileNameByUser == null || fileNameByUser == '')
    fileNameByUser = 'Pdf File'

fileNameByUser = fileNameByUser  + "-";

              $(imgHolder).find(".audio-upload-filename").text('File has been Uploaded');

                if (oldValue != '') {

                    $(imgHolder).find(".img-extra").html("<input type='hidden' name='" + fieldName + "[img_extra]' value='" + oldValue + "'/>");

                }

                $(imgHolder).find("." + fieldName).val(fileNameByUser + fileInfo.name);

                $(imgHolder).find("#" + fieldName).val(fileInfo.cdnUrl);

            });
        });


    }



    function remove_pdf(imgHolder) {


      //  $(imgHolder).find(".user_thumb").attr("src", "../users_uploads/NO-IMAGE-AVAILABLE-ICON.jpg");

        var fieldName = $(imgHolder).find(".user_thumb").attr("alt");

        var oldValue = $(imgHolder).find("." + fieldName).val();
        
        $(imgHolder).find(".audio-upload-filename").text('File has been removed!');


        if (oldValue != '') {

            $(imgHolder).find(".img-extra").html("<input type='hidden' name='" + fieldName + "[img_extra]' value='" + oldValue + "'/>");
        }

        // $(imgHolder).find("." + fieldName).val('');

    }




    function enlarge_pdf(imgHolder) {

         var fieldName = $(imgHolder).find(".user_thumb").attr("alt");

        var fileName = $(imgHolder).find("." + fieldName).val();
      
  
  if(fileName)
  window.open('../users_uploads/pdf/' + fileName ,'_blank');
  
      
       //../users_uploads/
       // $("#imgModal").find(".modal-title").html(fieldName);
        
        //$("#imgModal").find(".img-modal").attr("src", img_src);

       //$("#imgModal").modal('show');

    }


    function revert_pdf(imgHolder) {
        
       // console.log('revert');
        
        var fieldName = $(imgHolder).find(".user_thumb").attr("alt");
       
          $.ajax({
                method: "GET",
                url: "ajax-actions.php",
                data: {img_revert:'img-revert',field_name:fieldName}
            })
                    .done(function (msg) {




                       if(msg){
                       
                       $(imgHolder).find(".audio-upload-filename").text(msg);    
                
                $(imgHolder).find("." + fieldName).val(msg);
                
                 $(imgHolder).find("#" + fieldName).val('');
                
                 $(imgHolder).find(".img-extra").html('');
                           
                       }else{
                           
                       $(imgHolder).find(".audio-upload-filename").text('No File!');
                 
                  $(imgHolder).find("." + fieldName).val('');
                  
                  $(imgHolder).find(".img-extra").html('');
                       }
                    })
                    
                     .fail(function (msg) {

                       console.log('uh');
                    });
                    
                    
                    
                     
                    
              
       
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
