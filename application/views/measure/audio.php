<audio id="bip" src="<?php echo base_url();?>assets/audio/bips.mp3"></audio>
<audio id="last-bip" src="<?php echo base_url();?>assets/audio/last-bip.mp3"></audio>

<script type="text/javascript">
	
var bip;
var lastBip;

$( document ).ready(function() {

  bip =	new MediaElement(document.getElementById("bip"));
  lastBip = new MediaElement(document.getElementById("last-bip"));

});

function playBip(){
   bip.play();
}

function playLastBip(){
   lastBip.play();
}

</script>