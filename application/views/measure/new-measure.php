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
                                    echo '<option value="'.$watch->watchId.'">'.$watch->brand.' - '.$watch->name.'</option>';   
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <center class="sync-time">10</center>
                    </div>
                </div>
                <div class="form-group userTime">
                    <label for="watchTime" class="col-sm-4 control-label">Enter your time </label>
                    <div class="col-sm-8">
                        <input type="text" name="userTime" class="form-control" placeholder="ex: 12:34:56">
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
                            <button type="submit" class="btn btn-success btn-lg" name="syncDone" disabled>I'm synchronized!</button>
                        </center>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>