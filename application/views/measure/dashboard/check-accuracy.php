<li><a href="#" class="submitGetAccuracy" data-watch="<?php echo $id; ?>">Check the accuracy</a>
  <form method="post" action="/measures/get-accuracy/" name="get-accuracy-<?php echo $id; ?>">
    <input type="hidden" name="measureId" value="<?php echo $id; ?>">
    <input type="hidden" name="watchId" value="<?php echo $watchId; ?>">
  </form>
</li>
