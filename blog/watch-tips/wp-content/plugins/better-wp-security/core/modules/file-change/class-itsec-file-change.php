<?php

/**
 * File Change Detection Execution and Processing
 *
 * Handles all file change detection execution once the feature has been
 * enabled by the user.
 *
 * @since   4.0.0
 *
 * @package iThemes_Security
 */
class ITSEC_File_Change {

	/**
	 * Files and directories to be excluded from the scan
	 *
	 * @since  4.0.0
	 * @access private
	 * @var array
	 */
	private $excludes;

	/**
	 * Flag to indicate if a file change scan is in process
	 *
	 * @since  4.0.0
	 * @access private
	 * @var bool
	 */
	private $running;

	/**
	 * The module's saved options
	 *
	 * @since  4.0.0
	 * @access private
	 * @var array
	 */
	private $settings;

	/**
	 * Setup the module's functionality
	 *
	 * Loads the file change detection module's unpriviledged functionality including
	 * performing the scans themselves
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	function run() {

		global $itsec_globals;

		$this->settings = get_site_option( 'itsec_file_change' );
		$this->running  = false;
		$this->excludes = array(
			'file_change.lock',
			$itsec_globals['ithemes_backup_dir'],
			$itsec_globals['ithemes_log_dir'],
			'.lock',
		);
		$interval       = 86400; //Run daily

		// If we're splitting the file check run it every 6 hours.
		if ( isset( $this->settings['split'] ) && true === $this->settings['split'] ) {
			$interval = 12342;
		}

		add_filter( 'itsec_logger_modules', array( $this, 'itsec_logger_modules' ) );
		add_filter( 'itsec_sync_modules', array( $this, 'itsec_sync_modules' ) ); //register sync modules
		add_action( 'itsec_execute_file_check_cron', array( $this, 'execute_file_check' ) ); //Action to execute during a cron run.

		if (
			( ! defined( 'DOING_AJAX' ) || DOING_AJAX === false ) &&
			isset( $this->settings['enabled'] ) &&
			true === $this->settings['enabled'] &&
			isset( $this->settings['last_run'] ) &&
			( $itsec_globals['current_time'] - $interval ) > $this->settings['last_run'] &&
			( ! defined( 'ITSEC_FILE_CHECK_CRON' ) || false === ITSEC_FILE_CHECK_CRON )
		) {

			wp_clear_scheduled_hook( 'itsec_file_check' );
			add_action( 'init', array( $this, 'execute_file_check' ) );

		} elseif ( defined( 'ITSEC_FILE_CHECK_CRON' ) && true === ITSEC_FILE_CHECK_CRON && ! wp_next_scheduled( 'itsec_execute_file_check_cron' ) ) { //Use cron if needed

			wp_schedule_event( time(), 'daily', 'itsec_execute_file_check_cron' );

		}

	}

	/**
	 * Builds table section for file report
	 *
	 * Builds the individual table areas for files added, changed and deleted that goes in the file
	 * change notification emails.
	 *
	 * @since  4.6.0
	 *
	 * @access private
	 *
	 * @param string $title User readable title to display
	 * @param array  $files array of files to build the report on
	 *
	 * @return string the markup with the given files to be added to the report
	 */
	private function build_table_section( $title, $files ) {

		$section = '<h4>' . __( 'Files', 'it-l10n-better-wp-security' ) . ' ' . $title . '</h4>';
		$section .= '<table border="1" style="width: 100%; text-align: center;">' . PHP_EOL;
		$section .= '<tr>' . PHP_EOL;
		$section .= '<th>' . __( 'File', 'it-l10n-better-wp-security' ) . '</th>' . PHP_EOL;
		$section .= '<th>' . __( 'Modified', 'it-l10n-better-wp-security' ) . '</th>' . PHP_EOL;
		$section .= '<th>' . __( 'File Hash', 'it-l10n-better-wp-security' ) . '</th>' . PHP_EOL;
		$section .= '</tr>' . PHP_EOL;

		if ( isset( $files ) && is_array( $files ) && 0 < sizeof( $files ) ) {

			foreach ( $files as $item => $attr ) {

				$section .= '<tr>' . PHP_EOL;
				$section .= '<td>' . $item . '</td>' . PHP_EOL;
				$section .= '<td>' . date( 'l F jS, Y \a\t g:i a e', ( isset( $attr['mod_date'] ) ? $attr['mod_date'] : $attr['d'] ) ) . '</td>' . PHP_EOL;
				$section .= '<td>' . ( isset( $attr['hash'] ) ? $attr['hash'] : $attr['h'] ) . '</td>' . PHP_EOL;
				$section .= '</tr>' . PHP_EOL;

			}

		} else {

			$section .= '<tr>' . PHP_EOL;
			$section .= '<td colspan="3">' . __( 'No files were changed.', 'it-l10n-better-wp-security' ) . '</td>' . PHP_EOL;
			$section .= '</tr>' . PHP_EOL;

		}

		$section .= '</table>' . PHP_EOL;

		return $section;

	}

