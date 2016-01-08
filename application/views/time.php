<div style="margin-top:40px;" class="row">
    <div class="col-md-12"><div class="line black"></div></div>
</div>
<div id="clockdiv" class="row">

           <div class="col-sm-4">
             <br>
             <span class="days"></span>
             <span class="months"></span><br />
             <span class="years"></span>
           </div>

           <div class="col-sm-4" >
                <canvas id="canvas_animated_watch" width="250" height="250"></canvas>
                <img style="display:none" id="watch" src="<?php echo img_url('flatwatch-blank.png');?>">
           </div>


           <div class="col-sm-4">
             <br>
             <span class="hours"></span>
             : <span class="minutes"></span>
             : <span class="seconds"></span>
             <br />
            <img style="margin-top:-5px" id="moonPhaseImage" src="<?php echo base_url();?>/assets/img/moon/" border="0" width="60px" height="100%" title="">
           </div>
</div>
