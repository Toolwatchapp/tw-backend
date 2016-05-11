

<?php
$statusId = end($measures)['statusId'];

if(is_null($statusId) || $statusId == '0' || $statusId == '2') { ?>

    <a data-watch="<?php echo $watchId; ?>" class="submitNewMeasure btn btn-primary btn-lg  col-md-offset-1 col-md-3" href="#"><br>Start a <br>new measure <br>&nbsp;</a>

    <?php echo form_open('/measures/new-measure-for-watch/', array('name'=>'start-new-measure-'.$watchId, 'class'=>"no-display"));?>
    <input type="hidden" name="watchId" value="<?php echo $watchId;?>"></form>

<?php }else if($statusId == '1'){ ?>

  <a data-watch="<?php echo $watchId; ?>" class="submitGetAccuracy btn btn-primary btn-lg  col-md-offset-1 col-md-3" href="#"><br>Check your <br>accuracy <br>&nbsp;</a>

  <?php echo form_open('/measures/get-accuracy/', array('name'=>'get-accuracy-'.$watchId));?>
    <input type="hidden" name="measureId" value="<?php echo end($measures)['id']; ?>">
    <input type="hidden" name="watchId" value="<?php echo $watchId; ?>">
  </form>

<?php }else if($statusId == '1.5'){ ?>

  <a href="#" title="Warning" data-toggle="modal" data-target="#pageModal"
  data-modal-update="true" data-href="/modal/accuracyWarning/" class="btn btn-primary btn-lg col-md-offset-1 col-md-3" href="/measures/new-measure/"><br>Check  in<br>
  <?php echo end($measures)['accuracy'];?> hour(s).<br>&nbsp;</a>

<?php } ?>

<a data-watch="<?php echo $watchId; ?>" class="submitEditWatch btn btn-primary btn-lg  col-md-offset-1 col-md-3" href="#"><br>Edit your <br>watch <br>&nbsp;</a>
<?php echo form_open('/measures/edit_watch_p/', array('name'=>'edit-watch-'.$watchId, 'class'=>"no-display"));?>
<input type="hidden" name="watchId" value="<?php echo $watchId; ?>"></form>

<a data-watch="<?php echo $watchId; ?>" style="background-color:#ff3b30" class="submitDeleteWatch btn btn-primary btn-lg col-md-3 col-md-offset-1" href="#"><br>Delete <br>your watch <br>&nbsp;</a>

<?php echo form_open('/measures/delete_watch/', array('name'=>'delete-watch-'.$watchId, 'class'=>"no-display"));?>
<input type="hidden" name="watchId" value="<?php echo $watchId; ?>"></form>
