<?php

/**
 * Database backup Administrative Screens
 *
 * Sets up all administrative functions for the database backup feature
 * including fields, sanitation and all other privileged functions.
 *
 * @since   4.0.0
 *
 * @package iThemes_Security
 */
class ITSEC_Backup_Admin {

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
	 * Loads the database backup module's privileged functionality including
	 * settings fields.
	 *
	 * @since 4.0.0
	 *
	 * @param ITSEC_Core $core The core plugin instance
	 *
	 * @return void
	 */
	function run( $core ) {

		$this->core        = $core;
		$this->settings    = get_site_option( 'itsec_backup' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); //enqueue scripts for admin page
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'itsec_add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_action( 'itsec_admin_init', array( $this, 'itsec_admin_init' ) ); //initialize admin area

		add_filter( 'itsec_add_dashboard_status', array( $this, 'itsec_add_dashboard_status' ) ); //add information for plugin status
		add_filter( 'itsec_tooltip_modules', array( $this, 'itsec_tooltip_modules' ) ); //register tooltip action
		add_filter( 'itsec_tracking_vars', array( $this, 'itsec_tracking_vars' ) );

		if ( isset( $_POST['itsec_backup'] ) && $_POST['itsec_backup'] == 'one_time_backup' ) {
			add_action( 'itsec_admin_init', array( $this, 'one_time_backup' ) );
		}

		//manually save options on multisite
		if ( is_multisite() ) {
			add_action( 'itsec_admin_init', array( $this, 'itsec_admin_init_multisite' ) ); //save multisite options
		}

	}

	/**
	 * Build and echo the database backup description.
	 *
	 * Echos the module description for database backups.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function add_module_intro() {

		echo '<p>' . __( 'One of the best ways to protect yourself from an attack is to have access to a database backup of your site. If something goes wrong, you can get your site back by restoring the database from a backup and replacing the files with fresh ones. Use the button below to create a backup of your database for this purpose. You can also schedule automated backups and download or delete previous backups.', 'it-l10n-better-wp-security' ) . '</p>';

	}

	/**
	 * Add Files Admin Javascript
	 *
	 * Enqueues files used in the admin area for the database backup module
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) !== false ) {

			wp_register_script( 'itsec_backup_js', $this->module_path . 'js/admin-backup.js', array( 'jquery' ), $itsec_globals['plugin_build'] );
			wp_enqueue_script( 'itsec_backup_js' );
			wp_localize_script( 'itsec_backup_js', 'exclude_text', array(
				'available' => __( 'Tables for Backup', 'it-l10n-better-wp-security' ),
				'excluded'  => __( 'Excluded Tables', 'it-l10n-better-wp-security' ),
				'location'  => $itsec_globals['ithemes_backup_dir'],
			) );

			wp_register_script( 'jquery_multiselect', $this->module_path . 'js/jquery.multi-select.js', array( 'jquery' ), $itsec_globals['plugin_build'] );
			wp_enqueue_script( 'jquery_multiselect' );

			wp_register_style( 'itsec_ms_styles', $this->module_path . 'css/multi-select.css', array(), $itsec_globals['plugin_build'] ); //add multi-select css
			wp_enqueue_style( 'itsec_ms_styles' );

			wp_register_style( 'itsec_backup_styles', $this->module_path . 'css/admin-backup.css', array(), $itsec_globals['plugin_build'] ); //add multi-select css
			wp_enqueue_style( 'itsec_backup_styles' );

		}

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_backups' ) !== false ) {

			wp_register_style( 'itsec_backup_styles', $this->module_path . 'css/admin-backup.css', array(), $itsec_globals['plugin_build'] ); //add multi-select css
			wp_enqueue_style( 'itsec_backup_styles' );

		}

	}

	/**
	 * Link to external backup plugin's settings page.
	 *
	 * Allows another backup plugin to set the backup links in iThemes Security to the correct location.
	 *
	 * @since 4.0.0
	 *
	 * @return string  Link information for external backup plugin
	 */
	public function external_backup_link() {

		$backup_link = '#itsec_backup_enabled';

		/**
		 * Link to external backup plugin's settings page.
		 *
		 * Filterable variable to link backup locations in this plugin to the correct external backup page.
		 *
		 * @since 4.0.0
		 *
		 * @param string $backup_link Link information for external backup plugin
		 */

		return apply_filters( 'itsec_external_backup_link', $backup_link );

	}

