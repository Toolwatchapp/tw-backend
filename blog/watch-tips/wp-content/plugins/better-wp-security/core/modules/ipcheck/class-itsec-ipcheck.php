<?php

/**
 * iThemes IPCheck API Wrapper.
 *
 * Provides static calls to the iThemes IPCheck API
 *
 * @package iThemes_Security
 *
 * @since   4.5
 *
 */
class ITSEC_IPCheck extends ITSEC_Lockout {

	private static $endpoint = 'http://ipcheck-api.ithemes.com/?action=';

	private
		$settings;

	function run() {

		$this->settings = get_site_option( 'itsec_ipcheck' );

		//Execute API Brute force protection
		if ( isset( $this->settings['api_ban'] ) && $this->settings['api_ban'] === true ) {

			add_action( 'wp_login', array( $this, 'wp_login' ), 10, 2 );
			add_action( 'wp_login_failed', array( $this, 'wp_login_failed' ), 1, 1 );

			add_filter( 'authenticate', array( $this, 'authenticate' ), 10, 3 );
			add_filter( 'itsec_logger_modules', array( $this, 'itsec_logger_modules' ) );

		}

	}

	/**
	 * Sends to lockout class when login form isn't completely filled out and process xml_rpc username
	 *
	 * @since 4.5
	 *
	 * @param object $user     user or wordpress error
	 * @param string $username username attempted
	 * @param string $password password attempted
	 *
	 * @return user object or WordPress error
	 */
	public function authenticate( $user, $username = '', $password = '' ) {

		global $itsec_logger;

		//Execute brute force if username or password are empty
		if ( isset( $_POST['wp-submit'] ) && ( empty( $username ) || empty( $password ) ) ) {

			if ( $this->report_ip() === 1 ) {

				$itsec_logger->log_event( 'ipcheck', 10, array(), ITSEC_Lib::get_ip() );

				$this->execute_lock( false, true );

			}

		}

		return $user;

	}

	/**
	 * Set transient for caching IPs
	 *
	 * @since 4.5
	 *
	 * @param string $ip     IP Address
	 * @param bool   $status if the IP is blocked or not
	 * @param int    $time   length, in seconds, to cache
	 *
	 * @return void
	 */
	private function cache_ip( $ip, $status, $time ) {

		//@todo one size fits all is too long. Need to adjust time
		set_site_transient( 'itsec_ip_cache_' . esc_sql( $ip ), $status, $time );

	}

