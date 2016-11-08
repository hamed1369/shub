var audio_context;

function initAudioApp() {

  $(function () {

    try {
      // webkit shim
      window.AudioContext = window.AudioContext || window.webkitAudioContext;
      navigator.getUserMedia = ( navigator.getUserMedia ||
      navigator.webkitGetUserMedia ||
      navigator.mozGetUserMedia ||
      navigator.msGetUserMedia);
      window.URL = window.URL || window.webkitURL;
      var audio_context = new AudioContext;
      __log('Audio context set up.');
      __log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
    } catch (e) {
    }

    $('.recorder .start').on('click', function () {
      $this = $(this);
      $recorder = $this.parent();
      navigator.getUserMedia({audio: true}, function (stream) {
        var recorderObject = new MP3Recorder(audio_context, stream, {
          statusContainer: $recorder.find('.status'),
          statusMethod: 'replace'
        });
        $recorder.data('recorderObject', recorderObject);
        recorderObject.start();
      }, function (e) {
      });
    });

    $('.recorder .stop').on('click', function () {
      $this = $(this);
      $recorder = $this.parent();
      recorderObject = $recorder.data('recorderObject');
      recorderObject.stop();

      recorderObject.exportMP3(function (base64_mp3_data) {
        var url = 'data:audio/mp3;base64,' + base64_mp3_data;
        var au = document.createElement('audio');
        $("#blobField").val(url);
        au.controls = true;
        $("#audio").attr("src", url);

        recorderObject.logStatus('');
      });

    });

  });
}