navigator.mediaDevices.getUserMedia({
    audio: true
  })
  .then(stream => {
    rec = new MediaRecorder(stream);
    rec.ondataavailable = e => {
      audio.push(e.data);
      if (rec.state == "inactive") {
        let blob = new Blob(audio, {
          type: 'audio/x-mpeg-3'
        });
        // $('#recorded_audio').val(blob);
        recordedAudio.src = URL.createObjectURL(blob);
        recordedAudio.controls = true;
        // audioDownload.href = recordedAudio.src;
        // console.log(recordedAudio.src);
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
  $('#startRecord').addClass('disabled');
  $('#startRecord').text('Recording..');
  $('#stopRecord').removeClass('disabled');

  audio = [];
  recordedAudio.controls = false;
  rec.start();
}

function stopRecording() {
  rec.stop();
  $('#audio').hide();
  $('#startRecord').text('Record');
  $('#input_audio_file').hide();
  $('#stopRecord').addClass('disabled');
  $('#startRecord').removeClass('disabled');
}
