<div class="container container-fluid content first">
    <div class="row">
        <div class="col-md-12">
            <center><h1 id="mainTitle">Check the accuracy</h1></center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form class="form-horizontal" method="post" name="newAccuracy">

                
                <div id="mainExplanation" class="form-group">
                     <center>
                         At the end of the countdown, please enter below the exact time as it is on your watch. <br>Let's check the accuracy of your watch!
                    </center>
                </div>
                <div class="form-group watch-select">
                    <label for="brand" class="col-sm-4 control-label">Selected watch </label>
                    <div class="col-sm-6">
                        <select class="form-control" name="watchId">
                            <?php echo '<option value="'.$selectedWatch->watchId.'" selected>'.$selectedWatch->brand.' - '.$selectedWatch->name.'</option>'; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <center class="sync-time">5</center>
                    </div>
                </div>

                <div class="form-group userTime">
                    <label for="watchTime" class="col-sm-4 control-label">Enter your time </label>
                    <div class="col-sm-8">
                        <input type="text" name="userTime" class="form-control" placeholder="ex: 12:34:56">
                        <br>Please use the 24-hour clock format
                        <span class="signup-error time-error">Your time should be hours:minutes:seconds (like 11:22:33).</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <span class="signup-error measure-error"><center>An error occured while synchronizing with Toolwatch’s accuracy system.<br>Please try again later.</center></span>
                    </div>
                </div>

                <div class="form-group sync-success" style="display:none;">  

                    <div class="col-sm-12">

                        <canvas class="col-sm-4" id="canvas_animated_watch" width="250" height="250"></canvas>
                        <div class="col-sm-8">
                            
                            <div class="col-sm-8">
                                <h1>Congratulations!</h1> <br/> <p class="accuracy-subtitle"> The accuracy of your <strong><span class="watch-brand"><?php echo $selectedWatch->brand;?></span></strong> is </p>
                            </div>
                            <div class="col-sm-8">
                                <strong><span class="watch-accuracy"></span> seconds a day!</strong> 
                            </div>
                        </div>

                        <div class="share-plugin col-md-offset-1 col-sm-10">
                            <center><p><br />We are happy to have you around, help us spread the love for mechanical watches and share your accuracy on:</p></center>
                           
                        <div class="col-sm-12">
                            <div class="share-button" id="twitter" data-url="<?php echo base_url() . '/result' ;?>" data-text="My #<?php echo $selectedWatch->brand;?> runs at {WatchAccuracy} spd on @toolwatch"></div>
                            <div class="share-button" id="facebook" data-url="<?php echo base_url() . '/result' ;?>" data-text="My <?php echo $selectedWatch->brand;?> runs at {WatchAccuracy} spd on https://www.facebook.com/Toolwatch?"></div>
                            <div class="share-button" id="googleplus" data-url="<?php echo base_url() . '/result' ;?>" data-text="My #<?php echo $selectedWatch->brand;?> runs at {WatchAccuracy} spd on https://plus.google.com/104724190750629608501/"></div>        
                        </div>
                           

                        <center><p><br /><br />We <i style="color:#4d77a7" class="fa fa-heart"></i> <a href="https://instagram.com/toolwatchapp/">Instagram</a>, tag us with your wristshots and share your results using #ToolwatchApp !</p></center>
                           
                            <!-- www.intagme.com -->
                        <iframe src="http://www.intagme.com/in/?u=dG9vbHdhdGNoYXBwfGlufDEwMHw0fDJ8fHllc3w1fHVuZGVmaW5lZHx5ZXM=" allowTransparency="true" frameborder="0" scrolling="no" style="margin-left:5px; border:none; overflow:hidden; width:460px; height: 230px" ></iframe>

                        </div>
                        
                    </div>

                </div>

                    
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <center>
                            <input type="hidden" name="measureId" value="<?php echo $measureId;?>">
                            <button class="btn btn-primary btn-lg" name="startSync">Check now!</button>
                            <a class="btn btn-success btn-lg no-display backToMeasure" href="/measures/">Back to measures</a>
                            <button class="btn btn-primary btn-lg no-display" name="restartCountdown">Restart countdown</button>
                            <button type="submit" class="btn btn-success btn-lg btn-spinner" name="syncDone" disabled>Check the accuracy! <i class="fa fa-spinner fa-pulse"></i></button>
                        </center>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <img style="display:none" id="watch" src="<?php echo img_url('flatwatch-blank.png');?>">
</div>