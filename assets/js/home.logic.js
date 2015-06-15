
var delta =  Math.floor((Math.random() * 10) + 1) - 5;
var d;

$( window ).resize(function() {
  initSize();
});

$( document ).ready(function() {

    $("#demo-third-step").hide();
    $("#demo-fourth-step").hide();

    initSize();

    
    $('video,audio').mediaelementplayer({features: []});

    $(".continue").click(function(){
        setTimeout(countDownDisplay, 1000);
    });

    $( "#demo-cta" ).click(function() {

        var result = delta;

        $("#demo-pointer").hide();
        $("#demo-third-step").hide();
        $("#demo-fourth-step").fadeToggle();
        console.log();
        $(".watch-accuracy").html(result.toFixed(0));
    });


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

function countDownDisplay(){
    var countdown = $("#demo-sync-time").html();

    $("#demo-sync-time").html(countdown - 1);
    if(countdown > 1){
        setTimeout(countDownDisplay, 1000);
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

            var seconds = (d.getSeconds() + Math.abs(delta));
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
        setTimeout(writeToInput, Math.round(Math.random() * (300 - 100) + 100), text, i);
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