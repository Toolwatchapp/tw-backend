<div class="home-intro">
    <div class="home-intro-overlay">
      <video id="home-video" src="<?php echo vid_url('home.mp4');?>" width="100%" height="100%" autoplay loop muted></video>
        <div class="container container-fluid first slogan-home">

            <div class="row">
                <div class="col-md-12">
                    <center>
                        <h1>Measure your watch's accuracy</h1>
                    </center>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <center>
                        <a class="btn btn-lg btn-white" href="#" title="Signup" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/sign-up/">Measure your watch now!</a>
                    </center>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <center>
                        <h2>The most convenient way to measure <br /> the accuracy of your mechanical watch</h2>
                    </center>
                </div>
            </div>

            <div class="row continue">
                <div class="col-md-12">
                    <center>
                        <span class="fa fa-chevron-down"></span>
                    </center>
                </div>
            </div>


        </div>
    </div>
</div>
<div class="home-picto">
    <div class="container container-fluid">

         <div class="row">

                <div id="demo" class="col-sm-12">

                   <div class=" col-sm-offset-2 col-sm-3" >
                        <canvas id="canvas_animated_watch" width="250" height="250"></canvas>
                        <img style="display:none" id="watch" src="<?php echo img_url('flatwatch-blank.png');?>">
                   </div>

                    <div id="demo-second-step" class="col-sm-5" >
                        <center id="demo-sync-time">5</center>
                    </div>

                    <div id="demo-third-step" class="col-sm-5" >
                        <br />
                        <br />
                        <br />
                        <input type="text" id="inputUserTime" name="userTime" class="form-control" placeholder="ex: 12:34:56">
                        <br>Please use the 24-hour clock format
                        <span class="signup-error time-error">Your time should be hours:minutes:seconds (like 11:22:33).</span>
                        <button id="demo-cta" class="btn btn-primary btn-lg" name="startSync">Check now!</button>
                        <img id='demo-pointer' src="<?php echo img_url('pointer.png');?>">

                    </div>

                  
                    <div id="demo-fourth-step" class="col-sm-5" >
                        <div class="col-sm-12">
                            <h1>Congratulations!</h1> <br/> <p class="accuracy-subtitle"> The accuracy of your <strong><span class="watch-brand">watch</span></strong> is </p>
                        </div>
                        <div class="col-sm-12">
                            <strong><span class="watch-accuracy"></span> seconds a day!</strong> 
                        </div>
                    </div>

                </div>

         </div>

         <div class="row">
            <div id="toolwatch-explained" class="col-sm-offset-2 col-sm-8">
                <h1>MECHANICAL WATCH LOVER'S FAVORITE TOOL. IT'S INSANELY SIMPLE.</h1>
                <p>Toolwatch's accuracy measure is built for speed and ease to use. Measuring the accuracy of your mechanical watch is so simple that you'll actually use it. 
                Toolwatch helps you keep your loved ones on time.</p>
                <center>
                    <a class="btn btn-default btn-xlarge" href="#" title="Signup" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/sign-up/">GET STARTED <i class="fa fa-arrow-right"></i></a>
                </center>
            </div>
         </div>

    </div>
</div>

<div class="home-mosa">

        <div class="home-mosa-stats">
            <img src="<?php echo img_url('logo-blue.png');?>">
            <h2>XXX Followers on Instagram.</h2>
            <p>The most convenient way to measure 
the accuracy of your mechanical watch.</p>
        </div>


    <div class="home-mosa-pictures">
        <div id="mosa-picture-1" style="background-image: url('https://www.toggl.com/photos/makeable.jpg');" class="home-mosa-picture">
        </div>
        <div id="mosa-picture-2" style="background-image: url('https://www.toggl.com/photos/webhomes.jpg');" class="home-mosa-picture">
        </div>
        <div id="mosa-picture-3" style="background-image: url('https://www.toggl.com/photos/offerpop.jpg?1');" class="home-mosa-picture">
        </div>
        <div id="mosa-picture-4" style="background-image: url('https://www.toggl.com/photos/matt_alexander.jpg');" class="home-mosa-picture">
        </div>
    </div>


</div>

<script>
// using jQuery


var delta =  Math.floor((Math.random() * 10) + 1) - 5;
var d;


$( document ).ready(function() {

    $("#demo-third-step").hide();
    $("#demo-fourth-step").hide();

    var windowHeight = $(window).height();   
    var headerHeight = $('header').height();   
    var sloganHeight = $(".slogan-home");
    var windowRealHeight = windowHeight - headerHeight;
    //var align = dispoDisplay / 2 - sloganHeight / 2;

    $(".slogan-home").css("margin-top", -windowRealHeight + windowRealHeight/2);

    var ratio = $("#mosa-picture-1").width() / 800 ;

    $(".home-mosa").css("height", 450*ratio*2);

    $(".home-mosa-stats").css("left", $(window).width()/2-$(".home-mosa-stats").width()/2 - 20);
    $(".home-mosa-stats").css("marginTop", (450*ratio*2)/2 - $(".home-mosa-stats").height()/2 - 20);

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

    $( ".slogan-home" ).animate({
        marginTop: "-=300"
    }, 2000);
});

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

</script>