<div class="home-intro">
    <div class="home-intro-overlay">
      <video id="home-video" src="<?php echo vid_url('home.mp4');?>" width="100%" height="100%" autoplay loop muted></video>
        <div class="container container-fluid first slogan-home">

            <?php if(!$this->agent->is_mobile()){ ?>
                <div class="row">
                    <div class="col-md-12">
                        <center>
                            <h1>Measure your watch's accuracy</h1>
                        </center>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-12">
                    <center>
                        <a class="btn btn-lg btn-white" href="#" title="Signup" data-toggle="modal" data-target="#pageModal" 
                        data-modal-update="true" data-cta="MEASURE_NOW" data-href="/sign-up/">Measure your watch now!</a>
                    </center>
                </div>
            </div>
            <?php if(!$this->agent->is_mobile()){ ?>
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
            <?php } ?>


        </div>
    </div>
</div>

<div class="home-picto">
    <div class="container container-fluid">
<?php if(!$this->agent->is_mobile()){ ?>
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
 <?php } ?>
         <div class="row">
            <div id="toolwatch-explained" class="col-sm-offset-2 col-sm-8">
                <h1>MECHANICAL WATCH LOVER'S FAVORITE TOOL. IT'S INSANELY SIMPLE.</h1>
                <p>Toolwatch's accuracy measure is built for speed and ease to use. Measuring the accuracy of your mechanical watch is so simple that you'll actually use it. 
                Toolwatch helps you keep your loved ones on time.</p>
                <center>
                    <a class="btn btn-default btn-xlarge" href="#" title="Signup" data-cta="GET_STARTED" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/sign-up/">GET STARTED <i class="fa fa-arrow-right"></i></a>
                </center>
            </div>
         </div>

    </div>
</div>

<?php if(!$this->agent->is_mobile()){ ?>
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
 <?php } ?>
