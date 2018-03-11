<?php

/**
 * This helper constructs arrays for mailchimp "token" system
 * @see https://mandrillapp.com/api/docs/messages.php.html#method=send-template
 * 
 * List of templates
 * 
 * - reset_password_confirmation 
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392837
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
 * - reset_password
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392833
 * - - <span mc:edit="reset">{{token}}</span>
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
 * - signup
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392829
 * - - <span mc:edit="name">{{name}}</span>
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
 * - watch_result
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392825
 * - - <span mc:edit="name">{{name}}</span>
 * - - <span mc:edit="watch">{{brand}} {{model}} </span>
 * - - <span mc:edit="accuracy">{{accuracy}}</span>
 * - - <div mc:edit="dashboard">
 *        <ul style="list-style: none;">
 *           <li><span><span style="color:rgb(0, 0, 0); font-family:monospace; font-size:medium; line-height:normal; white-space:pre-wrap">⌚ {{Brand}} {{Name}} : {{Accuracy}}</span></span></li>
 *         </ul>
 *       </div>
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
 * - one_month_accuracy
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392821
 * - - <span mc:edit="name">{{name}}</span>
 * - - <div mc:edit="watches">
 *      <ul>
 *        <li><span>{{brand}} {{name}}</span></li>
 *      </ul>
 *      </div>
 * - - <div mc:edit="dashboard">
 *         <ul style="list-style: none;">
 *           <li><span><span style="color:rgb(0, 0, 0); font-family:monospace; font-size:medium; line-height:normal; white-space:pre-wrap">⌚ {{Brand}} {{Name}} : {{Accuracy}}</span></span></li>
 *        </ul>
 *       </div>
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
 * - one_week_accuracy
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392817
 * - - <span mc:edit="name">{{name}}</span>
 * - - <div mc:edit="watches">
 *      <ul>
 *        <li><span>{{brand}} {{name}}</span></li>
 *      </ul>
 *      </div>
 * - - <div mc:edit="dashboard">
 *         <ul style="list-style: none;">
 *           <li><span><span style="color:rgb(0, 0, 0); font-family:monospace; font-size:medium; line-height:normal; white-space:pre-wrap">⌚ {{Brand}} {{Name}} : {{Accuracy}}</span></span></li>
 *        </ul>
 *       </div>
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
 * - check_accuracy
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392813
 * - - <span mc:edit="name">{{name}}</span>
 * - - <div mc:edit="watches">
 *      <ul>
 *        <li><span>{{brand}} {{name}}</span></li>
 *      </ul>
 *      </div>
 * - - <div mc:edit="dashboard">
 *         <ul style="list-style: none;">
 *           <li><span><span style="color:rgb(0, 0, 0); font-family:monospace; font-size:medium; line-height:normal; white-space:pre-wrap">⌚ {{Brand}} {{Name}} : {{Accuracy}}</span></span></li>
 *        </ul>
 *       </div>
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
 * - add_first_watch
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392769
 * - - <span mc:edit="name">{{name}}</span>
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
 * - make_first_measure
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392793
 * - - <span mc:edit="name">{{name}}</span>
 * - - <div mc:edit="watches">
 *      <ul>
 *        <li><span>{{brand}} {{name}}</span></li>
 *      </ul>
 *      </div>
 * - - <div mc:edit="dashboard">
 *         <ul style="list-style: none;">
 *           <li><span><span style="color:rgb(0, 0, 0); font-family:monospace; font-size:medium; line-height:normal; white-space:pre-wrap">⌚ {{Brand}} {{Name}} : {{Accuracy}}</span></span></li>
 *        </ul>
 *       </div>
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
 * - add_second_watch
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392797
 * - - <span mc:edit="name">{{name}}</span>
 * - - <span mc:edit="first-watch">{{firstwatch}}</span>
 * - - <div mc:edit="dashboard">
 *         <ul style="list-style: none;">
 *           <li><span><span style="color:rgb(0, 0, 0); font-family:monospace; font-size:medium; line-height:normal; white-space:pre-wrap">⌚ {{Brand}} {{Name}} : {{Accuracy}}</span></span></li>
 *        </ul>
 *       </div>
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
 * - comeback
 * @see https://us9.admin.mailchimp.com/templates/edit?id=392805
 * - - <span mc:edit="name">{{name}}</span>
 * - - <span mc:edit="unsub"><a href="#">here</a></span>
 * 
*/

function constructReturnArray($templateName, $templateValues){

  $values = array();

  foreach ($templateValues as $key => $value) {
    
    array_push($values, (object) ['name'=>$key, 'content'=> ($value) == null ? " " : $value]);
  }

  return 
    array(
      "templateName" => $templateName,
      "templateValue" => $values
  );
}

