$( document ).ready(function() {

	$('video,audio').mediaelementplayer();

    $( ".slogan-home" ).animate({
        marginTop: "-="+$("video").height()/2
    }, 2000);
});

