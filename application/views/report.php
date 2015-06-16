<div class="container container-fluid first">
	<div class="row" style="margin-top: 150px;">
		
		<div class="col-md-offset-2 col-md-3">	<h2><?php echo $user . ' '; ?> <i class="fa fa-users"></i> </h2> </div>
		<div class="col-md-3">	<h2><?php echo $measure . " "; ?> <i class="fa fa-tachometer"></i> </h2> </div>
		<div class="col-md-3">	<h2><?php echo $watch . " "; ?> <i class="fa fa-clock-o"></i> </h2> </div>

	</div>
	<div class="row" style="margin-top: 15px;">
		<div class="col-md-offset-2 col-md-3">	<h2><?php echo sprintf("%.2f",$watch/$user) . " "; ?> <i class="fa fa-clock-o"></i> per <i class="fa fa-user"></i></h2> </div>
		<div class="col-md-3">	<h2><?php echo sprintf("%.2f",$measure/$user) . " "; ?> <i class="fa fa-tachometer"></i> per <i class="fa fa-user"></i></h2> </div>
		<div class="col-md-3">	<h2><?php echo sprintf("%.2f",$mobile->percent) . "% "; ?> <i class="fa fa-mobile"></i></h2> </div>
	</div>
	<div class="row" style="margin-top: 50px; margin-bottom: 150px" id="graphs">
	


	</div>

</div>
 <script type="text/javascript"
          src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>

<script type="text/javascript">
	$( document ).ready(function() {
		<?php 

		$pieBrowser = 'var pieBrowserData = google.visualization.arrayToDataTable([
          ["Os", "Amount"]';

        foreach ($browsers as $browser) {

        	$pieBrowser = $pieBrowser . ",['" . $browser->browser . "'," . $browser->nb . "]";
        	
        }

        echo $pieBrowser . '])';

		echo "\n $('#graphs').append('<div id=\"pie_browser\" style=\"height:300px; margin-top:20px\" class=\"col-md-4\"></div>');\n";

		echo "var options = { title: 'Event per platform'};\n";
		echo "var pieBrowser = new google.visualization.PieChart(document.getElementById('pie_browser'));\n";
        echo "pieBrowser.draw(pieBrowserData, options)\n";

		$pieOs = 'var pieOsData = google.visualization.arrayToDataTable([
          ["Os", "Amount"]';

        foreach ($platforms as $platform) {

        	$pieOs = $pieOs . ",['" . $platform->platform . "'," . $platform->nb . "]";
        	
        }

        echo $pieOs . '])';

		echo "\n $('#graphs').append('<div id=\"pie_platform\" style=\"height:300px; margin-top:20px\" class=\"col-md-4\"></div>');\n";

		echo "var options = { title: 'Event per platform'};\n";
		echo "var pieOs = new google.visualization.PieChart(document.getElementById('pie_platform'));\n";
        echo "pieOs.draw(pieOsData, options)\n";

		$pieDomain = 'var pieDomainData = google.visualization.arrayToDataTable([
          ["Domain", "Amount"]';

        foreach ($domain as $dom) {

        	$pieDomain = $pieDomain . ",['" . $dom->emailDomain . "'," . $dom->nb . "]";
        	
        }

        echo $pieDomain . '])';

		echo "\n $('#graphs').append('<div id=\"pie_domain\" style=\"height:300px; margin-top:20px\" class=\"col-md-4\"></div>');\n";

		echo "var options = { title: 'Email domain repartition'};\n";
		echo "var pieDomain = new google.visualization.PieChart(document.getElementById('pie_domain'));\n";
        echo "pieDomain.draw(pieDomainData, options)\n";


		$curentEvent = "";

		$allGraph = 'var allGraph = google.visualization.arrayToDataTable([
          ['.$eventsColumns.']';

		foreach ($allEvents as $event => $values) {
			$allGraph = $allGraph . ',[';
			$isFirst = true;
			foreach ($values as $value) {
				if($isFirst){
					$allGraph  = $allGraph . "'" . $value . "',";
					$isFirst = false;
				}else{
					$allGraph  = $allGraph .  $value . ",";
				}
				
			}
			$allGraph = rtrim($allGraph, ',') . ']';
		}

		echo $allGraph . '])';

		echo "\n $('#graphs').append('<div id=\"chart_all\" style=\"height:200px; margin-top:20px\" class=\"col-md-12\"></div>');\n";

		echo "var options = { title: 'chart_all_clicks', curveType: 'function', legend: { position: 'bottom' } };\n";
		echo "var chartAll = new google.visualization.LineChart(document.getElementById('chart_all'));\n";
        echo "chartAll.draw(allGraph, options)\n";

		foreach ($events as $event) {
			
			if($event->name !== $curentEvent){

				if($curentEvent !== ""){
					echo "['', '']]);";
				}

				$curentEvent = $event->name;
				echo "\n $('#graphs').append('<div id=\"".$curentEvent."\" style=\"height:200px; margin-top:20px\" class=\"col-md-6\"></div>');\n";
				echo "var " . $curentEvent . " = google.visualization.arrayToDataTable([['Date', '".$event->name."'],\n";
			}

			if(is_numeric($event->cnt)){
				echo "['" . $event->date . "', " . $event->cnt . "],\n ";
			}else{
				echo "['" . $event->date . "', 0],\n ";
			}

		} 

		echo "['', '']]);";

		foreach ($events as $event) {
			echo "var options = { title: '".$event->name."', curveType: 'function', legend: { position: 'bottom' } };\n";
			echo "var chart_" .$event->name. "= new google.visualization.LineChart(document.getElementById('".$event->name."'));\n";

        	echo "chart_" .$event->name .".draw(".$event->name.", options)\n";
		}

		?>
	});

</script>