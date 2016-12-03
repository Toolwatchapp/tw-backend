<?php if(is_object($selectedWatch)){$selectedWatch = (array) $selectedWatch;}?>

<div class="container container-fluid content first">
    <div class="row">
        <div class="col-md-12">
            <center><h1 id="mainTitle">Check the accuracy <span id="selectedWatch"> of your <?php if(isset($selectedWatch)){ echo $selectedWatch['brand'].' - '.$selectedWatch['name'];}?></span></h1></center>
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


    <div style="display:none;" class="form-group sync-success row">

      <div  class="col-md-12">
        <center>
        <div class="row">
          <h1>THANK YOU FOR MEASURING YOUR WATCH!</h1>
        </div>

        <div class="row">
            <div class="wrapper col-md-push-4 col-md-4">
                <div class="ribbon-wrapper-green"><div class="ribbon-green">More accurate than <span class="watch-percentile"></span>% <br> of all tested watches.</div></div>
                <center>
                  <br>
                  <br>
                  <br>
                  <br>
                  <h2>Congratulations</h2>
                  <h3>
                    The accuracy of your <strong><span class="watch-brand"><?php echo $selectedWatch['brand'];?></span></strong> is <span class="watch-accuracy"></span> spd.
                  </h3>
                </center>

                <canvas class="col-md-offset-1 col-md-10" id="canvas_animated_watch" width="250" height="250"></canvas>
            </div>
          </div>

          <div class="row">

          <p><br><br>You can continue enjoying this mechanical beauty on your wrist.<br><br></p>
          <h4>DON'T KEEP THIS GOLDMINE JUST FOR YOU, SHARE IT ;)</h4>

          <br />
          <a style="padding:15px; margin-left:20px; color:white; background-color: #36528c; width:45%; " class="col-md-6 share-button"
          href="http://www.facebook.com/sharer.php?s=100&p[url]=https://toolwatch.io&p[title]=I just tested the accuracy of my watch&p[summary]=My watch is more accurate than {WatchPercentile}%25 of all tested watches on https://www.facebook.com/Toolwatch"
          target="_blank">
            <i class="fa fa-facebook" aria-hidden="true"></i>&nbsp;FACEBOOK

          </a>

          <a style="padding:15px; width:45%; margin-left:10px; color:white; background-color: #00a3e0" class="col-md-6 share-button"
          href="https://twitter.com/intent/tweet?text=My watch is more accurate than {WatchPercentile}%25 of all tested watches on @ToolWatchApp&url=https://toolwatch.io"
          target="_blank">
            <i class="fa fa-twitter" aria-hidden="true"></i>&nbsp;TWITTER
          </a>

          <br>          <br>
                    <br>
                    <br>
                    <br>
          <a class="btn btn-success btn-lg no-display backToMeasure" href="/measures/">Back to measures</a>
          </div>
        </center>
      </div>

      <style media="screen">

.wrapper{
  background: white;
border-radius: 10px;
-webkit-box-shadow: 0px 0px 8px rgba(0,0,0,0.3);
-moz-box-shadow:    0px 0px 8px rgba(0,0,0,0.3);
box-shadow:         0px 0px 8px rgba(0,0,0,0.3);
position: relative;
z-index: 90;
}

      .ribbon-wrapper-green {
    width: 227px;
    height: 237px;
    overflow: hidden;
    position: absolute;
    top: -3px;
    right: -3px;
  }

  .ribbon-green {
    font: bold 15px Sans-Serif;
    color: #333;
    text-align: center;
    text-shadow: rgba(255,255,255,0.5) 0px 1px 0px;
    -webkit-transform: rotate(45deg);
    -moz-transform:    rotate(45deg);
    -ms-transform:     rotate(45deg);
    -o-transform:      rotate(45deg);
    position: relative;
    padding: 7px 0;
    left: -5px;
    top: 50px;
    width: 320px;
    background-color: #BFDC7A;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#BFDC7A), to(#8EBF45));
    background-image: -webkit-linear-gradient(top, #BFDC7A, #8EBF45);
    background-image:    -moz-linear-gradient(top, #BFDC7A, #8EBF45);
    background-image:     -ms-linear-gradient(top, #BFDC7A, #8EBF45);
    background-image:      -o-linear-gradient(top, #BFDC7A, #8EBF45);
    color: #6a6340;
    -webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.3);
    -moz-box-shadow:    0px 0px 3px rgba(0,0,0,0.3);
    box-shadow:         0px 0px 3px rgba(0,0,0,0.3);
  }

  .ribbon-green:before, .ribbon-green:after {
    content: "";
    border-top:   3px solid #6e8900;
    border-left:  3px solid transparent;
    border-right: 3px solid transparent;
    position:absolute;
    bottom: -3px;
  }

  .ribbon-green:before {
    left: 0;
  }
  .ribbon-green:after {
    right: 0;
  }
      </style>




    </div>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">

          <?php echo form_open('', array('name'=>'newAccuracy', 'class'=>'form-horizontal'));?>

              <select style="display:none" class="form-control" name="watchId">
                    <?php echo '<option value="'.$selectedWatch['watchId'].'" selected>'.$selectedWatch['brand'].' - '.$selectedWatch['name'].'</option>'; ?>
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

                </div>


                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <center>
                            <input type="hidden" name="measureId" value="<?php echo $measureId;?>">
                            <button class="btn btn-primary btn-lg" name="startSync">Check now!</button>
                        </center>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <img style="display:none" id="watch" src="<?php echo img_url('flatwatch-blank.png');?>">
</div>
