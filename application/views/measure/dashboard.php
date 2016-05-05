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

            $totalMeasure = 0;

            foreach ($allMeasure as $measure) {
              $totalMeasure = $totalMeasure + $measure['historySize'];
            }

            ?>

            <?php if(sizeof($allMeasure) == 0){ ?>
              <center>
                <h1> You don't have any watch yet</h1>
                <br>
                <br>
                <a class="btn btn-success btn-lg col-md-4 col-md-offset-4" href="/measures/new-watch/">Add a new watch</a>
                <br>
                <br>
                <h2> Once you do, they'll appear here...</h2>
              </center>
            <?php } else { ?>

                <div class="rt01">

                    <div>
                        <div class="rt01pagitem">OVERVIEW
                            <!-- <br><small class="tab-desc">embbed swipe gestures</small> -->
                        </div>

                        <?php if($totalMeasure === 0){?>
                          <center>
                            <h1> You don't have any completed measure yet</h1>
                            <br>
                            <br>
                            <h2> Once you do, they'll appear here...</h2>
                          </center>

                        <?php } else { ?>
                        <div id="overview-chart"></div>
                        <?php } ?>
                    </div>

                    <?php
                    for ($i=0; $i < sizeof($allMeasure); $i++) {
                      $this->load->view("measure/dashboard/watch-tab", ['watch'=> $allMeasure[$i], 'index'=> $i+1]);
                    }?>

                    <div>
                        <div class="rt01pagitem"><a style="width:100%" class="btn btn-success btn-lg" href="/measures/new-watch/">Add a watch</a>
                        </div>

                    </div>

                  </div>

                  <?php } ?>

                  </div>

        </div>
    </div>

<?php echo form_open('/measures/subscribe', array('style'=>"display:none"));?>
  <script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="pk_Oe4mxKPitEI0r8u4gE7YLL6aNVtIK"
    data-amount="5400"
    data-name="toolwatch.io"
    data-description="Widget"
    data-image="/assets/img/ico/apple-icon.png"
    data-locale="auto"
    data-currency="eur">
  </script>
</form>

    <?php if(!$this->agent->is_mobile()){
       $this->load->view("time");
    }?>

</div>

<script type="text/javascript">

jQuery(document).ready(function($) {
    $(".rt01").rubytabs({
        "fx"     : "cssOne",
        "speed"  : 800,
        "pag"    : { "direction" : "ver", "widthMinToHor": 747 }
    });

    var code = $(".rt01").data("rubytabs");

    chart0();

    code.ev.on("selectID", function(e, ID) {

      console.log(ID)
      window["chart"+ID]();

    });

});

function removeInterval(chart, name){
  chart.unload({
    ids: name+"Low"
  });
  chart.unload({
    ids: name+"High"
  });
}

function addIntervalToGraph(chart, name, low, high, count){

  var lowArray = [name+"Low"];
  var highArray = [name+"High"];

  for (var i = 0; i < count; i++) {
    lowArray.push(low);
    highArray.push(high);
  }

  chart.load({
    columns: [
      lowArray,
      highArray
    ]
  });
}

function chart0(){
  var chart = c3.generate({
    bindto: '#overview-chart',
    grid: {
      y: {
        lines: [{ value: 0 }] // add the value you want
      }
    },
    data: {
      columns: [
        <?php foreach ($allMeasure as $watch) {
          $this->load->view("measure/dashboard/watch-accuracy-column", $watch);
        }?>
      ],
      type: 'spline'
    }
  });
}
</script>

<script type="text/javascript">



var chart = c3.generate({
  bindto: '#chart',
  grid: {
    y: {
      lines: [{ value: 0 }] // add the value you want
    }
  },
  data: {
    columns: [
      ['Rolex', 5, 3, -3, 2, 1, 0],
      ['coscLow', -4, -4, -4, -4, -4, -4],
      ['coscHigh', 6, 6, 6, 6, 6, 6],
      ['patekLow', -3, -3, -3, -3, -3, -3],
      ['patekHigh', 2, 2, 2, 2, 2, 2]
    ],
    types: {
      Rolex: 'spline',
      Patek: 'spline',
      coscLow: 'area',
      coscHigh: 'area',
      patekLow: 'area',
      patekHigh: 'area'
    },
    colors: {
        coscLow: '#990033',
        coscHigh: '#990033',
        patekLow: '#463527',
        patekHigh: '#463527'
    }
  },
  point: {
      r: 0
  },
  legend:{
    show: false
  },
  tooltip: {
      contents: tooltip_contents
  }
});
</script>

<style media="screen">
.c3-area {
  opacity:1;
}
.rt01, .rt01slide{
  min-height: 400px;
}
</style>

