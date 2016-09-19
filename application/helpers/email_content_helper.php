<?php

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 */
function addFirstWatchContent($firstName){
  return array(
        'title' => 'Hey '.$firstName.'!',
        'content' =>
          'You signed in on <a href="https://toolwatch.io">Toolwatch.io</a> and
          we\'re thrilled to have you onboard! <br>
          <br>
          You can now start to measure the accuracy of your mechanical watches.
          Is your watch really accurate or should it be serviced?
          Let\'s start by adding your first watch and find out
          <a href="https://toolwatch.io/measures">now</a>!'
    );
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 */
function makeFirstMeasureContent($firstName, $watchesToCheck, $watches){

  $content = 'You\'ve added the following watch(es) on
  <a href="https://toolwatch.io">Toolwatch.io</a> : <ul>';

  foreach ($watchesToCheck as $watch) {
    $content .= '<li>'.$watch["brand"].' '.$watch["watchName"].'</li>';
  }

  return array(
        'title' => 'Hey '.$firstName.'!',
        'content' =>
          $content . '</ul>
          We\'re thrilled to
          have you onboard!<br /><br />

          You can now start <a href="https://toolwatch.io/measures/">
          to measure the accuracy of your watch(es)</a>.<br/><br />

          Are your watches really accurate or should they be serviced?
          Find out now!',
          'summary' => $watches
    );
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 * @param String $firstWatchName
 */
function addSecondWatchContent($firstName, $firstWatchName, $watches){
  return array(
    'title' => 'Hey '.$firstName.'!',
    'content' =>
      '2 days ago, you did your first measure on Toolwatch and we\'re so proud
      of that! Thank you for your trust, it means a lot to us.<br/><br/>
      If, like 37% of Toolwatch\'s users you have another mechanical watch,
      <a href="https://toolwatch.io/measures">let\'s start a new measure</a>
       and see how it compares to your '.$firstWatchName.'.
      <br/><br/>
      If you don\'t have another watch, you can still make our day by
      <a href="https://www.facebook.com/sharer/sharer.php?u=www.toolwatch.io">
      spreading the word about Toolwatch on social medias</a>
      (it works no matter how many watch you own!).',
      'summary' => $watches
  );
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 */
function comebackContent($firstName, $watches){
  return array(
    'title' => 'Hey '.$firstName.'!',
    'content' =>
      'It\'s been a while since we last saw you on
      <a href="https://toolwatch.io/">Toolwatch.io</a> !
      The accuracy of a watch should be regularly checked to make sure
      everything is fine and that you can continue enjoying this work of
      art and mechanics on your wrist AND we will be very happy to have you around!
      <br>
      <a href="https://toolwatch.io/measures">Let\'s start a new measure</a>
      and do not hesitate to also say hi on
      <a href="https://twitter.com/toolwatchapp">Twitter</a>!',
      'summary' => $watches
  );
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 * @param Array $watches represents the control panel of given user
 */
function checkAccuracyContent($firstName, $watchesToCheck, $watches){

  $content = 'One day ago, you\'ve synchronized the following watch(es): <ul>';

  foreach ($watchesToCheck as $watch) {
    $content .= '<li>'.$watch["brand"].' '.$watch["watchName"].'</li>';
  }

  $content .= "</ul> Now is the time to see the results
    of your watch's accuracy !<br>";


  return array(
    'title' => 'Hey '.$firstName.'!',
    'content' => $content . '
      Just make sure your have your watch(es) near you (it should already
      be on your wrist ;) ) and go to the
      <a href="https://toolwatch.io/measures/">measure page</a>.<br>
      <br>
      See you there !
      <br>',
    'summary' => $watches
  );
}

/**
 * util function to create the content emails
 * @param String $firstName of the recipient
 * @param Array $watches represents the control panel of given user
 */
function oneWeekAccuracyContent($firstName, $watchesToCheck, $watches){


  $content = 'One week ago, you\'ve synchronized the following watch(es): <ul>';

  foreach ($watchesToCheck as $watch) {
      $content .= '<li>'.$watch["brand"].' '.$watch["watchName"].'</li>';
  }

  $content .= "</ul> Now is the time to see the results
    of your watch's accuracy !<br>";

  return array(
    'title' => 'Hey '.$firstName.'!',
    'content' => $content . '
      Just make sure your have your watch(es) near you (it should already
      be on your wrist ;) ) and go to the
      <a href="https://toolwatch.io/measures/">measure page</a>.<br>
      <br>
      See you there !
      <br>',
    'summary' => $watches
  );
}

function oneMonthAccuracyContent($firstName, $watchesToCheck, $watches){

  $content = 'Last month you measured the following watch(es): <ul>';

  foreach ($watchesToCheck as $watch) {
      $content .= '<li>'.$watch["brand"].' '.$watch["watchName"].'</li>';
  }

  $content .= "</ul>";

  return array(
    'title' => 'Hey '.$firstName.'!',
    'content' =>
      $content . ' We\'re happy to count you as a cool member of the Toolwatch community!
      <br>
      The accuracy of a watch should be regularly checked to make sure everything
      is fine and that you can continue enjoying this work of art and mechanics on
      your wrist.
      <br>
      <a href="https://toolwatch.io/measures">Let\'s start a new measure</a>
      and see how it compares to last time!
      <br>
      And don\'t forget to share a wristshot on
      <a href="https://twitter.com/toolwatchapp">Twitter</a> or
      <a href="https://www.instagram.com/Toolwatchapp/">Instagram</a>
      using our <a href="https://twitter.com/hashtag/toolwatchapp?f=tweets&src=hash">#ToolwatchApp</a> hashtag and join us
      spreading the love for mechanical timepieces!',
      'summary' => $watches
    );
}

function watchResultContent($firstname, $brand, $model,
  $accuracy, $watches){
  return array(
    'title' => 'Hurray '.$firstname.'!',
    'content' =>
      'We are happy to share with you the results for the accuracy of
      your mechanical watch !
      <br /><br />
      Your '. $brand .' ' . $model . ' is running at <b>' . $accuracy . '</b> seconds per day.
      <br /><br />
      You should come back and check regularly that there aren\'t big
      variations in your watch\'s accuracy. Why not add in your calendar
      a reminder to come back and check that everything\'s fine in one
      month ?
      <br /><br />
      You might also want to <a href="https://blog.toolwatch.io/watch-tips/">
      read our tips and advices</a> for keeping your watch running
      safe and smooth !
      <br /><br />
      Take care of yourself and your watch !
      <br /><br />
      Happy toolwatching !
      <br />
      The Toolwatch Team',
      'summary' => $watches
    );
}

function signupContent($firstname){

  return array(
    'title' => 'Hey '.$firstname.'!',
    'content' =>
      'We are very happy to welcome you to Toolwatch ! <br>
      <br>
      Every single one of us is here for you to help you getting the most from your mechanical watch. Drop us a line anytime at <a href="mailto:hello@toolwatch.io">hello@toolwatch.io</a> or tweet us <a href="https://twitter.com/toolwatchapp/">@toolwatchapp</a>.<br>
      <br>
      We also have a little bonus for you. We\'ve pulled a resource with stats from the whole watchmaking industry after having measured 10k+ watches.
      <br>
      If you <a href="mailto:?bcc=bonus@toolwatch.io&subject=I%20think%20you%20will%20like%20this&body=I%20found%20this%20free%20tool%20for%20measuring%20and%20tracking%20the%20accuracy%20of%20your%20mechanical%20watches%20%20https%3A%2F%2Ftoolwatch.io%2F%20%0D%0A%0D%0AIt%20helps%20you%20taking%20care%20of%20your%20mechanical%20watches%20and%20see%20how%20they%20compete%20versus%20other%20watches%20from%20the%20same%20brand.%20I%27m%20taking%20it%20and%20think%20it%20could%20be%20really%20cool.">
      click here to email a friend about Toolwatch</a>, we’ll send this bonus to you right away. Just leave "bonus@toolwatch.io" BCC\'d so we know you sent it :)
      <br>
      <br>
      If there is just one single tip we’d share for taking care of your watch, we’d say try to keep your watch away from magnetic fields such as the speakers of your laptop or your radio alarm clock for example. That’s it !<br>
      <br>
      That being said, we can’t wait for helping you starting <a href="http://www.toolwatch.io/">measuring</a> the accuracy of your watch now !<br>
      <br>
      Happy toolwatching !<br>
      <br>
      The Toolwatch Team<br>'
  );
}

function resetPasswordConfirmationContent(){
    return array(
      'title' => 'Hey !',
      'content' =>
        'We have changed your password.<br>
        <br>
        If you didn\'t requested a password reset, <a href="mailto:hello@toolwatch.io">let us know</a>. <br><br>
        The Toolwatch Team<br>'
    );
}

function resetPasswordContent($resetToken){

    return array(
      'title' => 'Hey !',
      'content' =>
        'We saw that you\'ve forgotten your password. No worries, we got you covered !<br>
        <br>
        Simply browse to the following link and you’ll be asked to chose a new one: https://toolwatch.io/reset-password/'.$resetToken.'
        <br>
        <br>
        Happy toolwatching !<br>
        <br>
        The Toolwatch Team<br>'
    );
  }
