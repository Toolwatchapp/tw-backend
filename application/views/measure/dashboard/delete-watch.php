<li>
    <a href="#" class="submitDeleteWatch" data-watch="<?php echo $watchId; ?>">Delete watch</a>
    <form method="post" action="/measures/delete_watch" name="delete-watch-<?php echo $watchId; ?>" class="no-display">
    <input type="hidden" name="watchId" value="<?php echo $watchId; ?>"></form>
</li>
