<div id="watch-<?php echo $watch["watchId"];?>"></div>

<script type="text/javascript">

var chart<?php echo $watch["watchId"];?>;

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

window['chart'+ '<?php echo $index;?>'] = function(){

  chart<?php echo $watch["watchId"];?> = c3.generate({
    bindto: '#watch-<?php echo $watch["watchId"];?>',
    grid: {
      y: {
        lines: [{ value: 0 }] // add the value you want
      }
    },
    data: {
      x: 'x',
      columns: [
        <?php
        //['Rolex', 5, 3, -3, 2, 1, 0],
        echo "['x',";

        for ($i=0; $i < sizeof($watch["measures"]); $i++) {
          echo "'" . date("Y-m-d", $watch["measures"][$i]['accuracyUserTime']) . "'";
          if($i < sizeof($watch["measures"]) - 1){
            echo ",";
          }
        }
        echo '],';
        ?>
        <?php $this->load->view("measure/dashboard/watch-accuracy-column", $watch);?>
      ],
      type:'spline',
      types: {
        coscLow: 'area',
        coscHigh: 'area',
        patekLow: 'area',
        patekHigh: 'area',
        metasLow: 'area',
        metasHigh: 'area',
        ppsealLow: 'area',
        ppsealHigh: 'area',
        genevaLow: 'area',
        genevaHigh: 'area',
        toolwatchLow: 'area',
        toolwatchHigh: 'area',
        brandLow: 'area',
        brandHigh: 'area'
      },
      colors: {
          coscLow: '#5fa3c1',
          coscHigh: '#5fa3c1',
          patekLow: '#a6c1d1',
          patekHigh: '#a6c1d1',
          metasLow: '#7d929e',
          metasHigh: '#7d929e',
          ppsealLow: '#284252',
          ppsealHigh: '#284252',
          genevaLow: '#232526',
          genevaHigh: '#232526',
          toolwatchLow: '#03396c',
          toolwatchHigh: '#03396c',
          brandLow: '#005b96',
          brandHigh: '#005b96'
      }
    },
    axis: {
      x: {
          type: 'timeseries',
          tick: {
              format: '%Y-%m-%d'
          }
      }
    },
    point: {
        r: 5
    },
    legend:{
      show: false
    },
    tooltip: {
        contents: tooltip_contents
    }
  });
}


</script>
