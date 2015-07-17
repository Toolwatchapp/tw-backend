<?php

class ITSEC_IPCheck_Admin {

	private
		$module_path,
		$settings,
		$core;

	private static $endpoint = 'http://ipcheck-api.ithemes.com/?action=';

	function run( $core ) {

		$this->core        = $core;
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );
		$this->settings    = get_site_option( 'itsec_ipcheck' );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); //enqueue scripts for admin page
		add_action( 'itsec_admin_init', array( $this, 'itsec_admin_init' ) ); //initialize admin area
		add_action( 'wp_ajax_itsec_api_key_ajax', array( $this, 'wp_ajax_itsec_api_key_ajax' ) );

		//manually save options on multisite
		if ( is_multisite() ) {
			add_action( 'itsec_admin_init', array( $this, 'itsec_admin_init_multisite' ) ); //save multisite options
		}

	}

	/**
	 * Activate an IPCheck API Key
	 *
	 * @since 4.5
	 *
	 * @param string $api_key the API key to activate
	 *
	 * @return bool|string IPCheck activation secret or false if there is an error
	 */
	private function activate_api_key( $api_key ) {

		$activated = isset( $this->settings['api_s'] ) ? $this->settings['api_s'] : false;

		//if we already have an api secret just return it
		if ( $activated != false && $activated != '' ) { //see if the key was already saved correctly
			return $activated;
		}

		//Return false if we don't have an api key to check
		if ( $api_key == false || $api_key == '' ) {
			return false;
		}

		$response = wp_remote_get( self::$endpoint . 'activate-key&apikey=' . trim( sanitize_text_field( $api_key ) ) . '&site=' . home_url( '', 'http' ) );

		if ( is_array( $response ) && isset( $response['body'] ) ) { //verify we have a good response body

			$body = json_decode( $response['body'], true );

			if ( is_array( $body ) && isset( $body['secret'] ) ) { //verify we retrieved an apikey

				$activated = trim( sanitize_text_field( $body['secret'] ) );

				return $activated;

			}

		}

		return false; //assume an error

	}

	/**
	 * Add IPCheck admin Javascript
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && ( strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) !== false ) ) {

			wp_enqueue_script( 'itsec_ipcheck_js', $this->module_path . 'js/admin-ipcheck.js', array( 'jquery' ), $itsec_globals['plugin_build'] );
			wp_localize_script(
				'itsec_ipcheck_js',
				'itsec_ipcheck',
				array(
					'api_nonce' => wp_create_nonce( 'itsec_api_nonce' ),
					'text1'     => __( 'Receive email updates from iThemes', 'it-l10n-better-wp-security' ),
					'text2'     => __( 'Leverage the power of the iThemes Brute Force Protection network to ban IPs hitting your site.', 'it-l10n-better-wp-security' ),
					'text3'     => __( 'Enter email and click Save All Changes', 'it-l10n-better-wp-security' ),
				)
			);

		}

	}

	/**
	 * Retrieve and API key from the IPCheck server
	 *
	 * @since 4.5
	 *
	 * @param string $email the email address to associate with the key
	 * @param bool   $optin true to optin to mailing list else false
	 *
	 * @return bool|string the api key retrieced or false
	 */
	private function get_api_key( $email, $optin = true ) {

		$email      = sanitize_text_field( trim( $email ) );
		$good_email = is_email( $email ); //check that email is valid

		if ( $good_email !== false ) {

			$response = wp_remote_get( self::$endpoint . 'request-key&email=' . $email . '&optin=' . $optin );

			if ( is_array( $response ) && isset( $response['body'] ) ) { //verify we have a good response body

				$body = json_decode( $response['body'], true );

				if ( is_array( $body ) && isset( $body['apikey'] ) ) { //verify we retrieved an apikey

					$key = trim( sanitize_text_field( $body['apikey'] ) );

					if ( strlen( $key ) > 0 ) {

						return $key;

					}

				}

			}

		}

		return false;

	}

	/**
	 * Execute admin initializations
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function itsec_admin_init() {

		//Add Settings sections
		add_settings_section(
			'ipcheck-settings-brute-force',
			__( 'Get your iThemes Brute Force Protection API Key', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		/*
		add_settings_section(
			'ipcheck-settings-malware',
			__( 'Get your iThemes Brute Force Protection API Key', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);
		*/

		//Add Settings Fields
		add_settings_field(
			'itsec_ipcheck',
			__( 'Get your iThemes Brute Force Protection API Key', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_api_key_brute_force' ),
			'security_page_toplevel_page_itsec_settings',
			'ipcheck-settings-brute-force'
		);

		/*
		add_settings_field(
			'itsec_ipcheck',
			__( 'Get your iThemes Malware API Key', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_api_key_malware' ),
			'security_page_toplevel_page_itsec_settings',
			'ipcheck-settings-malware'
		);
		*/

		if ( isset( $this->settings['api_key'] ) && isset( $this->settings['api_s'] ) ) {

			add_settings_field(
				'itsec_ipcheck[api_ban]',
				__( 'Enable iThemes Brute Force Network Protection', 'it-l10n-better-wp-security' ),
				array( $this, 'settings_field_api_ban' ),
				'security_page_toplevel_page_itsec_settings',
				'ipcheck-settings-brute-force'
			);

		}

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_ipcheck',
			array( $this, 'sanitize_module_input' )
		);

	}

	/**
	 * Prepare and save options in network settings
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function itsec_admin_init_multisite() {

		if ( isset( $_POST['itsec_ipcheck'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
			}

			update_site_option( 'itsec_ipcheck', $_POST['itsec_ipcheck'] ); //we must manually save network options

		}

	}

	/**
	 * Sanitize and validate input
	 *
	 * @since 4.5
	 *
	 * @param  Array $input array of input fields
	 *
	 * @return Array         Sanitized array
	 */
	public function sanitize_module_input( $input ) {

		$input['api_ban']          = isset( $input['api_ban'] ) && $input['api_ban'] == 1 ? true : false;
		$this->settings['api_ban'] = $input['api_ban'];

		if ( isset( $input['reset'] ) ) {

			unset( $input['api_key'] );
			unset( $input['api_s'] );
			unset( $input['email'] );
			unset( $input['reset'] );

		} elseif ( ( isset( $input['email-brute-force'] ) && strlen( trim( $input['email-brute-force'] ) ) > 0 ) || ( isset( $input['email-malware'] ) && strlen( trim( $input['email-malware'] ) ) > 0 ) ) {

			if ( isset( $input['email-brute-force'] ) && strlen( trim( $input['email-brute-force'] ) ) > 0 ) {

				$raw_email = trim( sanitize_text_field( $input['email-brute-force'] ) );
				$optin     = isset( $input['optin-brute-force'] ) && $input['optin-brute-force'] == 1 ? true : false;

			} else {

				$raw_email = trim( sanitize_text_field( $input['email-malware'] ) );
				$optin     = isset( $input['optin-malware'] ) && $input['optin-malware'] == 1 ? true : false;

			}

			//We don't need to save the email addresses
			unset( $input['email-brute-force'] );
			unset( $input['email-malware'] );

			//but make sure we keep the optin
			$input['optin'] = $optin;

			if ( strlen( $raw_email ) > 0 ) {

				$email     = is_email( $raw_email ) ? $raw_email : false;
				$api_error = false;

				if ( $email === false ) {

					$type    = 'error';
					$message = __( 'The email address you used to get an iThemes API key appears to be invalid. Please enter a valid email address', 'it-l10n-better-wp-security' );

					add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

				} else {

					$key = $this->get_api_key( $email, $optin );

					if ( $key != false && $key != '' ) {

						$input['api_key'] = $key;

						$api_s = $this->activate_api_key( $key );

						if ( $api_s != false && $api_s != '' ) {

							$input['api_s']   = $api_s;
							$input['api_ban'] = true;

						} else {

							unset( $input['api_key'] );
							$api_error = true;

						}

					} else {

						$api_error = true;

					}

				}

				if ( $api_error === true && ! isset( $input['api_key'] ) && ! isset( $input['api_s'] ) ) {

					$type    = 'error';
					$message = __( 'There was an error getting an API key from the IPCheck server. If the problem persists please contact support.', 'it-l10n-better-wp-security' );

					add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

				}

				unset( $input['optin'] );

			}

		} else {

			$input = $this->settings;

		}

		if ( is_multisite() ) {

			$this->core->show_network_admin_notice( false );

			$this->settings = $input;

		}

		return $input;

	}

	/**
	 * echos api ban Field in brute force
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function settings_field_api_ban() {

		if ( isset( $this->settings['api_ban'] ) && $this->settings['api_ban'] === true ) {
			$api_ban = 1;
		} else {
			$api_ban = 0;
		}

		echo '<input type="checkbox" id="itsec_ipcheck_api_ban" name="itsec_ipcheck[api_ban]" value="1" ' . checked( 1, $api_ban, false ) . '/>';
		echo '<label for="itsec_ipcheck_api_ban"> ' . __( 'Use the iThemes IPCheck Service to ban IPs reported as a problem by other users in the community.', 'it-l10n-better-wp-security' ) . '</label>';

	}

	/**
	 * echos api key Field in brute force settings
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function settings_field_api_key_brute_force() {

		$this->settings_field_api_key_contents( 'brute-force' );

	}

	/**
	 * Echos the field contents
	 *
	 * @since 4.5
	 *
	 * @param string $module the module
	 *
	 * @return void
	 */
	private function settings_field_api_key_contents( $module ) {

		$api_key   = isset( $this->settings['api_key'] ) ? trim( sanitize_text_field( $this->settings['api_key'] ) ) : false;
		$activated = isset( $this->settings['api_s'] ) ? trim( sanitize_text_field( $this->settings['api_s'] ) ) : false;
		$email     = isset( $this->settings['email'] ) && is_email( $this->settings['email'] ) ? trim( $this->settings['email'] ) : '';

		if ( $api_key == '' || $api_key == false || $activated == '' || $activated == false ) {

			echo '<input type="text" class="regular-text" id="itsec_ipcheck_email" name="itsec_ipcheck[email-' . $module . ']" value="' . $email . '" placeholder="' . __( 'Enter email and click Save All Changes', 'it-l10n-better-wp-security' ) . '" /><br />';
			echo '<input type="checkbox" id="itsec_ipcheck_optin" name="itsec_ipcheck[optin-' . $module . ']" value="1" checked/>';
			echo '<label for="itsec_ipcheck_optin">' . __( 'Receive email updates about WP Security from iThemes', 'it-l10n-better-wp-security' ) . '</label>';

			echo '<p class="description">' . __( 'Leverage the power of the iThemes Brute Force Protection network to ban IPs hitting your site. ', 'it-l10n-better-wp-security' ) . '</p>';

		} else {

			echo '<div class="itsec_api_key_field">';
			echo '<input type="text" class="regular-text" id="itsec_ipcheck_api_key" name="itsec_ipcheck[api_key]" value="' . $api_key . '" readonly />';
			echo '<span class="submit"><a class="itsec_reset_ipcheck_api_key button-primary">' . __( 'Reset API Key', 'it-l10n-better-wp-security' ) . '</a></span>';
			echo '<p class="description">' . __( 'Your API key for the iThemes Security community appears to be valid.', 'it-l10n-better-wp-security' ) . '</p>';
			echo '</div>';

		}

	}

	/**
	 * echos api key Field in malware settings
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function settings_field_api_key_malware() {

		$this->settings_field_api_key_contents( 'malware' );

	}

	/**
	 * Resets API Key
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function wp_ajax_itsec_api_key_ajax() {

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_api_nonce' ) ) {
			die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
		}

		$this->settings['reset'] = true;

		update_site_option( 'itsec_ipcheck', $this->settings );

		die( true );

	}

}