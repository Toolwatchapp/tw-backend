<?php

/**
 * Miscellaneous plugin-wide functions.
 *
 * Various static functions to provide information to modules and other areas throughout the plugin.
 *
 * @package iThemes_Security
 *
 * @since   4.0.0
 */
final class ITSEC_Lib {

	/**
	 * Converts CIDR to ip range.
	 *
	 * Modified from function at http://stackoverflow.com/questions/4931721/getting-list-ips-from-cidr-notation-in-php
	 * as it was far more elegant than my own solution
	 *
	 * @since 4.0.0.0
	 *
	 * @param string $cidr cidr notation to convert
	 *
	 * @return array        range of ips returned
	 */
	public static function cidr_to_range( $cidr ) {

		$range = array();

		if ( strpos( $cidr, '/' ) ) {

			$cidr = explode( '/', $cidr );

			$range[] = long2ip( ( ip2long( $cidr[0] ) ) & ( ( - 1 << ( 32 - (int) $cidr[1] ) ) ) );
			$range[] = long2ip( ( ip2long( $cidr[0] ) ) + pow( 2, ( 32 - (int) $cidr[1] ) ) - 1 );

		} else { //if not a range just return the original ip

			$range[] = $cidr;

		}

		return $range;

	}

	/**
	 * Clear caches.
	 *
	 * Clears popular WordPress caching mechanisms.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $page [optional] true to clear page cache
	 *
	 * @return void
	 */
	public static function clear_caches( $page = false ) {

		//clear APC Cache
		if ( function_exists( 'apc_store' ) ) {
			apc_clear_cache(); //Let's clear APC (if it exists) when big stuff is saved.
		}

		//clear w3 total cache or wp super cache
		if ( function_exists( 'w3tc_pgcache_flush' ) ) {

			if ( true == $page ) {
				w3tc_pgcache_flush();
				w3tc_minify_flush();
			}

			w3tc_dbcache_flush();
			w3tc_objectcache_flush();

		} else if ( function_exists( 'wp_cache_clear_cache' ) && true == $page ) {

			wp_cache_clear_cache();

		}

	}

	/**
	 * Creates appropriate database tables.
	 *
	 * Uses dbdelta to create database tables either on activation or in the event that one is missing.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function create_database_tables() {

		global $wpdb;

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		//Set up log table
		$tables = "CREATE TABLE " . $wpdb->prefix . "itsec_log (
				log_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				log_type varchar(20) NOT NULL DEFAULT '',
				log_function varchar(255) NOT NULL DEFAULT '',
				log_priority int(2) NOT NULL DEFAULT 1,
				log_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				log_date_gmt datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				log_host varchar(20),
				log_username varchar(20),
				log_user bigint(20) UNSIGNED,
				log_url varchar(255),
				log_referrer varchar(255),
				log_data longtext NOT NULL,
				PRIMARY KEY  (log_id),
				KEY log_type (log_type),
				KEY log_date_gmt (log_date_gmt)
				) " . $charset_collate . ";";

		//set up lockout table
		$tables .= "CREATE TABLE " . $wpdb->prefix . "itsec_lockouts (
				lockout_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				lockout_type varchar(20) NOT NULL,
				lockout_start datetime NOT NULL,
				lockout_start_gmt datetime NOT NULL,
				lockout_expire datetime NOT NULL,
				lockout_expire_gmt datetime NOT NULL,
				lockout_host varchar(20),
				lockout_user bigint(20) UNSIGNED,
				lockout_username varchar(20),
				lockout_active int(1) NOT NULL DEFAULT 1,
				PRIMARY KEY  (lockout_id),
				KEY lockout_expire_gmt (lockout_expire_gmt),
				KEY lockout_host (lockout_host),
				KEY lockout_user (lockout_user),
				KEY lockout_username (lockout_username),
				KEY lockout_active (lockout_active)
				) " . $charset_collate . ";";

		//set up temp table
		$tables .= "CREATE TABLE " . $wpdb->prefix . "itsec_temp (
				temp_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				temp_type varchar(20) NOT NULL,
				temp_date datetime NOT NULL,
				temp_date_gmt datetime NOT NULL,
				temp_host varchar(20),
				temp_user bigint(20) UNSIGNED,
				temp_username varchar(20),
				PRIMARY KEY  (temp_id),
				KEY temp_date_gmt (temp_date_gmt),
				KEY temp_host (temp_host),
				KEY temp_user (temp_user),
				KEY temp_username (temp_username)
				) " . $charset_collate . ";";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		@dbDelta( $tables );

	}

	/**
	 * Gets location of wp-config.php.
	 *
	 * Finds and returns path to wp-config.php
	 *
	 * @since 4.0.0
	 *
	 * @return string path to wp-config.php
	 * */
	public static function get_config() {

		if ( file_exists( trailingslashit( ABSPATH ) . 'wp-config.php' ) ) {

			return trailingslashit( ABSPATH ) . 'wp-config.php';

		} else {

			return trailingslashit( dirname( ABSPATH ) ) . 'wp-config.php';

		}

	}

