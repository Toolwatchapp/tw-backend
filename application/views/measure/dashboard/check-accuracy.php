<li><a href="#" class="submitGetAccuracy" data-watch="<?php echo $id; ?>">Check the accuracy</a>
  <?php echo form_open('/measures/get-accuracy/', array('name'=>'get-accuracy-'.$id));?>
    <input type="hidden" name="measureId" value="<?php echo $id; ?>">
    <input type="hidden" name="watchId" value="<?php echo $watchId; ?>">
  </form>
</li>
