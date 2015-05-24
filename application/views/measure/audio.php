<audio id="bip" class="mejs-player" src="<?php echo base_url();?>assets/audio/bips-final.mp3"></audio>

<script>
var bips;
$( document ).ready(function() {

	new MediaElement(document.getElementById("bip"), {success: function(media) {
		// Trigger the load;
	    media.play();
	    media.pause();
	    bips = media;
	    console.log("sound is loading...")
	}});

});	

</script>