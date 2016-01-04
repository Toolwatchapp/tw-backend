<div class="container container-fluid content first">
    <div class="row">
        <div class="col-md-12"><center><h1>My measures</h1></center></div>
    </div>
    <div class="row">
        <div class="col-md-12"><div class="line black"></div></div>
    </div>
    <div class="row step-measure">
        <div class="col-sm-3"><center><img src="/assets/img/step1.jpg" alt="Step 1" width="100"><br> <b>Step 1</b><br>Add a watch to the list</center></div>
        <div class="col-sm-1"><span class="fa fa-arrow-right"></span></div>
        <div class="col-sm-4"><center><img src="/assets/img/step2.jpg" alt="Step 2" width="100"><br><b>Step 2</b><br>Synchronize with our accuracy system (first measure)</center></div>
        <div class="col-sm-1"><span class="fa fa-arrow-right"></span></div>
        <div class="col-sm-3"><center><img src="/assets/img/step3.jpg" alt="Step 2" width="100"><br><b>Step 3</b><br>Get your accuracy (second measure)</center></div>
    </div>
    <div class="row show-steps">
        <div class="col-md-12"><a class="btn btn-default" href="#" data-trigger="slideDown" data-target=".step-measure">Show steps</a></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php

            if(isset($error))
            {
                echo '<div class="alert alert-danger alert-dismissible" role="alert" >
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span>'.$error.'</span></div>';

            }
            else if(isset($success))
            {
                echo '<div class="alert alert-success alert-dismissible" role="alert" >
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span>'.$success.'</span></div>';
            }
            var_dump($allMeasure);
            ?>

            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th class="col-sm-3">Watch brand</th>
                        <th class="col-sm-3">Watch name</th>
                        <th class="col-sm-5">Accuracy</th>
                        <th class="col-sm-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if($allMeasure != NULL)
                        {
                           foreach($allMeasure as $measure)
                           {
                                echo '<tr>';
                                echo '<td>'.$measure->brand.'</td>';
                                echo '<td>'.$measure->name.'</td>';

                               if($measure->statusId == '0')
                               {
                                   echo '<td><a href="/measures/new-measure/">Measure me!</a></td>';
                                   echo '<td><div class="btn-group">
                                          <button type="button" class="btn btn-default btn-sm dropdown-toggle dropdown-menu-right" data-toggle="dropdown" aria-expanded="false">
                                            Action <span class="caret"></span>
                                          </button>
                                          <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a href="#" class="submitDeleteWatch" data-watch="'.$measure->watchId.'">Delete watch</a>
                                                <form method="post" action="/measures/" name="delete-watch-'.$measure->watchId.'" class="no-display">
                                                <input type="hidden" name="deleteWatch" value="'.$measure->watchId.'"></form>
                                            </li>
                                          </ul>
                                        </div></td>';
                                    echo '</tr>';
                               }
                               else if($measure->statusId == '1')
                               {
                                   echo '<td><a href="#" class="submitGetAccuracy" data-watch="'.$measure->id.'">Check the accuracy</a></td>';
                                   echo '<td><div class="btn-group">
                                          <button type="button" class="btn btn-default btn-sm dropdown-toggle dropdown-menu-right" data-toggle="dropdown" aria-expanded="false">
                                            Action <span class="caret"></span>
                                          </button>
                                          <ul class="dropdown-menu" role="menu">
                                          <li><a href="#" class="submitGetAccuracy" data-watch="'.$measure->id.'">Check the accuracy</a>
                                            <form method="post" action="/measures/get-accuracy/" name="get-accuracy-'.$measure->id.'"><input type="hidden" name="measureId" value="'.$measure->id.'"><input type="hidden" name="watchId" value="'.$measure->watchId.'">
                                            </form></li>
                                          <li class="divider"></li>
                                            <li><a href="/measures/new-measure/">Start a new measure</a>
                                              <form method="post" action="/measures/get-accuracy/" name="get-accuracy-'.$measure->id.'"><input type="hidden" name="measureId" value="'.$measure->id.'"><input type="hidden" name="watchId" value="'.$measure->watchId.'">
                                            </form></li>
                                            <li>
                                                <a href="#" class="submitDeleteMeasures" data-watch="'.$measure->id.'">Delete this measure</a>
                                                <form method="post" action="/measures/delete_measure" name="delete-measures-'.$measure->id.'" class="no-display">
                                                <input type="hidden" name="deleteMeasures" value="'.$measure->id.'">
                                                </form>
                                            </li>
                                            <li>
                                                <a href="#" class="submitDeleteWatch" data-watch="'.$measure->watchId.'">Delete watch</a>
                                                <form method="post" action="/measures/" name="delete-watch-'.$measure->watchId.'" class="no-display">
                                                <input type="hidden" name="deleteWatch" value="'.$measure->watchId.'"></form>
                                            </li>
                                          </ul>
                                        </div></td>';
                                    echo '</tr>';
                               }
                               else if($measure->statusId == '1.5')
                               {
                                  echo '<td><a href="#" title="Warning" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/modal/accuracyWarning/">Check the accuracy in '.$measure->accuracy.' hour(s) <i class="warning fa fa-info-circle"></i></a></td>';
                                   echo '<td><div class="btn-group">
                                          <button type="button" class="btn btn-default btn-sm dropdown-toggle dropdown-menu-right" data-toggle="dropdown" aria-expanded="false">
                                            Action <span class="caret"></span>
                                          </button>
                                          <ul class="dropdown-menu" role="menu">
                                            <li><a href="/measures/new-measure/">Start a new measure</a>
                                              <form method="post" action="/measures/get-accuracy/" name="get-accuracy-'.$measure->id.'"><input type="hidden" name="measureId" value="'.$measure->id.'"><input type="hidden" name="watchId" value="'.$measure->watchId.'">
                                            </form></li>
                                            <li>
                                                <a href="#" class="submitDeleteMeasures" data-watch="'.$measure->id.'">Delete this measure</a>
                                                <form method="post" action="/measures/delete_measure" name="delete-measures-'.$measure->id.'" class="no-display">
                                                <input type="hidden" name="deleteMeasures" value="'.$measure->id.'">
                                                </form>
                                            </li>
                                            <li>
                                                <a href="#" class="submitDeleteWatch" data-watch="'.$measure->watchId.'">Delete watch</a>
                                                <form method="post" action="/measures/" name="delete-watch-'.$measure->watchId.'" class="no-display">
                                                <input type="hidden" name="deleteWatch" value="'.$measure->watchId.'"></form>
                                            </li>
                                          </ul>
                                        </div></td>';
                                    echo '</tr>';
                              }
                              else if($measure->statusId == '2')
                              {
                                  if($measure->accuracy > 0)
                                      $accuracy = "+".$measure->accuracy;
                                  else
                                      $accuracy = $measure->accuracy;

                                  if(($measure->accuracy > 99.9) || (($measure->accuracy < -99.9)))
                                  {

                                      echo '<td>'.$accuracy.' seconds a day <br><small><i>Looks like there was an error measuring your watch, why not try another measure?</i></small></td>';
                                  }
                                  else
                                  {
                                      echo '<td>'.$accuracy.' seconds a day</td>';
                                  }

                                   echo '<td><div class="btn-group">
                                          <button type="button" class="btn btn-default btn-sm dropdown-toggle dropdown-menu-right" data-toggle="dropdown" aria-expanded="false">
                                            Action <span class="caret"></span>
                                          </button>
                                          <ul class="dropdown-menu" role="menu">
                                           <li><a href="/measures/new-measure/">Start a new measure</a>
                                            <form method="post" action="/measures/get-accuracy/" name="get-accuracy-'.$measure->id.'"><input type="hidden" name="measureId" value="'.$measure->id.'"><input type="hidden" name="watchId" value="'.$measure->watchId.'">
                                            </form></li>
                                          <li class="divider"></li>
                                            <li>
                                                <a href="#" class="submitDeleteMeasures" data-watch="'.$measure->id.'">Delete all measures</a>
                                                <form method="post" action="/measures/delete_measure" name="delete-measures-'.$measure->id.'" class="no-display">
                                                <input type="hidden" name="deleteMeasures" value="'.$measure->id.'">
                                                </form>
                                            </li>
                                            <li>
                                                <a href="#" class="submitDeleteWatch" data-watch="'.$measure->watchId.'">Delete watch</a>
                                                  <form method="post" action="/measures/" name="delete-watch-'.$measure->watchId.'" class="no-display">
                                                <input type="hidden" name="deleteWatch" value="'.$measure->watchId.'"></form>
                                            </li>
                                          </ul>
                                        </div></td>';
                                    echo '</tr>';
                               }
                           }
                        }
                        else
                        {
                            echo '<tr><td colspan="6"><center>You don\'t have any measure yet!</center></td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <center>
            <a class="btn btn-success btn-lg col-md-2 col-md-offset-5" href="/measures/new-watch/">Add a watch</a><br><br>
            <?php if($watches != null) { ?>

                <a class="btn btn-primary btn-lg col-md-2 col-md-offset-5" href="/measures/new-measure/">Start a new measure</a>

             <?php } ?>


            </center>
        </div>
    </div>
</div>
