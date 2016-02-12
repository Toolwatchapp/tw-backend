
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
  initDemo();

  $('video,audio').mediaelementplayer({
      enableAutosize: true,
      features: []
  });




});

var timeoutClick;

function initDemo(){

  $("#sync-button").html("<span>"
    + "Press this button when <br /> the second-hand  <br /><br />​​"
    + '<img src="../../assets/img/stepnew.jpg" style="width:30%;" />'
    + "<br /><br />"
    + "reaches <i><b>exactly</b></i>&nbsp; the twelve <br /> o'clock position </span> <br />"
  );

  $("#sync-button").show();
  $("#demo-first-step").show();
  $("#demo-second-step").hide();

  var d = new Date();
  var seconds = d.getSeconds();

  timeoutClick = setTimeout(
    function(){$( "#sync-button" ).click()},
    (60 - seconds+1) * 1000);

  $( "#sync-button" ).click(function() {

      clearInterval(timeoutClick);

      var result = delta;

      $("#demo-first-step").hide();
      $("#demo-second-step").fadeToggle();
      $(".watch-accuracy").html(result.toFixed(1));

      setTimeout(
        function(){initDemo();},
        10 * 1000);
    });

}

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
