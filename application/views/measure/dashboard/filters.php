<br>
<form class="filters" action="#">

  <div class="checkbox checkbox-primary">
      <input type="checkbox" id="toolwatch<?php echo $watch["watchId"];?>" />
      <label for="toolwatch<?php echo $watch["watchId"];?>">
      <span class="ui"></span>Toolwatch Average</label>
  </div>

  <div class="checkbox checkbox-primary">
      <input type="checkbox" id="brand<?php echo $watch["watchId"];?>" />
      <label for="brand<?php echo $watch["watchId"];?>">
      <span class="ui"></span><?php echo $watch["brand"];?> Average</label>
  </div>

  <div class="checkbox checkbox-primary">
      <input type="checkbox" id="cosc<?php echo $watch["watchId"];?>" />
      <label for="cosc<?php echo $watch["watchId"];?>">
      <span class="ui"></span>COSC</label>
  </div>

  <div class="checkbox checkbox-primary">
      <input type="checkbox" id="geneva<?php echo $watch["watchId"];?>" />
      <label for="geneva<?php echo $watch["watchId"];?>">
      <span class="ui"></span>Geneva Seal</label>
  </div>

  <div class="checkbox checkbox-primary">
      <input type="checkbox" id="metas<?php echo $watch["watchId"];?>" />
      <label for="metas<?php echo $watch["watchId"];?>">
      <span class="ui"></span>METAS</label>
  </div>

  <div class="checkbox checkbox-primary">
      <input type="checkbox" id="ppseal<?php echo $watch["watchId"];?>" />
      <label for="ppseal<?php echo $watch["watchId"];?>">
      <span class="ui"></span>Patek Philippe Seal</label>
  </div>

</form>


<?php if($this->session->userdata("plan") == "premium"){?>
  <script type="text/javascript">

  $( document ).ready(function() {

    $('#toolwatch<?php echo $watch["watchId"];?>').change(function(){
        this.checked ?
        addIntervalToGraph(chart<?php echo $watch["watchId"];?>, "toolwatch", <?php echo $watch['averages']['toolwatch_low'];?>,
        <?php echo $watch['averages']['toolwatch_high'];?>,
        <?php echo sizeof($watch["measures"]);?>)
        : removeInterval(chart<?php echo $watch["watchId"];?>, "toolwatch");
    });

    $('#brand<?php echo $watch["watchId"];?>').change(function(){
            console.log("awe");
        this.checked ?
        addIntervalToGraph(chart<?php echo $watch["watchId"];?>, "brand", <?php echo $watch['averages']['brand_low'];?>,
        <?php echo $watch['averages']['brand_high'];?>,
        <?php echo sizeof($watch["measures"]);?>)
        : removeInterval(chart<?php echo $watch["watchId"];?>, "brand");
    });

    $('#cosc<?php echo $watch["watchId"];?>').change(function(){
        this.checked ?
        addIntervalToGraph(chart<?php echo $watch["watchId"];?>, "cosc", -4, 6, <?php echo sizeof($watch["measures"]);?>)
        : removeInterval(chart<?php echo $watch["watchId"];?>, "cosc");
    });
    $('#geneva<?php echo $watch["watchId"];?>').change(function(){
        this.checked ?
        addIntervalToGraph(chart<?php echo $watch["watchId"];?>, "geneva", -8.57, 8.57, <?php echo sizeof($watch["measures"]);?>)
        : removeInterval(chart<?php echo $watch["watchId"];?>, "geneva");
    });
    $('#metas<?php echo $watch["watchId"];?>').change(function(){
        this.checked ?
        addIntervalToGraph(chart<?php echo $watch["watchId"];?>, "metas", 0, 5, <?php echo sizeof($watch["measures"]);?>)
        : removeInterval(chart<?php echo $watch["watchId"];?>, "metas");
    });
    $('#ppseal<?php echo $watch["watchId"];?>').change(function(){
        this.checked ?
        addIntervalToGraph(chart<?php echo $watch["watchId"];?>, "ppseal", -3, 2, <?php echo sizeof($watch["measures"]);?>)
        : removeInterval(chart<?php echo $watch["watchId"];?>, "ppseal");
    });
  });
  </script>
<?php } else { ?>

  <script type="text/javascript">

  $( document ).ready(function() {

    $('#toolwatch<?php echo $watch["watchId"];?>').change(function(){
        if(this.checked){
          $(".stripe-button-el")[0].click();
          this.checked = false;
        }
    });

    $('#brand<?php echo $watch["watchId"];?>').change(function(){
      if(this.checked){
        $(".stripe-button-el")[0].click();
        this.checked = false;
      }
    });

    $('#cosc<?php echo $watch["watchId"];?>').change(function(){
      if(this.checked){
        $(".stripe-button-el")[0].click();
        this.checked = false;
      }
    });
    $('#geneva<?php echo $watch["watchId"];?>').change(function(){
      if(this.checked){
        $(".stripe-button-el")[0].click();
        this.checked = false;
      }
    });
    $('#metas<?php echo $watch["watchId"];?>').change(function(){
      if(this.checked){
        $(".stripe-button-el")[0].click();
        this.checked = false;
      }
    });
    $('#ppseal<?php echo $watch["watchId"];?>').change(function(){
      if(this.checked){
        $(".stripe-button-el")[0].click();
        this.checked = false;
      }
    });
  });
  </script>
<?php } ?>
