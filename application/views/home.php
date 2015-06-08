<div class="home-intro">
    <div class="home-intro-overlay">
      <video id="home-video" src="<?php echo vid_url('home.mp4');?>" width="100%" height="90%" autoplay loop></video>
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
                    <button style="margin-left:50px;margin-top:20px" id="demo-new" class="btn btn-primary btn-lg" name="startSync">New measure</button>
                </div>

            </div>

     </div>

    </div>
</div>

<script>
// using jQuery


var delta =  Math.round((Math.random() * (-5 - 5) + -5));
var d;

$( document ).ready(function() {

    $("#demo-third-step").hide();
    $("#demo-fourth-step").hide();

    var windowHeight = $(window).height();   
    var headerHeight = $('header').height();   
    $(".slogan-home").css("margin-top", -windowHeight+headerHeight+70);


    $('video,audio').mediaelementplayer({features: []});

    $(".continue").click(function(){
        setTimeout(countDownDisplay, 1000);
    });

    $ ("#demo-new").click(function (){
        $("#demo-third-step").hide();
        $("#demo-fourth-step").hide();
        $("#demo-second-step").show();
        $("#inputUserTime").val("");
        $("#demo-sync-time").html("5");
        
        similateInput = function(){ 

            $("#demo-second-step").hide();
            $("#demo-third-step").fadeToggle();
        };

        setTimeout(countDownDisplay, 1000);
    });

    $( "#demo-cta" ).click(function() {
        $( "#demo-pointer" ).hide();
        $("#demo-third-step").hide();
        $("#demo-fourth-step").fadeToggle();
        $(".watch-accuracy").html(delta);
    });

    $( ".slogan-home" ).animate({
        marginTop: "-=70"
    }, 2000);
});

function countDownDisplay(){
    var countdown = $("#demo-sync-time").html();

    $("#demo-sync-time").html(countdown - 1);
    if(countdown > 1){
        setTimeout(countDownDisplay, 1000);
    }else{
        d = new Date();
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

            var seconds = (d.getSeconds() + delta);
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