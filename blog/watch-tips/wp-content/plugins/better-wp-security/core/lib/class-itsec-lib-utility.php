<?php
/**
 * iThemes Security utility function library.
 *
 * Contains the ITSEC_Lib_Utility class.
 *
 * @package iThemes_Security
 */

/**
 * iThemes Security Utility Library class.
 *
 * Various utility functions.
 *
 * @package iThemes_Security
 * @since 1.15.0
 */
class ITSEC_Lib_Utility {
	/**
	 * Determines if a function is callable.
	 *
	 * @since 1.15.0
	 *
	 * @param string $function Name of function.
	 * @return bool Boolean true if the function is callable, false otherwise.
	 */
	public static function is_callable_function( $function ) {
		if ( ! is_callable( $function ) ) {
			return false;
		}
		
		if ( ! isset( $GLOBALS['itsec_lib_cached_values'] ) ) {
			$GLOBALS['itsec_lib_cached_values'] = array();
		}
		
		if ( ! isset( $GLOBALS['itsec_lib_cached_values']['ini_get:disable_functions'] ) ) {
			$GLOBALS['itsec_lib_cached_values']['ini_get:disable_functions'] = preg_split( '/\s*,\s*/', (string) ini_get( 'disable_functions' ) );
		}
		
		if ( in_array( $function, $GLOBALS['itsec_lib_cached_values']['ini_get:disable_functions'] ) ) {
			return false;
		}
		
		if ( ! isset( $GLOBALS['itsec_lib_cached_values']['ini_get:suhosin.executor.func.blacklist'] ) ) {
			$GLOBALS['itsec_lib_cached_values']['ini_get:suhosin.executor.func.blacklist'] = preg_split( '/\s*,\s*/', (string) ini_get( 'suhosin.executor.func.blacklist' ) );
		}
		
		if ( in_array( $function, $GLOBALS['itsec_lib_cached_values']['ini_get:suhosin.executor.func.blacklist'] ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns the type of web server.
	 *
	 * This code makes a best effort attempt of identifying the active web server. If the ITSEC_SERVER_OVERRIDE define
	 * is defined, this value is returned.
	 *
	 * @since 1.15.0
	 *
	 * @return string Returns apache, nginx, litespeed, or iis. Defaults to apache when the server cannot be identified.
	 */
	public static function get_web_server() {
		if ( defined( 'ITSEC_SERVER_OVERRIDE' ) ) {
			return ITSEC_SERVER_OVERRIDE;
		}
		
		
		if ( isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
			$server_software = strtolower( $_SERVER['SERVER_SOFTWARE'] );
		} else {
			$server_software = '';
		}
		
		if ( false !== strpos( $server_software, 'apache' ) ) {
			$server = 'apache';
		} else if ( false !== strpos( $server_software, 'nginx' ) ) {
			$server = 'nginx';
		} else if ( false !== strpos( $server_software, 'litespeed' ) ) {
			$server = 'litespeed';
		} else if ( false !== strpos( $server_software, 'thttpd' ) ) {
			$server = 'thttpd';
		} else if ( false !== strpos( $server_software, 'microsoft-iis' ) ) {
			$server = 'iis';
		} else {
			$server = 'apache';
		}
		
		return apply_filters( 'itsec_filter_web_server', $server );
	}
	
	/**
	 * Updates the supplied content to use the same line endings.
	 *
	 * @since 1.15.0
	 *
	 * @param string $content     The content to update.
	 * @param string $line_ending Optional. The line ending to use. Defaults to "\n".
	 * @return string The content with normalized line endings.
	 */
	public static function normalize_line_endings( $content, $line_ending = "\n" ) {
		return preg_replace( '/(?<!\r)\n|\r(?!\n)|(?<!\r)\r\n|\r\r\n/', $line_ending, $content );
	}
}
