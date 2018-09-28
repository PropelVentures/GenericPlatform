
<script>
function restore(elem) {

    elem.find("#pause").replaceWith('<a class="button one" id="pause"><?= audioPause?></a>');
    elem.find("#record").replaceWith('<a class="button" id="record"><?= audioRecord?></a>');
    elem.find("#remove").replaceWith("<a class='button' id='remove'><?= audioClear?></a>");
    elem.find(".one").addClass("disabled");

    // elem.find("#audio_hidden").remove();
    Fr.voice.stop();
}
$(document).ready(function () {


    /*
     * *****
     * ********
     * *************
     * ********
     * 
     * ******************************************when click on Record/stop
     * *********************
     * ********************************
     * ****************************************
     * **************************************************
     */

    $(document).on("click", "#record:not(.disabled)", function () {

        elem = $(this).parents('.audio-css');



        if ($(this).hasClass("stop")) {



            elem.find(".recording_msg").hide();



            if (!elem.find("#audio").length) {

                elem.prepend("<audio controls src='' id='audio'></audio>");

            }

            Fr.voice.export(function (url) {
                elem.find("#audio").attr("src", url);
                elem.find("#audio")[0].play();
                var field_name = elem.find('.old_audio').attr('id');
                elem.append("<input type='hidden' name='" + field_name + "'  id='audio_hidden'>");

                elem.find("#audio_hidden").val(url);
                elem.find("#audio").show('slow');

                elem.find("#remove").replaceWith("<a class='button cancel' id='remove'><?= audioCancel?></a>");


            }, "base64");
            restore(elem);
        } else {

            Fr.voice.record(elem.find("#live").is(":checked"), function () {
                
                //elem.addClass("disabled");
                elem.find("#live").addClass("disabled");
                elem.find(".one").removeClass("disabled");
                elem.find("#remove").addClass("disabled");

                elem.find("#audio").hide();
                elem.find(".recording_msg").show();

                elem.find(".recording_msg").text("<?= audioRecordingMsg?>");

                elem.find(".fileField").remove();

                ////hidding .wav file name////
                elem.find(".audio-upload-filename").hide();

                elem.find("#remove").replaceWith("<a class='button cancel' id='remove'><?= audioCancel?></a>");

            });
            elem.find("#record").replaceWith('<a class="button one stop" id="record"><?= audioStop?></a>');


        }


    });


    $(document).on("click", "#pause:not(.disabled)", function () {

        if ($(this).hasClass("resume")) {
            Fr.voice.resume();
            elem.find(".recording_msg").text("<?= audioRecordingResume?>");
            
            $(this).replaceWith('<a class="button one" id="pause"><?= audioPause?></a>');
        } else {
            Fr.voice.pause();
            elem.find(".recording_msg").text("<?= audioPauseMsg?>");
            $(this).replaceWith('<a class="button one resume" id="pause"><?= audioResume?></a>');
        }
    });



    $(document).on("click", "#stop:not(.disabled)", function () {

        elem = $(this).parents('.audio-css');

        restore(elem);
    });



    /*
     * 
     * 
     ////////////when click on REMOVE /CANCEL BUTTON/////////
     //////////////////
     /////////////////////////////////////////////////
     ////////
     
     
     */
    $(document).on("click", "#remove:not(.disabled)", function () {

        elem = $(this).parents('.audio-css');

        if ($(this).hasClass("cancel")) {

            var field_value = elem.find('.old_audio').val();

            elem.find("#audio").remove();

            if (field_value) {

                //finding whether value is a file or database value


                if (field_value.indexOf(".wav") >= 0) {

                    elem.prepend("<audio controls src='" + "../users_uploads/audio/" + field_value + "' id='audio'></audio>");



                } else {


                    elem.prepend("<audio controls src='" + field_value + "' id='audio'></audio>");


                }

                elem.find(".fileField").remove();

            } else {
                ///adding file field

                if (elem.find(".fileField").length) {

                    elem.find(".fileField").replaceWith("<input type='file' name='" + elem.find('.old_audio').attr('id') + "' class='form-control fileField' >");

                } else {

                    elem.prepend("<input type='file' name='" + elem.find('.old_audio').attr('id') + "' class='form-control fileField' >");
                }


            }
            
             ////Displaying .wav file name////
                 elem.find(".audio-upload-filename").show();

            elem.find("#audio").show();
            elem.find(".recording_msg").hide();

            restore(elem);

        } else {

            //elem.find("#audio").attr("src", '');

            //var field_name = elem.find('.old_audio').attr('id');

            //elem.append("<input type='hidden' name='" + field_name + "'  id='audio_hidden' value=''>");

            if (elem.find("#audio").length) {


                elem.find("#audio").remove();

                elem.prepend("<input type='file' name='" + elem.find('.old_audio').attr('id') + "' class='form-control fileField' >");

            } else {

                elem.find(".fileField").replaceWith("<input type='file' name='" + elem.find('.old_audio').attr('id') + "' class='form-control fileField' >");
            }

            ////hidding .wav file name////
            elem.find(".audio-upload-filename").hide();

            $(this).replaceWith("<a class='button cancel' id='remove'><?= audioCancel?></a>");
        }
    });



    $(document).on("click", ".fileField", function () {

        elem = $(this).parents('.audio-css');

        elem.find("#remove").replaceWith("<a class='button cancel' id='remove'><?= audioCancel?></a>");


    });


});

</script>