
var delta =  (Math.random() * 6) + 1 - 2;
var d;

var timeouts = [];

$( window ).resize(function() {
  initSize();
});



$( document ).ready(function() {

  var mousewheelevt = (/Firefox/i.test(navigator.userAgent)) ? "DOMMouseScroll" : "mousewheel" //FF doesn't recognize mousewheel as of FF3.x
    $(window).bind(mousewheelevt, function(e){

      if(document.body.scrollTop > $('.home-intro').height()){
        $('header').addClass('blue');
      }else{
        $('header').removeClass('blue');
      }

  });

  initSize();

  $('video,audio').mediaelementplayer({
      enableAutosize: true,
      features: []
  });

  $(".watch-accuracy").html(delta.toFixed(1));
  $("#demo-second-step").show();

});

function initSize(){

    var windowHeight = $(window).height();

    var headerHeight = $('header').height();
    var sloganHeight = $(".slogan-home");
    var windowRealHeight = windowHeight - headerHeight;
    //var align = dispoDisplay / 2 - sloganHeight / 2;

    $(".slogan-home").css("margin-top", -windowRealHeight + windowRealHeight/2);
    $(".home-picto").css("margin-top", Math.abs(-windowRealHeight + windowRealHeight/2));

    var ratio = $("#mosa-picture-1").width() / 800 ;

    $(".home-mosa").css("height", 450*ratio*2);

    $(".home-mosa-stats").css("left", $(window).width()/2-$(".home-mosa-stats").width()/2 - 20);
    $(".home-mosa-stats").css("marginTop", (450*ratio*2)/2 - $(".home-mosa-stats").height()/2 - 20);

    $( ".slogan-home" ).animate({
        marginTop: "-=300"
    }, 2000);

}
