<?php

function addFirstWatch($firstName){
  return array(
        'title' => 'Hey '.$firstName.'!',
        'content' =>
          'You signed in on <a href="https://toolwatch.io">Toolwatch.io</a> and
          we\'re thrilled to have you onboard! <br>
          <br>
          You can now start the first measuring for the accuracy of your
          mechanical watches. Is your watch really accurate or should it be serviced?
          Let’s start by adding your first watch and find out
          <a href="https://toolwatch.io/measures">now</a>!'
    );
}

function makeFirstMeasure($firstName){
  return array(
        'title' => 'Hey '.$firstName.'!',
        'content' =>
          'You’ve added your first watch on Toolwatch.io and we’re thrilled to
          have you onboard!<br>
          You can now start the first measuring for
          the accuracy of your mechanical watches.
          Is your watch really accurate or should it be serviced?
          Let’s begin by starting your first measure and find out now!
          '
    );
}

function addSecondWatch($firstName, $firstWatchName){
  return array(
    'title' => 'Hey '.$firstName.'!',
    'content' =>
      '2 days ago, you did your first measure on Toolwatch and we’re so proud
      of that! Thank you for your trust, it means a lot to us.<br>
      If, like 37% of Toolwatch’s users you have another mechanical watch,
      <a href="https://toolwatch.io/measures">let’s start a new measure</a>
       and see how it compares to your '.$firstWatchName.'
      <br>
      If you don’t have another watch, you can still make our day by
      <a href="https://www.facebook.com/sharer/sharer.php?u=www.toolwatch.io">
      spreading the word about Toolwatch on social</a>
      (it works no matter how many watch you own!).'
  );
}

function comeback($firstName){
  return array(
    'title' => 'Hey '.$firstName.'!',
    'content' =>
      'It’s been a while since we last saw you on Toolwatch.io!
      The accuracy of a watch should be regularly checked to make sure
      everything is fine and that you can continue enjoying this work of
      art and mechanics on your wrist AND we will be very happy to have you around!
      <br>
      <a href="https://toolwatch.io/measures">Let’s start a new measure</a>
      and do not hesitate to also say hi on
      <a href="https://twitter.com/toolwatchapp">Twitter</a>!'
  );
}

function oneWeekAccuracy($firstName, $watchBrand, $watchName){
  return array(
    'title' => 'Hey '.$firstName.'!',
    'content' =>
      'You’ve synchronized your '.$watchBrand.' ' . $watchName . '
      with Toolwatch one week ago and now is the time to see the results
      of your watch’s accuracy!
      <br>
      Just make sure you have your watch near you
      (it should already be on your wrist ;) )
      and go to the <a href="https://toolwatch.io/measures">measure page</a>.
      <br>
      See you there!'
    );
}

function oneMonthAccuracy($firstName, $watchBrand, $watchName){

  return array(
    'title' => 'Hey '.$firstName.'!',
    'content' =>
      'Last month you measured your '.$watchBrand.' ' . $watchName . ' on
      <a href="https://toolwatch.io/">Toolwatch</a> and
      we’re happy to count you as a cool member of the Toolwatch community!
      <br>
      The accuracy of a watch should be regularly checked to make sure everything
      is fine and that you can continue enjoying this work of art and mechanics on
      your wrist.
      <br>
      <a href="https://toolwatch.io/measures">Let’s start a new measure</a>
      and see how it compares to last time!
      <br>
      And don’t forget to share a wristshot or
      <a href="https://twitter.com/toolwatchapp">Twitter</a> or
      <a href="https://www.instagram.com/Toolwatchapp/">Instagram</a>
      using our <a href="https://twitter.com/hashtag/toolwatchapp?f=tweets&src=hash">#ToolwatchApp</a> hashtag and join us 
      spreading the love for mechanical timepieces!'
    );
}
