<?php
//['Rolex', 5, 3, -3, 2, 1, 0],
echo "['" . $brand . " " . $name .  "',";

for ($i=0; $i < sizeof($measures); $i++) {
  if($measures[$i]['statusId'] != "1.5"){
    echo $measures[$i]['accuracy'];
    if($i < sizeof($measures) - 1){
      echo ",";
    }
  }
}
echo '],';
?>
