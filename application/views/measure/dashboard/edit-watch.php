<li>
    <a href="#" class="submitEditWatch" data-watch="<?php echo $watchId; ?>">Edit watch</a>
    <form method="post" action="/measures/edit_watch_p" name="edit-watch-<?php echo $watchId; ?>" class="no-display">
    <input type="hidden" name="watchId" value="<?php echo $watchId; ?>"></form>
</li>
