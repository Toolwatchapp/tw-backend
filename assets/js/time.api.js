window.syncedDate = null;
window.syncedDataAnchor = null;
window.offset = null;
window.callback = null;
window.diff = null;

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

          offsets.push(Date.now()-serverTime)
          if (counter < maxTimes) {
            // Repeat
            getTimeDiff();
          } else {
            var medianOffset = median(offsets);
            window.syncedDate = new Date(Date.now()-medianOffset);
            window.syncedDataAnchor = window.perfNow();
            console.log(window.syncedDate);
            console.log("median offset:" + medianOffset);
            window.offset = (medianOffset/1000).toFixed(2);
          }

          if(window.callback !== null){
              window.callback(counter, maxTimes);
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