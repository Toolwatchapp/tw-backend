<div class="home-intro-overlay">

    <img id="home-video" src="<?php echo $video_url;?>" width="100%" height="100%"/>

    <div class="container container-fluid first slogan-home">

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

    </div>
</div>