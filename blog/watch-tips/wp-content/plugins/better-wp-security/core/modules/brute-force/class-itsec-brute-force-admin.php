<?php

class ITSEC_Brute_Force_Admin {

	private
		$settings,
		$core,
		$module_path;

	function run( $core ) {

		$this->core        = $core;
		$this->settings    = get_site_option( 'itsec_brute_force' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); //enqueue scripts for admin page
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'itsec_add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_action( 'itsec_admin_init', array( $this, 'itsec_admin_init' ) ); //initialize admin area

		add_filter( 'itsec_add_dashboard_status', array( $this, 'itsec_add_dashboard_status' ) ); //add information for plugin status
		add_filter( 'itsec_logger_displays', array( $this, 'itsec_logger_displays' ) ); //adds logs metaboxes
		add_filter( 'itsec_one_click_settings', array( $this, 'itsec_one_click_settings' ) );
		add_filter( 'itsec_tracking_vars', array( $this, 'itsec_tracking_vars' ) );

		//manually save options on multisite
		if ( is_multisite() ) {
			add_action( 'itsec_admin_init', array( $this, 'save_network_options' ) ); //save multisite options
		}

	}

	/**
	 * Add Away mode Javascript
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) !== false ) {

			wp_enqueue_script( 'itsec_brute_force_js', $this->module_path . 'js/admin-brute-force.js', array( 'jquery' ), $itsec_globals['plugin_build'] );

		}

	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * @return void
	 */
	public function itsec_add_admin_meta_boxes() {

		$id    = 'brute_force_options';
		$title = __( 'Brute Force Protection', 'it-l10n-better-wp-security' );

		add_meta_box(
			$id,
			$title,
			array( $this, 'metabox_brute_force_settings' ),
			'security_page_toplevel_page_itsec_settings',
			'advanced',
			'core'
		);

		$this->core->add_toc_item(
		           array(
			           'id'    => $id,
			           'title' => $title,
		           )
		);

	}