	/**
	 * Executes file checking
	 *
	 * Performs the actual execution of a file scan after determining that such an execution is needed.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $scheduled_call [optional] true if this is an automatic check
	 * @param bool $data           whether to return a data array (true) or not (false)
	 *
	 * @return mixed
	 */
	public function execute_file_check( $scheduled_call = true, $data = false ) {

		global $itsec_files, $itsec_logger, $itsec_globals;

		if ( false === $this->running ) {

			$this->running = true;
			$send_email    = true;

			ITSEC_Lib::set_minimum_memory_limit( '128M' );

			if ( $itsec_files->get_file_lock( 'file_change', 300 ) ) { //make sure it isn't already running

				define( 'ITSEC_DOING_FILE_CHECK', true );

				//figure out what chunk we're on
				if ( isset( $this->settings['split'] ) && true === $this->settings['split'] ) {

					if ( isset( $this->settings['last_chunk'] ) && false !== $this->settings['last_chunk'] && 6 > $this->settings['last_chunk'] ) {

						$chunk = $this->settings['last_chunk'] + 1;

					} else {

						$chunk = 0;

					}

				} else {

					$chunk = false;

				}

				if ( false !== $chunk ) {

					$db_field = 'itsec_local_file_list_' . $chunk;

				} else {

					$db_field = 'itsec_local_file_list';

				}

				//set base memory
				$memory_used = @memory_get_peak_usage();

				$logged_files = get_site_option( $db_field );

				//if there are no old files old file list is an empty array
				if ( false === $logged_files ) {

					$send_email = false;

					$logged_files = array();

					if ( is_multisite() ) {

						add_site_option( $db_field, $logged_files );

					} else {

						add_option( $db_field, $logged_files, '', 'no' );

					}

				}

				$current_files = $this->scan_files( '', $scheduled_call, $chunk ); //scan current files

				$itsec_files->release_file_lock( 'file_change' );

				$files_added          = @array_diff_assoc( $current_files, $logged_files ); //files added
				$files_removed        = @array_diff_assoc( $logged_files, $current_files ); //files deleted
				$current_minus_added  = @array_diff_key( $current_files, $files_added ); //remove all added files from current filelist
				$logged_minus_deleted = @array_diff_key( $logged_files, $files_removed ); //remove all deleted files from old file list
				$files_changed        = array(); //array of changed files

				//compare file hashes and mod dates
				foreach ( $current_minus_added as $current_file => $current_attr ) {

					if ( array_key_exists( $current_file, $logged_minus_deleted ) ) {

						//if attributes differ added to changed files array
						if (
							(
								(
									isset( $current_attr['mod_date'] ) &&
									0 != strcmp( $current_attr['mod_date'], $logged_minus_deleted[ $current_file ]['mod_date'] )
								) ||
								0 != strcmp( $current_attr['d'], $logged_minus_deleted[ $current_file ]['d'] )
							) ||
							(
								(
									isset( $current_attr['hash'] ) &&
									0 != strcmp( $current_attr['hash'], $logged_minus_deleted[ $current_file ]['hash'] ) ) ||
								0 != strcmp( $current_attr['h'], $logged_minus_deleted[ $current_file ]['h'] )
							)
						) {

							$remote_check = apply_filters( 'itsec_process_changed_file', true, $current_file, $current_attr['h'] ); //hook to run actions on a changed file at time of discovery

							if ( true === $remote_check ) { //don't list the file if it matches the WordPress.org hash

								$files_changed[ $current_file ]['h'] = isset( $current_attr['hash'] ) ? $current_attr['hash'] : $current_attr['h'];
								$files_changed[ $current_file ]['d'] = isset( $current_attr['mod_date'] ) ? $current_attr['mod_date'] : $current_attr['d'];

							}

						}

					}

				}

				//get count of changes
				$files_added_count   = sizeof( $files_added );
				$files_deleted_count = sizeof( $files_removed );
				$files_changed_count = sizeof( $files_changed );

				if ( 0 < $files_added_count ) {

					$files_added       = apply_filters( 'itsec_process_added_files', $files_added ); //hook to run actions on all files added
					$files_added_count = sizeof( $files_added );

				}

				if ( 0 < $files_deleted_count ) {
					do_action( 'itsec_process_removed_files', $files_removed ); //hook to run actions on all files removed
				}

				//create single array of all changes
				$full_change_list = array(
					'added'   => $files_added,
					'removed' => $files_removed,
					'changed' => $files_changed,
				);

				update_site_option( $db_field, $current_files );

				//Cleanup variables when we're done with them
				unset( $files_added );
				unset( $files_removed );
				unset( $files_changed );
				unset( $current_files );

				$this->settings['last_run']   = $itsec_globals['current_time'];
				$this->settings['last_chunk'] = $chunk;

				update_site_option( 'itsec_file_change', $this->settings );

				//get new max memory
				$check_memory = @memory_get_peak_usage();
				if ( $check_memory > $memory_used ) {
					$memory_used = $check_memory - $memory_used;
				}

				$full_change_list['memory'] = round( ( $memory_used / 1000000 ), 2 );

				$itsec_logger->log_event(
					'file_change',
					8,
					$full_change_list
				);

				if (
					true === $send_email &&
					false !== $scheduled_call &&
					isset( $this->settings['email'] ) &&
					true === $this->settings['email'] &&
					(
						0 < $files_added_count ||
						0 < $files_changed_count ||
						0 < $files_deleted_count
					)
				) {

					$email_details = array(
						$files_added_count,
						$files_deleted_count,
						$files_changed_count,
						$full_change_list
					);

					$this->send_notification_email( $email_details );
				}

				if (
					function_exists( 'get_current_screen' ) &&
					(
						! isset( get_current_screen()->id ) ||
						false === strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_logs' )
					) &&
					isset( $this->settings['notify_admin'] ) &&
					true === $this->settings['notify_admin']
				) {
					add_site_option( 'itsec_file_change_warning', true );
				}

				$itsec_files->release_file_lock( 'file_change' );

				if ( 0 < $files_added_count || 0 < $files_changed_count || 0 < $files_deleted_count ) {

					$this->running = false;

					//There were changes found
					if ( true === $data ) {

						return $full_change_list;

					} else {

						return true;

					}

				} else {

					$this->running = false;

					return false; //No changes were found

				}

			}

			$this->running = false;

			return - 1; //An error occured

		}

	}

