<?php

class ITSEC_Brute_Force {

	private
		$settings,
		$username;

	function run() {

		$this->settings = get_site_option( 'itsec_brute_force' );
		$this->username = null;

		add_action( 'wp_login', array( $this, 'wp_login' ), 10, 2 );
		add_action( 'wp_login_failed', array( $this, 'wp_login_failed' ), 1, 1 );

		add_filter( 'authenticate', array( $this, 'authenticate' ), 10, 3 );
		add_filter( 'itsec_lockout_modules', array( $this, 'itsec_lockout_modules' ) );
		add_filter( 'itsec_logger_modules', array( $this, 'itsec_logger_modules' ) );
		add_filter( 'xmlrpc_login_error', array( $this, 'xmlrpc_login_error' ), 10, 2 );
		add_filter( 'jetpack_get_default_modules', array( $this, 'jetpack_get_default_modules' ) ); //disable jetpack protect via Geoge Stephanis

	}

	/**
	 * Sends to lockout class when login form isn't completely filled out and process xml_rpc username
	 *
	 * @since 4.0
	 *
	 * @param object $user     user or wordpress error
	 * @param string $username username attempted
	 * @param string $password password attempted
	 *
	 * @return user object or WordPress error
	 */
	public function authenticate( $user, $username = '', $password = '' ) {

		global $itsec_lockout, $itsec_logger;

		//Look for the "admin" user name and ban it if it is set to auto-ban
		if ( isset( $this->settings['auto_ban_admin'] ) && $this->settings['auto_ban_admin'] === true && trim( sanitize_text_field( $username ) ) == 'admin' ) {

			$itsec_logger->log_event( 'brute_force', 5, array(), ITSEC_Lib::get_ip(), sanitize_text_field( $username ) );

			$itsec_lockout->do_lockout( 'brute_force_admin_user', sanitize_text_field( $username ) );

		}

		//Execute brute force if username or password are empty
		if ( isset( $_POST['wp-submit'] ) && ( empty( $username ) || empty( $password ) ) ) {

			$user_id = username_exists( sanitize_text_field( $username ) );

			if ( $user_id === false || $user_id === null ) {

				$itsec_lockout->check_lockout( false, $username );

			} else {

				$itsec_lockout->check_lockout( $user_id );

			}

			$itsec_logger->log_event( 'brute_force', 5, array(), ITSEC_Lib::get_ip(), sanitize_text_field( $username ), intval( $user_id ) );

			$itsec_lockout->do_lockout( 'brute_force', sanitize_text_field( $username ) );

		}

		//Set username for xml_rpc block
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST === true ) {

			$this->username = trim( sanitize_text_field( $username ) );

		}

		return $user;

	}

	/**
	 * Register Brute Force for lockout
	 *
	 * @since 4.0
	 *
	 * @param  array $lockout_modules array of lockout modules
	 *
	 * @return array                   array of lockout modules
	 */
	public function itsec_lockout_modules( $lockout_modules ) {

		if ( $this->settings['enabled'] === true ) {

			$lockout_modules['brute_force'] = array(
				'type'   => 'brute_force',
				'reason' => __( 'too many bad login attempts', 'it-l10n-better-wp-security' ),
				'host'   => $this->settings['max_attempts_host'],
				'user'   => $this->settings['max_attempts_user'],
				'period' => $this->settings['check_period'],
			);

			$lockout_modules['brute_force_admin_user'] = array(
				'type'   => 'brute_force',
				'reason' => __( 'user tried to login as "admin."', 'it-l10n-better-wp-security' ),
				'host'   => 1,
				'user'   => 1,
				'period' => $this->settings['check_period']
			);

		}

		return $lockout_modules;

	}

	/**
	 * Register Brute Force for logger
	 *
	 * @since 4.0
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array                   array of logger modules
	 */
	public function itsec_logger_modules( $logger_modules ) {

		if ( $this->settings['enabled'] === true ) {

			$logger_modules['brute_force'] = array(
				'type'     => 'brute_force',
				'function' => __( 'Invalid Login Attempt', 'it-l10n-better-wp-security' ),
			);

		}

		return $logger_modules;

	}

	/**
	 * Disables the jetpack protect module
	 *
	 * Sent by George Stephanis
	 *
	 * @since 4.5
	 *
	 * @param array $modules array of Jetpack modules
	 *
	 * @return array array of Jetpack modules
	 */
	public function jetpack_get_default_modules( $modules ) {

		return array_diff( $modules, array( 'protect' ) );

	}

	/**
	 * Make sure user isn't already locked out even on successful form submission
	 *
	 * @since 4.0
	 *
	 * @param string $username the username attempted
	 * @param        object    wp_user the user
	 *
	 * @return void
	 */
	public function wp_login( $username, $user = null ) {

		global $itsec_lockout;

		if ( ! $user === null ) {

			$itsec_lockout->check_lockout( $user );

		} elseif ( is_user_logged_in() ) {

			$current_user = wp_get_current_user();

			$itsec_lockout->check_lockout( $current_user->ID );

		}

	}

	/**
	 * Sends to lockout class when username and password are filled out and wrong
	 *
	 * @since 4.0
	 *
	 * @param string $username the username attempted
	 *
	 * @return void
	 */
	public function wp_login_failed( $username ) {

		global $itsec_lockout, $itsec_logger;

		if ( isset( $this->settings['auto_ban_admin'] ) && $this->settings['auto_ban_admin'] === true && trim( sanitize_text_field( $username ) ) == 'admin' ) {

			$itsec_logger->log_event( 'brute_force', 5, array(), ITSEC_Lib::get_ip(), sanitize_text_field( $username ) );

			$itsec_lockout->do_lockout( 'brute_force_admin_user', sanitize_text_field( $username ) );

		}

		if ( isset( $_POST['log'] ) && $_POST['log'] != '' && isset( $_POST['pwd'] ) && $_POST['pwd'] != '' ) {

			$user_id = username_exists( sanitize_text_field( $username ) );

			if ( $user_id === false || $user_id === null ) {

				$itsec_lockout->check_lockout( false, $username );

			} else {

				$itsec_lockout->check_lockout( $user_id );

			};

			$itsec_logger->log_event( 'brute_force', 5, array(), ITSEC_Lib::get_ip(), sanitize_text_field( $username ), intval( $user_id ) );

			$itsec_lockout->do_lockout( 'brute_force', sanitize_text_field( $username ) );

		}

	}

	/**
	 * Execute brute force against xml_rpc login
	 *
	 * @Since 4.4
	 *
	 * @param mixed $error WordPress error
	 *
	 * @return mixed WordPress error
	 */
	public function xmlrpc_login_error( $error ) {

		global $itsec_lockout, $itsec_logger;

		if ( isset( $this->settings['auto_ban_admin'] ) && $this->settings['auto_ban_admin'] === true && trim( sanitize_text_field( $this->username ) ) == 'admin' ) {

			$itsec_logger->log_event( 'brute_force', 5, array(), ITSEC_Lib::get_ip(), $this->username );

			$itsec_lockout->do_lockout( 'brute_force_admin_user', $this->username );

		} else {

			$user_id = username_exists( $this->username );

			if ( $user_id === false || $user_id === null ) {

				$itsec_lockout->check_lockout( false, $this->username );

			} else {

				$itsec_lockout->check_lockout( $user_id );

			};

			$itsec_logger->log_event( 'brute_force', 5, array(), ITSEC_Lib::get_ip(), $this->username, intval( $user_id ) );

			$itsec_lockout->do_lockout( 'brute_force', $this->username );

		}

		return $error;
	}

}