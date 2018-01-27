<li>
    <a href="#" class="submitDeleteWatch" data-watch="<?php echo $watchId; ?>">Delete watch</a>
    <?php echo form_open('/measures/delete_watch/', array('name'=>'delete-watch-'.$watchId, 'class'=>"no-display"));?>
    <input type="hidden" name="watchId" value="<?php echo $watchId; ?>"></form>
</li>