	/**
	 * Get Report Details
	 *
	 * Creates the HTML markup for the email that is to be built
	 *
	 * @since 4.0.0
	 *
	 * @param array $email_details array of details to build email
	 *
	 * @return string report details
	 */
	public function get_email_report( $email_details ) {

		global $itsec_globals;

		//seperate array by category
		$added   = $email_details[3]['added'];
		$removed = $email_details[3]['removed'];
		$changed = $email_details[3]['changed'];
		$report  = '<strong>' . __( 'Scan Time:', 'it-l10n-better-wp-security' ) . '</strong> ' . date( 'l, F jS g:i a e', $itsec_globals['current_time'] ) . "<br />" . PHP_EOL;
		$report .= '<strong>' . __( 'Files Added:', 'it-l10n-better-wp-security' ) . '</strong> ' . $email_details[0] . "<br />" . PHP_EOL;
		$report .= '<strong>' . __( 'Files Deleted:', 'it-l10n-better-wp-security' ) . '</strong> ' . $email_details[1] . "<br />" . PHP_EOL;
		$report .= '<strong>' . __( 'Files Modified:', 'it-l10n-better-wp-security' ) . '</strong> ' . $email_details[2] . "<br />" . PHP_EOL;
		$report .= '<strong>' . __( 'Memory Used:', 'it-l10n-better-wp-security' ) . '</strong> ' . $email_details[3]['memory'] . " MB<br />" . PHP_EOL;

		$report .= $this->build_table_section( __( 'Added', 'it-l10n-better-wp-security' ), $added );
		$report .= $this->build_table_section( __( 'Deleted', 'it-l10n-better-wp-security' ), $removed );
		$report .= $this->build_table_section( __( 'Modified', 'it-l10n-better-wp-security' ), $changed );

		return $report;

	}

