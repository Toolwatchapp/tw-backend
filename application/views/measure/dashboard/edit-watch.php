<li>
    <a href="#" class="submitEditWatch" data-watch="<?php echo $watchId; ?>">Edit watch</a>
    <?php echo form_open('/measures/edit_watch_p/', array('name'=>'edit-watch-'.$watchId, 'class'=>"no-display"));?>
    <input type="hidden" name="watchId" value="<?php echo $watchId; ?>"></form>
</li>