function constructDashboardWatches($watches){

  $dasboardWatches = "";

  if($watches && is_array($watches)){
    $dasboardWatches .= '<ul style="list-style-type: none;">';

    foreach ($watches as $watch) {
      $watch = (object) $watch;
      $dasboardWatches .= '<li><span style="color:#000000;font-family:monospace;font-size:medium;line-height:normal;white-space:pre-wrap"><img goomoji="231a" data-goomoji="231a" style="margin:0 0.2ex;vertical-align:middle;max-height:24px" alt="⌚" src="https://mail.google.com/mail/e/231a" class="CToWUd">';

      if($watch->statusId === 1.5){
        $dasboardWatches .= ' ' . $watch->brand.' '.$watch->name.': Check accuracy in '.$watch->accuracy.' hours.';
      }else if($watch->statusId == 1){
        $dasboardWatches .= ' ' . $watch->brand .' '.$watch->name .': <a href="'.base_url().'/measures">Check accuracy now</a>.';
      }else if($watch->statusId == null){
        $dasboardWatches .= ' ' . $watch->brand .' '.$watch->name .': <a href="'.base_url().'/measures">Measure now</a>.';
      }else{
        $dasboardWatches .= ' ' . $watch->brand .' '.$watch->name .': Runs at ' . $watch->accuracy . ' spd (' . (($watch->accuracyAge == 0) ? 'today).' : $watch->accuracyAge  . ' day(s) ago).');
      }

      $dasboardWatches .= '</span></li>';
    }

    $dasboardWatches .= '</ul>';
  }

  return $dasboardWatches;
}

function constructContentWatches($watches){

  $contentWatches = "<ul>";

  foreach ($watches as $watch) {
    $contentWatches .= '<li>'.$watch["brand"].' '.$watch["watchName"].'</li>';
  }
  
  return $contentWatches . "</ul>";
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 */
function addFirstWatchContent($firstName, $alphaId){

  return constructReturnArray(
    "add_first_watch", 
    array(
      'name' => $firstName,
      'unsub' => "<a href='". base_url() . 'Unsubscribe/index/'.$alphaId."'>here</a>"
    )
  );
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 */
function makeFirstMeasureContent($firstName, $watchesToCheck, $watches, $alphaId){

  return constructReturnArray(
    "make_first_measure", 
    array(
      'name' => $firstName,
      'watches'=> constructContentWatches($watchesToCheck),
      'dashboard'=> constructDashboardWatches($watches),
      'unsub' => "<a href='". base_url() . 'Unsubscribe/index/'.$alphaId."'>here</a>"
    )
  );
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 * @param String $firstWatchName
 */
function addSecondWatchContent($firstName, $firstWatchName, $watches, $alphaId){
  
  return constructReturnArray(
    "add_second_watch", 
    array(
      'name' => $firstName,
      'firstwatch' => $firstWatchName,
      'dashboard'=> constructDashboardWatches($watches),
      'unsub' => "<a href='". base_url() . 'Unsubscribe/index/'.$alphaId."'>here</a>"
    )
  );
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 */
function comebackContent($firstName, $watches, $alphaId){

  return constructReturnArray(
    "comeback", 
    array(
      'name' => $firstName,
      'dashboard'=> constructDashboardWatches($watches),
      'unsub' => "<a href='". base_url() . 'Unsubscribe/index/'.$alphaId."'>here</a>"
    )
  );
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 * @param Array $watches represents the control panel of given user
 */
function checkAccuracyContent($firstName, $watchesToCheck, $watches, $alphaId){

  return constructReturnArray(
    "check_accuracy", 
    array(
      'name' => $firstName,
      'watches'=> constructContentWatches($watchesToCheck),
      'dashboard'=> constructDashboardWatches($watches),
      'unsub' => "<a href='". base_url() . 'Unsubscribe/index/'.$alphaId."'>here</a>"
    )
  );
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 * @param Array $watches represents the control panel of given user
 */
function oneWeekAccuracyContent($firstName, $watchesToCheck, $watches, $alphaId){

  return constructReturnArray(
    "one_week_accuracy", 
    array(
      'name' => $firstName,
      'watches'=> constructContentWatches($watchesToCheck),
      'dashboard'=> constructDashboardWatches($watches),
      'unsub' => "<a href='". base_url() . 'Unsubscribe/index/'.$alphaId."'>here</a>"
    )
  );
}

function oneMonthAccuracyContent($firstName, $watchesToCheck, $watches, $alphaId){

  return constructReturnArray(
    "one_month_accuracy", 
    array(
      'name' => $firstName,
      'watches'=> constructContentWatches($watchesToCheck),
      'dashboard'=> constructDashboardWatches($watches),
      'unsub' => "<a href='". base_url() . 'Unsubscribe/index/'.$alphaId."'>here</a>"
    )
  );
}

function watchResultContent($firstName, $brand, $model,
  $accuracy, $watches, $alphaId){

  return constructReturnArray(
    "watch_result", 
    array(
      'name' => $firstName,
      'watch' => $brand . ' ' . $model,
      'accuracy' => $accuracy,
      'dashboard'=> constructDashboardWatches($watches),
      'unsub' => "<a href='". base_url() . 'Unsubscribe/index/'.$alphaId."'>here</a>"
    )
  );
}

function signupContent($firstName, $alphaId){

  return constructReturnArray(
    "signup", 
    array(
      'name' => $firstName,
      'unsub' => "<a href='". base_url() . 'Unsubscribe/index/'.$alphaId."'>here</a>"
    )
  );
}

function blumsafeContent(){

  return constructReturnArray(
    "blumsafe", 
    array()
  );
}

function resetPasswordConfirmationContent(){

  return constructReturnArray(
    "reset_password_confirmation", 
    array()
  );
}

function resetPasswordContent($resetToken){

  return constructReturnArray(
    "reset_password", 
    array("reset"=>$resetToken)
  );
}

function customBrandContent($templateName, $firstName){

  return constructReturnArray(
    $templateName, 
    array("name"=>$firstName)
  );
}