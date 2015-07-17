<?php
/**
 * File Change Administrative Screens
 *
 * Sets up all administrative functions for the file change detection feature
 * including fields, sanitation and all other privileged functions.
 *
 * @since   4.0.0
 *
 * @package iThemes_Security
 */
class ITSEC_File_Change_Admin {

	/**
	 * The module's saved options
	 *
	 * @since  4.0.0
	 * @access private
	 * @var array
	 */
	private $settings;

	/**
	 * The core plugin class utilized in order to set up admin and other screens
	 *
	 * @since  4.0.0
	 * @access private
	 * @var ITSEC_Core
	 */
	private $core;

	/**
	 * The absolute web patch to the module's files
	 *
	 * @since  4.0.0
	 * @access private
	 * @var string
	 */
	private $module_path;

	/**
	 * Setup the module's administrative functionality
	 *
	 * Loads the file change detection module's privileged functionality including
	 * settings fields.
	 *
	 * @since 4.0.0
	 *
	 * @param ITSEC_Core $core The core plugin instance
	 *
	 * @return void
	 */
	public function run( $core ) {

		$this->core        = $core;
		$this->settings    = get_site_option( 'itsec_file_change' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); //enqueue scripts for admin page
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'itsec_add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_action( 'itsec_admin_init', array( $this, 'itsec_admin_init' ) ); //initialize admin area
		add_action( 'wp_ajax_itsec_file_change_ajax', array( $this, 'wp_ajax_itsec_file_change_ajax' ) );
		add_action( 'wp_ajax_itsec_file_change_warning_ajax', array( $this, 'wp_ajax_itsec_file_change_warning_ajax' ) );
		add_action( 'wp_ajax_itsec_jquery_filetree_ajax', array( $this, 'wp_ajax_itsec_jquery_filetree_ajax' ) );

		add_filter( 'itsec_add_dashboard_status', array( $this, 'itsec_add_dashboard_status' ) ); //add information for plugin status
		add_filter( 'itsec_logger_displays', array( $this, 'itsec_logger_displays' ) ); //adds logs metaboxes
		add_filter( 'itsec_tracking_vars', array( $this, 'itsec_tracking_vars' ) );

		//manually save options on multisite
		if ( is_multisite() ) {
			add_action( 'itsec_admin_init', array( $this, 'itsec_admin_init_multisite' ) ); //save multisite options
		}

	}

