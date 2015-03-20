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
                            <th>Watch brand</th>
                            <th>Watch name</th>
                            <th>Accuracy</th>
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
                                    echo '<td>'.$measure['accuracy'].' seconds a day</td>';
                                   echo '</tr>';
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
              <center>
                  <a class="btn btn-success btn-lg col-sm-2 col-sm-offset-5" href="/measures/new-watch/">Add a watch</a><br><br>
                  <a class="btn btn-primary btn-lg col-sm-2 col-sm-offset-5" href="/measures/new-measure/">Start a new measure</a>
              </center>
         </div>
    </div>
</div>