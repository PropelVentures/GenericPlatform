navigator.mediaDevices.getUserMedia({
    audio: true
  })
  .then(stream => {
    rec = new MediaRecorder(stream);
    rec.ondataavailable = e => {
      audio.push(e.data);
      if (rec.state == "inactive") {
        let blob = new Blob(audio, {
          type: 'audio/mpeg'
        });

      //   var filename = "test.wav";
      // var data = new FormData();
      // data.append('file', blob);
      //
      // $.ajax({
      //   url :  '?action=user_recorded_file',
      //   type: 'POST',
      //   data: data,
      //   contentType: false,
      //   processData: false,
      //   success: function(data) {
      //     debugger;
      //     alert("boa!");
      //   },
      //   error: function() {
      //     alert("not so boa!");
      //   }
      // });
        // $('#recorded_audio').val(blob);
        recordedAudio.src = URL.createObjectURL(blob);
        recordedAudio.controls = true;
        // audioDownload.href = recordedAudio.src;
        // audioDownload.download = 'audio.mp3';
        // audioDownload.innerHTML = 'Download';
        // console.log('BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBB');
        var reader = new window.FileReader();
        temp = reader.readAsDataURL(blob);
        reader.onloadend = function() {
         base64data = reader.result;
         document.getElementById("recorded_audio").value = base64data;
        }
        // debugger;

        // submit(blob);
      }
    }
  })
  .catch(e => console.log(e));

// TODO: This needs work. Submit button currently does not do anything.
// Also, page does not get reloaded and therefore the results are not shown.
// The POST request has to be done without AJAX.

function startRecording(el) {
  $('#audio_message').text('');
  $('#startRecord').hide();
  $('#stopRecord').css('display','inline-block');
  $('#remove').addClass('disabled');
  // $('#input_audio_file').hide();
  // $('#audio').hide();
  // $('#recordedAudio').css('display','block');
  audio = [];
  // recordedAudio.controls = false;
  rec.start();
}

function stopRecording() {
  rec.stop();
  // $('#audio_message').text('Recording is recorded,Will be uploaded on updating form');
  $('#audio').hide();
  $('#startRecord').show();
  $('#stopRecord').css('display','none');
  // $('#input_audio_file').hide();
  $('#remove').removeClass('disabled');
  $('#recordedAudio').css('display','block');

}

function clearAudio(){
  if ($('#remove').hasClass('disabled')) return;
  $('#audio_message').text('');
  $('#input_audio_file').val('');
  // $('#input_audio_file').show();
  // $('#audio').hide();
  // $('#recordedAudio').hide();
  $('#remove').addClass('disabled');
}

function onInputFileChange(){
  if($('#input_audio_file').val().length > 0){
    var extension = $('#input_audio_file').val().replace(/^.*\./, '');
    if(extension== 'mp3' || extension=='wav'){
      $('#remove').removeClass('disabled');
      $('#audio_message').text( $('#input_audio_file').val().replace(/C:\\fakepath\\/i, ''));
    }else{
       $('#input_audio_file').val('');
      alert('Only wav or mp3 files are allowed');
    }
  }
}
