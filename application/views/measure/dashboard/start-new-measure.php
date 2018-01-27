<li>
  <a href="#" class="submitNewMeasure" data-watch="<?php echo $watchId; ?>">Start a new measure</a>
  <?php echo form_open('/measures/new-measure-for-watch/', array('name'=>'start-new-measure-'.$watchId, 'class'=>"no-display"));?>

    <input type="hidden" name="watchId" value="<?php echo $watchId;?>"></form>
</li>
