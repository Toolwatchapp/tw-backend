<li>
    <a href="#" class="submitDeleteMeasures" data-watch="<?php echo $id; ?>">Delete this measure</a>
    <?php echo form_open('/measures/delete_measure/', array('name'=>'delete-measures-'.$id, 'class'=>"no-display"));?>
    <input type="hidden" name="deleteMeasures" value="<?php echo $id; ?>">
    </form>
</li>
