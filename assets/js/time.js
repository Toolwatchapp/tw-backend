window.syncedDate = null;
window.syncedDataAnchor = null;
window.offset = null;

(function(w){
	var perfNow;
	var perfNowNames = ['now', 'webkitNow', 'msNow', 'mozNow'];
	if(!!w['performance']) for(var i = 0; i < perfNowNames.length; ++i)
	{
		var n = perfNowNames[i];
		if(!!w['performance'][n])
		{
			perfNow = function(){return w['performance'][n]()};
			break;
		}
	}
	if(!perfNow)
	{
    if(Date.now) {
        perfNow = Date.now();
    } else {
        perfNow = +(new Date());
    }
	}
	w.perfNow = perfNow;
})(window);

var offsets = [];
var counter = 0;
var maxTimes = 10;
var beforeTime = null;

function median(offset){
  offset.sort( function(a,b) {return a - b;} );

  var half = Math.floor(offset.length/2);

  if(offset.length % 2){
    return offset[half];
  } else {
    return (offset[half-1] + offset[half]) / 2.0;
  }
}

function getTimeDiff(){
  beforeTime = window.perfNow();
  $.ajax('/api/time', {
      type: 'GET',
      success: function(response) {
          var now, timeDiff, serverTime, offset;
          counter++;

          // Get offset
          now = window.perfNow();
          timeDiff = (now-beforeTime)/2;

          serverTime = response.time-timeDiff;
          offset = Date.now()-serverTime;

          // Push to array
          offsets.push(offset)
          if (counter < maxTimes) {
            // Repeat
            getTimeDiff();
          } else {
            var averageOffset = median(offsets);
            window.syncedDate = new Date(Date.now()-averageOffset);
            window.syncedDataAnchor = window.perfNow();
            console.log(window.syncedDate);
            console.log("average offset:" + averageOffset);
            window.offset  = averageOffset;
          }
      }
  });
}

function getAccurateTime(){
  if(window.syncedDate === null){
    getTimeDiff();
    setTimeout(getAccurateTime, 5000);
    console.log("waiting");
    return;
  }

  window.syncedDate = new Date(
    window.syncedDate.getTime() +
    window.perfNow() - window.syncedDataAnchor
  );

  window.syncedDataAnchor = window.perfNow();

  console.log(window.syncedDate);

  return window.syncedDate;
}

function getTime() {

  getAccurateTime();

  return {
    'total': window.syncedDate,
    'years':window.syncedDate.getFullYear(),
    'months':window.syncedDate.getMonth(),
    'days': window.syncedDate.getDate() + "<sup>" + nth(window.syncedDate.getDate()) + "</sup>",
    'hours': window.syncedDate.getHours(),
    'minutes': window.syncedDate.getMinutes(),
    'seconds': window.syncedDate.getSeconds(),
    'milliseconds': window.syncedDate.getMilliseconds(),
    'offset':window.offset
  };
}

/**
 * The following code is adapted and improved upon http://www.sitepoint.com/build-javascript-countdown-timer-no-dependencies/
 */

function nth(d) {
  if(d>3 && d<21) return 'th';
  switch (d % 10) {
        case 1:  return "st";
        case 2:  return "nd";
        case 3:  return "rd";
        default: return "th";
    }
}

function initializeClock(id, endtime) {
  var clock = document.getElementById(id);
  var daysSpan = clock.querySelector('.days');
  var hoursSpan = clock.querySelector('.hours');
  var minutesSpan = clock.querySelector('.minutes');
  var secondsSpan = clock.querySelector('.seconds');
  var monthsSpan = clock.querySelector('.months');
  var yearsSpan = clock.querySelector('.years');

  var monthNames = ["January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];

  function updateClock() {
    var t = getTime();

    daysSpan.innerHTML = t.days;
    monthsSpan.innerHTML = monthNames[t.months];
    yearsSpan.innerHTML = t.years;
    hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
    minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
    secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

    if (t.total <= 0) {
      clearInterval(timeinterval);
    }
  }

  updateClock();
  var timeinterval = setInterval(updateClock, 1000);
}

/**
 * The following code is
 * Adapted from http://xjubier.free.fr/en/site_pages/LunarEclipseCalculator.html
 * Copyright Xavier Jubier
 */

var myPhaseName = "";
var forecastImage = new Array();

function moonPhasePercent(theDate)
{
  var synodic = 29.53058867;
  var msPerDay = 86400000;
  var baseDate = new Date();
  baseDate.setUTCFullYear(2005);
  baseDate.setUTCMonth(4);
  baseDate.setUTCDate(8);
  baseDate.setUTCHours(8);
  baseDate.setUTCMinutes(48);

  var diff = theDate - baseDate;
  var phase = diff / (synodic * msPerDay);
  phase *= 100;
  phase %= 100;
  if ( phase < 0 )
    phase += 100;

  return(phase);
}

function getMoonPhase()
{

  var theDate = new Date();

  var phasePercent = moonPhasePercent(theDate);

  var phaseNames = [
    "New Moon", "New Moon",
    "Waxing Crescent", "Waxing Crescent", "Waxing Crescent", "Waxing Crescent",
    "First Quarter", "First Quarter", "First Quarter",
    "Waxing Gibbous", "Waxing Gibbous", "Waxing Gibbous", "Waxing Gibbous", "Waxing Gibbous",
    "Full Moon", "Full Moon",
    "Waning Gibbous", "Waning Gibbous", "Waning Gibbous", "Waning Gibbous",
    "Last Quarter", "Last Quarter", "Last Quarter",
    "Waning Crescent", "Waning Crescent", "Waning Crescent", "Waning Crescent",
    "New Moon"
  ];

  thePhase = Math.round(phasePercent * 0.279);
  myPhaseName = phaseNames[thePhase];

  return thePhase;
}

function calcMoonPhase()
{
  $("#moonPhaseImage").attr("src", $("#moonPhaseImage").attr("src") + getMoonPhase() + ".png");
  $("#moonPhaseImage").attr("title", myPhaseName);
  $("#moonPhaseTitle").html(myPhaseName);
}

$( document ).ready(function() {
  getAccurateTime();
  if(window.syncedDate === null){
      setTimeout(initClock, 3000);
  }else{
    initClock();
  }

});

function initClock(){
  initializeClock('clockdiv', new Date(Date.parse(new Date())));
  calcMoonPhase();
}
