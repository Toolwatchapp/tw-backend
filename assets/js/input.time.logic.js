var offsetedDate;
var timeoutPopup;
var clickedDate;
var isAccuracy = false;
var validateFunction = "validateBaseMeasure();";

$( document ).ready(function() {

    if ($( "#accuracyHolder" ).length){
      isAccuracy = true;
      validateFunction = "validateAccuracyMeasure();";
    }

    console.log(isAccuracy);

    $('body').on('click', 'button[name="startSync"]', function(e)
    {
        e.preventDefault();
        var watchId = $('select[name="watchId"]').val();
        $('.watch-error').hide();

        if(watchId != null)
        {
            $('button[name="startSync"]').hide();
            $('button[name="restartCountdown"]').show();
            $('.watch-select').hide();
            $('span#selectedWatch').text(
              "for your " +
              $('select[name="watchId"]').find(":selected").text()
            );
            getNextQuarterMinute();

        }
        else
        {
           $('.watch-error').show();
        }
    });

    $('body').on('click', 'button[name="restartCountdown"]', function(e)
    {
      e.preventDefault();
      reset();
    });
});

function getNextQuarterMinute(){

  var d = new Date();
  var seconds = d.getSeconds();
  var offsetSeconds;

  if(seconds < 15){
    offsetSeconds = 30 - seconds;
  }else if(seconds < 30){
    offsetSeconds = 45 - seconds;
  }else if(seconds < 45){
    offsetSeconds = 60 - seconds;
  }else if(seconds < 60){
    offsetSeconds = 60 - seconds + 15;
  }

  offsetedDate = new Date(d.getTime()+offsetSeconds*1000);

  console.log(seconds);
  console.log(offsetSeconds);
  console.log(offsetedDate.toString());

  $("#sync-button").html("Press this button exactly at <br />"
    + '<span style="font-size:40px">'
    + constructoffsetedDateString(offsetedDate)
    + '</span>'
  );

  $("#sync-button").show();

  timeoutPopup = setTimeout(function(){deadlinePassed();},
    (offsetSeconds + 20) * 1000);
}

function constructoffsetedDateString(date){
  var hours = (date.getHours() < 10) ? "0"+date.getHours() :
    date.getHours();

  var minutes = (offsetedDate.getMinutes() < 10) ? "0"+date.getMinutes() :
    date.getMinutes();

  var seconds = (offsetedDate.getSeconds() < 10) ? "0"+date.getSeconds() :
      date.getSeconds();

  return hours + ":" + minutes + ":" + seconds;
}

function deadlinePassed(){

  console.log("Deadline Passed");

  var deadlinePassedText =
    `<center>
        <h1>Did you missed it ?</h1>
        <p>
        To begin measuring the accuracy of your watch, we must first
        synchronize it with Toolwatch's accuracy system.<br><br>
        You were supposed to click at <b>`
        + constructoffsetedDateString(offsetedDate)
        + `</b>.
        </p>
        <a class="btn btn-success btn-lg" href="javascript:reset();">Retry</a>
    </center>`;

  $('#pageModal .modal-body').html(deadlinePassedText);

  $('#pageModal').modal({
    show: true
  });
}


function reset(){
  $('button.close').click();
  getNextQuarterMinute();
}

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
        All good, take me home scotty
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

function retrieveMinute(){
  offsetedDate = new Date(offsetedDate.getTime()-60*1000);
  $("span#timeSyncAt").text(constructoffsetedDateString(offsetedDate));
}

function addMinute(){
  offsetedDate = new Date(offsetedDate.getTime()+60*1000);
  $("span#timeSyncAt").text(constructoffsetedDateString(offsetedDate));
}

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