	/**
	 * Sets the status in the plugin dashboard
	 *
	 * @since 4.0
	 *
	 * @param array $statuses array of statuses
	 *
	 * @return array array of statuses
	 */
	public function itsec_add_dashboard_status( $statuses ) {

		$ipcheck = get_site_option( 'itsec_ipcheck' );
		$api_ban = false;

		if ( class_exists( 'ITSEC_IPCheck_Admin' ) && isset( $ipcheck['api_key'] ) && isset( $ipcheck['api_s'] ) && isset( $ipcheck['api_ban'] ) && $ipcheck['api_ban'] === true ) {
			$api_ban = true;
		}

		if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true && $api_ban === true ) {

			$status_array = 'safe-high';
			$status       = array( 'text' => __( 'Your login area is protected from brute force attacks.', 'it-l10n-better-wp-security' ), 'link' => '#itsec_brute_force_settings', );

		} elseif ( ( ( ! isset( $this->settings['enabled'] ) || $this->settings['enabled'] === false ) && $api_ban === true ) || ( ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) && $api_ban === false ) ) {

			$status_array = 'medium';
			$status       = array( 'text' => __( 'Your login area is partially protected from brute force attacks. We recommend you use both network and local blocking for full security.', 'it-l10n-better-wp-security' ), 'link' => '#itsec_brute_force_settings', );

		} else {

			$status_array = 'high';
			$status       = array( 'text' => __( 'Your login area is not protected from brute force attacks.', 'it-l10n-better-wp-security' ), 'link' => '#itsec_brute_force_settings', );

		}

		array_push( $statuses[$status_array], $status );

		return $statuses;

	}

	/**
	 * Execute admin initializations
	 *
	 * @return void
	 */
	public function itsec_admin_init() {

		//Add Settings sections
		add_settings_section(
			'brute_force-enabled',
			__( 'Enable Brute Force Protection', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'brute_force-settings',
			__( 'Brute Force Protection Settings', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		//Brute Force Protection Fields
		add_settings_field(
			'itsec_brute_force[enabled]',
			__( 'Enable local brute force protection', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_enabled' ),
			'security_page_toplevel_page_itsec_settings',
			'brute_force-enabled'
		);

		add_settings_field(
			'itsec_brute_force[max_attempts_host]',
			__( 'Max Login Attempts Per Host', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_max_attempts_host' ),
			'security_page_toplevel_page_itsec_settings',
			'brute_force-settings'
		);

		add_settings_field(
			'itsec_brute_force[max_attempts_user]',
			__( 'Max Login Attempts Per User', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_max_attempts_user' ),
			'security_page_toplevel_page_itsec_settings',
			'brute_force-settings'
		);

		add_settings_field(
			'itsec_brute_force[check_period]',
			__( 'Minutes to Remember Bad Login (check period)', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_check_period' ),
			'security_page_toplevel_page_itsec_settings',
			'brute_force-settings'
		);

		add_settings_field(
			'itsec_brute_force[auto_ban_admin]',
			__( 'Automatically ban "admin" user', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_auto_ban_admin' ),
			'security_page_toplevel_page_itsec_settings',
			'brute_force-settings'
		);

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_brute_force',
			array( $this, 'sanitize_module_input' )
		);

	}

	/**
	 * Array of metaboxes for the logs screen
	 *
	 * @since 4.0
	 *
	 * @param object $displays metabox array
	 *
	 * @return array metabox array
	 */
	public function itsec_logger_displays( $displays ) {

		//Don't attempt to display logs if brute force isn't enabled
		if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {

			$displays[] = array(
				'module'   => 'brute_force',
				'title'    => __( 'Invalid Login Attempts', 'it-l10n-better-wp-security' ),
				'callback' => array( $this, 'logs_metabox_content' ),
			);

		}

		return $displays;

	}

	/**
	 * Register one-click settings
	 *
	 * @since 4.0
	 *
	 * @param array $one_click_settings array of one-click settings
	 *
	 * @return array array of one-click settings
	 */
	public function itsec_one_click_settings( $one_click_settings ) {

		$one_click_settings['itsec_brute_force'][] = array(
			'option' => 'enabled',
			'value'  => 1,
		);

		return $one_click_settings;

	}

	/**
	 * Adds fields that will be tracked for Google Analytics
	 *
	 * @since 4.0
	 *
	 * @param array $vars tracking vars
	 *
	 * @return array tracking vars
	 */
	public function itsec_tracking_vars( $vars ) {

		$vars['itsec_brute_force'] = array(
			'enabled' => '0:b',
		);

		return $vars;

	}

	/**
	 * Render the settings metabox
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function logs_metabox_content() {

		if ( ! class_exists( 'ITSEC_Brute_Force_Log' ) ) {
			require( dirname( __FILE__ ) . '/class-itsec-brute-force-log.php' );
		}

		$log_display = new ITSEC_Brute_Force_Log();
		$log_display->prepare_items();
		$log_display->display();

	}

	/**
	 * Render the settings metabox
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_brute_force_settings() {

		global $itsec_lockout;

		echo '<div id="itsec_brute_force_settings">';

		echo '<p>' . __( 'If one had unlimited time and wanted to try an unlimited number of password combinations to get into your site they eventually would, right? This method of attack, known as a brute force attack, is something that WordPress is acutely susceptible by default as the system doesn\'t care how many attempts a user makes to login. It will always let you try again. Enabling login limits will ban the host user from attempting to login again after the specified bad login threshold has been reached.', 'it-l10n-better-wp-security' ) . '</p>';

		echo '<p><strong>' . __( 'Network vs Local Brute Force Protection', 'it-l10n-better-wp-security' ) . '</strong><br />';
		echo __( 'Local brute force protection looks only at attempts to access your site and bans users per the lockout rules specified locally. Network brute force protection takes this a step further by banning users who have tried to break into other sites from breaking into yours. The network protection will automatically report the IP addresses of failed login attempts to iThemes and will block them for a length of time necessary to protect your site based on the number of other sites that have seen a similar attack.', 'it-l10n-better-wp-security' ) . '</p>';

		if ( class_exists( 'ITSEC_IPCheck_Admin' ) ) {
			$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'ipcheck-settings-brute-force', false ); //show ipcheck settings if the module is present
		}

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'brute_force-enabled', false );
		echo '<div class="itsec_brute_force_lockout_information">' . $itsec_lockout->get_lockout_description() . '</div>';
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'brute_force-settings', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save All Changes', 'it-l10n-better-wp-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

		echo '</div>';

	}

	/**
	 * Sanitize and validate input
	 *
	 * @param  Array $input array of input fields
	 *
	 * @return Array         Sanitized array
	 */
	public function sanitize_module_input( $input ) {

		//process brute force settings
		$input['enabled']           = ( isset( $input['enabled'] ) && intval( $input['enabled'] == 1 ) ? true : false );
		$input['auto_ban_admin']    = ( isset( $input['auto_ban_admin'] ) && intval( $input['auto_ban_admin'] == 1 ) ? true : false );
		$input['max_attempts_host'] = isset( $input['max_attempts_host'] ) ? absint( $input['max_attempts_host'] ) : 5;
		$input['max_attempts_user'] = isset( $input['max_attempts_user'] ) ? absint( $input['max_attempts_user'] ) : 10;
		$input['check_period']      = isset( $input['check_period'] ) ? absint( $input['check_period'] ) : 5;

		if ( is_multisite() ) {

			$this->core->show_network_admin_notice( false );

			$this->settings = $input;

		}

		return $input;

	}

	/**
	 * Prepare and save options in network settings
	 *
	 * @return void
	 */
	public function save_network_options() {

		if ( isset( $_POST['itsec_brute_force'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
			}

			update_site_option( 'itsec_brute_force', $_POST['itsec_brute_force'] ); //we must manually save network options

		}

	}

	/**
	 * echos Auto ban admin login Field
	 *
	 * @since 4.3
	 *
	 * @return void
	 */
	public function settings_field_auto_ban_admin() {

		if ( isset( $this->settings['auto_ban_admin'] ) && $this->settings['auto_ban_admin'] === true ) {

			$auto_ban_admin = 1;

		} else {

			$auto_ban_admin = 0;

		}

		if ( ! username_exists( 'admin' ) ) {

			echo '<input type="checkbox" id="itsec_brute_force_auto_ban_admin" name="itsec_brute_force[auto_ban_admin]" value="1" ' . checked( 1, $auto_ban_admin, false ) . '/>';
			echo '<label for="itsec_brute_force_auto_ban_admin"> ' . __( 'Immediately ban a host that attempts to login using the "admin" username.', 'it-l10n-better-wp-security' ) . '</label>';

		} else {

			echo '<p>' . __( 'You are still using an account with the username "admin." Please rename it before using this feature', 'it-l10n-better-wp-security' ) . '</p>';

		}

	}

	/**
	 * echos Check Period Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function settings_field_check_period() {

		if ( isset( $this->settings['check_period'] ) ) {

			$check_period = absint( $this->settings['check_period'] );

		} else {

			$check_period = 5;

		}

		echo '<input class="small-text" name="itsec_brute_force[check_period]" id="itsec_brute_force_check_period" value="' . $check_period . '" type="text"> ';
		echo '<label for="itsec_brute_force_check_period"> ' . __( 'Minutes', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description"> ' . __( 'The number of minutes in which bad logins should be remembered.', 'it-l10n-better-wp-security' ) . '</p>';

	}

	/**
	 * echos Enable Brute Force Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function settings_field_enabled() {

		if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {

			$enabled = 1;

		} else {

			$enabled = 0;

		}

		echo '<input type="checkbox" id="itsec_brute_force_enabled" name="itsec_brute_force[enabled]" value="1" ' . checked( 1, $enabled, false ) . '/>';
		echo '<label for="itsec_brute_force_enabled"> ' . __( 'Enable local brute force protection.', 'it-l10n-better-wp-security' ) . '</label>';

	}

	/**
	 * echos Max Attempts per host  Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function settings_field_max_attempts_host() {

		if ( isset( $this->settings['max_attempts_host'] ) ) {

			$max_attempts_host = absint( $this->settings['max_attempts_host'] );

		} else {

			$max_attempts_host = 5;

		}

		echo '<input class="small-text" name="itsec_brute_force[max_attempts_host]" id="itsec_brute_force_max_attempts_host" value="' . $max_attempts_host . '" type="text"> ';
		echo '<label for="itsec_brute_force_max_attempts_host"> ' . __( 'Attempts', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description"> ' . __( 'The number of login attempts a user has before their host or computer is locked out of the system. Set to 0 to record bad login attempts without locking out the host.', 'it-l10n-better-wp-security' ) . '</p>';

	}

	/**
	 * echos Max Attempts per user  Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function settings_field_max_attempts_user() {

		if ( isset( $this->settings['max_attempts_user'] ) ) {

			$max_attempts_user = absint( $this->settings['max_attempts_user'] );

		} else {

			$max_attempts_user = 10;

		}

		echo '<input class="small-text" name="itsec_brute_force[max_attempts_user]" id="itsec_brute_force_max_attempts_user" value="' . $max_attempts_user . '" type="text"> ';
		echo '<label for="itsec_brute_force_max_attempts_user"> ' . __( 'Attempts', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description"> ' . __( 'The number of login attempts a user has before their username is locked out of the system. Note that this is different from hosts in case an attacker is using multiple computers. In addition, if they are using your login name you could be locked out yourself. Set to zero to log bad login attempts per user without ever locking the user out (this is not recommended)', 'it-l10n-better-wp-security' ) . '</p>';

	}

}