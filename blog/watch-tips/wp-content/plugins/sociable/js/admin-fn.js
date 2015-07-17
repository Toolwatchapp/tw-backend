function hideOrShow(sID){

	if(document.getElementById(sID+'-Tab').innerHTML == '-'){
		document.getElementById(sID+'-Tab').innerHTML = '+';
		document.getElementById(sID+'-Content').style.display='none';

	}else{
		document.getElementById(sID+'-Tab').innerHTML = '-';
		document.getElementById(sID+'-Content').style.display='block';
	}

}