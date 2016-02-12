<div class="row">

    <div id="demo" class="col-sm-12">

       <div style="margin-top:60px;" class=" col-sm-offset-2 col-sm-3" >
            <canvas id="canvas_animated_watch" width="250" height="250"></canvas>
            <img style="display:none" id="watch" src="<?php echo img_url('flatwatch-blank.png');?>">
       </div>


        <div  id="demo-first-step" class="form-group">
            <div class="col-sm-5">
              <center>
                <br />
                <br />
                <br />
                <a
                  style="display:none"
                  id="sync-button"
                  class="btn btn-primary  btn-lg">
                </a>
              </center>
            </div>
        </div>


        <div style="margin-top:100px;" id="demo-second-step" class="col-sm-5" >
            <div class="col-sm-12">
                <h1>Congratulations!</h1> <br/> <p class="accuracy-subtitle"> The accuracy of your <strong><span class="watch-brand">watch</span></strong> is </p>
            </div>
            <div class="col-sm-12">
                <strong><span class="watch-accuracy"></span> seconds a day!</strong>
            </div>
        </div>

    </div>

</div>
