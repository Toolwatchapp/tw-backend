<?php

class ITSEC_Four_Oh_Four_Admin {

	private
		$default_white_list,
		$settings,
		$core,
		$module_path;

	function run( $core ) {

		$this->core        = $core;
		$this->settings    = get_site_option( 'itsec_four_oh_four' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		$this->default_white_list = array(
			'/favicon.ico',
			'/robots.txt',
			'/apple-touch-icon.png',
			'/apple-touch-icon-precomposed.png',
		);

		add_action( 'itsec_add_admin_meta_boxes', array(
			$this, 'add_admin_meta_boxes'
		) ); //add meta boxes to admin page
		add_action( 'itsec_admin_init', array( $this, 'initialize_admin' ) ); //initialize admin area
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) ); //enqueue scripts for admin page
		add_filter( 'itsec_add_dashboard_status', array(
			$this, 'dashboard_status'
		) ); //add information for plugin status
		add_filter( 'itsec_logger_displays', array( $this, 'register_logger_displays' ) ); //adds logs metaboxes
		add_filter( 'itsec_tracking_vars', array( $this, 'tracking_vars' ) );

		//manually save options on multisite
		if ( is_multisite() ) {
			add_action( 'itsec_admin_init', array( $this, 'save_network_options' ) ); //save multisite options
		}

	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * @return void
	 */
	public function add_admin_meta_boxes() {

		$id    = 'intrusion_detection_404_options';
		$title = __( '404 Detection', 'it-l10n-better-wp-security' );

		add_meta_box(
			$id,
			$title,
			array( $this, 'metabox_advanced_four_oh_four_settings' ),
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
	 * Add Files Admin Javascript
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function admin_script() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id,
		                                                  'security_page_toplevel_page_itsec_settings' ) !== false
		) {

			wp_enqueue_script( 'itsec_four_oh_four_js', $this->module_path . 'js/admin-four-oh-four.js',
			                   array( 'jquery' ), $itsec_globals['plugin_build'] );

		}

	}

	/**
	 * Sets the status in the plugin dashboard
	 *
	 * @since 4.0
	 *
	 * @return array statuses
	 */
	public function dashboard_status( $statuses ) {

		if ( $this->settings['enabled'] === true ) {

			$status_array = 'safe-medium';
			$status       = array(
				'text' => __( 'Your site is protecting against bots looking for known vulnerabilities.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_four_oh_four_enabled',
			);

		} else {

			$status_array = 'medium';
			$status       = array(
				'text' => __( 'Your website is not protected against bots looking for known vulnerabilities. Consider turning on 404 protection.',
				              'it-l10n-better-wp-security' ), 'link' => '#itsec_four_oh_four_enabled',
			);

		}

		array_push( $statuses[$status_array], $status );

		return $statuses;

	}

	/**
	 * echos Check Period Field
	 *
	 * @param  array $args field arguments
	 *
	 * @return void
	 */
	public function check_period() {

		if ( isset( $this->settings['check_period'] ) ) {
			$check_period = absint( $this->settings['check_period'] );
		} else {
			$check_period = 5;
		}

		$content = '<input class="small-text" name="itsec_four_oh_four[check_period]" id="itsec_four_oh_four_check_period" value="' . $check_period . '" type="text"> ';
		$content .= '<label for="itsec_four_oh_four_check_period"> ' . __( 'Minutes', 'it-l10n-better-wp-security' ) . '</label>';
		$content .= '<p class="description"> ' . __( 'The number of minutes in which 404 errors should be remembered and counted towards lockouts.',
		                                             'it-l10n-better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Enable 404 Detection Field
	 *
	 * @param  array $args field arguments
	 *
	 * @return void
	 */
	public function enabled() {

		if ( ( get_option( 'permalink_structure' ) == '' || get_option( 'permalink_structure' ) == false ) && ! is_multisite() ) {

			$adminurl = is_multisite() ? admin_url() . 'network/' : admin_url();

			$content = sprintf( '<p class="noPermalinks">%s <a href="%soptions-permalink.php">%s</a> %s</p>',
			                    __( 'You must turn on', 'it-l10n-better-wp-security' ), $adminurl, __( 'WordPress permalinks', 'it-l10n-better-wp-security' ),
			                    __( 'to use this feature.', 'it-l10n-better-wp-security' ) );

		} else {

			if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {
				$enabled = 1;
			} else {
				$enabled = 0;
			}

			$content = '<input type="checkbox" id="itsec_four_oh_four_enabled" name="itsec_four_oh_four[enabled]" value="1" ' . checked( 1,
			                                                                                                                             $enabled,
			                                                                                                                             false ) . '/>';
			$content .= '<label for="itsec_four_oh_four_enabled"> ' . __( 'Enable 404 detection', 'it-l10n-better-wp-security' ) . '</label>';

		}

		echo $content;

	}

	/**
	 * echos Error Threshold Field
	 *
	 * @param  array $args field arguments
	 *
	 * @return void
	 */
	public function error_threshold() {

		if ( isset( $this->settings['error_threshold'] ) ) {
			$error_threshold = absint( $this->settings['error_threshold'] );
		} else {
			$error_threshold = 20;
		}

		$content = '<input class="small-text" name="itsec_four_oh_four[error_threshold]" id="itsec_four_oh_four_error_threshold" value="' . $error_threshold . '" type="text"> ';
		$content .= '<label for="itsec_four_oh_four_error_threshold"> ' . __( 'Errors', 'it-l10n-better-wp-security' ) . '</label>';
		$content .= '<p class="description"> ' . __( 'The numbers of errors (within the check period time frame) that will trigger a lockout. Set to zero (0) to record 404 errors without locking out users. This can be useful for troubleshooting content or other errors. The default is 20.',
		                                             'it-l10n-better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function logs_metabox_content() {

		if ( ! class_exists( 'ITSEC_Four_Oh_Four_Log' ) ) {
			require( dirname( __FILE__ ) . '/class-itsec-four-oh-four-log.php' );
		}

		$log_display = new ITSEC_Four_Oh_Four_Log();

		$log_display->prepare_items();
		$log_display->display();

	}

	/**
	 * echos 404 white list field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function white_list() {

		if ( isset( $this->settings['white_list'] ) && is_array( $this->settings['white_list'] ) ) {
			$white_list = implode( PHP_EOL, $this->settings['white_list'] );
		} else {
			$white_list = implode( PHP_EOL, $this->default_white_list );
		}

		$content = '<textarea id="itsec_four_oh_four_white_list" name="itsec_four_oh_four[white_list]" rows="10" cols="50">' . $white_list . PHP_EOL . '</textarea>';
		$content .= '<p class="description">' . __( 'Use the white list above to prevent recording common 404 errors. If you know a common file on your site is missing and you do not want it to count towards a lockout record it here. You must list the full path beginning with the "/"', 'it-l10n-better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * Execute admin initializations
	 *
	 * @return void
	 */
	public function initialize_admin() {

		//Add Settings sections
		add_settings_section(
			'four_oh_four-enabled',
			__( 'Enable 404 Detection', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'four_oh_four-settings',
			__( '404 Detection Settings', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		//404 Detection Fields
		add_settings_field(
			'itsec_four_oh_four[enabled]',
			__( '404 Detection', 'it-l10n-better-wp-security' ),
			array( $this, 'enabled' ),
			'security_page_toplevel_page_itsec_settings',
			'four_oh_four-enabled'
		);

		add_settings_field(
			'itsec_four_oh_four[check_period]',
			__( 'Minutes to Remember 404 Error (Check Period)', 'it-l10n-better-wp-security' ),
			array( $this, 'check_period' ),
			'security_page_toplevel_page_itsec_settings',
			'four_oh_four-settings'
		);

		add_settings_field(
			'itsec_four_oh_four[error_threshold]',
			__( 'Error Threshold', 'it-l10n-better-wp-security' ),
			array( $this, 'error_threshold' ),
			'security_page_toplevel_page_itsec_settings',
			'four_oh_four-settings'
		);

		add_settings_field(
			'itsec_four_oh_four[white_list]',
			__( '404 File/Folder White List', 'it-l10n-better-wp-security' ),
			array( $this, 'white_list' ),
			'security_page_toplevel_page_itsec_settings',
			'four_oh_four-settings'
		);

		add_settings_field(
			'itsec_four_oh_four[types]',
			__( 'Ignored File Types', 'it-l10n-better-wp-security' ),
			array( $this, 'types' ),
			'security_page_toplevel_page_itsec_settings',
			'four_oh_four-settings'
		);

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_four_oh_four',
			array( $this, 'sanitize_module_input' )
		);

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function metabox_advanced_four_oh_four_settings() {

		global $itsec_lockout;

		echo '<p>' . __( '404 detection looks at a user who is hitting a large number of non-existent pages and getting a large number of 404 errors. 404 detection assumes that a user who hits a lot of 404 errors in a short period of time is scanning for something (presumably a vulnerability) and locks them out accordingly. This also gives the added benefit of helping you find hidden problems causing 404 errors on unseen parts of your site. All errors will be logged in the \"View Logs\" page. You can set thresholds for this feature below.', 'it-l10n-better-wp-security' ) . '</p>';
		echo $itsec_lockout->get_lockout_description();

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'four_oh_four-enabled', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'four_oh_four-settings',
		                                  false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save All Changes',
		                                                                               'it-l10n-better-wp-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

	}

	/**
	 * Array of displays for the logs screen
	 *
	 * @since 4.0
	 *
	 * @param array $logger_displays metabox array
	 *
	 * @return array metabox array
	 */
	public function register_logger_displays( $logger_displays ) {

		//Don't attempt to display logs if brute force isn't enabled
		if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {

			$logger_displays[] = array(
				'module'   => 'four_oh_four',
				'title'    => __( '404 Errors Found', 'it-l10n-better-wp-security' ),
				'callback' => array( $this, 'logs_metabox_content' )
			);

		}

		return $logger_displays;

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
		$input['enabled']         = ( isset( $input['enabled'] ) && intval( $input['enabled'] == 1 ) ? true : false );
		$input['check_period']    = isset( $input['check_period'] ) ? absint( $input['check_period'] ) : 5;
		$input['error_threshold'] = isset( $input['error_threshold'] ) ? absint( $input['error_threshold'] ) : 20;

		if ( isset ( $input['white_list'] ) ) {

			if ( ! is_array( $input['white_list'] ) ) {
				$raw_paths = explode( PHP_EOL, $input['white_list'] );
			} else {
				$raw_paths = $input['white_list'];
			}

			$good_paths = array();

			foreach ( $raw_paths as $path ) {

				$path = sanitize_text_field( trim( $path ) );

				if ( strlen( $path ) > 0 && $path[0] != '/' ) {
					$path = '/' . $path;
				}

				if ( strlen( $path ) > 1 ) {
					$good_paths[] = $path;
				}

			}

			$input['white_list'] = $good_paths;

		} else {

			$input['white_list'] = array();

		}

		if ( ! is_array( $input['types'] ) ) {
			$file_types = explode( PHP_EOL, $input['types'] );
		} else {
			$file_types = $input['types'];
		}

		$good_types = array();

		foreach ( $file_types as $file_type ) {

			$file_type = trim( $file_type );

			if ( strlen( $file_type ) > 0 && $file_type != '.' ) {

				$good_type = sanitize_text_field( '.' . str_replace( '.', '', $file_type ) );

				$good_types[] = sanitize_text_field( trim( $good_type ) );

			}
		}

		$input['types'] = $good_types;

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

		if ( isset( $_POST['itsec_four_oh_four'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
			}

			update_site_option( 'itsec_four_oh_four',
			                    $_POST['itsec_four_oh_four'] ); //we must manually save network options

		}

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
	public function tracking_vars( $vars ) {

		$vars['itsec_four_oh_four'] = array(
			'enabled' => '0:b',
		);

		return $vars;

	}

	/**
	 * echos 404 file types Field
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function types() {

		if ( isset( $this->settings['types'] ) && is_array( $this->settings['types'] ) ) {
			$types = implode( PHP_EOL, $this->settings['types'] );
		} else {
			$types = implode( PHP_EOL, array(
				'.jpg',
				'.jpeg',
				'.png',
				'.gif',
				'.css',
			) );
		}

		echo '<textarea id="itsec_four_oh_four_types" name="itsec_four_oh_four[types]" wrap="off" cols="20" rows="10">' . $types . PHP_EOL . '</textarea><br />';
		echo '<label for="itsec_four_oh_four_types"> ' . __( 'File types listed here will be recorded as 404 errors but will not lead to lockouts.', 'it-l10n-better-wp-security' ) . '</label>';

	}

}