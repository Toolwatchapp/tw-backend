<?php

class Ithemes_Sync_Verb_ITSEC_Get_Temp_Whitelist extends Ithemes_Sync_Verb {

	public static $name = 'itsec-get-temp-whitelist';
	public static $description = 'Retrieve and report temporarily whitelisted IP.';

	public $default_arguments = array(
	);

	public function run( $arguments ) {

		return array( 'temp_whitelist' => get_site_option( 'itsec_temp_whitelist_ip' ) );

	}

}