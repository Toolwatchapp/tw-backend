<div>
    <div class="rt01pagitem">

      <?php echo $watch["brand"] . ' ' . $watch["name"];

      if(end($watch["measures"])['statusId'] == "1.5"){
        echo "(check in ". end($watch["measures"])['accuracy']. "hours)";
      }else if(end($watch["measures"])['statusId'] == "2"){
        echo "(" . end($watch["measures"])['accuracy'] . ' spd)';
      }

      ?>
    </div>

      <div class="col-md-10">

        <?php if(sizeof($watch["measures"]) > 0 && end($watch["measures"])['statusId'] == "2"
        && (sizeof($watch["measures"]) != 1 || end($watch["measures"])['statusId'] != "1.5")){
          $this->load->view('measure/dashboard/graph', self);
        }else{?>
          <script type="text/javascript">
            window['chart'+ '<?php echo $index;?>'] = function(){};
          </script>
          <div class="height:2px; width:100%; padding-bottom:200px;">
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
          </div>
        <?php }

        $this->load->view('measure/dashboard/call-to-action', $watch);
        ?>

      </div>
      <div class="col-md-2">
        <?php if(sizeof($watch["measures"]) > 0 && end($watch["measures"])['statusId'] == "2"
        && (sizeof($watch["measures"]) != 1 || end($watch["measures"])['statusId'] != "1.5")){
          $this->load->view('measure/dashboard/filters', self);
        }?>
      </div>

</div>
