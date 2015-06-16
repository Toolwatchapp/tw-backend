$( document ).ready(function() {

	$('video,audio').mediaelementplayer({features: []});

    $( ".slogan-home" ).animate({
        marginTop: "-="+$("video").height()/2
    }, 2000);
});

