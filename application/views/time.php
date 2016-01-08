<div style="margin-top:30px;" class="row">
    <div class="col-md-12"><div class="line black"></div></div>
</div>
<div class="row">

    <div id="demo" class="col-sm-12">

       <div class="col-sm-3" >
            <canvas id="canvas_animated_watch" width="250" height="250"></canvas>
            <img style="display:none" id="watch" src="<?php echo img_url('flatwatch-blank.png');?>">
       </div>

       <div id="clockdiv" class="col-sm-offset-1 col-sm-8">
         <div class="row">

           <div class="col-sm-12">
             <span class="days"></span>
             <span class="months"></span>,
             <span class="years"></span>
           </div>

         </div>

         <div class="row">

           <div class="col-sm-offset-1 col-sm-7">

             <span class="hours"></span>
             :<span class="minutes"></span>
             :<span class="seconds"></span>

           </div>

           <div class="col-sm-3">
             <img style="margin-top:-5px" id="moonPhaseImage" src="<?php echo base_url();?>/assets/img/moon/" border="0" width="100%" height="100%" title="">
           </div>

         </div>

       </div>

    </div>

</div>
