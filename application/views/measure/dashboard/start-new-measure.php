<li><a class="submitNewMeasure" data-watch="<?php echo $watchId; ?>">Start a new measure</a>
  <form method="post" action="/measures/new-measure-for-watch/" name="start-new-measure-<?php echo $watchId;?>">
    <input type="hidden" name="watchId" value="<?php echo $watchId;?>">
  </form>
</li>