	/**
	 * Add Files Admin Javascript
	 *
	 * Enqueues files used in the admin area for the file change module
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		global $itsec_globals;

		wp_register_script( 'itsec_file_change_warning_js', $this->module_path . 'js/admin-file-change-warning.js', array( 'jquery' ), $itsec_globals['plugin_build'] );
		wp_enqueue_script( 'itsec_file_change_warning_js' );
		wp_localize_script(
			'itsec_file_change_warning_js',
			'itsec_file_change_warning',
			array(
				'nonce' => wp_create_nonce( 'itsec_file_change_warning' ),
				'url'   => admin_url() . 'admin.php?page=toplevel_page_itsec_logs&itsec_log_filter=file_change',
			)
		);

		if ( isset( get_current_screen()->id ) && ( false !== strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) || false !== strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_logs' ) || false !== strpos( get_current_screen()->id, 'dashboard' ) ) ) {

			wp_register_script( 'itsec_file_change_js', $this->module_path . 'js/admin-file-change.js', array( 'jquery' ), $itsec_globals['plugin_build'] );
			wp_enqueue_script( 'itsec_file_change_js' );
			wp_localize_script(
				'itsec_file_change_js',
				'itsec_file_change',
				array(
					'mem_limit'            => ITSEC_Lib::get_memory_limit(),
					'text'                 => __( 'Warning: Your server has less than 128MB of RAM dedicated to PHP. If you have many files in your installation or a lot of active plugins activating this feature may result in your site becoming disabled with a memory error. See the plugin homepage for more information.', 'it-l10n-better-wp-security' ),
					'module_path'          => $this->module_path,
					'button_text'          => isset( $this->settings['split'] ) && true === $this->settings['split'] ? __( 'Scan Next File Chunk', 'it-l10n-better-wp-security' ) : __( 'Scan Files Now', 'it-l10n-better-wp-security' ),
					'scanning_button_text' => __( 'Scanning...', 'it-l10n-better-wp-security' ),
					'no_changes'           => __( 'No changes were detected.', 'it-l10n-better-wp-security' ),
					'changes'              => __( 'Changes were detected. Please check the log page for details.', 'it-l10n-better-wp-security' ),
					'error'                => __( 'An error occured. Please try again later', 'it-l10n-better-wp-security' ),
					'ABSPATH'              => ITSEC_Lib::get_home_path(),
					'nonce'                => wp_create_nonce( 'itsec_do_file_check' ),
				)
			);

			wp_register_script( 'itsec_jquery_filetree', $this->module_path . 'filetree/jqueryFileTree.js', array( 'jquery' ), '1.01' );
			wp_enqueue_script( 'itsec_jquery_filetree' );
			wp_localize_script(
				'itsec_jquery_filetree',
				'itsec_jquery_filetree',
				array(
					'nonce' => wp_create_nonce( 'itsec_jquery_filetree' ),
				)
			);

			wp_register_style( 'itsec_jquery_filetree_style', $this->module_path . 'filetree/jqueryFileTree.css', array(), $itsec_globals['plugin_build'] ); //add multi-select css
			wp_enqueue_style( 'itsec_jquery_filetree_style' );

			wp_register_style( 'itsec_file_change_css', $this->module_path . 'css/admin-file-change.css', array(), $itsec_globals['plugin_build'] ); //add multi-select css
			wp_enqueue_style( 'itsec_file_change_css' );

		}

	}

	/**
	 * Display admin warning
	 *
	 * Displays a warning in the Dashboard to administrators  when file changes have been detected
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function dashboard_warning() {

		global $blog_id; //get the current blog id

		if ( ( is_multisite() && ( 1 != $blog_id || ! current_user_can( 'manage_network_options' ) ) ) || ! current_user_can( 'activate_plugins' ) ) { //only display to network admin if in multisite
			return;
		}

		//if there is a warning to display
		if ( '1' == get_site_option( 'itsec_file_change_warning' ) ) {

			if ( ! function_exists( 'itsec_intrusion_warning' ) ) {

				/**
				 * Echos warning markup.
				 *
				 * Build and echos the file change warning markup that appears in the dashboard
				 *
				 * @since 4.0.0
				 *
				 * @return void
				 */
				function itsec_intrusion_warning() {

					global $itsec_globals;

					printf(
						'<div id="itsec_file_change_warning_dialog" class="error"><p>%s %s</p> <p><input type="button" id="itsec_go_to_logs" class="button-primary" value="%s">&nbsp;<input type="button" id="itsec_dismiss_file_change_warning" class="button-secondary" value="%s"></p></div>',
						$itsec_globals['plugin_name'],
						__( 'has noticed a change to some files in your WordPress site. Please review the logs to make sure your system has not been compromised.', 'it-l10n-better-wp-security' ),
						__( 'View Logs', 'it-l10n-better-wp-security' ),
						__( 'Dismiss Warning', 'it-l10n-better-wp-security' )

					);

				}

			}

			//put the warning in the right spot
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', 'itsec_intrusion_warning' ); //register notification
			} else {
				add_action( 'admin_notices', 'itsec_intrusion_warning' ); //register notification
			}

		}

		//if they've clicked a button hide the notice
		if ( ( isset( $_GET['bit51_view_logs'] ) || isset( $_GET['bit51_dismiss_warning'] ) ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bit51-nag' ) ) {

			//Get the options
			if ( is_multisite() ) {

				switch_to_blog( 1 );

				delete_option( 'bwps_intrusion_warning' );

				restore_current_blog();

			} else {

				delete_option( 'bwps_intrusion_warning' );

			}

			//take them back to where they started
			if ( isset( $_GET['bit51_dismiss_warning'] ) ) {
				wp_redirect( $_SERVER['HTTP_REFERER'], 302 );
			}

			//take them to the correct logs page
			if ( isset( $_GET['bit51_view_logs'] ) ) {

				if ( is_multisite() ) {

					wp_redirect( admin_url() . 'network/admin.php?page=better-wp-security-logs#file-change', 302 );

				} else {

					wp_redirect( admin_url() . 'admin.php?page=better-wp-security-logs#file-change', 302 );

				}

			}

		}

	}

	/**
	 * Echos the one-time file change scan form
	 *
	 * Echos the form necessary to perform one-time file scans which can then be accessed
	 * by various administrative screens including the plugin's settings and logs pages
	 *
	 * @since 4.0.0
	 *
	 * @param string $origin the origin
	 *
	 * @return void
	 */
	public function file_change_form( $origin ) {

		if ( isset( $this->settings['enabled'] ) && true === $this->settings['enabled'] ) {

			echo '<form id="itsec_one_time_file_check" method="post" action="">';
			echo wp_nonce_field( 'itsec_do_file_check', 'wp_nonce' );
			echo '<input type="hidden" name="itsec_file_change_origin" value="' . sanitize_text_field( $origin ) . '">';
			echo '<p>' . __( "Press the button below to scan your site's files for changes. Note that if changes are found this will take you to the logs page for details.", 'it-l10n-better-wp-security' ) . '</p>';
			echo '<p><input type="submit" id="itsec_one_time_file_check_submit" class="button-primary" value="' . ( isset( $this->settings['split'] ) && true === $this->settings['split'] ? __( 'Scan Next File Chunk', 'it-l10n-better-wp-security' ) : __( 'Scan Files Now', 'it-l10n-better-wp-security' ) ) . '" /></p>';
			echo '<div id="itsec_file_change_status"><p></p></div>';
			echo '</form>';

		}

	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * Adds the module's meta settings box to the settings page and
	 * registers the added box in the page's table of contents.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function itsec_add_admin_meta_boxes() {

		$id    = 'file_change_options';
		$title = __( 'File Change Detection', 'it-l10n-better-wp-security' );

		add_meta_box(
			$id,
			$title,
			array( $this, 'metabox_advanced_file_change_settings' ),
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
	 * Sets a medium priority item for the module's functionality in the plugin
	 * dashboard.
	 *
	 * @since 4.0.0
	 *
	 * @param array $statuses array of existing plugin dashboard statuses
	 *
	 * @return array statuses
	 */
	public function itsec_add_dashboard_status( $statuses ) {

		if ( isset( $this->settings['enabled'] ) && true === $this->settings['enabled'] ) {

			$status_array = 'safe-medium';
			$status       = array(
				'text' => __( 'Your site will detect changes to your files.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_file_change_enabled',
			);

		} else {

			$status_array = 'medium';
			$status       = array(
				'text' => __( 'Your website is not looking for changed files. Consider turning on file change detections.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_file_change_enabled',
			);

		}

		array_push( $statuses[ $status_array ], $status );

		return $statuses;

	}

	/**
	 * Execute admin initializations
	 *
	 * Calls the dashboard warning method and sets up all module settings fields and
	 * sections.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function itsec_admin_init() {

		$this->dashboard_warning();

		//Add Settings sections
		add_settings_section(
			'file_change-enabled',
			__( 'File Change Detection', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'file_change-settings',
			__( 'File Change Detection Settings', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		//File Change Detection Fields
		add_settings_field(
			'itsec_file_change[enabled]',
			__( 'File Change Detection', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_enabled' ),
			'security_page_toplevel_page_itsec_settings',
			'file_change-enabled'
		);

		add_settings_field(
			'itsec_file_change[split]',
			__( 'Split File Scanning', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_split' ),
			'security_page_toplevel_page_itsec_settings',
			'file_change-settings'
		);

		add_settings_field(
			'itsec_file_change[method]',
			__( 'Include/Exclude Files and Folders', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_method' ),
			'security_page_toplevel_page_itsec_settings',
			'file_change-settings'
		);

		add_settings_field(
			'itsec_file_change[file_list]',
			__( 'Files and Folders List', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_file_list' ),
			'security_page_toplevel_page_itsec_settings',
			'file_change-settings'
		);

		add_settings_field(
			'itsec_file_change[types]',
			__( 'Ignore File Types', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_types' ),
			'security_page_toplevel_page_itsec_settings',
			'file_change-settings'
		);

		add_settings_field(
			'itsec_file_change[email]',
			__( 'Email File Change Notifications', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_email' ),
			'security_page_toplevel_page_itsec_settings',
			'file_change-settings'
		);

		add_settings_field(
			'itsec_file_change[notify_admin]',
			__( 'Display File Change Admin Warning', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_notify_admin' ),
			'security_page_toplevel_page_itsec_settings',
			'file_change-settings'
		);

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_file_change',
			array( $this, 'sanitize_module_input' )
		);

	}

	/**
	 * Prepare and save options in network settings
	 *
	 * Saves the options in a multi-site network where data sensitization and processing is not
	 * called automatically on form submission.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function itsec_admin_init_multisite() {

		if ( isset( $_POST['itsec_file_change'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
			}

			update_site_option( 'itsec_file_change', $_POST['itsec_file_change'] ); //we must manually save network options

		}

	}

	/**
	 * Array of displays for the logs screen
	 *
	 * Registers the custom log page with the core plugin to allow for access from the log page's
	 * dropdown menu.
	 *
	 * @since 4.0.0
	 *
	 * @param array $displays metabox array
	 *
	 * @return array metabox array
	 */
	public function itsec_logger_displays( $displays ) {

		if ( isset( $this->settings['enabled'] ) && true === $this->settings['enabled'] ) {

			$displays[] = array(
				'module'   => 'file_change',
				'title'    => __( 'File Change History', 'it-l10n-better-wp-security' ),
				'callback' => array( $this, 'logs_metabox_content' )
			);

		}

		return $displays;

	}

	/**
	 * Adds fields that will be tracked for Google Analytics
	 *
	 * Registers all settings in the module that will be tracked on change by
	 * Google Analytics if "allow tracking" is enabled.
	 *
	 * @since 4.0.0
	 *
	 * @param array $vars tracking vars
	 *
	 * @return array tracking vars
	 */
	public function itsec_tracking_vars( $vars ) {

		$vars['itsec_file_change'] = array(
			'enabled' => '0:b',
			'method'  => '1:b',
			'email'   => '1:b',
		);

		return $vars;

	}

	/**
	 * Render the file change log metabox
	 *
	 * Displays a metabox on the logs page, when filtered, showing all file change items.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function logs_metabox_content() {

		global $itsec_globals;

		$this->file_change_form( 'settings' );

		if ( ! class_exists( 'ITSEC_File_Change_Log' ) ) {
			require( dirname( __FILE__ ) . '/class-itsec-file-change-log.php' );
		}

		if ( isset( $this->settings['enabled'] ) && true === $this->settings['enabled'] ) {

			// If we're splitting the file check run it every 6 hours. Else daily.
			if ( isset( $this->settings['split'] ) && true === $this->settings['split'] ) {

				$interval = 12342;

			} else {

				$interval = 86400;

			}

			$next_run_raw = $this->settings['last_run'] + $interval;

			if ( date( 'j', $next_run_raw ) == date( 'j', $itsec_globals['current_time'] ) ) {
				$next_run_day = __( 'Today', 'it-l10n-better-wp-security' );
			} else {
				$next_run_day = __( 'Tomorrow', 'it-l10n-better-wp-security' );
			}

			$next_run = $next_run_day . ' at ' . date( 'g:i a', $next_run_raw );

			echo '<p>' . __( 'Next automatic scan at: ', 'it-l10n-better-wp-security' ) . '<strong>' . $next_run . '*</strong></p>';
			echo '<p><em>*' . __( 'Automatic file change scanning is triggered by a user visiting your page and may not happen exactly at the time listed.', 'it-l10n-better-wp-security' ) . '</em>';

		}

		$log_display = new ITSEC_File_Change_Log();

		$log_display->prepare_items();
		$log_display->display();

	}

	/**
	 * Render the settings metabox
	 *
	 * Displays the contents of the module's settings metabox on the "Settings"
	 * page with all module options.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function metabox_advanced_file_change_settings() {

		echo '<p>' . __( 'Even the best security solutions can fail. How do you know if someone gets into your site? You will know because they will change something. File Change detection will tell you what files have changed in your WordPress installation alerting you to changes not made by yourself. Unlike other solutions this plugin will look only at your installation and compare files to the last check instead of comparing them with a remote installation thereby taking into account whether or not you modify the files yourself.', 'it-l10n-better-wp-security' ) . '</p>';

		echo $this->file_change_form( 'logs' );

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'file_change-enabled', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'file_change-settings', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save All Changes', 'it-l10n-better-wp-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

	}

	/**
	 * Sanitize and validate input
	 *
	 * Sanitizes and validates module options saved on the settings page or via multisite.
	 *
	 * @since 4.0.0
	 *
	 * @param  Array $input array of input fields
	 *
	 * @return Array         Sanitized array
	 */
	public function sanitize_module_input( $input ) {

		global $itsec_globals;

		//File Change Detection Fields
		$input['enabled']      = ( isset( $input['enabled'] ) && intval( $input['enabled'] == 1 ) ? true : false );
		$input['split']        = ( isset( $input['split'] ) && intval( $input['split'] == 1 ) ? true : false );
		$input['method']       = ( isset( $input['method'] ) && intval( $input['method'] == 1 ) ? true : false );
		$input['email']        = ( isset( $input['email'] ) && intval( $input['email'] == 1 ) ? true : false );
		$input['notify_admin'] = ( isset( $input['notify_admin'] ) && intval( $input['notify_admin'] == 1 ) ? true : false );
		$input['last_chunk']   = ( isset( $input['last_chunk'] ) ? $input['last_chunk'] : false );

		if ( ! is_array( $input['file_list'] ) ) {

			$file_list = explode( PHP_EOL, $input['file_list'] );

		} else {

			$file_list = $input['file_list'];

		}

		$good_files = array();

		foreach ( $file_list as $file ) {
			$good_files[] = sanitize_text_field( trim( $file ) );
		}

		$input['file_list'] = $good_files;

		if ( ! is_array( $input['types'] ) ) {

			$file_types = explode( PHP_EOL, $input['types'] );

		} else {

			$file_types = $input['types'];

		}

		$good_types = array();

		foreach ( $file_types as $file_type ) {

			$file_type = trim( $file_type );

			if ( 0 < strlen( $file_type ) && '.' != $file_type ) {

				$good_type = sanitize_text_field( '.' . str_replace( '.', '', $file_type ) );

				$good_types[] = sanitize_text_field( trim( $good_type ) );

			}
		}

		$input['types'] = $good_types;

		if ( isset( $input['split'] ) && true === $input['split'] ) {

			$interval = 12282;

		} else {

			$interval = 86340;

		}

		if ( defined( 'ITSEC_DOING_FILE_CHECK' ) && true === ITSEC_DOING_FILE_CHECK ) {

			$input['last_run'] = $itsec_globals['current_time'];

		} else {

			$input['last_run'] = isset( $this->settings['last_run'] ) && $this->settings['last_run'] > $itsec_globals['current_time'] - $interval ? $this->settings['last_run'] : ( $itsec_globals['current_time'] - $interval + 120 );

		}

		if ( is_multisite() ) {

			$this->core->show_network_admin_notice( false );

			$this->settings = $input;

		}

		return $input;

	}

	/**
	 * echos Email File Change Notifications Field
	 *
	 * Echo's the settings field that determines whether or not file change notifications
	 * will be emailed to the site admin.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_email() {

		if ( isset( $this->settings['email'] ) && false === $this->settings['email'] ) {

			$email = 0;

		} else {

			$email = 1;

		}

		echo '<input type="checkbox" id="itsec_file_change_email" name="itsec_file_change[email]" value="1" ' . checked( 1, $email, false ) . '/>';
		echo '<label for="itsec_file_change_email"> ' . __( 'Email file change notifications', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description">' . __( 'Notifications will be sent to all emails set to receive notifications on the global settings page.', 'it-l10n-better-wp-security' ) . '</p>';

	}

	/**
	 * echos Enable File Change Detection Field
	 *
	 * Echo's the settings field that determines whether or not the file change detection module is enabled.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_enabled() {

		if ( isset( $this->settings['enabled'] ) && true === $this->settings['enabled'] ) {

			$enabled = 1;

		} else {

			$enabled = 0;

		}

		echo '<input type="checkbox" id="itsec_file_change_enabled" name="itsec_file_change[enabled]" value="1" ' . checked( 1, $enabled, false ) . '/>';
		echo '<label for="itsec_file_change_enabled"> ' . __( 'Enable File Change detection', 'it-l10n-better-wp-security' ) . '</label>';

	}

	/**
	 * echos Enable File Change List Field
	 *
	 * Echo's the settings field that determines specific folders in the site root for exclusion or inclusion.
	 *
	 * @param  array $args field arguments
	 *
	 * @return void
	 */
	public function settings_field_file_list() {

		if ( isset( $this->settings['file_list'] ) && is_array( $this->settings['file_list'] ) ) {

			$file_list = implode( PHP_EOL, $this->settings['file_list'] );

		} else {

			$file_list = '';

		}

		echo '<p class="description">' . __( 'Exclude files or folders by clicking the red minus next to the file or folder name.', 'it-l10n-better-wp-security' ) . '</p>';
		echo '<div class="file_list">';
		echo '<div class="file_chooser"><div class="jquery_file_tree"></div></div>';
		echo '<div class="list_field">';
		echo '<textarea id="itsec_file_change_file_list" name="itsec_file_change[file_list]" wrap="off">' . $file_list . PHP_EOL . '</textarea>';
		echo '</div></div>';

	}

	/**
	 * echos method Field
	 *
	 * Echo's the settings field that determines whether selected files and folders in the file_list
	 * field will be excluded or included.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_method() {

		if ( isset( $this->settings['method'] ) && true === $this->settings['method'] ) {

			$method = 1;

		} else {

			$method = 0;

		}

		echo '<select id="itsec_file_change_method" name="itsec_file_change[method]">';
		echo '<option value="1" ' . selected( $method, '1' ) . '>' . __( 'Exclude Selected', 'it-l10n-better-wp-security' ) . '</option>';
		echo '<option value="0" ' . selected( $method, '0' ) . '>' . __( 'Include Selected', 'it-l10n-better-wp-security' ) . '</option>';
		echo '</select><br />';
		echo '<label for="itsec_file_change_method"> ' . __( 'Include/Exclude Files', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description">' . __( 'Select whether we should exclude files and folders selected or whether the scan should only include files and folders selected.' ) . '</p>';

	}

	/**
	 * echos Email File Change Notifications Field
	 *
	 * Echo's the settings field that determines if the notification banner will be displayed to
	 * administrators in the Dashboard.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_notify_admin() {

		if ( isset( $this->settings['notify_admin'] ) && false === $this->settings['notify_admin'] ) {

			$notify_admin = 0;

		} else {

			$notify_admin = 1;

		}

		echo '<input type="checkbox" id="itsec_file_change_notify_admin" name="itsec_file_change[notify_admin]" value="1" ' . checked( 1, $notify_admin, false ) . '/>';
		echo '<label for="itsec_file_change_notify_admin"> ' . __( 'Display file change admin warning', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description">' . __( 'Disabling this feature will prevent the file change warning from displaying to the site administrator in the WordPress Dashboard. Note that disabling both the error message and the email notification will result in no notifications of file changes. The only way you will be able to tell is by manually checking the log files.', 'it-l10n-better-wp-security' ) . '</p>';

	}

	/**
	 * echos split file checks Field
	 *
	 * Echo's the settings field that determines if file change scanning will be split into 7
	 * chunks throughout the day of if the entire site will be scanned in a single pass.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_split() {

		if ( isset( $this->settings['split'] ) && true === $this->settings['split'] ) {

			$split = 1;

		} else {

			$split = 0;

		}

		echo '<input type="checkbox" id="itsec_file_change_split" name="itsec_file_change[split]" value="1" ' . checked( 1, $split, false ) . '/>';
		echo '<label for="itsec_file_change_split"> ' . __( 'Split file checking into chunks.', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description"> ' . __( 'Splits file checking into 7 chunks (plugins, themes, wp-admin, wp-includes, uploads, the rest of wp-content and everything that is left over) and divides the checks evenly over the course of a day. This feature may result in more notifications but will allow for the scanning of bigger sites to continue even on a lower-end web host.', 'it-l10n-better-wp-security' ) . '</p>';

	}

	/**
	 * echos file change types Field
	 *
	 * Echo's the settings field that determines various file types that can be excluded from
	 * the file scan detection.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_types() {

		if ( isset( $this->settings['types'] ) && is_array( $this->settings['types'] ) ) {

			$types = implode( PHP_EOL, $this->settings['types'] );

		} else {

			$types = implode( PHP_EOL, array(
				'.jpg',
				'.jpeg',
				'.png',
				'.log',
				'.mo',
				'.po',
			) );

		}

		echo '<textarea id="itsec_file_change_types" name="itsec_file_change[types]" wrap="off" cols="20" rows="10">' . $types . PHP_EOL . '</textarea><br />';
		echo '<label for="itsec_file_change_types"> ' . __( 'File types listed here will not be checked for changes. While it is possible to change files such as images it is quite rare and nearly all known WordPress attacks exploit php, js and other text files.', 'it-l10n-better-wp-security' ) . '</label>';

	}

	/**
	 * Dismisses the file change notifications.
	 *
	 * Processes the ajax request for dismissing the file change notification box in the
	 * WordPress Dashboard.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function wp_ajax_itsec_file_change_warning_ajax() {

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_file_change_warning' ) ) {
			die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
		}

		die( delete_site_option( 'itsec_file_change_warning' ) );

	}

	/**
	 * Gets file list for tree.
	 *
	 * Processes the ajax request for retreiving the list of files and folders that can later either
	 * excluded or included.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function wp_ajax_itsec_jquery_filetree_ajax() {

		global $itsec_globals;

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_jquery_filetree' ) || ! current_user_can( $itsec_globals['plugin_access_lvl'] ) ) {
			die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
		}

		$directory = sanitize_text_field( $_POST['dir'] );

		$directory = urldecode( $directory );

		if ( file_exists( $directory ) ) {

			$files = scandir( $directory );

			natcasesort( $files );

			if ( 2 < count( $files ) ) { /* The 2 accounts for . and .. */

				echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";

				//two loops keep directories sorted before files

				// All files and directories (alphabetical sorting)
				foreach ( $files as $file ) {

					if ( '.' != $file && '..' != $file && file_exists( $directory . $file ) && is_dir( $directory . $file ) ) {

						echo '<li class="directory collapsed"><a href="#" rel="' . htmlentities( $directory . $file ) . '/">' . htmlentities( $file ) . '<div class="itsec_treeselect_control"><img src="' . plugins_url( 'images/redminus.png', __FILE__ ) . '" style="vertical-align: -3px;" title="Add to exclusions..." class="itsec_filetree_exclude"></div></a></li>';

					} elseif ( '.' != $file && '..' != $file && file_exists( $directory . $file ) && ! is_dir( $directory . $file ) ) {

						$ext = preg_replace( '/^.*\./', '', $file );
						echo '<li class="file ext_' . $ext . '"><a href="#" rel="' . htmlentities( $directory . $file ) . '">' . htmlentities( $file ) . '<div class="itsec_treeselect_control"><img src="' . plugins_url( 'images/redminus.png', __FILE__ ) . '" style="vertical-align: -3px;" title="Add to exclusions..." class="itsec_filetree_exclude"></div></a></li>';

					}

				}

				echo "</ul>";

			}

		}

		exit;

	}

	/**
	 * Executes one-time file scan.
	 *
	 * Processes the ajax request to execute a one-time file scan.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function wp_ajax_itsec_file_change_ajax() {

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_do_file_check' ) ) {
			die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
		}

		$module = new ITSEC_File_Change();
		$module->run();

		die( $module->execute_file_check( false ) );

	}

}