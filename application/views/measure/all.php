<div class="container container-fluid content first">
    <div class="row">
        <div class="col-md-12"><center><h1>My measures</h1></center></div>
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
            ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="col-sm-3">Watch brand</th>
                            <th class="col-sm-3">Watch name</th>
                            <th class="col-sm-3">Accuracy</th>
                            <th class="col-sm-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            if($allMeasure != NULL)
                            {
                               foreach($allMeasure as $measure)
                               {
                                    echo '<tr>';   
                                    echo '<td>'.$measure['brand'].'</td>';
                                    echo '<td>'.$measure['name'].'</td>';
                                   
                                   
                                   
                                   if($measure['accuracy'] == 'newMeasure')
                                   {
                                       echo '<td><a href="/measures/new-measure/">Measure me!</a></td>';
                                       echo '<td><div class="btn-group">
                                              <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                Action <span class="caret"></span>
                                              </button>
                                              <ul class="dropdown-menu" role="menu">
                                                <li>
                                                    <a href="#" class="submitDeleteWatch" data-watch="'.$measure['watchId'].'">Delete watch</a>
                                                    <form method="post" action="/measures/" name="delete-watch-'.$measure['watchId'].'" class="no-display"><input type="hidden" name="deleteWatch" value="'.$measure['watchId'].'"></form>
                                                </li>
                                              </ul>
                                            </div></td>';
                                        echo '</tr>';
                                   }
                                   else if($measure['accuracy'] == 'getAccuracy')
                                   {
                                       echo '<td><a href="#" class="submitGetAccuracy" data-watch="'.$measure['watchId'].'">Check the accuracy</a></td>';
                                       echo '<td><div class="btn-group">
                                              <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                Action <span class="caret"></span>
                                              </button>
                                              <ul class="dropdown-menu" role="menu">
                                              <li><a href="#" class="submitGetAccuracy" data-watch="'.$measure['watchId'].'">Check the accuracy</a>
                                                <form method="post" action="/measures/get-accuracy/" name="get-accuracy-'.$measure['watchId'].'"><input type="hidden" name="watchId" value="'.$measure['watchId'].'">
                                                </form></li>
                                              <li class="divider"></li>
                                                <li>
                                                    <a href="#" class="submitDeleteMeasures" data-watch="'.$measure['watchId'].'">Delete all measures</a>
                                                    <form method="post" action="/measures/" name="delete-measures-'.$measure['watchId'].'" class="no-display">
                                                    <input type="hidden" name="deleteMeasures" value="'.$measure['watchId'].'">
                                                    </form>
                                                </li>
                                                <li>
                                                    <a href="#" class="submitDeleteWatch" data-watch="'.$measure['watchId'].'">Delete watch</a>
                                                    <form method="post" action="/measures/" name="delete-watch-'.$measure['watchId'].'" class="no-display">
                                                    <input type="hidden" name="deleteWatch" value="'.$measure['watchId'].'"></form>
                                                </li>
                                              </ul>
                                            </div></td>';
                                        echo '</tr>';
                                   }
                                  else
                                   {
                                      if(($measure['accuracy'] > 99.9) || (($measure['accuracy'] < -99.9)))
                                      {
                                          echo '<td>Looks like there was an error measuring your watch, why not try another measure?</td>'; 
                                      }
                                      else
                                      {
                                         echo '<td>'.$measure['accuracy'].' seconds a day</td>';  
                                      }
                                      
                                       echo '<td><div class="btn-group">
                                              <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                Action <span class="caret"></span>
                                              </button>
                                              <ul class="dropdown-menu" role="menu">
                                                <li><a href="#" class="submitGetAccuracy" data-watch="'.$measure['watchId'].'">Check the accuracy</a>
                                                <form method="post" action="/measures/get-accuracy/" name="get-accuracy-'.$measure['watchId'].'" class="no-display">
                                                <input type="hidden" name="watchId" value="'.$measure['watchId'].'">
                                                </form></li>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="#" class="submitDeleteMeasures" data-watch="'.$measure['watchId'].'">Delete all measures</a>
                                                    <form method="post" action="/measures/" name="delete-measures-'.$measure['watchId'].'" class="no-display">
                                                    <input type="hidden" name="deleteMeasures" value="'.$measure['watchId'].'">
                                                    </form>
                                                </li>
                                                <li>
                                                    <a href="#" class="submitDeleteWatch" data-watch="'.$measure['watchId'].'">Delete watch</a>
                                                    <form method="post" action="/measures/" name="delete-watch-'.$measure['watchId'].'" class="no-display">
                                                    <input type="hidden" name="deleteWatch" value="'.$measure['watchId'].'"></form>
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
    </div>
    <div class="row">
        <div class="col-md-12">
            <center>Step1: Add a watch to the list<br> 
Step2: First measure (synchronization with accuracy system)<br>
Step3: Second measure (get your accuracy)</center>
        </div>     
    </div>
     <div class="row">
          <div class="col-md-12">
              <center>
                  <a class="btn btn-success btn-lg col-md-2 col-md-offset-5" href="/measures/new-watch/">Add a watch</a><br><br>
                  <a class="btn btn-primary btn-lg col-md-2 col-md-offset-5" href="/measures/new-measure/">Start a new measure</a>
              </center>
         </div>
    </div>
</div>