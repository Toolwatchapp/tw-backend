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






            <?php var_dump($allMeasure);?>

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
                                echo '<td>'.$measure->name.'<div id="chart"></div></td>';

                                $this->load->view("measure/dashboard/call-to-action.php", $measure);

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

<script type="text/javascript">

function tooltip_contents(d, defaultTitleFormat, defaultValueFormat, color) {
    var $$ = this, config = $$.config, CLASS = $$.CLASS,
        titleFormat = config.tooltip_format_title || defaultTitleFormat,
        nameFormat = config.tooltip_format_name || function (name) { return name; },
        valueFormat = config.tooltip_format_value || defaultValueFormat,
        text, i, title, value, name, bgcolor;

    // You can access all of data like this:
    console.log($$.data.targets);

    for (i = 0; i < d.length; i++) {
        if (! (d[i] && (d[i].value || d[i].value === 0))) { continue; }


        // ADD
        if (d[i].name.indexOf("Low") > -1
        || d[i].name.indexOf("High") > -1)
        { continue; }

        if (! text) {
            text = "<table class='" + CLASS.tooltip + "'>" + (title || title === 0 ? "<tr><th colspan='2'>" + title + "</th></tr>" : "");
        }

        name = nameFormat(d[i].name);
        value = valueFormat(d[i].value, d[i].ratio, d[i].id, d[i].index);
        bgcolor = $$.levelColor ? $$.levelColor(d[i].value) : color(d[i].id);

        text += "<tr class='" + CLASS.tooltipName + "-" + d[i].id + "'>";
        text += "<td class='name'><span style='background-color:" + bgcolor + "'></span>" + name + "</td>";
        text += "<td class='value'>" + value + " spd</td>";
        text += "</tr>";
    }
    return text + "</table>";
}

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
</style>
