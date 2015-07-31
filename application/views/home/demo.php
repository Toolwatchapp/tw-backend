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