	/**
	 * IP to check for blacklist
	 *
	 * @since 4.5
	 *
	 * @param string|null $ip ip to report
	 *
	 * @return bool true if successfully reported else false
	 */
	public function check_ip( $ip = null ) {

		global $itsec_globals, $itsec_logger;

		//get current IP if needed
		if ( $ip === null ) {

			$ip = ITSEC_Lib::get_ip();

		} else {

			$ip = trim( sanitize_text_field( $ip ) );

		}

		if ( $this->is_ip_whitelisted( $ip ) ) {
			return false;
		}

		//See if we've checked this IP in the last hour
		$cache_check = get_site_transient( 'itsec_ip_cache_' . esc_sql( $ip ) );

		if ( is_array( $cache_check ) && isset( $cache_check['status'] ) ) {
			return $cache_check['status'];
		}

		$action = 'check-ip';

		if ( ITSEC_Lib::validates_ip_address( $ip ) ) { //verify IP address is valid

			if ( ! isset( $this->settings['api_key'] ) || ! isset( $this->settings['api_s'] ) ) {
				return false; //invalid key or secret
			}

			$args = json_encode(
				array(
					'apikey'    => $this->settings['api_key'], //the api key
					'behavior'  => 'brute-force-login', //type of behanvior we're reporting
					'ip'        => $ip, //the ip to report
					'site'      => home_url( '', 'http' ), //the current site URL
					'timestamp' => $itsec_globals['current_time_gmt'], //current time (GMT)
				)
			);

			//Build the request parameters
			$request = array(
				'body' => array(
					'request'   => $args,
					'signature' => self::hmac_sha1( $this->settings['api_s'], $action . $args ),
				),
			);

			$response = wp_remote_post( self::$endpoint . $action, $request );

			//Make sure the request was valid and has a valid body
			if ( is_array( $response ) && isset( $response['body'] ) ) {

				$response = json_decode( $response['body'], true );

				if ( is_array( $response ) && isset( $response['success'] ) && $response['success'] == true ) {

					$cache = isset( $response['cache_ttl'] ) ? absint( $response['cache_ttl'] ) : 3600;

					if ( isset( $response['block'] ) && $response['block'] == true ) {

						$expiration     = date( 'Y-m-d H:i:s', $itsec_globals['current_time'] + $cache );
						$expiration_gmt = date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] + $cache );

						$itsec_logger->log_event( __( 'lockout', 'it-l10n-better-wp-security' ), 10, array(
							'expires' => $expiration, 'expires_gmt' => $expiration_gmt, 'type' => 'host'
						), $ip );

						self::cache_ip( $ip, array( 'status' => true ), $cache );

						return true; //API reports IP is blocked

					} else {

						self::cache_ip( $ip, array( 'status' => false ), $cache );

						return false; //API reports IP is not blocked or no report (default to no block)

					}

				}

			}

		}

		return false;

	}

	/**
	 * Calculates the HMAC of a string using SHA1.
	 *
	 * there is a native PHP hmac function, but we use this one for
	 * the widest compatibility with older PHP versions
	 *
	 * @param   string $key  the shared secret key used to generate the mac
	 * @param   string $data data to be signed
	 *
	 *
	 * @return  string    base64 encoded hmac
	 */
	private function hmac_sha1( $key, $data ) {

		if ( strlen( $key ) > 64 ) {
			$key = pack( 'H*', sha1( $key ) );
		}

		$key = str_pad( $key, 64, chr( 0x00 ) );

		$ipad = str_repeat( chr( 0x36 ), 64 );

		$opad = str_repeat( chr( 0x5c ), 64 );

		$hmac = pack( 'H*', sha1( ( $key ^ $opad ) . pack( 'H*', sha1( ( $key ^ $ipad ) . $data ) ) ) );

		return base64_encode( $hmac );

	}

	/**
	 * Register IPCheck for logger
	 *
	 * @since 4.5
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array                   array of logger modules
	 */
	public function itsec_logger_modules( $logger_modules ) {

		$logger_modules['ipcheck'] = array(
			'type'     => 'ipcheck',
			'function' => __( 'IP Flagged as bad by iThemes IPCheck', 'it-l10n-better-wp-security' ),
		);

		return $logger_modules;

	}

	/**
	 * Send offending IP to IPCheck API
	 *
	 * @since 4.5
	 *
	 * @param string|null $ip   ip to report
	 * @param int         $type type of behavior to report
	 *
	 * @return int -1 on failure, 0 if report successful and IP not blocked, 1 if IP successful and IP blocked
	 */
	public function report_ip( $ip = null, $type = 1 ) {

		global $itsec_globals, $itsec_logger;

		$action = 'report-ip';

		/**
		 * Switch types or return false if no valid type
		 *
		 * Valid types:
		 * 1 = invalid/failed login
		 *
		 */
		switch ( $type ) {

			case 1:
				$behavior = 'brute-force-login';
				break;
			default:
				return -1;

		}

		//get current IP if needed
		if ( $ip === null ) {

			$ip = ITSEC_Lib::get_ip();

		} else {

			$ip = trim( sanitize_text_field( $ip ) );

		}

		if ( $this->is_ip_whitelisted( $ip ) ) {
			return 0;
		}

		if ( ITSEC_Lib::validates_ip_address( $ip ) ) { //verify IP address is valid

			if ( ! isset( $this->settings['api_key'] ) || ! isset( $this->settings['api_s'] ) ) {
				return -1; //invalid key or secret
			}

			$args = json_encode(
				array(
					'apikey'    => $this->settings['api_key'], //the api key
					'behavior'  => $behavior, //type of behanvior we're reporting
					'ip'        => $ip, //the ip to report
					'site'      => home_url( '', 'http' ), //the current site URL
					'timestamp' => $itsec_globals['current_time_gmt'], //current time (GMT)
				)
			);

			//Build the request parameters
			$request = array(
				'body' => array(
					'request'   => $args,
					'signature' => self::hmac_SHA1( $this->settings['api_s'], $action . $args ),
				),
			);

			$response = wp_remote_post( self::$endpoint . $action, $request );

			//Make sure the request was valid and has a valid body
			if ( is_array( $response ) && isset( $response['body'] ) ) {

				$response = json_decode( $response['body'], true );

				if ( is_array( $response ) && isset( $response['success'] ) && $response['success'] == true ) {

					if ( isset( $response['block'] ) && $response['block'] == true ) {

						$cache = isset( $response['cache_ttl'] ) ? absint( $response['cache_ttl'] ) : 3600;

						$expiration     = date( 'Y-m-d H:i:s', $itsec_globals['current_time'] + $cache );
						$expiration_gmt = date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] + $cache );

						$itsec_logger->log_event( __( 'lockout', 'it-l10n-better-wp-security' ), 10, array(
							'expires' => $expiration, 'expires_gmt' => $expiration_gmt, 'type' => 'host'
						), $ip );

						self::cache_ip( $ip, array( 'status' => true ), $cache );

						return 1; //ip report success. Just return true for now

					} else {

						return 0;

					}

				}

			}

		}

		return -1;

	}

	/**
	 * Make sure user isn't already locked out even on successful form submission
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function wp_login() {

		global $itsec_logger;

		if ( $this->check_ip() === true ) {

			$itsec_logger->log_event( 'ipcheck', 10, array(), ITSEC_Lib::get_ip() );

			$this->execute_lock( false, true );

		}

	}

	/**
	 * Sends to lockout class when username and password are filled out and wrong
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function wp_login_failed() {

		global $itsec_logger;

		if ( $this->report_ip() === 1 ) {

			$itsec_logger->log_event( 'ipcheck', 10, array(), ITSEC_Lib::get_ip() );

			$this->execute_lock( false, true );

		}

	}

}