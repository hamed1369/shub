var mp3WorkerPath;
var recorderWorkerPath;
var audioTimerInterval;

function CreateAudio(){
    audioFloatBox=OW.ajaxFloatBox('IISAUDIO_CMP_Audio', {} , {width:700, iconClass: 'ow_ic_add'});
}

function addAudioComplete($cmp){
    $cmp.close();
    window.location = window.location;
}

function defineMP3Recorder(a){
    recorderWorkerPath = a;
}

function defineMP3Worker(a){
    mp3WorkerPath = a;
}

function __log(e, data) {
    log.innerHTML += "\n" + e + " " + (data || '');
}

function resetAuidoTimer(){
    $('#audio_timer').html(0);
}

function stopAudioTimer(){
    if(audioTimerInterval!=null && audioTimerInterval!='undefined'){
        clearInterval(audioTimerInterval);
    }
    $('#audio_timer').html();
}

function startAudioTimer(){
    audioTimerInterval = setInterval(function(){$('#audio_timer').html(parseInt($('#audio_timer').html())+1)}, 1000);
}