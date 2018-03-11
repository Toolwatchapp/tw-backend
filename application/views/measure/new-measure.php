<div class="container container-fluid content first">
    <div class="row">
        <div class="col-md-12">
            <center><h1>New measure <span id="selectedWatch"></span></h1></center>
        </div>
        <div class="col-md-12">
          <div style="display:none" id="sync-text">
            <center>
              <h1 id="perc-sync">0%</h1>
              <p>Synchroniz<span id="tense-sync">ing</span> with the U. S. Naval Observatory's atomic clock.</p>
            </center>
          </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <?php echo form_open('', array('name'=>'newMeasure', 'class'=>"form-horizontal"));?>
                <div class="form-group watch-select">
                    <label for="brand" class="col-sm-4 control-label">Select your watch </label>
                    <div class="col-sm-8">
                        <select class="form-control" data-placeholder="Select your watch" name="watchId">
                            <?php
                                foreach($watches as $watch)
                                {
                                  $selected = "";
                                  if(isset($selected_watch)){
                                    $selected = ($watch->watchId === $selected_watch) ? "selected" : "";
                                  }

                                  echo '<option value="'.$watch->watchId.'" '.$selected.'>'.$watch->brand.' - '.$watch->name.'</option>';
                                }
                            ?>
                        </select>
                        <span class="signup-error watch-error">Please, select a watch.</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                      <center>
                        <br />
                        <br />
                        <br />
                        <a href="javascript:clicked();"
                          style="display:none"
                          id="sync-button"
                          class="btn btn-primary  btn-lg">
                        </a>
                      </center>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <span class="signup-error measure-error"><center>An error occured while synchronizing with Toolwatch’s accuracy system.<br>Please try again later.</center></span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <center>
                            <button class="btn btn-primary btn-lg" name="startSync">Start now!</button>
                        </center>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if(isset($selected_watch)){
?>

<script>
  $( document ).ready(function() {
    createCTA();
  });
</script>

<?php
}
?>
