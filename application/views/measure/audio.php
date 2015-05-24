<audio id="bip" src="<?php echo base_url();?>assets/audio/bips-final.mp3"></audio>

<script>
var bips;
$( document ).ready(function() {

	new MediaElement(document.getElementById("bip"), {success: function(media) {
		// Trigger the load;
	    media.play();
	    media.pause();
	    bips = media;
	    console.log("sound is loading...");
	    media.addEventListener('timeupdate', function(e) {
            var countdown = $('.sync-time').html();

            if(6-bips.currentTime < countdown){

                if((countdown-1) > 0)
                {
                    $('.sync-time').html(countdown-1);
                }
                else
                {
                    $('.sync-time').html('Go!');
                    $('.userTime').show();
                    $('button[name="syncDone"]').removeAttr('disabled');        
                    $.post('/ajax/getReferenceTime');
                }
            }

        }, false);
	}});

});	

</script>