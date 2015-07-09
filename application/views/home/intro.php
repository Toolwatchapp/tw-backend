<div class="home-intro-overlay">

    <video id="home-video" src="<?php echo $video_url;?>" width="100%" height="100%" autoplay loop muted></video>

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

                    <?php 
                        if($userIsLoggedIn)
                        {
                            echo '<a class="btn btn-lg btn-white" href="/measures/">Measure your watch now!</a>';
                        }
                        else
                        {
                            
                            echo '<a class="btn btn-lg btn-white" href="#" title="Signup" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-cta="MEASURE_NOW" data-href="/sign-up/">Measure your watch now!</a>';
                        }
                    ?>

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

    </div>
</div>