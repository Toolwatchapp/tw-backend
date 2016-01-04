<li>
    <a href="#" class="submitDeleteMeasures" data-watch="<?php echo $id; ?>">Delete this measure</a>
    <form method="post" action="/measures/delete_measure" name="delete-measures-<?php echo $id; ?>" class="no-display">
    <input type="hidden" name="deleteMeasures" value="<?php echo $id; ?>">
    </form>
</li>