	/**
	 * Gets current url
	 *
	 * Finds and returns current url.
	 *
	 * @since 4.3.0
	 *
	 * @return string current url
	 * */
	public static function get_current_url() {

		$page_url = 'http';

		if ( isset( $_SERVER["HTTPS"] ) ) {

			if ( 'on' == $_SERVER["HTTPS"] ) {
				$page_url .= "s";
			}

		}

		$page_url .= "://";

		if ( '80' != $_SERVER["SERVER_PORT"] ) {

			$page_url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];

		} else {

			$page_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

		}

		return esc_url( $page_url );
	}

	/**
	 * Return primary domain from given url.
	 *
	 * Returns primary domain name (without subdomains) of given URL.
	 *
	 * @since 4.0.0
	 *
	 * @param string $url          URL to filter
	 *
	 * @return string domain name or '*' on error or domain mapped multisite
	 * */
	public static function get_domain( $url ) {
		if ( is_multisite() && function_exists( 'domain_mapping_warning' ) ) {
			return '*';
		}
		
		
		$host = parse_url( $url, PHP_URL_HOST );
		
		if ( false === $host ) {
			return '*';
		}
		if ( 'www.' == substr( $host, 0, 4 ) ) {
			return substr( $host, 4 );
		}
		
		$host_parts = explode( '.', $host );
		
		if ( count( $host_parts ) > 2 ) {
			$host_parts = array_slice( $host_parts, -2, 2 );
		}
		
		return implode( '.', $host_parts );
	}

	/**
	 * Get path to WordPress install.
	 *
	 * Get the absolute filesystem path to the root of the WordPress installation.
	 *
	 * @since 4.3.0
	 *
	 * @return string Full filesystem path to the root of the WordPress installation
	 */
	public static function get_home_path() {

		$home    = set_url_scheme( get_option( 'home' ), 'http' );
		$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );

		if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {

			$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
			$pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );

			if ( $pos === false ) {

				$home_path = dirname( $_SERVER['SCRIPT_FILENAME'] );

			} else {

				$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );

			}

		} else {

			$home_path = ABSPATH;

		}

		return trailingslashit( str_replace( '\\', '/', $home_path ) );

	}

	/**
	 * Returns the root of the WordPress install.
	 *
	 * Get's the URI path to the WordPress installation.
	 *
	 * @since 4.0.6
	 *
	 * @return string the root folder
	 */
	public static function get_home_root() {

		//homeroot from wp_rewrite
		$home_root = parse_url( site_url() );

		if ( isset( $home_root['path'] ) ) {

			$home_root = trailingslashit( $home_root['path'] );

		} else {

			$home_root = '/';

		}

		return $home_root;

	}

	/**
	 * Gets location of .htaccess
	 *
	 * Finds and returns path to .htaccess or nginx.conf if appropriate
	 *
	 * @since 4.0.0
	 *
	 * @return string path to .htaccess
	 */
	public static function get_htaccess() {

		global $itsec_globals;

		if ( 'nginx' === ITSEC_Lib::get_server() ) {

			return $itsec_globals['settings']['nginx_file'];

		} else {

			return ITSEC_Lib::get_home_path() . '.htaccess';

		}

	}

	/**
	 * Returns the actual IP address of the user.
	 *
	 * Determines the user's IP address by returning the forwarded IP address if present or
	 * the direct IP address if not.
	 *
	 * @since 4.0.0
	 *
	 * @return  String The IP address of the user
	 */
	public static function get_ip() {

		global $itsec_globals;

		if ( isset( $itsec_globals['settings']['proxy_override'] ) && true === $itsec_globals['settings']['proxy_override'] ) {
			return esc_sql( $_SERVER['REMOTE_ADDR'] );
		}

		//Just get the headers if we can or else use the SERVER global
		if ( function_exists( 'apache_request_headers' ) ) {

			$headers = apache_request_headers();

		} else {

			$headers = $_SERVER;

		}

		//Get the forwarded IP if it exists
		if ( array_key_exists( 'X-Forwarded-For', $headers ) &&
		     (
			     filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ||
			     filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) )
		) {

			$the_ip = $headers['X-Forwarded-For'];

		} elseif (
			array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) &&
			(
				filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ||
				filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 )
			)
		) {

			$the_ip = $headers['HTTP_X_FORWARDED_FOR'];

		} else if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {

			$the_ip = $_SERVER['REMOTE_ADDR'];

		} else {
			$the_ip = '';
		}

		return esc_sql( $the_ip );

	}

	/**
	 * Gets PHP Memory Limit.
	 *
	 * Attempts to get the maximum amount of memory allowed for the application by the server.
	 *
	 * @since 4.0.0
	 *
	 * @return int php memory limit in megabytes
	 */
	public static function get_memory_limit() {

		return (int) ini_get( 'memory_limit' );

	}

	/**
	 * Returns the URL of the current module.
	 *
	 * Get's the full URL of the current module.
	 *
	 * @since 4.0.0
	 *
	 * @param string $file the module file from which to derive the path
	 *
	 * @return string the path of the current module
	 */
	public static function get_module_path( $file ) {

		global $itsec_globals;

		$path = str_replace( $itsec_globals['plugin_dir'], '', dirname( $file ) );
		$path = ltrim( str_replace( '\\', '/', $path ), '/' );

		return trailingslashit( trailingslashit( $itsec_globals['plugin_url'] ) . $path );

	}

	/**
	 * Returns a psuedo-random string of requested length.
	 *
	 * Builds a random string similar to the WordPress password functions.
	 *
	 * @since 4.0.0
	 *
	 * @param int  $length        how long the string should be (max 62)
	 * @param bool $base32        true if use only base32 characters to generate
	 * @param bool $special_chars whether to include special characters in generation
	 *
	 * @return string
	 */
	public static function get_random( $length, $base32 = false, $special_chars = false ) {

		if ( true === $base32 ) {

			$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

		} else {

			$string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

			if ( true === $special_chars ) {

				$string .= '_)(*&^%$#@!~`:;<>,.?/{}[]|';

			}

		}

		return substr( str_shuffle( $string ), mt_rand( 0, strlen( $string ) - $length ), $length );

	}

	/**
	 * Returns the server type of the plugin user.
	 *
	 * Attempts to figure out what http server the visiting user is running.
	 *
	 * @since 4.0.0
	 *
	 * @return string|bool server type the user is using of false if undetectable.
	 */
	public static function get_server() {

		//Allows to override server authentication for testing or other reasons.
		if ( defined( 'ITSEC_SERVER_OVERRIDE' ) ) {
			return ITSEC_SERVER_OVERRIDE;
		}

		$server_raw = strtolower( filter_var( $_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING ) );

		//figure out what server they're using
		if ( false !== strpos( $server_raw, 'apache' ) ) {

			return 'apache';

		} elseif ( false !== strpos( $server_raw, 'nginx' ) ) {

			return 'nginx';

		} elseif ( false !== strpos( $server_raw, 'litespeed' ) ) {

			return 'litespeed';

		} else { //unsupported server

			return false;

		}

	}

	/**
	 * Determine whether the server supports SSL (shared cert not supported.
	 *
	 * Attempts to retrieve an HTML version of the homepage in an effort to determine if SSL is available.
	 *
	 * @since 4.0.0
	 *
	 * @return bool true if ssl is supported or false
	 */
	public static function get_ssl() {

		$url = str_ireplace( 'http://', 'https://', get_bloginfo( 'url' ) );

		if ( function_exists( 'wp_http_supports' ) && wp_http_supports( array( 'ssl' ), $url ) ) {

			return true;

		} elseif ( function_exists( 'curl_init' ) ) {

			//use a manual CURL request to better account for self-signed certificates
			$timeout    = 5; //timeout for the request
			$site_title = trim( get_bloginfo() );

			$request = curl_init();

			curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $request, CURLOPT_VERBOSE, false );
			curl_setopt( $request, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $request, CURLOPT_HEADER, true );
			curl_setopt( $request, CURLOPT_URL, $url );
			curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $request, CURLOPT_CONNECTTIMEOUT, $timeout );

			$data = curl_exec( $request );

			$header_size = curl_getinfo( $request, CURLINFO_HEADER_SIZE );
			$http_code   = intval( curl_getinfo( $request, CURLINFO_HTTP_CODE ) );
			$body        = substr( $data, $header_size );

			preg_match( '/<title>(.+)<\/title>/', $body, $matches );

			if ( 200 == $http_code && isset( $matches[1] ) && false !== strpos( $matches[1], $site_title ) ) {

				return true;

			} else {

				return false;

			}

		}

		return false;

	}

	/**
	 * Converts IP with a netmask wildcards to one with * instead
	 *
	 * Allows use of wildcards in IP address by converting them to standard notation.
	 *
	 * @since 4.0.0
	 *
	 * @param string $ip ip to convert
	 *
	 * @return string     the converted ip
	 */
	public static function ip_mask_to_range( $ip ) {

		if ( strpos( $ip, '/' ) ) {

			$parts  = explode( '/', trim( $ip ) );
			$octets = array_reverse( explode( '.', trim( $parts[0] ) ) );

			if ( isset( $parts[1] ) && 0 < intval( $parts[1] ) ) {

				$wildcards = ( 32 - $parts[1] ) / 8;

				for ( $count = 0; $count < $wildcards; $count ++ ) {

					$octets[$count] = '[0-9]+';

				}

				return implode( '.', array_reverse( $octets ) );

			} else {

				return $ip;

			}

		}

		return $ip;

	}

	/**
	 * Converts IP with * wildcards to one with a netmask instead
	 *
	 * Attempts to create a standardized CIDR block from an IP using wildcards.
	 *
	 * @since 4.0.0
	 *
	 * @param string $ip ip to convert
	 *
	 * @return string     the converted ip
	 */
	public static function ip_wild_to_mask( $ip ) {

		$host_parts = array_reverse( explode( '.', trim( $ip ) ) );

		if ( strpos( $ip, '*' ) ) {

			$mask           = 32; //used to calculate netmask with wildcards
			$converted_host = str_replace( '*', '0', $ip );

			//convert hosts with wildcards to host with netmask and create rule lines
			foreach ( $host_parts as $part ) {

				if ( '*' === $part ) {
					$mask = $mask - 8;
				}

			}

			$converted_host = trim( $converted_host );

			//Apply a mask if we had to convert
			if ( 0 < $mask ) {
				$converted_host .= '/' . $mask;
			}

			return $converted_host;

		}

		return $ip;

	}

	/**
	 * Determine whether we're on the login page or not.
	 *
	 * Attempts to determine whether or not the user is on the WordPress dashboard login page.
	 *
	 * @since 4.0.0
	 *
	 * @return bool true if is login page else false
	 */
	public static function is_login_page() {

		return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );

	}

	/**
	 * Checks jQuery version.
	 *
	 * Checks if the jquery version saved is vulnerable to http://bugs.jquery.com/ticket/9521
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|bool true if known safe false if unsafe or null if untested
	 */
	public static function safe_jquery_version() {

		$jquery_version = get_site_option( 'itsec_jquery_version' );

		if ( false !== $jquery_version && version_compare( $jquery_version, '1.6.3', '>=' ) ) {

			return true;

		} elseif ( false === $jquery_version ) {

			return null;

		}

		return false;

	}

	/**
	 * Set a 404 error.
	 *
	 * Forces the given page to a WordPress 404 error.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function set_404() {

		global $wp_query;

		status_header( 404 );

		if ( function_exists( 'nocache_headers' ) ) {
			nocache_headers();
		}

		$wp_query->set_404();
		$page_404 = get_404_template();

		if ( 1 < strlen( $page_404 ) ) {

			include( $page_404 );

		} else {

			include( get_query_template( 'index' ) );

		}

		die();

	}

	/**
	 * Increases minimum memory limit.
	 *
	 * This function, adopted from builder, attempts to increase the minimum
	 * memory limit before heavy functions.
	 *
	 * @since 4.0.0
	 *
	 * @param int $new_memory_limit what the new memory limit should be
	 *
	 * @return void
	 */
	public static function set_minimum_memory_limit( $new_memory_limit ) {

		$memory_limit = @ini_get( 'memory_limit' );

		if ( - 1 < $memory_limit ) {

			$unit = strtolower( substr( $memory_limit, - 1 ) );

			$new_unit = strtolower( substr( $new_memory_limit, - 1 ) );

			if ( 'm' == $unit ) {

				$memory_limit *= 1048576;

			} else if ( 'g' == $unit ) {

				$memory_limit *= 1073741824;

			} else if ( 'k' == $unit ) {

				$memory_limit *= 1024;

			}

			if ( 'm' == $new_unit ) {

				$new_memory_limit *= 1048576;

			} else if ( 'g' == $new_unit ) {

				$new_memory_limit *= 1073741824;

			} else if ( 'k' == $new_unit ) {

				$new_memory_limit *= 1024;

			}

			if ( (int) $memory_limit < (int) $new_memory_limit ) {
				@ini_set( 'memory_limit', $new_memory_limit );
			}

		}

	}

	/**
	 * Checks if user exists.
	 *
	 * Checks to see if WordPress user with given id exists.
	 *
	 * @since 4.0.0
	 *
	 * @param int $user_id user id of user to check
	 *
	 * @return bool true if user exists otherwise false
	 *
	 * */
	public static function user_id_exists( $user_id ) {

		global $wpdb;

		//return false if username is null
		if ( '' == $user_id ) {
			return false;
		}

		//queary the user table to see if the user is there
		$saved_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM `" . $wpdb->users . "` WHERE ID='%s';", sanitize_text_field( $user_id ) ) );

		if ( $saved_id == $user_id ) {

			return true;

		} else {

			return false;

		}

	}

	/**
	 * Validates a list of ip addresses.
	 *
	 * Makes sure that the provided IP addresses are in fact valid IPV4 addresses.
	 *
	 * @since 4.0.0
	 *
	 * @param string $ip string of hosts to check
	 *
	 * @return array array of good hosts or false
	 */
	public static function validates_ip_address( $ip ) {
		$ip = trim( filter_var( $ip, FILTER_SANITIZE_STRING ) );
		
		if ( substr_count( $ip, '.' ) !== 3 ) {
			return false;
		}
		
		$has_cidr = ( false !== strpos( $ip, '/' ) );
		$has_wildcard = ( false !== strpos( $ip, '*' ) );
		
		if ( $has_cidr && $has_wildcard ) {
			return false;
		}
		
		$ip_digit_regex = '(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)';
		$cidr_digit_regex = '(?:3[0-2]|2[0-9]|1[1-9]|[148])';
		
		$ip_regex = "(?:$ip_digit_regex\.){3}$ip_digit_regex";
		
		if ( $has_cidr ) {
			return (boolean) preg_match( "{^$ip_regex/$cidr_digit_regex$}", $ip );
		}
		
		if ( $has_wildcard ) {
			$wildcard_count = substr_count( $ip, '*' );
			
			if ( 1 === $wildcard_count ) {
				return (boolean) preg_match( "{^(?:$ip_digit_regex\.){3}\*$}", $ip );
			} else if ( 2 === $wildcard_count ) { 
				return (boolean) preg_match( "{^(?:$ip_digit_regex\.){2}\*\.\*$}", $ip );
			} else if ( 3 === $wildcard_count ) { 
				return (boolean) preg_match( "{^(?:$ip_digit_regex\.)\*\.\*\.\*$}", $ip );
			}
			
			return false;
		}
		
		return (boolean) preg_match( "{^$ip_regex$}", $ip );
	}
	
	/**
	 * Validates a file path
	 *
	 * Adapted from http://stackoverflow.com/questions/4049856/replace-phps-realpath/4050444#4050444 as a replacement for PHP's realpath
	 *
	 * @since 4.0.0
	 *
	 * @param string $path The original path, can be relative etc.
	 *
	 * @return bool true if the path is valid and writeable else false
	 */
	public static function validate_path( $path ) {

		// whether $path is unix or not
		$unipath = strlen( $path ) == 0 || $path{0} != '/';

		// attempts to detect if path is relative in which case, add cwd
		if ( false === strpos( $path, ':' ) && $unipath ) {
			$path = getcwd() . DIRECTORY_SEPARATOR . $path;
		}

		// resolve path parts (single dot, double dot and double delimiters)
		$path      = str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, $path );
		$parts     = array_filter( explode( DIRECTORY_SEPARATOR, $path ), 'strlen' );
		$absolutes = array();

		foreach ( $parts as $part ) {

			if ( '.' == $part ) {
				continue;
			}

			if ( '..' == $part ) {

				array_pop( $absolutes );

			} else {

				$absolutes[] = $part;

			}

		}

		$path = implode( DIRECTORY_SEPARATOR, $absolutes );

		// resolve any symlinks
		if ( function_exists( 'linkinfo' ) ) { //linkinfo not available on Windows with PHP < 5.3.0

			if ( file_exists( $path ) && 0 < linkinfo( $path ) ) {
				$path = @readlink( $path );
			}

		} else {

			if ( file_exists( $path ) && 0 < linkinfo( $path ) ) {
				$path = @readlink( $path );
			}

		}

		// put initial separator that could have been lost
		$path = ! $unipath ? '/' . $path : $path;

		$test = @touch( $path . '/test.txt' );
		@unlink( $path . '/test.txt' );

		return $test;

	}

	/**
	 * Validates a URL
	 *
	 * Ensures the provided URL is a valid URL.
	 *
	 * @since 4.3.0
	 *
	 * @param string $url the url to validate
	 *
	 * @return bool true if valid url else false
	 */
	public static function validate_url( $url ) {

		$pattern = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";

		return (bool) preg_match( $pattern, $url );

	}

}