<style media="screen">
span.new-measure{
  margin-top: 5px;
  text-align: center;
  width: 100%;
  background-color: #4d77a7;
  padding: 5px;
  font-size: 10px;
  color:white;
  border-radius: 5px;
}
.c3 .c3-axis-x path, .c3 .c3-axis-x line {
    stroke: #7F8C9A;
}
.c3 .c3-axis-y path, .c3 .c3-axis-y line {
    stroke: #7F8C9A;
}
.checkbox {
  padding-left: 20px;
}
.checkbox label {
  display: inline-block;
  vertical-align: middle;
  position: relative;
  padding-left: 5px;
}
.checkbox label::before {
  content: "";
  display: inline-block;
  position: absolute;
  width: 17px;
  height: 17px;
  left: 0;
  margin-left: -20px;
  border: 1px solid #cccccc;
  border-radius: 3px;
  background-color: #fff;
  -webkit-transition: border 0.15s ease-in-out, color 0.15s ease-in-out;
  -o-transition: border 0.15s ease-in-out, color 0.15s ease-in-out;
  transition: border 0.15s ease-in-out, color 0.15s ease-in-out;
}
.checkbox label::after {
  display: inline-block;
  position: absolute;
  width: 16px;
  height: 16px;
  left: 0;
  top: 0;
  margin-left: -20px;
  padding-left: 3px;
  padding-top: 1px;
  font-size: 11px;
  color: #555555;
}
.checkbox input[type="checkbox"],
.checkbox input[type="radio"] {
  opacity: 0;
  z-index: 1;
}
.checkbox input[type="checkbox"]:focus + label::before,
.checkbox input[type="radio"]:focus + label::before {
  outline: thin dotted;
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}
.checkbox input[type="checkbox"]:checked + label::after,
.checkbox input[type="radio"]:checked + label::after {
  font-family: "FontAwesome";
  content: "\f00c";
}
.checkbox input[type="checkbox"]:indeterminate + label::after,
.checkbox input[type="radio"]:indeterminate + label::after {
  display: block;
  content: "";
  width: 10px;
  height: 3px;
  background-color: #555555;
  border-radius: 2px;
  margin-left: -16.5px;
  margin-top: 7px;
}
.checkbox input[type="checkbox"]:disabled + label,
.checkbox input[type="radio"]:disabled + label {
  opacity: 0.65;
}
.checkbox input[type="checkbox"]:disabled + label::before,
.checkbox input[type="radio"]:disabled + label::before {
  background-color: #eeeeee;
  cursor: not-allowed;
}
.checkbox.checkbox-circle label::before {
  border-radius: 50%;
}
.checkbox.checkbox-inline {
  margin-top: 0;
}

.checkbox-primary input[type="checkbox"]:checked + label::before,
.checkbox-primary input[type="radio"]:checked + label::before {
  background-color: #337ab7;
  border-color: #337ab7;
}
.checkbox-primary input[type="checkbox"]:checked + label::after,
.checkbox-primary input[type="radio"]:checked + label::after {
  color: #fff;
}

.checkbox-danger input[type="checkbox"]:checked + label::before,
.checkbox-danger input[type="radio"]:checked + label::before {
  background-color: #d9534f;
  border-color: #d9534f;
}
.checkbox-danger input[type="checkbox"]:checked + label::after,
.checkbox-danger input[type="radio"]:checked + label::after {
  color: #fff;
}

.checkbox-info input[type="checkbox"]:checked + label::before,
.checkbox-info input[type="radio"]:checked + label::before {
  background-color: #5bc0de;
  border-color: #5bc0de;
}
.checkbox-info input[type="checkbox"]:checked + label::after,
.checkbox-info input[type="radio"]:checked + label::after {
  color: #fff;
}

.checkbox-warning input[type="checkbox"]:checked + label::before,
.checkbox-warning input[type="radio"]:checked + label::before {
  background-color: #f0ad4e;
  border-color: #f0ad4e;
}
.checkbox-warning input[type="checkbox"]:checked + label::after,
.checkbox-warning input[type="radio"]:checked + label::after {
  color: #fff;
}

.checkbox-success input[type="checkbox"]:checked + label::before,
.checkbox-success input[type="radio"]:checked + label::before {
  background-color: #5cb85c;
  border-color: #5cb85c;
}
.checkbox-success input[type="checkbox"]:checked + label::after,
.checkbox-success input[type="radio"]:checked + label::after {
  color: #fff;
}

.checkbox-primary input[type="checkbox"]:indeterminate + label::before,
.checkbox-primary input[type="radio"]:indeterminate + label::before {
  background-color: #337ab7;
  border-color: #337ab7;
}

.checkbox-primary input[type="checkbox"]:indeterminate + label::after,
.checkbox-primary input[type="radio"]:indeterminate + label::after {
  background-color: #fff;
}

.checkbox-danger input[type="checkbox"]:indeterminate + label::before,
.checkbox-danger input[type="radio"]:indeterminate + label::before {
  background-color: #d9534f;
  border-color: #d9534f;
}

