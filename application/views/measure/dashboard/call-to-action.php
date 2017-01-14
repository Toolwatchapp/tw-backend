<?php 
if(is_null($statusId) || $statusId == '0')
{

  echo '<td><a href="#" class="submitNewMeasure" data-watch="'.$watchId.'">Measure me!</a>' .
  form_open('/measures/new-measure-for-watch/', array('name'=>'start-new-measure-'.$watchId)).'
      <input type="hidden" name="watchId" value="'.$watchId.'">
    </form></td>';

}else if($statusId == '1'){

  echo '<td><a href="#" class="submitGetAccuracy" data-watch="'.$id.'">Check the accuracy</a></td>';

}else if($statusId == '1.5'){

  echo '<td><a href="#" title="Warning" data-toggle="modal" data-target="#pageModal"
  data-modal-update="true" data-href="/modal/accuracyWarning/">Check the accuracy in
  '.$accuracy.' hour(s) <i class="warning fa fa-info-circle"></i></a></td>';

}else if($statusId == '2'){

  if($accuracy > 0){
    $accuracy = "+".$accuracy;
  }else{
    $accuracy = $accuracy;
  }

  if(($accuracy > 99.9) || (($accuracy < -99.9)))
  {

      echo '<td>'.$accuracy.' seconds a day <br><small><i>Looks like there was
      an error measuring your watch, why not try another measure?</i></small></td>';
  }
  else
  {
      echo '<td>'.$accuracy.' seconds a day</td>';
  }
}
?>
