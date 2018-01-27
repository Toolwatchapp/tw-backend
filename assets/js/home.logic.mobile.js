$( document ).ready(function() {

	$('header').addClass('blue');
	$('header').addClass('blue');
	$('.navbar').css('min-height', '20px');
	$('.home-intro').css('margin-top', '38px');
	$('#toolwatch-explained').css('margin-top', '80px');
	var delta = $("video").height()/2 + $(".slogan-home").height() + 72;
    $( ".slogan-home" ).animate({
        marginTop: "-="+delta
    }, 2000);

    console.log(delta);
});

