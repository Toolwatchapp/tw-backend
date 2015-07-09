
var delta =  (Math.random() * 6) + 1 - 2;
var d;

var activeIndex = 0;
var possibleIndex = ["home-video", "demo-screen", "mosa-screen", "publication_footer"];
var animationUnderWay = false;
var timeouts = [];

$( window ).resize(function() {
  initSize();
});



$( document ).ready(function() {


    var mousewheelevt = (/Firefox/i.test(navigator.userAgent)) ? "DOMMouseScroll" : "mousewheel" //FF doesn't recognize mousewheel as of FF3.x
    $(window).bind(mousewheelevt, function(e){
        
        if(!animationUnderWay){
            for (var i=0; i<timeouts.length; i++) {
                clearTimeout(timeouts[i]);
            }

            $("#demo-second-step").show();
            $("#demo-third-step").hide();
            $("#demo-fourth-step").hide();
            $("#demo-sync-time").html(5);
            $("#inputUserTime").val("");
            $("#demo-pointer").removeAttr('style');

            animationUnderWay = true;
            var evt = window.event || e; //equalize event object     
            evt = evt.originalEvent ? evt.originalEvent : evt; //convert to originalEvent if possible               
            var delta = evt.detail ? evt.detail*(-40) : evt.wheelDelta; //check for detail first, because it is used by Opera and FF

            if(delta > 0) {
                activeIndex = activeIndex -1;
            } else {
                activeIndex = activeIndex +1;
            }   

            if(activeIndex === -1){
                activeIndex = 0;
            }else if(activeIndex === 4){
                 activeIndex = 3;
            }

            if(activeIndex!==0){
                $('header').addClass('blue');   
            }else{
                $('header').removeClass('blue');   
            }

            if(activeIndex === 1){
                timeouts.push(setTimeout(countDownDisplay, 1000));
            }
            var scrollTo = $("#"+possibleIndex[activeIndex]).offset().top -  $(".navbar").height();
            console.log(scrollTo);
            $('html, body').animate({
                scrollTop: scrollTo
            }, 2000, function() {
                animationUnderWay = false;
            });
        }else{
            e.preventDefault();
        }
       
    });

    $("#demo-third-step").hide();
    $("#demo-fourth-step").hide();

    initSize();

    
    $('video,audio').mediaelementplayer({
        enableAutosize: true,
        features: []
    });

    $( "#demo-cta" ).click(function() {

        var result = delta;

        $("#demo-pointer").hide();
        $("#demo-third-step").hide();
        $("#demo-fourth-step").fadeToggle();
        $(".watch-accuracy").html(result.toFixed(1));
    });


});

function initCounDown(){

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

function countDownDisplay(){
    var countdown = $("#demo-sync-time").html();

    $("#demo-sync-time").html(countdown - 1);
    if(countdown > 1){
        timeouts.push(setTimeout(countDownDisplay, 1000));
    }else{
        d = new Date(new Date().getTime());
        similateInput();
    }
}

function similateInput(){
    $("#demo-second-step").hide();
    $("#demo-third-step").fadeToggle("slow", function (){

        var leftDelta = $("#demo-pointer").position().left 
            - $("#inputUserTime").position().left;

        var topDelta = $("#demo-pointer").position().top 
            - $("#inputUserTime").position().top - $("#inputUserTime").height() /2;

        $( "#demo-pointer" ).animate({
            marginTop: "-="+topDelta,
            marginLeft: "-="+leftDelta
        }, 2000, function(){

            $("#inputUserTime").focus();

            var seconds = (d.getSeconds() + Math.floor(Math.abs(delta)));
            if(seconds <= 9){
                seconds = "0"+seconds;
            }

            var hours = d.getHours();

            if(hours <= 9){
                hours = "0"+seconds;
            }

            var minutes = d.getMinutes();

            if(minutes <= 9){
                minutes = "0"+minutes;
            }

            var date = hours + ':' + minutes  + ':' + seconds;
            writeToInput(date, 0);
        });
    });
}

function writeToInput(text, i){

    if(i < text.length){
        $("#inputUserTime").val($("#inputUserTime").val() + text.charAt(i));
        i = i +1;
        timeouts.push(setTimeout(writeToInput, Math.round(Math.random() * (300 - 100) + 100), text, i));
    }else{
        clickCTA();
    }
}

function clickCTA(){
    var leftDelta = $("#demo-pointer").position().left - $("#demo-cta").position().left + $("#demo-cta").width() * 2 + 30;

    var topDelta = $("#demo-pointer").position().top - $("#demo-cta").position().top + $("#demo-cta").height() * 3;

    $( "#demo-pointer" ).animate({
                marginTop: "+="+topDelta,
                marginLeft: "+="+leftDelta
    }, 2000, function(){
        $("#demo-cta").click();
    });
}