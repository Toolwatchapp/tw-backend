<div class="row">
    <div id="toolwatch-explained" class="col-sm-offset-2 col-sm-8">
        <h1>MECHANICAL WATCH LOVER'S FAVORITE TOOL. IT'S INSANELY SIMPLE.</h1>
        <p>Toolwatch's accuracy measure is built for speed and ease to use. Measuring the accuracy of your mechanical watch is so simple that you'll actually use it. 
        Toolwatch helps you keep your loved ones on time.</p>
        <center>
             <?php 
                if($userIsLoggedIn)
                {
                    echo '<a class="btn btn-default btn-xlarge" href="/measures/">GET STARTED <i class="fa fa-arrow-right"></i></a>';
                }
                else
                {
                    echo '<a class="btn btn-default btn-xlarge" href="#" title="Signup" data-cta="GET_STARTED" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/sign-up/">GET STARTED <i class="fa fa-arrow-right"></i></a>';
                }
            ?>
        </center>
    </div>
</div>