	/**
	 * Is another backup function present
	 *
	 * Allows another backup plugin to register itself thereby preventing duplicate backups.
	 *
	 * @since 4.0.0
	 *
	 * @return bool true if another backup is present or false.
	 */
	public function has_backup() {

		$has_backup = false;

		/**
		 * Is another backup plugin present.
		 *
		 * Filterable variable to let this plugin know that another backup solution is present.
		 *
		 * @since 4.0.0
		 *
		 * @param bool $has_backup Whether or not another backup plugin is present.
		 */

		return apply_filters( 'itsec_has_external_backup', $has_backup );

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

		if ( ! class_exists( 'backupbuddy_api' ) || is_multisite() ) {

			add_meta_box(
				'backup_description',
				__( 'Description', 'it-l10n-better-wp-security' ),
				array( $this, 'add_module_intro' ),
				'security_page_toplevel_page_itsec_backups',
				'normal',
				'core'
			);

			add_meta_box(
				'backup_one_time',
				__( 'Make a Database Backup', 'it-l10n-better-wp-security' ),
				array( $this, 'metabox_one_time' ),
				'security_page_toplevel_page_itsec_backups',
				'advanced',
				'core'
			);

			$id    = 'backup_options';
			$title = __( 'Database Backups', 'it-l10n-better-wp-security' );

			add_meta_box(
				$id,
				$title,
				array( $this, 'metabox_advanced_settings' ),
				'security_page_toplevel_page_itsec_settings',
				'advanced',
				'core'
			);

			add_meta_box(
				'backupbuddy_info',
				__( 'Take the Next Steps in Security with BackupBuddy', 'it-l10n-better-wp-security' ),
				array( $this, 'metabox_backupbuddy' ),
				'security_page_toplevel_page_itsec_backups',
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

		if ( ! is_multisite() && class_exists( 'backupbuddy_api' ) && 1 <= sizeof( backupbuddy_api::getSchedules() ) ) {

			if ( true === $this->settings['enabled'] ) { //disable our backups if we have to

				$this->settings['enabled'] = false;
				update_site_option( 'itsec_backup', $this->settings );

			}

			$status_array = 'safe-medium';
			$status       = array(
				'text' => __( 'Your site is performing scheduled database and file backups.', 'it-l10n-better-wp-security' ),
				'link' => '?page=pb_backupbuddy_scheduling',
			);

		} elseif ( ! is_multisite() && class_exists( 'backupbuddy_api' ) ) {

			if ( $this->settings['enabled'] === true ) { //disable our backups if we have to

				$this->settings['enabled'] = false;
				update_site_option( 'itsec_backup', $this->settings );

			}

			$status_array = 'medium';
			$status       = array(
				'text' => __( 'BackupBuddy is installed but backups do not appear to have been scheduled. Please schedule backups.', 'it-l10n-better-wp-security' ),
				'link' => '?page=pb_backupbuddy_scheduling',
			);

		} elseif ( true === $this->has_backup() && true === $this->scheduled_backup() ) {

			if ( true === $this->settings['enabled'] ) { //disable our backups if we have to

				$this->settings['enabled'] = false;
				update_site_option( 'itsec_backup', $this->settings );

			}

			$status_array = 'safe-medium';
			$status       = array(
				'text' => __( 'You are using a 3rd party backup solution.', 'it-l10n-better-wp-security' ),
				'link' => $this->external_backup_link(),
			);

		} elseif ( true === $this->has_backup() ) {

			if ( true === $this->settings['enabled'] ) { //disable our backups if we have to

				$this->settings['enabled'] = false;
				update_site_option( 'itsec_backup', $this->settings );

			}

			$status_array = 'medium';
			$status       = array(
				'text' => __( 'It looks like you have a 3rd-party backup solution in place but are not using it. Please turn on scheduled backups.', 'it-l10n-better-wp-security' ),
				'link' => $this->external_backup_link(),
			);

		} elseif ( true === $this->settings['enabled'] ) {

			$status_array = 'medium';
			$status       = array(
				'text' => __( 'Your site is performing scheduled database backups but is not backing up files. Consider purchasing or scheduling BackupBuddy to protect your investment.', 'it-l10n-better-wp-security' ),
				'link' => 'http://ithemes.com/better-backups',
			);

		} else {

			$status_array = 'high';
			$status       = array(
				'text' => __( 'Your site is not performing any scheduled database backups.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_backup_enabled',
			);

		}

		array_push( $statuses[$status_array], $status );

		return $statuses;

	}

	/**
	 * Execute admin initializations
	 *
	 * Sets up all module settings fields and sections.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function itsec_admin_init() {

		//Add Settings sections
		add_settings_section(
			'backup-settings-2',
			__( 'Configure Database Backups', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'backup-enabled',
			__( 'Enable Database Backups', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'backup-settings',
			__( 'Backup Schedule Settings', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_field(
			'itsec_backup[enabled]',
			__( 'Schedule Database Backups', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_enabled' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-enabled'
		);

		if ( ! defined( 'ITSEC_BACKUP_CRON' ) || false === ITSEC_BACKUP_CRON ) { //Use cron if needed

			add_settings_field(
				'itsec_backup[interval]',
				__( 'Backup Interval', 'it-l10n-better-wp-security' ),
				array( $this, 'settings_field_interval' ),
				'security_page_toplevel_page_itsec_settings',
				'backup-settings'
			);

		}

		add_settings_field(
			'itsec_backup[all_sites]',
			__( 'Backup Full Database', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_all_sites' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		add_settings_field(
			'itsec_backup[method]',
			__( 'Backup Method', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_method' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		add_settings_field(
			'itsec_backup[location]',
			__( 'Backup Location', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_location' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		add_settings_field(
			'itsec_backup[retain]',
			__( 'Backups to Retain', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_retain' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		add_settings_field(
			'itsec_backup[zip]',
			__( 'Compress Backup Files', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_zip' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		add_settings_field(
			'itsec_backup[exclude]',
			__( 'Exclude Tables', 'it-l10n-better-wp-security' ),
			array( $this, 'settings_field_exclude' ),
			'security_page_toplevel_page_itsec_settings',
			'backup-settings-2'
		);

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_backup',
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

		if ( isset( $_POST['itsec_backup'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
			}

			update_site_option( 'itsec_backup', $_POST['itsec_backup'] ); //we must manually save network options

		}

	}

	/**
	 * Register backups for tooltips.
	 *
	 * Registers the backup module for the tooltips that are displayed with a new activation.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $tooltip_modules array of tooltip modules
	 *
	 * @return array                   array of tooltip modules
	 */
	public function itsec_tooltip_modules( $tooltip_modules ) {

		$tooltip_modules['backup'] = array(
			'priority'  => 10,
			'class'     => 'itsec_tooltip_backup',
			'heading'   => __( 'Back up your site', 'it-l10n-better-wp-security' ),
			'text'      => __( 'We recommend making a database backup before you get started securing your site.', 'it-l10n-better-wp-security' ),
			'link_text' => __( 'Make a backup', 'it-l10n-better-wp-security' ),
			'callback'  => array( $this, 'tooltip_ajax' ),
			'success'   => __( 'Backup completed. Please check your email or uploads folder.', 'it-l10n-better-wp-security' ),
			'failure'   => __( 'Whoops. Something went wrong. Check the backup page or contact support.', 'it-l10n-better-wp-security' ),
		);

		return $tooltip_modules;

	}

	/**
	 * Adds fields that will be tracked for Google Analytics.
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

		$vars['itsec_backup'] = array(
			'enabled' => '0:b',
			'method'  => '3:s',
			'zip'     => '1:b',
		);

		return $vars;

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
	public function metabox_advanced_settings() {

		echo '<p>' . __( 'One of the best ways to protect yourself from an attack is to have access to a database backup of your site. If something goes wrong, you can get your site back by restoring the database from a backup and replacing the files with fresh ones. Use the button below to create a backup of your database for this purpose. You can also schedule automated backups and download or delete previous backups.', 'it-l10n-better-wp-security' ) . '</p>';

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'backup-settings-2', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'backup-enabled', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'backup-settings', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save All Changes', 'it-l10n-better-wp-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

	}

	/**
	 * Render the BackupBuddy metabox.
	 *
	 * Display the BackupBuddy information metabox.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function metabox_backupbuddy() {

		echo '<p>' . __( 'A database backup is just a simple start. BackupBuddy goes one step further to provide complete backups of all your site files (including image and media files, themes, plugins, widgets and settings) - which aren\'t included in a database backup. With BackupBuddy you can customize backup schedules, send your backup files  safely off-site to remote storage destinations, restore your site quickly & easily and even move your whole site to a new host or domain.', 'it-l10n-better-wp-security' ) . '</p>';
		echo '<h4>' . __( '5 Reasons You Need a Complete Backup Strategy', 'it-l10n-better-wp-security' ) . '</h4>';
		echo '<ol>';
		echo '<li><strong>' . __( 'Database backups aren\'t enough.', 'it-l10n-better-wp-security' ) . '</strong> ' . __( 'You need complete backups of your entire site (including images and media files, themes, plugins, widgets and settings).', 'it-l10n-better-wp-security' ) . '</li>';
		echo '<li><strong>' . __( 'Backup files should be protected.', 'it-l10n-better-wp-security' ) . '</strong> ' . __( 'Send and store them safely off-site to a secure remote destination (like email, Dropbox, Amazon S3, etc.)', 'it-l10n-better-wp-security' ) . '</li>';
		echo '<li><strong>' . __( 'Backups should be automated and scheduled so you don\'t forget.', 'it-l10n-better-wp-security' ) . '</strong> ' . __( 'Set daily, weekly or monthly backups that automatically send backups off-site.', 'it-l10n-better-wp-security' ) . '</li>';
		echo '<li><strong>' . __( 'Restoring your site should be quick and easy.', 'it-l10n-better-wp-security' ) . '</strong> ' . __( 'If you get hacked or your server crashes, you shouldn\'t have to worry about restoring your site. Reliable backups mean nothing gets corrupted or broken during the restore process.', 'it-l10n-better-wp-security' ) . '</li>';
		echo '<li><strong>' . __( 'You should own your backup files.', 'it-l10n-better-wp-security' ) . '</strong> ' . __( 'Don\'t just rely on a host or service. It\'s your site, so you should own everything on it.', 'it-l10n-better-wp-security' ) . '</li>';
		echo '</ol>';

		echo '<p class="bub-cta"><a href="http://ithemes.com/better-backups" target="_blank" class="button-primary" >' . __( 'Learn more about BackupBuddy', 'it-l10n-better-wp-security' ) . '</a></p>';

	}

	/**
	 * Render the one-time backup metabox.
	 *
	 * Display the form for one-time database backups.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function metabox_one_time() {

		echo '<form method="post" action="">';
		echo wp_nonce_field( 'itsec_do_backup', 'wp_nonce' );
		echo '<input type="hidden" name="itsec_backup" value="one_time_backup" />';
		echo '<p>' . __( 'Press the button below to create a backup of your WordPress database. If you have "Send Backups By Email" selected in automated backups you will receive an email containing the backup file.', 'it-l10n-better-wp-security' ) . '</p>';
		echo '<p class="submit"><input type="submit" class="button-primary" value="' . __( 'Create Database Backup', 'it-l10n-better-wp-security' ) . '" /></p>';
		echo '<p><a href="?page=toplevel_page_itsec_settings#itsec_backup_all_sites">' . __( 'Adjust Backup Settings', 'it-l10n-better-wp-security' ) . '</a>';
		echo '</form>';

	}

	/**
	 * Executes one-time backup.
	 *
	 * Performs execution of one-time backups which are typically called by the user.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function one_time_backup() {

		if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'itsec_do_backup' ) ) {
			die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
		}

		if ( ! class_exists( 'ITSEC_Backup' ) ) {
			require( dirname( __FILE__ ) . '/class-itsec-backup.php' );
		}

		$module = new ITSEC_Backup();
		$module->run( $this->core );
		$module->do_backup( true );

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

		$input['enabled']   = ( isset( $input['enabled'] ) && intval( $input['enabled'] == 1 ) ? true : false );
		$input['all_sites'] = ( isset( $input['all_sites'] ) && intval( $input['all_sites'] == 1 ) ? true : false );
		$input['interval']  = isset( $input['interval'] ) ? absint( $input['interval'] ) : 3;
		$input['method']    = isset( $input['method'] ) ? intval( $input['method'] ) : 0;
		$input['location']  = isset( $input['location'] ) ? sanitize_text_field( $input['location'] ) : $itsec_globals['ithemes_backup_dir'];
		$input['last_run']  = isset( $this->settings['last_run'] ) ? $this->settings['last_run'] : 0;
		$input['retain']    = isset( $input['retain'] ) ? absint( $input['retain'] ) : 0;

		if ( isset( $input['location'] ) && $input['location'] != $itsec_globals['ithemes_backup_dir'] ) {

			$good_path = ITSEC_Lib::validate_path( $input['location'] );

		} else {

			$good_path = true;

		}

		if ( true !== $good_path ) {

			$input['location'] = $itsec_globals['ithemes_backup_dir'];

			$type    = 'error';
			$message = __( 'The file path entered for the backup file location does not appear to be valid. it has been reset to: ' . $itsec_globals['ithemes_backup_dir'], 'it-l10n-better-wp-security' );

			add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

		}

		$input['exclude'] = ( isset( $input['exclude'] ) ? $input['exclude'] : array() );

		$input['zip'] = ( isset( $input['zip'] ) && intval( $input['zip'] == 1 ) ? true : false );

		if ( is_multisite() ) {

			if ( isset( $type ) ) {

				$error_handler = new WP_Error();

				$error_handler->add( $type, $message );

				$this->core->show_network_admin_notice( $error_handler );

			} else {

				$this->core->show_network_admin_notice( false );

			}

			$this->settings = $input;

		}

		return $input;

	}

	/**
	 * Is another backup plugin scheduling regular backups.
	 *
	 * Allows another backup plugin to let the dashboard status know if it is scheduling regular backups.
	 *
	 * @since 4.0.0
	 *
	 * @return bool true if another backup is scheduling backups or false.
	 */
	public function scheduled_backup() {

		$scheduled_backup = false;

		/**
		 * Is another backup plugin scheduling regular backups.
		 *
		 * Filterable variable to let this plugin know that another backup solution is scheduling regular backups.
		 *
		 * @since 4.0.0
		 *
		 * @param bool $scheduled_backup Whether or not another backup plugin is scheduling regular backups.
		 */

		return apply_filters( 'itsec_scheduled_external_backup', $scheduled_backup );

	}

	/**
	 * Echos all sites Field.
	 *
	 * Echos the settings field for backing up all sites vs a single site in multisite.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_all_sites() {

		if ( isset( $this->settings['all_sites'] ) && true === $this->settings['all_sites'] ) {

			$all_sites = 1;

		} else {

			$all_sites = 0;

		}

		echo '<input type="checkbox" id="itsec_backup_all_sites" name="itsec_backup[all_sites]" value="1" ' . checked( 1, $all_sites, false ) . '/>';
		echo '<label for="itsec_backup_all_sites"> ' . __( 'Checking this box will have the backup script backup all tables in your database, even if they are not part of this WordPress site.', 'it-l10n-better-wp-security' ) . '</label>';

	}

	/**
	 * echos Enable database backup Field
	 *
	 * Echo's the settings field that determines whether or not the database backup module is enabled.
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

		echo '<input type="checkbox" id="itsec_backup_enabled" name="itsec_backup[enabled]" value="1" ' . checked( 1, $enabled, false ) . '/>';
		echo '<label for="itsec_backup_enabled"> ' . __( 'Enable Scheduled Database Backups', 'it-l10n-better-wp-security' ) . '</label>';

	}

	/**
	 * echos exclude tables Field.
	 *
	 * Echo's the settings field that determines which tables will be excluded or included from the backup.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_exclude() {

		global $wpdb;

		$ignored_tables = array(
			'commentmeta',
			'comments',
			'links',
			'options',
			'postmeta',
			'posts',
			'term_relationships',
			'term_taxonomy',
			'terms',
			'usermeta',
			'users'
		);

		//get all of the tables
		if ( isset( $this->settings['all_sites'] ) && true === $this->settings['all_sites'] ) {

			$tables = $wpdb->get_results( 'SHOW TABLES', ARRAY_N ); //retrieve a list of all tables in the DB

		} else {

			$tables = $wpdb->get_results( 'SHOW TABLES LIKE "' . $wpdb->base_prefix . '%"', ARRAY_N ); //retrieve a list of all tables for this WordPress installation

		}

		echo '<label for="itsec_backup_exclude"> ' . __( 'Tables with data that does not need to be backed up', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<select multiple="multiple" name="itsec_backup[exclude][]" id="itsec_backup_exclude">';

		foreach ( $tables as $table ) {

			$short_table = substr( $table[0], strlen( $wpdb->prefix ) );

			if ( in_array( $short_table, $ignored_tables ) === false ) {

				if ( isset( $this->settings['exclude'] ) && in_array( $short_table, $this->settings['exclude'] ) ) {
					$selected = ' selected';
				} else {
					$selected = '';
				}

				echo '<option value="' . $short_table . '"' . $selected . '>' . $table[0] . '</option>';

			}

		}

		echo '</select>';
		echo '<p class="description"> ' . __( 'Some plugins can create log files in your database. While these logs might be handy for some functions, they can also take up a lot of space and, in some cases, even make backing up your database almost impossible. Select log tables above to exclude their data from the backup. Note: The table itself will be backed up, but not the data in the table.', 'it-l10n-better-wp-security' ) . '</p>';

	}

	/**
	 * echos Backup Interval Field.
	 *
	 * Echos the field that lets the user choose the numeric value applying to how often backups should occur.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_interval() {

		if ( isset( $this->settings['interval'] ) ) {

			$interval = absint( $this->settings['interval'] );

		} else {

			$interval = 3;

		}

		echo '<input class="small-text" name="itsec_backup[interval]" id="itsec_backup_interval" value="' . $interval . '" type="text"> ';
		echo '<label for="itsec_backup_interval"> ' . __( 'Days', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description"> ' . __( 'The number of days between database backups.', 'it-l10n-better-wp-security' ) . '</p>';

	}

	/**
	 * echos Backup Location Field.
	 *
	 * Echos the field that lets the user set where the backup should be stored.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_location() {

		global $itsec_globals;

		if ( isset( $this->settings['location'] ) ) {

			$location = sanitize_text_field( $this->settings['location'] );

		} else {

			$location = $itsec_globals['ithemes_backup_dir'];

		}

		echo '<input class="large-text" name="itsec_backup[location]" id="itsec_backup_location" value="' . $location . '" type="text">';
		echo '<label for="itsec_backup_location"> ' . __( 'The path on your machine where backup files should be stored.', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description"> ' . __( 'This path must be writable by your website. For added security, it is recommended you do not include it in your website root folder.', 'it-l10n-better-wp-security' ) . '</p>';
		echo '<input id="itsec_reset_backup_location" class="button-secondary" name="itsec_reset_backup_location" type="button" value="' . __( 'Restore Default Location', 'it-l10n-better-wp-security' ) . '" />' . PHP_EOL;

	}

	/**
	 * echos method Field.
	 *
	 * Echos the field that determines if backups will be saved locally, emailed or both.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $args field arguements
	 *
	 * @return void
	 */
	public function settings_field_method() {

		if ( isset( $this->settings['method'] ) ) {

			$method = $this->settings['method'];

		} else {

			$method = 0;

		}

		echo '<select id="itsec_backup_method" name="itsec_backup[method]">';

		echo '<option value="0" ' . selected( $method, '0' ) . '>' . __( 'Save Locally and Email', 'it-l10n-better-wp-security' ) . '</option>';
		echo '<option value="1" ' . selected( $method, '1' ) . '>' . __( 'Email Only', 'it-l10n-better-wp-security' ) . '</option>';
		echo '<option value="2" ' . selected( $method, '2' ) . '>' . __( 'Save Locally Only', 'it-l10n-better-wp-security' ) . '</option>';
		echo '</select><br />';
		echo '<label for="itsec_backup_method"> ' . __( 'Backup Save Method', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description">' . __( 'Select what we should do with your backup file. You can have it emailed to you, saved locally or both.' ) . '</p>';

	}

	/**
	 * echos Files to Retain Field
	 *
	 * Allows the user to set the number of files that are saved to disk.
	 *
	 * @since 4.0.27
	 *
	 * @return void
	 */
	public function settings_field_retain() {

		if ( isset( $this->settings['retain'] ) ) {

			$retain = absint( $this->settings['retain'] );

		} else {

			$retain = 0;

		}

		echo '<input class="small-text" name="itsec_backup[retain]" id="itsec_backup_retain" value="' . $retain . '" type="text">';
		echo '<label for="itsec_backup_retain"> ' . __( 'Backups', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description"> ' . __( 'Limit the number of backups stored locally (on this server). Any older backups beyond this number will be removed. Setting to "0" will retain all backups.', 'it-l10n-better-wp-security' ) . '</p>';

	}

	/**
	 * echos Zip Backups Field.
	 *
	 * Allows the user to choose whether or not database backups should be zipped before saving.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings_field_zip() {

		if ( isset( $this->settings['zip'] ) && false === $this->settings['zip'] ) {

			$zip = 0;

		} else {

			$zip = 1;

		}

		echo '<input type="checkbox" id="itsec_backup_zip" name="itsec_backup[zip]" value="1" ' . checked( 1, $zip, false ) . '/>';
		echo '<label for="itsec_backup_zip"> ' . __( 'Zip Database Backups', 'it-l10n-better-wp-security' ) . '</label>';
		echo '<p class="description">' . __( 'You may need to turn this off if you are having problems with backups.', 'it-l10n-better-wp-security' ) . '</p>';

	}

	/**
	 * Performs actions for tooltip function.
	 *
	 * When the backup button on the new activation tooltip is clicked this will execute the one time backup.
	 *
	 * @since 4.0.0
	 *
	 * return void
	 */
	public function tooltip_ajax() {

		if ( ! class_exists( 'ITSEC_Backup' ) ) {
			require( dirname( __FILE__ ) . '/class-itsec-backup.php' );
		}

		$module = new ITSEC_Backup();
		$module->run( $this->core );
		$result = $module->do_backup( true );

		if ( true === $result ) {

			die( 'true' );

		} else {

			die( 'false' );

		}

	}

}
