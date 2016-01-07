<div class="container container-fluid content first">
    <div class="row">
        <div class="col-md-12">
            <center><h1>New measure</h1></center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form class="form-horizontal" method="post" name="newMeasure">
                <div class="form-group">
                     <center>
                         To begin measuring the accuracy of your watch, we must first synchronize your watch with Toolwatch’s accuracy system.
                         Start clicking on « Start now! » to start the countdown then look at your watch.<br><br>
                         At the end of the countdown, please enter below the exact time as it is on your watch. Let’s start measuring!
                    </center>
                </div>
                <div class="form-group watch-select">
                    <label for="brand" class="col-sm-4 control-label">Select your watch </label>
                    <div class="col-sm-8">
                        <select class="form-control" data-placeholder="Select your watch" name="watchId">
                            <?php
                                foreach($watches as $watch)
                                {
                                  if(isset($selected_watch)){
                                    $selected = ($watch->watchId === $selected_watch) ? "selected" : "";
                                  }

                                  echo '<option value="'.$watch->watchId.'" '.$selected.'>'.$watch->brand.' - '.$watch->name.'</option>';
                                }
                            ?>
                        </select>
                        <span class="signup-error watch-error">Please, select a watch.</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <center class="sync-time">5</center>
                    </div>
                </div>
                <div class="form-group sync-success" style="display:none;">
                    <div class="col-sm-12">
                        <div class="alert alert-success">
                            <center>
                            Congratulations, your watch is now synchronized.
                            For a better accuracy of the measurement, you must wait at least 12 hours before coming back.
                            Do not worry, we will send you an email when you should come back and check your results.
                            </center>
                        </div>
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
                <div class="form-group">
                    <div class="col-sm-12">
                        <center>
                            <button class="btn btn-primary btn-lg" name="startSync">Start now!</button>
                            <a class="btn btn-success btn-lg no-display backToMeasure" href="/measures/">Back to measures</a>
                            <button class="btn btn-primary btn-lg no-display" name="restartCountdown">Restart countdown</button>
                            <button type="submit" class="btn btn-success btn-lg btn-spinner" name="syncDone" disabled>I'm synchronized! <i class="fa fa-spinner fa-pulse"></i></button>
                        </center>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
