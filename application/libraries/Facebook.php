<?php

class Facebook{

    public static $is_valid = false;

	/**
	* Check if a given facebook token is valid
	* 
	* @param String $token. A facebook token
	**/
	function is_token_valid($token){

        if(self::$is_valid){
            return true;
        }

		$url = 'https://graph.facebook.com/debug_token?input_token='
				.$token.'&access_token='.getenv("FB_AT");

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url);
		$fb = json_decode(curl_exec($ch));
		curl_close($ch);

		return $fb->data->is_valid && $fb->data->application == "Toolwatch";
	}

}

?>