	/**
	 * Check file list
	 *
	 * Checks if given file should be included in file check based on exclude/include options
	 *
	 * @since  4.0.0
	 *
	 * @access private
	 *
	 * @param string $file path of file to check from site root
	 *
	 * @return bool true if file should be checked false if not
	 */
	private function is_checkable_file( $file ) {

		//get file list from last check
		$file_list = $this->settings['file_list'];
		$type_list = $this->settings['types'];

		//Make sure the file list is an array
		if ( ! is_array( $file_list ) ) {
			$file_list = array();
		}

		//lets check the absolute path too for excludes just to be sure
		$abs_file = ITSEC_Lib::get_home_path() . $file;

		//assume not a directory and not checked
		$flag = false;

		if ( is_array( $this->excludes ) && ( in_array( $file, $this->excludes ) || in_array( $abs_file, $this->excludes ) ) ) {
			return false;
		}

		if ( in_array( $file, $file_list ) ) {
			$flag = true;
		}

		if ( ! is_dir( $file ) ) {

			$path_info = pathinfo( $file );

			if ( isset( $path_info['extension'] ) && in_array( '.' . $path_info['extension'], $this->excludes ) ) {

				return false;

			}

			if ( isset( $path_info['extension'] ) && in_array( '.' . $path_info['extension'], $type_list ) ) {
				$flag = true;
			}

		}

		if ( true === $this->settings['method'] ) {

			if ( true === $flag ) { //if exclude reverse
				return false;
			} else {
				return true;
			}

		} else { //return flag

			return $flag;

		}

	}

	/**
	 * Register file change detection for logger
	 *
	 * Registers the file change detection module with the core logger functionality.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array array of logger modules
	 */
	public function itsec_logger_modules( $logger_modules ) {

		$logger_modules['file_change'] = array(
			'type'     => 'file_change',
			'function' => __( 'File Changes Detected', 'it-l10n-better-wp-security' ),
		);

		return $logger_modules;

	}

	/**
	 * Register file change detection for Sync
	 *
	 * Reigsters iThemes Sync verbs for the file change detection module.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $sync_modules array of sync modules
	 *
	 * @return array array of sync modules
	 */
	public function itsec_sync_modules( $sync_modules ) {

		$sync_modules['file-change'] = array(
			'verbs' => array(
				'itsec-perform-file-scan' => 'Ithemes_Sync_Verb_ITSEC_Perform_File_Scan',
			),
			'path'  => dirname( __FILE__ ),
		);

		return $sync_modules;

	}

