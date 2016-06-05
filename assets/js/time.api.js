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

  return window.syncedDate;
}

getAccurateTime();
