var offsetedDate;
var clickedDate;
var isAccuracy = false;
var validateFunction = "validateBaseMeasure();";
var ctaText = "All good, take me home scotty";

/**
 * Link button click
 */
$( document ).ready(function() {

    // This is shared between the baseMeasure and accuracyMeasure pages.
    // Here, we do the difference between both and change the validateFunction
    // used in the clicked function;
    if ($( "#accuracyHolder" ).length){
      isAccuracy = true;
      validateFunction = "validateAccuracyMeasure();";
      ctaText = "Give my accuracy, baby";
      createCTA();
    }

    console.log(isAccuracy);

    $('body').on('click', 'button[name="startSync"]', function(e)
    {
        e.preventDefault();
        createCTA();
    });
});

/**
 * Check if a watch is selected and displays the CTA
 */
function createCTA(){
  var watchId = $('select[name="watchId"]').val();
  $('.watch-error').hide();

  if(watchId != null)
  {
      $('button[name="startSync"]').hide();
      $('.watch-select').hide();
      $('span#selectedWatch').text(
        "for your " +
        $('select[name="watchId"]').find(":selected").text()
      );
      getNextMinute();

  }
  else
  {
     $('.watch-error').show();
  }
}

/**
 * Compute the next minute
 */
function getNextMinute(){

  var d = new Date();
  var seconds = d.getSeconds();
  var offsetSeconds = 0;

  // If it's somewhere between xx:xx:51 and xx:xx:60,
  // users won't have the time to click.
  // So, we add a minute.
  if(seconds >= 50){
    offsetSeconds = 60 - seconds;
  }else{
    offsetSeconds = -seconds;
  }

  offsetedDate = new Date(d.getTime() + offsetSeconds * 1000 + 60 * 1000);

  $("#sync-button").html("<span style='"+computeFontSize()+"'>"
    + "Press this button when <br /> the second-hand  <br /><br />​​"
    + '<img src="../../assets/img/stepnew.jpg" style="width:30%;" />'
    + "<br /><br />"
    + "reaches <i><b>exactly</b></i>&nbsp; the twelve <br /> o'clock position </span> <br />"
  );

  $("#sync-button").show();

  //The maximum waiting time is 70 secs. If nothing happens in the next 80
  //sec, we display a popup.
  timeoutPopup = setTimeout(function(){deadlinePassed();},
    80 * 1000);
}

/**
 * Bootstrap css doesn't like us to override the
 * default font for buttons...
 * @return font-size depending on the screen
 */
function computeFontSize(){
  var style = "";
  if(document.body.clientWidth > 500){
    style = "font-size:20px";
  }
  return style;
}

/**
 * Helper function to transform a javascript Date in xx:xx:xx
 *
 * @param  Date date
 * @return String
 */
function constructoffsetedDateString(date){
  var hours = (date.getHours() < 10) ? "0"+date.getHours() :
    date.getHours();

  var minutes = (offsetedDate.getMinutes() < 10) ? "0"+date.getMinutes() :
    date.getMinutes();

  var seconds = (offsetedDate.getSeconds() < 10) ? "0"+date.getSeconds() :
      date.getSeconds();

  return hours + ":" + minutes + ":" + seconds;
}

/**
 * This function is triggererd by a setTimeout function and displays a
 * popop
 */
function deadlinePassed(){

  console.log("Deadline Passed");

  var deadlinePassedText =
    `<center>
        <h1>Is everything ok, Doc ?</h1>
        <p>
        To begin measuring the accuracy of your watch, we must first
        synchronize it with Toolwatch's accuracy system.<br><br>
        You were supposed to click when the second hand was
        <b>
        exactly at 12
        </b>.
        </p>
        <a class="btn btn-success btn-lg" href="javascript:reset();">Retry</a>
    </center>`;

  $('#pageModal .modal-body').html(deadlinePassedText);

  $('#pageModal').modal({
    show: true
  });
}

/**
 * Resets everything
 */
function reset(){
  $('button.close').click();
  getNextMinute();
}

/**
 * Big ass button has been clicked.
 * Displays confirmation popup.
 */
function clicked(){

  clearInterval(timeoutPopup);
  clickedDate = new Date();

  var clickedText =
    `<center>
        <h1>Almost there youngling</h1>
        <br />
        <p>You've synchronized you watch at <b>
        <span style="font-size:25px" id="timeSyncAt">`
        + constructoffsetedDateString(offsetedDate) +
        `</span></b> <br /></p>

        <a class="btn btn-success btn-lg"
        href="javascript:` + validateFunction + `">
        `+ctaText+`
        </a>

        <br /><br />

        <p><i>One does not simply</i> have a timepiece without drift !
        Press the following
        button to add/retrieve a minute or
        <a href="javascript:reset()">click here to retry.</a></p>

        <br />

        <a style="float:left" class="btn btn-success btn-lg"
        href="javascript:retrieveMinute();">
        -1 minute
        </a>

        <a style="float:right" class="btn btn-success btn-lg"
        href="javascript:addMinute();">
        +1 minute
        </a>
        <br />
        <br />
    </center>`;

    $('#pageModal .modal-body').html(clickedText);

    $('#pageModal').modal({
      show: true
    });

}

/**
 * Removes a minute from the offsetedDate
 */
function retrieveMinute(){
  offsetedDate = new Date(offsetedDate.getTime()-60*1000);
  $("span#timeSyncAt").text(constructoffsetedDateString(offsetedDate));
}

/**
 * Adds a minute from the offsetedDate
 */
function addMinute(){
  offsetedDate = new Date(offsetedDate.getTime()+60*1000);
  $("span#timeSyncAt").text(constructoffsetedDateString(offsetedDate));
}

/**
 * Validation function fot the accuracyMeasure
 */
function validateAccuracyMeasure(){

  var measureId = $('input[name="measureId"]').val();

  $.post('/measures/accuracyMeasure',
    {
      measureId: measureId,
      referenceTimestamp: offsetedDate.getTime(),
      userTimestamp: clickedDate.getTime()
    }, function(data){
      var result = $.parseJSON(data);
      if(result.success == true)
      {

          $('button.close').click();

          $("#sync-button").hide();
          $('button[name="restartCountdown"]').hide();
          $('.sync-success').show();
          $('.backToMeasure').show();
          $('#mainTitle').hide();

          if(result.accuracy != null)
          {
              $('button.close').click();
              if(result.accuracy > 0){
                  result.accuracy = '+'+result.accuracy;
              }

              $('.watch-accuracy').html(result.accuracy);
              $('.watch-percentile').html(result.percentile);

              $('.share-button').each(function(index){
                  $(this).attr("href", $(this).attr("href").replace("{WatchPercentile}", result.percentile));
              });

          }
      }
      else
      {
          $('.measure-error').show();
          $('.btn-spinner i').css('display', 'none');
      }
    }
  );
}

/**
 * Validation function for the baseMeasure
 */
function validateBaseMeasure(){
  var watchId = $('select[name="watchId"]').val();

  $.post('/measures/baseMeasure',
    {
      watchId: watchId,
      referenceTimestamp: offsetedDate.getTime(),
      userTimestamp: clickedDate.getTime()
    }, function(data){
      var response = $.parseJSON(data);
      if(response.success == true){
        window.location.replace(window.location.origin+"/measures");
      }
    }
  );
}
