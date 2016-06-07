<div class="container container-fluid content first">
    <div class="row">
        <div class="col-md-12">
            <center><h1 id="mainTitle">Check the accuracy <span id="selectedWatch"> of your <?php echo $selectedWatch->brand.' - '.$selectedWatch->name;?></span></h1></center>
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
    <span id="accuracyHolder"></span>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">

          <?php echo form_open('', array('name'=>'newAccuracy', 'class'=>'form-horizontal'));?>

              <select style="display:none" class="form-control" name="watchId">
                    <?php echo '<option value="'.$selectedWatch->watchId.'" selected>'.$selectedWatch->brand.' - '.$selectedWatch->name.'</option>'; ?>
              </select>

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

                <div class="form-group sync-success" style="display:none; text-align: center">

                    <div class="col-sm-12">

                        <canvas class="col-sm-4" id="canvas_animated_watch" width="250" height="250"></canvas>
                        <div class="col-sm-8">

                            <div class="col-sm-8">
                                <h1>Congratulations!</h1> <br/> <p class="accuracy-subtitle"> The accuracy of your <strong><span class="watch-brand"><?php echo $selectedWatch->brand;?></span></strong> is </p>
                            </div>
                            <div class="col-sm-8">
                                <strong><span class="watch-accuracy"></span> seconds a day!</strong>
                            </div>
                        </div>

                        <div class="share-plugin col-md-offset-1 col-sm-10">
                            <center><p><br /><br />Your watch is <b>more accurate than <span class="watch-percentile"></span>%</b> of all tested watches.</p></center>

                        <div class="col-sm-12">
                          <a class="share-button"
                          href="https://twitter.com/intent/tweet?text=My watch is more accurate than {WatchPercentile}%25 of all tested watches on @ToolWatchApp&url=https://toolwatch.io"
                          target="_blank">
                            <img src="<?php echo base_url();?>assets/img/tweet.png" alt="Tweet on tweeter" />
                          </a>

                          <a class="share-button"
                          href="http://www.facebook.com/sharer.php?s=100&p[url]=https://toolwatch.io&p[title]=I just tested the accuracy of my watch&p[summary]=My watch is more accurate than {WatchPercentile}%25 of all tested watches on https://www.facebook.com/Toolwatch"
                          target="_blank">
                            <img src="<?php echo base_url();?>assets/img/facebook.png" alt="Share on timeline" />
                          </a>

                          <a class="share-button"
                          href="https://plus.google.com/share?url=https://toolwatch.io"
                          target="_blank">
                            <img src="<?php echo base_url();?>assets/img/google-plus.png" alt="Share on timeline" />
                          </a>

                          <a class="share-button"
                          href="mailto:?to=&Subject=I just tested the accuracy of my watch&body=My watch is more accurate than {WatchPercentile}%25 of all tested watches on https://toolwatch.io"
                          target="_top">
                            <img src="<?php echo base_url();?>assets/img/email-share.png" alt="Share by email" />
                          </a>
                        </div>

                        <br/>
                        <br />
                        <center><p><br /><br />We <i style="color:#4d77a7" class="fa fa-heart"></i> <a href="https://instagram.com/toolwatchapp/">Instagram</a>, tag us with your wristshots and share your results using #ToolwatchApp !</p></center>
                        <br />
                        <!-- SnapWidget -->
                          <script src="http://snapwidget.com/js/snapwidget.js"></script>
                          <iframe src="http://snapwidget.com/in/?u=dG9vbHdhdGNoYXBwfGlufDEyNXwzfDJ8fG5vfDV8bm9uZXxvblN0YXJ0fHllc3x5ZXM=&ve=290216" title="Instagram Widget" class="snapwidget-widget" allowTransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden; width:100%;"></iframe>
                        </div>

                    </div>

                </div>


                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <center>
                            <input type="hidden" name="measureId" value="<?php echo $measureId;?>">
                            <button class="btn btn-primary btn-lg" name="startSync">Check now!</button>
                            <a class="btn btn-success btn-lg no-display backToMeasure" href="/measures/">Back to measures</a>
                        </center>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <img style="display:none" id="watch" src="<?php echo img_url('flatwatch-blank.png');?>">
</div>
