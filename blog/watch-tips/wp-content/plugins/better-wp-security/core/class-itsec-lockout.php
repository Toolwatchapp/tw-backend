<?php

/**
 * Handles lockouts for modules and core
 *
 * @package iThemes-Security
 * @since   4.0
 */
class ITSEC_Lockout {

	private
		$core,
		$lockout_modules;

	function __construct( $core ) {

		$this->core            = $core;
		$this->lockout_modules = array(); //array to hold information on modules using this feature

		//Run database cleanup daily with cron
		if ( ! wp_next_scheduled( 'itsec_purge_lockouts' ) ) {
			wp_schedule_event( time(), 'daily', 'itsec_purge_lockouts' );
		}

		add_action( 'itsec_purge_lockouts', array( $this, 'purge_lockouts' ) );

		//Check for host lockouts
		add_action( 'init', array( $this, 'check_lockout' ) );

		//Register all plugin modules
		add_action( 'plugins_loaded', array( $this, 'register_modules' ) );

		//Set an error message on improper logout
		add_action( 'login_head', array( $this, 'set_lockout_error' ) );

		//Add the metabox
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) );

		//Process clear lockout form
		add_action( 'itsec_admin_init', array( $this, 'release_lockout' ) );

		//Register Logger
		add_filter( 'itsec_logger_modules', array( $this, 'register_logger' ) );

		//Register Sync
		add_filter( 'itsec_sync_modules', array( $this, 'register_sync' ) );

		//Add Javascripts script
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) ); //enqueue scripts for admin page

		//Run ajax for temp whitelist
		add_action( 'wp_ajax_itsec_temp_whitelist_ajax', array( $this, 'itsec_temp_whitelist_ajax' ) );
		add_action( 'wp_ajax_itsec_temp_whitelist_release_ajax', array( $this, 'itsec_temp_whitelist_release_ajax' ) );

	}

	/**
	 * Add meta boxes to primary options pages.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	function add_admin_meta_boxes() {

		add_meta_box(
			'itsec_lockouts',
			__( 'Active Lockouts', 'it-l10n-better-wp-security' ),
			array( $this, 'lockout_metabox' ),
			'toplevel_page_itsec',
			'bottom',
			'core'
		);

		$lockout_pages = array(
			'toplevel_page_itsec',
			'security_page_toplevel_page_itsec_settings',
			'security_page_toplevel_page_itsec_logs'
		);

		foreach ( $lockout_pages as $page ) {

			add_meta_box(
				'itsec_self_protect',
				__( "Don't Lock Yourself Out", 'it-l10n-better-wp-security' ),
				array( $this, 'self_protect_metabox' ),
				$page,
				'top',
				'core'
			);

		}

	}

	/**
	 * Add Tracking Javascript.
	 *
	 * Adds javascript for tracking settings to all itsec admin pages
	 *
	 * @since 4.3
	 *
	 * @return void
	 */
	public function admin_script() {

		global $itsec_globals;

		//scripts for all itsec pages
		if ( isset( get_current_screen()->id ) && ( strpos( get_current_screen()->id, 'itsec' ) !== false || strpos( get_current_screen()->id, 'dashboard' ) !== false ) ) {

			wp_enqueue_script( 'itsec_temp_whitelist', $itsec_globals['plugin_url'] . 'core/js/admin-whitelist.js', array( 'jquery' ), $itsec_globals['plugin_build'] );
			wp_localize_script( 'itsec_temp_whitelist', 'itsec_temp_whitelist', array(
				'nonce'   => wp_create_nonce( 'itsec_temp_whitelist_nonce' ),
				'success' => __( 'Temporarily Whitelist my IP', 'it-l10n-better-wp-security' ),
			) );

		}

	}

	/**
	 * Checks if the host or user is locked out and executes lockout
	 *
	 * @since 4.0
	 *
	 * @param mixed $user     WordPress user object or false
	 * @param mixed $username the username to check
	 *
	 * @return void
	 */
	public function check_lockout( $user = false, $username = false ) {

		global $wpdb, $itsec_globals;

		$wpdb->hide_errors(); //Hide database errors in case the tables aren't there

		$host           = ITSEC_Lib::get_ip();
		$username       = sanitize_text_field( trim( $username ) );
		$username_check = false;
		$user_check     = false;
		$host_check     = false;

		if ( $user !== false && $user !== '' && $user !== null ) {

			$user    = get_userdata( intval( $user ) );
			$user_id = $user->ID;

		} else {

			$user    = wp_get_current_user();
			$user_id = $user->ID;

			if ( $username !== false && $username != '' ) {
				$username_check = $wpdb->get_var( "SELECT `lockout_username` FROM `" . $wpdb->base_prefix . "itsec_lockouts` WHERE `lockout_active`=1 AND `lockout_expire_gmt` > '" . date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ) . "' AND `lockout_username`='" . $username . "';" );
			}

			$host_check = $wpdb->get_var( "SELECT `lockout_host` FROM `" . $wpdb->base_prefix . "itsec_lockouts` WHERE `lockout_active`=1 AND `lockout_expire_gmt` > '" . date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ) . "' AND `lockout_host`='" . $host . "';" );

		}

		if ( $user_id !== 0 && $user_id !== null ) {

			$user_check = $wpdb->get_var( "SELECT `lockout_user` FROM `" . $wpdb->base_prefix . "itsec_lockouts` WHERE `lockout_active`=1 AND `lockout_expire_gmt` > '" . date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ) . "' AND `lockout_user`=" . intval( $user_id ) . ";" );

		}

		$error = $wpdb->last_error;

		if ( strlen( trim( $error ) ) > 0 ) {
			ITSEC_Lib::create_database_tables();
		}

		if ( $host_check !== null && $host_check !== false ) {

			$this->execute_lock();

		} elseif ( ( $user_check !== false && $user_check !== null ) || ( $username_check !== false && $username_check !== null ) ) {

			$this->execute_lock( true );

		}

	}

	/**
	 * Executes lockout and logging for modules
	 *
	 * @since 4.0
	 *
	 * @param string $module string name of the calling module
	 * @param string $user   username of user
	 *
	 * @return void
	 */
	public function do_lockout( $module, $user = null ) {

		global $wpdb, $itsec_globals;

		$wpdb->hide_errors(); //Hide database errors in case the tables aren't there

		$lock_host     = null;
		$lock_user     = null;
		$lock_username = null;
		$options       = $this->lockout_modules[$module];

		$host = ITSEC_Lib::get_ip();

		if ( isset( $options['host'] ) && $options['host'] > 0 ) {

			$wpdb->insert(
				$wpdb->base_prefix . 'itsec_temp',
				array(
					'temp_type'     => $options['type'],
					'temp_date'     => date( 'Y-m-d H:i:s', $itsec_globals['current_time'] ),
					'temp_date_gmt' => date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ),
					'temp_host'     => $host,
				)
			);

			$host_count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "itsec_temp` WHERE `temp_date_gmt` > '%s' AND `temp_host`='%s';",
					date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] - ( $options['period'] * 60 ) ),
					$host
				)
			);

			if ( $host_count >= $options['host'] ) {

				$lock_host = $host;

			}

		}

		if ( $user !== null && isset( $options['user'] ) && $options['user'] > 0 ) {

			$user_id = username_exists( sanitize_text_field( $user ) );

			if ( $user_id !== null ) {

				$wpdb->insert(
					$wpdb->base_prefix . 'itsec_temp',
					array(
						'temp_type'     => $options['type'],
						'temp_date'     => date( 'Y-m-d H:i:s', $itsec_globals['current_time'] ),
						'temp_date_gmt' => date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ),
						'temp_user'     => intval( $user_id ),
						'temp_username' => sanitize_text_field( $user ),
					)
				);

				$user_count = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "itsec_temp` WHERE `temp_date_gmt` > '%s' AND `temp_username`='%s' OR `temp_user`=%s;",
						date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] - ( $options['period'] * 60 ) ),
						sanitize_text_field( $user ),
						intval( $user_id )
					)
				);

				if ( $user_count >= $options['user'] ) {

					$lock_user = $user_id;

				}

			} else {

				$user = sanitize_text_field( $user );

				$wpdb->insert(
					$wpdb->base_prefix . 'itsec_temp',
					array(
						'temp_type'     => $options['type'],
						'temp_date'     => date( 'Y-m-d H:i:s', $itsec_globals['current_time'] ),
						'temp_date_gmt' => date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ),
						'temp_username' => $user,
					)
				);

				$user_count = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "itsec_temp` WHERE `temp_date_gmt` > '%s' AND `temp_username`='%s';",
						date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] - ( $options['period'] * 60 ) ),
						$user
					)
				);

				if ( $user_count >= $options['user'] ) {

					$lock_username = $user;

				}

			}

		}

		$error = $wpdb->last_error;

		if ( strlen( trim( $error ) ) > 0 ) {
			ITSEC_Lib::create_database_tables();
		}

		if ( ! $this->is_ip_whitelisted( $host ) && ( $lock_host !== null || $lock_user !== null || $lock_username !== null ) ) {

			$this->lockout( $options['type'], $options['reason'], $lock_host, $lock_user, $lock_username );

		} elseif ( $lock_host !== null || $lock_user !== null ) {

			global $itsec_logger;

			$itsec_logger->log_event( __( 'lockout', 'it-l10n-better-wp-security' ), 10, array( __( 'A whitelisted host has triggered a lockout condition but was not locked out.', 'it-l10n-better-wp-security' ) ), sanitize_text_field( $host ) );

		}

	}

	/**
	 * Executes lockout (locks user out)
	 *
	 * @param boolean $user if we're locking out a user or not
	 *
	 * @return void
	 */
	protected function execute_lock( $user = false, $network = false ) {

		if ( $this->is_ip_whitelisted( ITSEC_Lib::get_ip() ) ) {
			return;
		}

		global $itsec_globals;

		wp_logout();
		@header( 'HTTP/1.0 403 Forbidden' );
		@header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
		@header( 'Expires: Thu, 22 Jun 1978 00:28:00 GMT' );
		@header( 'Pragma: no-cache' );

		if ( $network === true ) { //lockout triggered by iThemes Network

			if ( isset( $itsec_globals['settings']['community_lockout_message'] ) ) {

				die( $itsec_globals['settings']['community_lockout_message'] );

			} else {

				die( __( "Your IP address has been flagged as a threat by the iThemes Security network.", 'it-l10n-better-wp-security' ) );

			}

		} elseif ( $user === true ) { //lockout the user

			if ( isset( $itsec_globals['settings']['user_lockout_message'] ) ) {

				die( $itsec_globals['settings']['user_lockout_message'] );

			} else {

				die( __( 'You have been locked out due to too many invalid login attempts.', 'it-l10n-better-wp-security' ) );

			}

		} else { //just lockout the host

			if ( isset( $itsec_globals['settings']['lockout_message'] ) ) {

				die( $itsec_globals['settings']['lockout_message'] );

			} else {

				die( __( 'error', 'it-l10n-better-wp-security' ) );

			}

		}

	}

	/**
	 * Provides a description of lockout configuration for use in module settings.
	 *
	 * @since 4.0
	 *
	 * @return string the description of settings.
	 */
	public function get_lockout_description() {

		global $itsec_globals;

		$settings = $itsec_globals['settings'];

		$description = sprintf(
			'<h4>%s</h4><p>%s <a href="#global_options">%s</a>.<br /> %s</p><ul><li><strong>%s:</strong> %s</li><li><strong>%s:</strong> %s</li><li><strong>%s:</strong> %s</li><li><strong>%s:</strong> %s</li><li><strong>%s:</strong> %s</li><li><strong>%s:</strong> %s</li></ul>',
			__( 'About Lockouts', 'it-l10n-better-wp-security' ),
			__( 'Your lockout settings can be configured in', 'it-l10n-better-wp-security' ),
			__( 'Global Settings', 'it-l10n-better-wp-security' ),
			__( 'Your current settings are configured as follows:', 'it-l10n-better-wp-security' ),
			__( 'Permanently ban', 'it-l10n-better-wp-security' ),
			( $settings['blacklist'] === true ? __( 'yes', 'it-l10n-better-wp-security' ) : __( 'no', 'it-l10n-better-wp-security' ) ),
			__( 'Number of lockouts before permanent ban', 'it-l10n-better-wp-security' ),
			$settings['blacklist_count'],
			__( 'How long lockouts will be remembered for ban', 'it-l10n-better-wp-security' ),
			$settings['blacklist_period'],
			__( 'Host lockout message', 'it-l10n-better-wp-security' ),
			$settings['lockout_message'],
			__( 'User lockout message', 'it-l10n-better-wp-security' ),
			$settings['user_lockout_message'],
			__( 'Is this computer white-listed', 'it-l10n-better-wp-security' ),
			( $this->is_ip_whitelisted( ITSEC_Lib::get_ip() ) === true ? __( 'yes', 'it-l10n-better-wp-security' ) : __( 'no', 'it-l10n-better-wp-security' ) )
		);

		return $description;

	}

	/**
	 * Shows all lockouts currently in the database.
	 *
	 * @since 4.0
	 *
	 * @param string $type    'all', 'host', or 'user'
	 * @param bool   $current true for all lockouts, false for current lockouts
	 * @param int    $limit   the maximum number of locks to return
	 *
	 * @return array all lockouts in the system
	 */
	public function get_lockouts( $type = 'all', $current = false, $limit = 0 ) {

		global $wpdb, $itsec_globals;

		if ( $type !== 'all' || $current === true ) {
			$where = " WHERE ";
		} else {
			$where = '';
		}

		switch ( $type ) {

			case 'host':
				$type_statement = "`lockout_host` IS NOT NULL && `lockout_host` != ''";
				break;
			case 'user':
				$type_statement = "`lockout_user` != 0";
				break;
			case 'username':
				$type_statement = "`lockout_username` IS NOT NULL && `lockout_username` != ''";
				break;
			default:
				$type_statement = '';
				break;

		}

		if ( $current === true ) {

			if ( $type_statement !== '' ) {
				$and = ' AND ';
			} else {
				$and = '';
			}

			$active = $and . " `lockout_active`=1 AND `lockout_expire_gmt` > '" . date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ) . "'";

		} else {

			$active = '';

		}

		if ( absint( $limit ) > 0 ) {

			$limit = " LIMIT " . absint( $limit );

		} else {

			$limit = '';

		}

		return $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "itsec_lockouts`" . $where . $type_statement . $active . $limit . ";", ARRAY_A );

	}

	/**
	 * Determines whether a given IP address is whitelisted.
	 *
	 * @since  4.0
	 *
	 * @access private
	 *
	 * @param  string $ip_to_check ip to check
	 *
	 * @return boolean               true if whitelisted or false
	 */
	protected function is_ip_whitelisted( $ip_to_check, $current = false ) {

		global $itsec_globals;

		$white_ips = isset( $itsec_globals['settings']['lockout_white_list'] ) ? $itsec_globals['settings']['lockout_white_list'] : array();

		if ( ! is_array( $white_ips ) ) {
			$white_ips = explode( PHP_EOL, $white_ips );
		}

		//Add the server IP address
		if ( isset( $_SERVER['LOCAL_ADDR'] ) ) {

			$white_ips[] = $_SERVER['LOCAL_ADDR'];

		} elseif ( isset( $_SERVER['SERVER_ADDR'] ) ) {

			$white_ips[] = $_SERVER['SERVER_ADDR'];

		}

		if ( $current === true ) {
			$white_ips[] = ITSEC_Lib::get_ip(); //add current user ip to whitelist to check automatically
		}

		$temp = get_site_option( 'itsec_temp_whitelist_ip' );

		if ( $temp !== false ) {

			if ( $temp['exp'] < $itsec_globals['current_time'] ) {

				delete_site_option( 'itsec_temp_whitelist_ip' );

			} else {

				$white_ips[] = filter_var( $temp['ip'],
				                           FILTER_VALIDATE_IP,
				                           FILTER_FLAG_IPV4 );

			}

		}

		if ( is_array( $white_ips ) && sizeof( $white_ips > 0 ) ) {

			foreach ( $white_ips as $white_ip ) {

				$converted_white_ip = ITSEC_Lib::ip_wild_to_mask( $white_ip );

				$check_range = ITSEC_Lib::cidr_to_range( $converted_white_ip );
				$ip_range    = ITSEC_Lib::cidr_to_range( $ip_to_check );

				if ( sizeof( $check_range ) === 2 ) { //range to check

					$check_min = ip2long( $check_range[0] );
					$check_max = ip2long( $check_range[1] );

					if ( sizeof( $ip_range ) === 2 ) {

						$ip_min = ip2long( $ip_range[0] );
						$ip_max = ip2long( $ip_range[1] );

						if ( ( $check_min < $ip_min && $ip_min < $check_max ) || ( $check_min < $ip_max && $ip_max < $check_max ) ) {
							return true;
						}

					} else {

						$ip = ip2long( $ip_range[0] );

						if ( $check_min < $ip && $ip < $check_max ) {
							return true;
						}

					}

				} else { //single ip to check

					$check = ip2long( $check_range[0] );

					if ( sizeof( $ip_range ) === 2 ) {

						$ip_min = ip2long( $ip_range[0] );
						$ip_max = ip2long( $ip_range[1] );

						if ( $ip_min < $check && $check < $ip_max ) {
							return true;
						}

					} else {

						$ip = ip2long( $ip_range[0] );

						if ( $check == $ip ) {
							return true;
						}

					}

				}

			}

		}

		return false;

	}

	/**
	 * Process ajax request to set temp whitelist
	 *
	 * @since 4.3
	 *
	 * @return void
	 */
	public function itsec_temp_whitelist_ajax() {

		global $itsec_globals;

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_temp_whitelist_nonce' ) ) {
			die ();
		}

		$add_temp   = false;
		$current_ip = ITSEC_Lib::get_ip();
		$temp_ip    = get_site_option( 'itsec_temp_whitelist_ip' );

		if ( $temp_ip !== false ) {

			if ( $temp_ip['exp'] < $itsec_globals['current_time'] ) {
				delete_site_option( 'itsec_temp_whitelist_ip' );
				$add_temp = true;
			}

		} else {

			$add_temp = true;

		}

		if ( $add_temp === false ) {

			die( 'error' );

		} else {

			$response = array(
				'ip'  => ITSEC_Lib::get_ip(),
				'exp' => $itsec_globals['current_time'] + 86400,
			);

			add_site_option( 'itsec_temp_whitelist_ip', $response );

			$response['exp']      = human_time_diff( $itsec_globals['current_time'], $response['exp'] );
			$response['message1'] = __( 'Your IP Address', 'it-l10n-better-wp-security' );
			$response['message2'] = __( 'is whitelisted for', 'it-l10n-better-wp-security' );
			$response['message3'] = __( 'Remove IP from Whitelist', 'it-l10n-better-wp-security' );

			die( json_encode( $response ) );

		}

	}

	/**
	 * Process ajax request to release temp whitelist
	 *
	 * @since 4.6
	 *
	 * @return void
	 */
	public function itsec_temp_whitelist_release_ajax() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_temp_whitelist_nonce' ) ) {
			die ();
		}

		delete_site_option( 'itsec_temp_whitelist_ip' );
		die( 'true' );

	}

	/**
	 * Locks out given user or host
	 *
	 * @since 4.0
	 *
	 * @param  string $type     The type of lockout (for user reference)
	 * @param  string $reason   Reason for lockout, for notifications
	 * @param  string $host     Host to lock out
	 * @param  int    $user     user id to lockout
	 * @param string  $username username to lockout
	 *
	 * @return void
	 */
	private function lockout( $type, $reason, $host = null, $user = null, $username = null ) {

		global $wpdb, $itsec_logger, $itsec_globals, $itsec_files;

		$host_expiration = null;
		$user_expiration = null;
		$username        = sanitize_text_field( trim( $username ) );

		if ( $itsec_files->get_file_lock( 'lockout_' . $host . $user . $username ) ) {

			//Do we have a good host to lock out or not
			if ( $host != null && $this->is_ip_whitelisted( sanitize_text_field( $host ) ) === false && ITSEC_Lib::validates_ip_address( $host ) === true ) {
				$good_host = sanitize_text_field( $host );
			} else {
				$good_host = false;
			}

			//Do we have a valid user to lockout or not
			if ( $user !== null && ITSEC_Lib::user_id_exists( intval( $user ) ) === true ) {
				$good_user = intval( $user );
			} else {
				$good_user = false;
			}

			//Do we have a valid username to lockout or not
			if ( $username !== null && $username != '' ) {
				$good_username = $username;
			} else {
				$good_username = false;
			}

			$blacklist_host = false; //assume we're not permanently blcking the host

			//Sanitize the data for later
			$type   = sanitize_text_field( $type );
			$reason = sanitize_text_field( $reason );

			//handle a permanent host ban (if needed)
			if ( isset( $itsec_globals['settings']['blacklist'] ) && $itsec_globals['settings']['blacklist'] === true && $good_host !== false ) { //permanent blacklist

				$blacklist_period = isset( $itsec_globals['settings']['blacklist_period'] ) ? $itsec_globals['settings']['blacklist_period'] * 24 * 60 * 60 : 604800;

				$host_count = 1 + $wpdb->get_var(
						$wpdb->prepare(
							"SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "itsec_lockouts` WHERE `lockout_expire_gmt` > '%s' AND `lockout_host`='%s';",
							date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] - $blacklist_period ),
							$host
						)
					);

				if ( $host_count >= $itsec_globals['settings']['blacklist_count'] && isset( $itsec_globals['settings']['write_files'] ) && $itsec_globals['settings']['write_files'] === true ) {

					$host_expiration = false;

					if ( ! class_exists( 'ITSEC_Ban_Users' ) ) {
						require( trailingslashit( $itsec_globals['plugin_dir'] ) . 'core/modules/ban-users/class-itsec-ban-users.php' );
					}

					ITSEC_Ban_Users::insert_ip( sanitize_text_field( $host ) ); //Send it to the Ban Users module for banning

					$blacklist_host = true; //flag it so we don't do a temp ban as well

				}

			}

			//We have temp bans to perform
			if ( $good_host !== false || $good_user !== false || $good_username || $good_username !== false ) {

				if ( $this->is_ip_whitelisted( sanitize_text_field( $host ) ) ) {

					$whitelisted    = true;
					$expiration     = date( 'Y-m-d H:i:s', 1 );
					$expiration_gmt = date( 'Y-m-d H:i:s', 1 );

				} else {

					$whitelisted    = false;
					$exp_seconds    = ( intval( $itsec_globals['settings']['lockout_period'] ) * 60 );
					$expiration     = date( 'Y-m-d H:i:s', $itsec_globals['current_time'] + $exp_seconds );
					$expiration_gmt = date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] + $exp_seconds );

				}

				if ( $good_host !== false && $blacklist_host === false ) { //temp lockout host

					$host_expiration = $expiration;

					$wpdb->insert(
						$wpdb->base_prefix . 'itsec_lockouts',
						array(
							'lockout_type'       => $type,
							'lockout_start'      => date( 'Y-m-d H:i:s', $itsec_globals['current_time'] ),
							'lockout_start_gmt'  => date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ),
							'lockout_expire'     => $expiration,
							'lockout_expire_gmt' => $expiration_gmt,
							'lockout_host'       => sanitize_text_field( $host ),
						)
					);

					$itsec_logger->log_event( __( 'lockout', 'it-l10n-better-wp-security' ), 10, array(
						'expires' => $expiration, 'expires_gmt' => $expiration_gmt, 'type' => $type
					), sanitize_text_field( $host ) );

				}

				if ( $good_user !== false ) { //blacklist host and temp lockout user

					$user_expiration = $expiration;

					$wpdb->insert(
						$wpdb->base_prefix . 'itsec_lockouts',
						array(
							'lockout_type'       => $type,
							'lockout_start'      => date( 'Y-m-d H:i:s', $itsec_globals['current_time'] ),
							'lockout_start_gmt'  => date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ),
							'lockout_expire'     => $expiration,
							'lockout_expire_gmt' => $expiration_gmt,
							'lockout_host'       => '',
							'lockout_user'       => intval( $user ),
						)
					);

					if ( $whitelisted === false ) {
						$itsec_logger->log_event( 'lockout', 10, array(
							'expires' => $expiration, 'expires_gmt' => $expiration_gmt, 'type' => $type
						), '', '', intval( $user ) );
					} else {
						$itsec_logger->log_event( 'lockout', 10, array(
							__( 'White Listed', 'it-l10n-better-wp-security' ), 'type' => $type
						), '', '', intval( $user ) );
					}

				}

				if ( $good_username !== false ) { //blacklist host and temp lockout username

					$user_expiration = $expiration;

					$wpdb->insert(
						$wpdb->base_prefix . 'itsec_lockouts',
						array(
							'lockout_type'       => $type,
							'lockout_start'      => date( 'Y-m-d H:i:s', $itsec_globals['current_time'] ),
							'lockout_start_gmt'  => date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ),
							'lockout_expire'     => $expiration,
							'lockout_expire_gmt' => $expiration_gmt,
							'lockout_host'       => '',
							'lockout_username'   => $username,
						)
					);

					if ( $whitelisted === false ) {
						$itsec_logger->log_event( 'lockout', 10, array(
							'expires' => $expiration, 'expires_gmt' => $expiration_gmt, 'type' => $type
						), '', '', $username );
					} else {
						$itsec_logger->log_event( 'lockout', 10, array(
							__( 'White Listed', 'it-l10n-better-wp-security' ), 'type' => $type
						), '', '', $username );
					}

				}

				if ( $whitelisted === false ) {

					if ( $itsec_globals['settings']['email_notifications'] === true ) { //send email notifications
						$this->send_lockout_email( $good_host, $good_user, $good_username, $host_expiration, $user_expiration, $reason );
					}

					if ( $good_host !== false ) {

						$itsec_files->release_file_lock( 'lockout_' . $host . $user . $username );
						$this->execute_lock();

					} else {

						$itsec_files->release_file_lock( 'lockout_' . $host . $user . $username );
						$this->execute_lock( true );

					}

				}

			}

			$itsec_files->release_file_lock( 'lockout_' . $host . $user . $username );

		}

	}

	/**
	 * Active lockouts table and form for dashboard.
	 *
	 * @Since 4.0
	 *
	 * @return void
	 */
	public function lockout_metabox() {

		global $itsec_globals;

		?>
		<form method="post" action="" id="itsec_release_lockout_form">
			<?php wp_nonce_field( 'itsec_release_lockout', 'wp_nonce' ); ?>
			<input type="hidden" name="itsec_release_lockout" value="true"/>
			<?php //get locked out hosts and users from database
			$host_locks     = $this->get_lockouts( 'host', true, 50 );
			$user_locks     = $this->get_lockouts( 'user', true, 50 );
			$username_locks = $this->get_lockouts( 'username', true, 50 );
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row" class="settinglabel">
						<?php _e( 'Locked out hosts', 'it-l10n-better-wp-security' ); ?>
					</th>
					<td class="settingfield">
						<?php if ( sizeof( $host_locks ) > 0 ) { ?>
							<ul>
								<?php foreach ( $host_locks as $host ) { ?>
									<li style="list-style: none;"><input type="checkbox"
									                                     name="lo_<?php echo $host['lockout_id']; ?>"
									                                     id="lo_<?php echo $host['lockout_id']; ?>"
									                                     value="<?php echo $host['lockout_id']; ?>"/>
										<label
											for="lo_<?php echo $host['lockout_id']; ?>"><strong><?php echo filter_var( $host['lockout_host'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );; ?></strong>
											- <?php _e( 'Expires in', 'it-l10n-better-wp-security' ); ?>
											<em> <?php echo human_time_diff( $itsec_globals['current_time_gmt'], strtotime( $host['lockout_expire_gmt'] ) ); ?></em></label>
									</li>
								<?php } ?>
							</ul>
						<?php } else { //no host is locked out ?>
							<ul>
								<li style="list-style: none;">
									<p><?php _e( 'Currently no hosts are locked out of this website.', 'it-l10n-better-wp-security' ); ?></p>
								</li>
							</ul>
						<?php } ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="settinglabel">
						<?php _e( 'Locked out users', 'it-l10n-better-wp-security' ); ?>
					</th>
					<td class="settingfield">
						<?php if ( sizeof( $user_locks ) > 0 ) { ?>
							<ul>
								<?php foreach ( $user_locks as $user ) { ?>
									<?php $userdata = get_userdata( $user['lockout_user'] ); ?>
									<li style="list-style: none;"><input type="checkbox"
									                                     name="lo_<?php echo $user['lockout_id']; ?>"
									                                     id="lo_<?php echo $user['lockout_id']; ?>"
									                                     value="<?php echo $user['lockout_id']; ?>"/>
										<label
											for="lo_<?php echo $user['lockout_id']; ?>"><strong><?php echo isset( $userdata->lockout ) ? $userdata->user_login : '';  ?></strong>
											- <?php _e( 'Expires in', 'it-l10n-better-wp-security' ); ?>
											<em> <?php echo human_time_diff( $itsec_globals['current_time_gmt'], strtotime( $user['lockout_expire_gmt'] ) ); ?></em></label>
									</li>
								<?php } ?>
							</ul>
						<?php } else { //no user is locked out ?>
							<ul>
								<li style="list-style: none;">
									<p><?php _e( 'Currently no users are locked out of this website.', 'it-l10n-better-wp-security' ); ?></p>
								</li>
							</ul>
						<?php } ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="settinglabel">
						<?php _e( 'Locked out usernames (not real users)', 'it-l10n-better-wp-security' ); ?>
					</th>
					<td class="settingfield">
						<?php if ( sizeof( $username_locks ) > 0 ) { ?>
							<ul>
								<?php foreach ( $username_locks as $user ) { ?>
									<li style="list-style: none;"><input type="checkbox"
									                                     name="lo_<?php echo $user['lockout_id']; ?>"
									                                     id="lo_<?php echo $user['lockout_id']; ?>"
									                                     value="<?php echo $user['lockout_id']; ?>"/>
										<label
											for="lo_<?php echo $user['lockout_id']; ?>"><strong><?php echo sanitize_text_field( $user['lockout_username'] ); ?></strong>
											- <?php _e( 'Expires in', 'it-l10n-better-wp-security' ); ?>
											<em> <?php echo human_time_diff( $itsec_globals['current_time_gmt'], strtotime( $user['lockout_expire_gmt'] ) ); ?></em></label>
									</li>
								<?php } ?>
							</ul>
						<?php } else { //no user is locked out ?>
							<ul>
								<li style="list-style: none;">
									<p><?php _e( 'Currently no usernames are locked out of this website.', 'it-l10n-better-wp-security' ); ?></p>
								</li>
							</ul>
						<?php } ?>
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary"
			                         value="<?php _e( 'Release Lockout', 'it-l10n-better-wp-security' ); ?>"/></p>
		</form>
	<?php
	}

	/**
	 * Purges lockouts more than 7 days old from the database
	 *
	 * @return void
	 */
	public function purge_lockouts() {

		global $wpdb, $itsec_globals;

		$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "itsec_lockouts` WHERE `lockout_expire_gmt` < '" . date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] - ( ( $itsec_globals['settings']['blacklist_period'] + 1 ) * 24 * 60 * 60 ) ) . "';" );
		$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "itsec_temp` WHERE `temp_date_gmt` < '" . date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] - 86400 ) . "';" );

	}

	/**
	 * Register 404 and file change detection for logger
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array                   array of logger modules
	 */
	public function register_logger( $logger_modules ) {

		$logger_modules['lockout'] = array(
			'type'     => 'lockout',
			'function' => __( 'Host or User Lockout', 'it-l10n-better-wp-security' ),
		);

		return $logger_modules;

	}

	/**
	 * Register Lockouts for Sync
	 *
	 * @param  array $sync_modules array of logger modules
	 *
	 * @return array                   array of logger modules
	 */
	public function register_sync( $sync_modules ) {

		$sync_modules['lockout'] = array(
			'verbs'      => array(
				'itsec-get-lockouts'       => 'Ithemes_Sync_Verb_ITSEC_Get_Lockouts',
				'itsec-release-lockout'    => 'Ithemes_Sync_Verb_ITSEC_Release_Lockout',
				'itsec-get-temp-whitelist' => 'Ithemes_Sync_Verb_ITSEC_Get_Temp_Whitelist',
				'itsec-set-temp-whitelist' => 'Ithemes_Sync_Verb_ITSEC_Set_Temp_Whitelist',
			),
			'everything' => array(
				'itsec-get-lockouts',
				'itsec-get-temp-whitelist',
			),
			'path'       => dirname( __FILE__ ),
		);

		return $sync_modules;

	}

	/**
	 * Register modules that will use the lockout service
	 *
	 * @return void
	 */
	public function register_modules() {

		$this->lockout_modules = apply_filters( 'itsec_lockout_modules', $this->lockout_modules );

	}

	/**
	 * Process clearing lockouts on view log page
	 *
	 * @since 4.0
	 *
	 * @return bool true on success or false
	 */
	public function release_lockout( $id = null ) {

		global $wpdb;

		if ( $id !== null && trim( $id ) !== '' ) {

			$sanitized_id = intval( $id );

			$lockout = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "itsec_lockouts` WHERE lockout_id = " . $sanitized_id . ";", ARRAY_A );

			if ( is_array( $lockout ) && sizeof( $lockout ) >= 1 ) {

				$success = $wpdb->update(
					$wpdb->base_prefix . 'itsec_lockouts',
					array(
						'lockout_active' => 0,
					),
					array(
						'lockout_id' => $sanitized_id,
					)
				);

				return $success === false ? false : true;

			} else {

				return false;

			}

		} elseif ( isset( $_POST['itsec_release_lockout'] ) && $_POST['itsec_release_lockout'] == 'true' ) {

			if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'itsec_release_lockout' ) ) {
				die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
			}

			$type    = 'updated';
			$message = __( 'The selected lockouts have been cleared.', 'it-l10n-better-wp-security' );

			foreach ( $_POST as $key => $value ) {

				if ( strstr( $key, "lo_" ) ) { //see if it's a lockout to avoid processing extra post fields

					$wpdb->update(
						$wpdb->base_prefix . 'itsec_lockouts',
						array(
							'lockout_active' => 0,
						),
						array(
							'lockout_id' => intval( $value ),
						)
					);

				}

			}

			ITSEC_Lib::clear_caches();

			if ( is_multisite() ) {

				$error_handler = new WP_Error();

				$error_handler->add( $type, $message );

				$this->core->show_network_admin_notice( $error_handler );

			} else {

				add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

			}

		}

	}

	/**
	 * Active lockouts table and form for dashboard.
	 *
	 * @Since 4.0
	 *
	 * @return void
	 */
	public function self_protect_metabox() {

		global $itsec_globals;

		echo '<p>' . __( 'Security is a delicate item. It does not care who you are, if it sees that you are trying to do something strange it will lock you out. This can be troublesome on sites with existing errors, particularly missing assets such as images and others.', 'it-l10n-better-wp-security' ) . '</p>';
		echo '<p>' . __( 'Use the button below to temporarily white list your IP from lockouts for 24 hours. It will still notify you of the situation but it will not lock you out of your site allowing you a chance to fix the issue.', 'it-l10n-better-wp-security' ) . '</p>';
		echo '<p>' . __( 'Please note that if your IP address changes at any time during the period (such as you switch locations) you could still inadvertently lock yourself out.', 'it-l10n-better-wp-security' ) . '</p>';

		$temp = get_site_option( 'itsec_temp_whitelist_ip' );

		if ( $temp !== false ) {

			echo '<p class="itsec_temp_whitelist submit">';
			echo __( 'Your IP Address', 'it-l10n-better-wp-security' ) . ', <strong>' . $temp['ip'] . '</strong>, ' . __( 'is whitelisted for', 'it-l10n-better-wp-security' ) . ' <strong>' . human_time_diff( $itsec_globals['current_time'], $temp['exp'] ) . '</strong>.<br />';
			echo '<a href="#" class="itsec_temp_whitelist_release_ajax button-primary">' . __( 'Remove IP from Whitelist', 'it-l10n-better-wp-security' ) . '</a>';
			echo '</p>';

		} else {

			echo '<p class="itsec_temp_whitelist submit"><a href="#" class="itsec_temp_whitelist_ajax button-primary">' . __( 'Temporarily Whitelist my IP', 'it-l10n-better-wp-security' ) . '</a></p>';

		}

	}

	/**
	 * Sends an email to notify site admins of lockouts
	 *
	 * @since 4.0
	 *
	 * @param  string $host            the host to lockout
	 * @param  int    $user            the user id to lockout
	 * @param string  $username        the username to lockout
	 * @param  string $host_expiration when the host login expires
	 * @param  string $user_expiration when the user lockout expires
	 * @param  string $reason          the reason for the lockout to show to the user
	 *
	 * @return void
	 */
	private function send_lockout_email( $host, $user, $username, $host_expiration, $user_expiration, $reason ) {

		global $itsec_notify, $itsec_globals;

		if ( ! isset( $itsec_globals['settings']['digest_email'] ) || $itsec_globals['settings']['digest_email'] === false ) {

			$plural_text = __( 'has', 'it-l10n-better-wp-security' );

			//Tell which host was locked out
			if ( $host !== false ) {

				$host_text = sprintf( '%s, <a href="http://ip-adress.com/ip_tracer/%s"><strong>%s</strong></a>, ', __( 'host', 'it-l10n-better-wp-security' ), sanitize_text_field( $host ), sanitize_text_field( $host ) );

				$host_expiration_text = __( 'The host has been locked out ', 'it-l10n-better-wp-security' );

				if ( $host_expiration === false ) {

					$host_expiration_text .= '<strong>' . __( 'permanently', 'it-l10n-better-wp-security' ) . '</strong>';
					$release_text = sprintf( '%s <a href="%s">%s</a>.', __( 'To release the host lockout you can remove the host from the', 'it-l10n-better-wp-security' ), wp_login_url( get_admin_url( '', 'admin.php?page=toplevel_page_itsec_settings' ) ), __( 'host list', 'it-l10n-better-wp-security' ) );

				} else {

					$host_expiration_text .= sprintf( '<strong>%s %s</strong>', __( 'until', 'it-l10n-better-wp-security' ), sanitize_text_field( $host_expiration ) );
					$release_text = sprintf( '%s <a href="%s">%s</a>.', __( 'To release the lockout please visit', 'it-l10n-better-wp-security' ), wp_login_url( get_admin_url( '', 'admin.php?page=itsec' ) ), __( 'the admin area', 'it-l10n-better-wp-security' ) );

				}

			} else {

				$host_expiration_text = '';
				$host_text            = '';
				$release_text         = '';

			}

			$user_object = get_userdata( $user ); //try to get and actual user object

			//Tell them which user was locked out and setup the expiration copy
			if ( $user_object !== false || $username !== false ) {

				if ( $user_object !== false ) {
					$login = $user_object->user_login;
				} else {
					$login = sanitize_text_field( $username );
				}

				if ( $host_text === '' ) {

					$user_expiration_text = sprintf( '%s <strong>%s %s</strong>.', __( 'The user has been locked out', 'it-l10n-better-wp-security' ), __( 'until', 'it-l10n-better-wp-security' ), sanitize_text_field( $user_expiration ) );

					$user_text = sprintf( '%s, <strong>%s</strong>, ', __( 'user', 'it-l10n-better-wp-security' ), $login );

					$release_text = sprintf( '%s <a href="%s">%s</a>.', __( 'To release the lockout please visit', 'it-l10n-better-wp-security' ), wp_login_url( get_admin_url( '', 'admin.php?page=itsec' ) ), __( 'the lockouts page', 'it-l10n-better-wp-security' ) );

				} else {

					$user_expiration_text = sprintf( '%s <strong>%s %s</strong>.', __( 'and the user has been locked out', 'it-l10n-better-wp-security' ), __( 'until', 'it-l10n-better-wp-security' ), sanitize_text_field( $user_expiration ) );
					$plural_text          = __( 'have', 'it-l10n-better-wp-security' );
					$user_text            = sprintf( '%s, <strong>%s</strong>, ', __( 'and a user', 'it-l10n-better-wp-security' ), $login );

					if ( $host_expiration === false ) {

						$release_text .= sprintf( '%s <a href="%s">%s</a>.', __( 'To release the user lockout please visit', 'it-l10n-better-wp-security' ), wp_login_url( get_admin_url( '', 'admin.php?page=itsec' ) ), __( 'the lockouts page', 'it-l10n-better-wp-security' ) );

					} else {

						$release_text = sprintf( '%s <a href="%s">%s</a>.', __( 'To release the lockouts please visit', 'it-l10n-better-wp-security' ), wp_login_url( get_admin_url( '', 'admin.php?page=itsec' ) ), __( 'the lockouts page', 'it-l10n-better-wp-security' ) );

					}

				}

			} else {

				$user_expiration_text = '.';
				$user_text            = '';
				$release_text         = '';

			}

			//Put the copy all together
			$body = sprintf(
				'<p>%s,</p><p>%s %s %s %s %s <a href="%s">%s</a> %s <strong>%s</strong>.</p><p>%s %s</p><p>%s</p><p><em>*%s %s. %s <a href="%s">%s</a>.</em></p>',
				__( 'Dear Site Admin', 'it-l10n-better-wp-security' ),
				__( 'A', 'it-l10n-better-wp-security' ),
				$host_text,
				$user_text,
				$plural_text,
				__( ' been locked out of the WordPress site at', 'it-l10n-better-wp-security' ),
				get_option( 'siteurl' ),
				get_option( 'siteurl' ),
				__( 'due to', 'it-l10n-better-wp-security' ),
				sanitize_text_field( $reason ),
				$host_expiration_text,
				$user_expiration_text,
				$release_text,
				__( 'This email was generated automatically by' ),
				$itsec_globals['plugin_name'],
				__( 'To change your email preferences please visit', 'it-l10n-better-wp-security' ),
				wp_login_url( get_admin_url( '', 'admin.php?page=toplevel_page_itsec_settings' ) ),
				__( 'the plugin settings', 'it-l10n-better-wp-security' ) );

			//Setup the remainder of the email
			$subject = '[' . get_option( 'siteurl' ) . '] ' . __( 'Site Lockout Notification', 'it-l10n-better-wp-security' );
			$subject = apply_filters( 'itsec_lockout_email_subject', $subject );
			$headers = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' . "\r\n";

			$args = array(
				'headers' => $headers,
				'message' => $body,
				'subject' => $subject,
			);

			$itsec_notify->notify( $args );

		}

	}

	/**
	 * Sets an error message when a user has been forcibly logged out due to lockout
	 *
	 * @return string
	 */
	public function set_lockout_error() {

		global $itsec_globals;

		//check to see if it's the logout screen
		if ( isset( $_GET['itsec'] ) && $_GET['itsec'] == true ) {
			return '<div id="login_error">' . $itsec_globals['settings']['user_lockout_message'] . '</div>' . PHP_EOL;
		}

	}

}