	/**
	 * Scans all files in a given path
	 *
	 * Scans all items in a given path recursively building an array of items including
	 * hashes, filenames and modification dates
	 *
	 * @since  4.0.0
	 *
	 * @access private
	 *
	 * @param string $path           [optional] path to scan, defaults to WordPress root
	 * @param bool   $scheduled_call is this a scheduled call
	 * @param mixed  $chunk          the current chunk or false
	 *
	 * @return array array of files found and their information
	 *
	 */
	private function scan_files( $path = '', $scheduled_call, $chunk ) {

		if ( $chunk !== false ) {

			$content_dir = explode( '/', WP_CONTENT_DIR );
			$plugin_dir  = explode( '/', WP_PLUGIN_DIR );

			$dirs = array(
				'wp-admin/',
				'wp-includes/',
				$content_dir[ sizeof( $content_dir ) - 1 ] . '/',
				$content_dir[ sizeof( $content_dir ) - 1 ] . '/uploads/',
				$content_dir[ sizeof( $content_dir ) - 1 ] . '/themes/',
				$content_dir[ sizeof( $content_dir ) - 1 ] . '/' . $plugin_dir[ sizeof( $plugin_dir ) - 1 ] . '/',
				''
			);

			$path = $dirs[ $chunk ];

			unset( $dirs[ $chunk ] );

			$this->excludes = $dirs;

		}

		$data = array();

		$clean_path = sanitize_text_field( $path );

		if ( $directory_handle = @opendir( ITSEC_Lib::get_home_path() . $clean_path ) ) { //get the directory

			while ( false !== ( $item = @readdir( $directory_handle ) ) ) { // loop through dirs

				if ( '.' != $item && '..' != $item ) { //don't scan parents

					$relname = $path . $item;

					$absname = ITSEC_Lib::get_home_path() . $relname;

					if ( is_dir( $absname ) && 'dir' == filetype( $absname ) ) {

						$is_dir     = true;
						$check_name = trailingslashit( $relname );

					} else {

						$is_dir     = false;
						$check_name = $relname;

					}

					if ( true === $this->is_checkable_file( $check_name ) ) { //make sure the user wants this file scanned

						if ( true === $is_dir ) { //if directory scan it

							$data = array_merge( $data, $this->scan_files( $relname . '/', $scheduled_call, false ) );

						} else { //is file so add to array

							$data[ $relname ]      = array();
							$data[ $relname ]['d'] = @filemtime( $absname );
							$data[ $relname ]['h'] = @md5_file( $absname );

						}

					}

				}

			}

			@closedir( $directory_handle ); //close the directory we're working with

		}

		return $data; // return the files we found in this dir

	}

	/**
	 * Builds and sends notification email
	 *
	 * Sends the notication email too all applicable administrative users notifying them
	 * that file changes have been detected
	 *
	 * @since  4.0.0
	 *
	 * @access private
	 *
	 * @param array $email_details array of details for the email messge
	 *
	 * @return void
	 */
	private function send_notification_email( $email_details ) {

		global $itsec_globals, $itsec_notify;

		if ( ! isset( $itsec_globals['settings']['digest_email'] ) || false === $itsec_globals['settings']['digest_email'] ) {

			$headers = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' . "\r\n";
			$subject = '[' . get_option( 'siteurl' ) . '] ' . __( 'WordPress File Change Warning', 'it-l10n-better-wp-security' ) . ' ' . date( 'l, F jS, Y \a\\t g:i a e', $itsec_globals['current_time'] );

			$body = '<p>' . __( 'A file (or files) on your site at ', 'it-l10n-better-wp-security' ) . ' ' . get_option( 'siteurl' ) . __( ' have been changed. Please review the report below to verify changes are not the result of a compromise.', 'it-l10n-better-wp-security' ) . '</p>';
			$body .= $this->get_email_report( $email_details ); //get report

			$args = array(
				'headers' => $headers,
				'message' => $body,
				'subject' => $subject,
			);

			$itsec_notify->notify( $args );

		} else {

			$changed = $email_details[0] + $email_details[1] + $email_details[2];

			if ( 0 < $changed ) {

				$message = sprintf(
					'<strong>%s:</strong> %s %s.',
					__( 'File changes detected', 'it-l10n-better-wp-security' ),
					$itsec_globals['plugin_name'],
					__( 'detected file changes on your system', 'it-l10n-better-wp-security' )
				);

				$itsec_notify->notify( $message );

			}

		}

	}

	/**
	 * Set HTML content type for email
	 *
	 * This filter allows for the content type of the file change notification emails to be set to
	 * HTML in order to send the tables and related data included in file change reporting.
	 *
	 * @since 4.0.0
	 *
	 * @return string html content type
	 */
	public function set_html_content_type() {

		return 'text/html';

	}

}
