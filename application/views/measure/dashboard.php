<div class="container container-fluid content first">

    <?php $this->load->view("measure/dashboard/steps"); ?>

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

                                $this->load->view("measure/dashboard/call-to-action.php", (array) $measure);

                                $this->load->view("measure/dashboard/start-action-button");

                                if($measure->statusId === "1"){
                                  $this->load->view("measure/dashboard/check-accuracy", $measure);
                                }

                                if($measure->statusId > 0){
                                  $this->load->view("measure/dashboard/delete-measure", $measure);
                                }

                                $this->load->view("measure/dashboard/start-new-measure", $measure);


                                $this->load->view("measure/dashboard/edit-watch", $measure);
                                $this->load->view("measure/dashboard/delete-watch", $measure);
                                $this->load->view("measure/dashboard/end-action-button");

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
            <?php if($allMeasure != null) { ?>

                <a class="btn btn-primary btn-lg col-md-2 col-md-offset-5" href="/measures/new-measure/">Start a new measure</a>

             <?php } ?>


            </center>
        </div>
    </div>

    <?php if(!$this->agent->is_mobile()){
       $this->load->view("time");
    }?>

</div>