.checkbox-danger input[type="checkbox"]:indeterminate + label::after,
.checkbox-danger input[type="radio"]:indeterminate + label::after {
  background-color: #fff;
}

.checkbox-info input[type="checkbox"]:indeterminate + label::before,
.checkbox-info input[type="radio"]:indeterminate + label::before {
  background-color: #5bc0de;
  border-color: #5bc0de;
}

.checkbox-info input[type="checkbox"]:indeterminate + label::after,
.checkbox-info input[type="radio"]:indeterminate + label::after {
  background-color: #fff;
}

.checkbox-warning input[type="checkbox"]:indeterminate + label::before,
.checkbox-warning input[type="radio"]:indeterminate + label::before {
  background-color: #f0ad4e;
  border-color: #f0ad4e;
}

.checkbox-warning input[type="checkbox"]:indeterminate + label::after,
.checkbox-warning input[type="radio"]:indeterminate + label::after {
  background-color: #fff;
}

.checkbox-success input[type="checkbox"]:indeterminate + label::before,
.checkbox-success input[type="radio"]:indeterminate + label::before {
  background-color: #5cb85c;
  border-color: #5cb85c;
}

.checkbox-success input[type="checkbox"]:indeterminate + label::after,
.checkbox-success input[type="radio"]:indeterminate + label::after {
  background-color: #fff;
}

.radio {
  padding-left: 20px;
}
.radio label {
  display: inline-block;
  vertical-align: middle;
  position: relative;
  padding-left: 5px;
}
.radio label::before {
  content: "";
  display: inline-block;
  position: absolute;
  width: 17px;
  height: 17px;
  left: 0;
  margin-left: -20px;
  border: 1px solid #cccccc;
  border-radius: 50%;
  background-color: #fff;
  -webkit-transition: border 0.15s ease-in-out;
  -o-transition: border 0.15s ease-in-out;
  transition: border 0.15s ease-in-out;
}
.radio label::after {
  display: inline-block;
  position: absolute;
  content: " ";
  width: 11px;
  height: 11px;
  left: 3px;
  top: 3px;
  margin-left: -20px;
  border-radius: 50%;
  background-color: #555555;
  -webkit-transform: scale(0, 0);
  -ms-transform: scale(0, 0);
  -o-transform: scale(0, 0);
  transform: scale(0, 0);
  -webkit-transition: -webkit-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
  -moz-transition: -moz-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
  -o-transition: -o-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
  transition: transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
}
.radio input[type="radio"] {
  opacity: 0;
  z-index: 1;
}
.radio input[type="radio"]:focus + label::before {
  outline: thin dotted;
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}
.radio input[type="radio"]:checked + label::after {
  -webkit-transform: scale(1, 1);
  -ms-transform: scale(1, 1);
  -o-transform: scale(1, 1);
  transform: scale(1, 1);
}
.radio input[type="radio"]:disabled + label {
  opacity: 0.65;
}
.radio input[type="radio"]:disabled + label::before {
  cursor: not-allowed;
}
.radio.radio-inline {
  margin-top: 0;
}

.radio-primary input[type="radio"] + label::after {
  background-color: #337ab7;
}
.radio-primary input[type="radio"]:checked + label::before {
  border-color: #337ab7;
}
.radio-primary input[type="radio"]:checked + label::after {
  background-color: #337ab7;
}

.radio-danger input[type="radio"] + label::after {
  background-color: #d9534f;
}
.radio-danger input[type="radio"]:checked + label::before {
  border-color: #d9534f;
}
.radio-danger input[type="radio"]:checked + label::after {
  background-color: #d9534f;
}

.radio-info input[type="radio"] + label::after {
  background-color: #5bc0de;
}
.radio-info input[type="radio"]:checked + label::before {
  border-color: #5bc0de;
}
.radio-info input[type="radio"]:checked + label::after {
  background-color: #5bc0de;
}

.radio-warning input[type="radio"] + label::after {
  background-color: #f0ad4e;
}
.radio-warning input[type="radio"]:checked + label::before {
  border-color: #f0ad4e;
}
.radio-warning input[type="radio"]:checked + label::after {
  background-color: #f0ad4e;
}

.radio-success input[type="radio"] + label::after {
  background-color: #5cb85c;
}
.radio-success input[type="radio"]:checked + label::before {
  border-color: #5cb85c;
}
.radio-success input[type="radio"]:checked + label::after {
  background-color: #5cb85c;
}

input[type="checkbox"].styled:checked + label:after,
input[type="radio"].styled:checked + label:after {
  font-family: 'FontAwesome';
  content: "\f00c";
}
input[type="checkbox"] .styled:checked + label::before,
input[type="radio"] .styled:checked + label::before {
  color: #fff;
}
input[type="checkbox"] .styled:checked + label::after,
input[type="radio"] .styled:checked + label::after {
  color: #fff;
}
</style>
