
<div class="container container-fluid content first">
  <div class="row">
    <div class="col-md-offset-3 col-md-6">
      <h2>Email preferences</h2>
      <p>
        <?php if($success) { ?>
          <div class="alert alert-success alert-dismissible" role="alert" >
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <span>Your preferences have been updated.</span></div>
      </p>
    </div>
  </div>
        <?php } else { ?>
          <p>
            From this page, you can update your email preferences.
          </p>
      </div>
    </div>

    <style>
        input.col-md-1 {display: block;}
    </style>

    <script type="text/javascript">
    $( document ).ready(function() {
      $.each($( "form input:checkbox" ), function(key, value){

        $(value).on("click", function(){

          toggleValue($(value));
        });
      });
    });

    function toggleValue(element){

      hiddenElement = $("#"+element.prop("id").replace("-chkbox", ""));

      if(hiddenElement.prop("value") == "0"){
        hiddenElement.prop("value", "1");
      }else{
        hiddenElement.prop("value", "0");
      }
    }
    </script>

    <div class="row">
        <div class="col-md-offset-3 col-md-6">

          <?php echo form_open('Unsubscribe/update', array('name'=>'preferences', 'class'=>'form-horizontal'));?>

          <h3>Notification of your measures</h3>
          <p>We highly recommend not unsubscribing from those.</p>

          <div class="row">

            <input class="col-md-1"
            <?php if($dayAccuracy == 1){echo "checked ";}?>
            type="checkbox"
            id="dayAccuracy-chkbox">
            <input id="dayAccuracy" type="hidden" name="dayAccuracy" value="<?php echo $dayAccuracy;?>">
            <p class="col-md-11">Remind me to take my second measure 24 hours after the first one.</p>
          </div>


            
          <div class="row">

            <input class="col-md-1"
            <?php if($weekAccuracy == 1){echo "checked ";}?>
            type="checkbox"
            id="weekAccuracy-chkbox">
            <input id="weekAccuracy" type="hidden" name="weekAccuracy" value="<?php echo $weekAccuracy;?>">
            <p class="col-md-11">Remind me to take my second measure 1 week after the first one.</p>
           </div>


          <div class="row">

            <input class="col-md-1"
            <?php if($result == 1){echo "checked ";}?>
            type="checkbox"
            id="result-chkbox">
            <input id="result" type="hidden" name="result" value="<?php echo $result;?>">
            <p class="col-md-11">Send me my watch accuracy by email.</p>
           </div>

          <div class="row">

            <input class="col-md-1"
            <?php if($newMeasure == 1){echo "checked ";}?>
            type="checkbox"
            id="newMeasure-chkbox">
            <input id="newMeasure" type="hidden" name="newMeasure" value="<?php echo $newMeasure;?>">
            <p class="col-md-11">Remind me to check my watch accuracy once a month.</p>
          </div>

          <h3>Tips to make better use of Toolwatch</h3>



          <div class="row">

            <input class="col-md-1"
            <?php if($tips == 1){echo "checked ";}?>
            type="checkbox"
            id="tips-chkbox">
            <input type="hidden" name="userId" value="<?php echo $userId;?>">
            <input id="tips" type="hidden" name="tips" value="<?php echo $tips;?>">
            <p class="col-md-11">Once in a while, a tip for better using Toolwatch.</p>
          </div>

          <div class="row">

          <input class="col-md-1"
          <?php if($platformAnnonces == 1){echo "checked ";}?>
          type="checkbox"
          id="platformAnnonces-chkbox">
          <input type="hidden" name="userId" value="<?php echo $userId;?>">
          <input id="platformAnnonces" type="hidden" name="platformAnnonces" value="<?php echo $platformAnnonces;?>">
          <p class="col-md-11">Important Toolwatch news.</p>
          </div>

          <br>
          <br>
          <input type="submit" class="btn btn-primary btn-lg col-md-5 col-md-offset-5" value="Save">

          </form>

        </div>
    </div>
    <?php } ?>
</div>
