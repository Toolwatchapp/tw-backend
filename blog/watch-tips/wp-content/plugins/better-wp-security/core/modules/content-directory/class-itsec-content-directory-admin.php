<?php

class ITSEC_Content_Directory_Admin {

	private
		$last_error,
		$core,
		$module_path;

	function run( $core ) {

		$this->core        = $core;
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_filter( 'itsec_tracking_vars', array( $this, 'tracking_vars' ) );
		add_filter( 'itsec_add_dashboard_status', array( $this, 'dashboard_status' ) );

		if ( ! empty( $_POST ) ) {
			add_action( 'itsec_admin_init', array( $this, 'initialize_admin' ) );
		}

		if ( ! $this->is_custom_directory() ) {
			// Changing the content directory is only supported when the content directory is set to default values.
			
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) );
			add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) );
		}
	}
	
	protected function is_custom_directory() {
		if ( isset( $GLOBALS['__itsec_content_directory_is_custom_directory'] ) ) {
			return $GLOBALS['__itsec_content_directory_is_custom_directory'];
		}
		
		if ( ABSPATH . 'wp-content' !== WP_CONTENT_DIR ) {
			$GLOBALS['__itsec_content_directory_is_custom_directory'] = true;
		} else if ( get_option( 'siteurl' ) . '/wp-content' !== WP_CONTENT_URL ) {
			$GLOBALS['__itsec_content_directory_is_custom_directory'] = true;
		} else {
			$GLOBALS['__itsec_content_directory_is_custom_directory'] = false;
		}
		
		return $GLOBALS['__itsec_content_directory_is_custom_directory'];
	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * @param array $available_pages array of available page_hooks
	 */
	public function add_admin_meta_boxes() {
		add_meta_box(
			'content_directory_options',
			__( 'Change Content Directory', 'it-l10n-better-wp-security' ),
			array( $this, 'metabox_advanced_settings' ),
			'security_page_toplevel_page_itsec_advanced',
			'advanced',
			'core'
		);
	}

	/**
	 * Add Away mode Javascript
	 *
	 * @return void
	 */
	public function admin_script() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_advanced' ) !== false ) {

			wp_enqueue_script( 'itsec_content_directory_js', $this->module_path . 'js/admin-content_directory.js', array( 'jquery' ), $itsec_globals['plugin_build'] );

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

		if ( $this->is_custom_directory() ) {

			$status_array = 'safe-low';
			$status       = array(
				'text' => __( 'You have renamed the wp-content directory of your site.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_enable_content_dir', 'advanced' => true,
			);

		} else {

			$status_array = 'low';
			$status       = array(
				'text' => __( 'You should rename the wp-content directory of your site.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_enable_content_dir', 'advanced' => true,
			);

		}

		array_push( $statuses[$status_array], $status );

		return $statuses;

	}

	/**
	 * Execute admin initializations
	 *
	 * @return void
	 */
	public function initialize_admin() {

		if ( ! $this->is_custom_directory() && isset( $_POST['itsec_enable_content_dir'] ) && 'true' == $_POST['itsec_enable_content_dir'] ) {

			if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'ITSEC_admin_save' ) ) {

				die( __( 'Security check', 'it-l10n-better-wp-security' ) );

			}

			$this->process_directory();

		}

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function metabox_advanced_settings() {

		global $itsec_globals;

		if ( $this->is_custom_directory() ) {

			if ( isset( $_POST['itsec_one_time_save'] ) ) {

				$dir_name = sanitize_file_name( $_POST['name'] );

			} else {

				$dir_name = substr( WP_CONTENT_DIR, strrpos( WP_CONTENT_DIR, '/' ) + 1 );
			}

			$content = '<p>' . __( 'Congratulations! You have already renamed your "wp-content" directory.', 'it-l10n-better-wp-security' ) . '</p>';
			$content .= '<p>' . __( 'Your current content directory is: ', 'it-l10n-better-wp-security' );
			$content .= '<strong>' . $dir_name . '</strong></p>';
			$content .= '<p>' . __( 'No further actions are available on this page.', 'it-l10n-better-wp-security' ) . '</p>';

		} else {

			$content = '<p>' . __( 'By default, WordPress puts all your content (including images, plugins, themes, uploads and more) in a directory called "wp-content." This default folder name makes it easy for attackers to scan for files with security vulnerabilities on your WordPress installation because they know where the vulnerable files are located. Moving the "wp-content" folder can make it more difficult for an attacker to find problems with your site, as scans of your site\'s file system will not produce any results.', 'it-l10n-better-wp-security' ) . '</p>';
			$content .= '<p>' . __( 'This tool will not allow further changes to your wp-content folder once it has been renamed in order to avoid accidentally breaking the site later. Uninstalling this plugin will not revert the changes made by this feature.', 'it-l10n-better-wp-security' ) . '</p>';
			$content .= '<p>' . __( 'Changing the name of the wp-content directory may in fact break plugins and themes that have "hard-coded" it into their design rather than calling it dynamically.', 'it-l10n-better-wp-security' ) . '</p>';
			$content .= sprintf( '<div class="itsec-warning-message"><span>%s: </span><a href="?page=toplevel_page_itsec_backups">%s</a> %s</div>', __( 'WARNING', 'it-l10n-better-wp-security' ), __( 'Backup your database', 'it-l10n-better-wp-security' ), __( 'before using this tool.', 'it-l10n-better-wp-security' ) );
			$content .= '<div class="itsec-warning-message">' . __( 'Please note: Changing the name of your wp-content directory on a site that already has images and other content referencing it will break your site. For this reason, we highly recommend you only try this technique on a fresh WordPress install.', 'it-l10n-better-wp-security' ) . '</div>';
			$content .= '<div class="itsec-warning-message">' . __( '<strong>WARNING:</strong> BackupBuddy only works with sites that have the standard wp-content folder name and location. If you have altered the name or the location of this folder, BackupBuddy will be unable to properly create backups or migrate the site.', 'it-l10n-better-wp-security' ) . '</div>';

		}

		echo $content;

		if ( isset( $itsec_globals['settings']['write_files'] ) && $itsec_globals['settings']['write_files'] === true ) {

			if ( ! $this->is_custom_directory() ) {
				?>

				<form method="post" action="?page=toplevel_page_itsec_advanced&settings-updated=true" class="itsec-form">

					<?php wp_nonce_field( 'ITSEC_admin_save', 'wp_nonce' ); ?>

					<table class="form-table">
						<tr valign="top">
							<th scope="row" class="settinglabel">
								<label for="itsec_enable_content_dir"><?php _e( 'Enable Change Directory Name', 'it-l10n-better-wp-security' ); ?></label>
							</th>
							<td class="settingfield">
								<input type="checkbox" id="itsec_enable_content_dir" name="itsec_enable_content_dir" value="true"/>

								<p class="description"><?php _e( 'Check this box to enable content directory renaming.', 'it-l10n-better-wp-security' ); ?></p>
							</td>
						</tr>
						<tr valign="top" id="content_directory_name_field">
							<th scope="row" class="settinglabel">
								<label for="itsec_content_name"><?php _e( 'Directory Name', 'it-l10n-better-wp-security' ); ?></label>
							</th>
							<td class="settingfield">
								<input id="itsec_content_name" name="name" type="text" value="wp-content"/>

								<p class="description"><?php _e( 'Enter a new directory name to replace "wp-content." You may need to log in again after performing this operation.', 'it-l10n-better-wp-security' ); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Change Content Directory', 'it-l10n-better-wp-security' ); ?>"/>
					</p>
				</form>

			<?php

			}

		} else {
			echo '<p>' . sprintf( __( 'You must allow this plugin to write to the wp-config.php file on the <a href="%s">Settings</a> page to use this feature.', 'it-l10n-better-wp-security' ), admin_url( 'admin.php?page=toplevel_page_itsec_settings' ) ) . '</p>';
		}

	}

	public function process_directory() {
		if ( $this->is_custom_directory() ) {
			$this->show_error( __( 'The <code>wp-content</code> directory has already been renamed. No Directory Name changes have been made.', 'it-l10n-better-wp-security' ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		
		$dir_name = sanitize_file_name( $_POST['name'] );
		
		if ( empty( $dir_name ) ) {
			$this->show_error( __( 'The Directory Name cannot be empty.', 'it-l10n-better-wp-security' ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		if ( 'wp-content' === $dir_name ) {
			$this->show_error( __( 'You have not chosen a new name for wp-content. Nothing was saved.', 'it-l10n-better-wp-security' ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		if ( preg_match( '{^(?:/|\\|[a-z]:)}i', $dir_name ) ) {
			$this->show_error( sprintf( __( 'The Directory Name cannot be an absolute path. Please supply a path that is relative to <code>ABSPATH</code> (<code>%s</code>).', 'it-l10n-better-wp-security' ), ABSPATH ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		
		$dir = ABSPATH . $dir_name;
		
		if ( file_exists( $dir ) ) {
			$this->show_error( sprintf( __( 'A file or directory already exists at <code>%s</code>. No Directory Name changes have been made. Please choose a new Directory Name or remove the existing file or directory and try again.', 'it-l10n-better-wp-security' ), $dir ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
		
		
		$old_permissions = ITSEC_Lib_Directory::get_permissions( WP_CONTENT_DIR );
		$result = rename( WP_CONTENT_DIR, $dir );
		
		if ( ! $result ) {
			$this->show_error( sprintf( __( 'Unable to rename the <code>wp-content</code> directory to <code>%s</code>. This could indicate a file permission issue or that your server does not support the supplied name as a valid directory name. No config file or directory changes have been made.', 'it-l10n-better-wp-security' ), $dir_name ) );
			$this->show_network_admin_notice();
			
			return;
		}
		
		$new_permissions = ITSEC_Lib_Directory::get_permissions( $dir );
		
		if ( is_int( $old_permissions) && is_int( $new_permissions ) && ( $old_permissions != $new_permissions ) ) {
			$result = ITSEC_Lib_Directory::chmod( $dir, $old_permissions );
			
			if ( is_wp_error( $result ) ) {
				$this->show_error( sprintf( __( 'Unable to set the permissions of the new Directory Name (<code>%1$s</code>) to match the permissions of the old Directory Name. You may have to manually change the permissions of the directory to <code>%2$s</code> in order for your site to function properly.', 'it-l10n-better-wp-security' ), $dir_name, $old_permissions ) );
			}
		}
		
		
		$php_content_dir = str_replace( "'", "\\'", $dir );
		$php_content_url = str_replace( "'", "\\'", get_option( 'siteurl' ) . "/$dir_name" );
		
		$modification  = "define( 'WP_CONTENT_DIR', '$php_content_dir' ); // " . __( 'Do not remove. Removing this line could break your site. Added by Security > Settings > Change Content Directory.', 'it-l10n-better-wp-security' ) . "\n";
		$modification .= "define( 'WP_CONTENT_URL', '$php_content_url' ); // " . __( 'Do not remove. Removing this line could break your site. Added by Security > Settings > Change Content Directory.', 'it-l10n-better-wp-security' ) . "\n";
		
		$append_result = ITSEC_Lib_Config_File::append_wp_config( $modification, true );
		
		
		if ( is_wp_error( $append_result ) ) {
			$rename_result = rename( $dir, WP_CONTENT_DIR );
			
			if ( $rename_result ) {
				ITSEC_Lib_Directory::chmod( WP_CONTENT_DIR, $old_permissions );
				
				$this->show_error( sprintf( __( 'Unable to update the <code>wp-config.php</code> file. No directory or config file changes have been made. %1$s (%2$s)', 'it-l10n-better-wp-security' ), $append_result->get_error_message(), $append_result->get_error_code() ) );
				
				$this->show_error( sprintf( __( 'In order to change the content directory on your server, you will have to manually change the configuration and rename the directory. Details can be found <a href="%s">here</a>.', 'it-l10n-better-wp-security' ), 'https://codex.wordpress.org/Editing_wp-config.php#Moving_wp-content_folder' ) );
			} else {
				$this->show_error( sprintf( __( 'CRITICAL ERROR: The <code>wp-content</code> directory was successfully renamed to the new name (<code>%1$s</code>). However, an error occurred when updating the <code>wp-config.php</code> file to configure WordPress to use the new content directory. iThemes Security attempted to rename the directory back to its original name, but an unknown error prevented the rename from working as expected. In order for your site to function properly, you will either need to rename the <code>%1$s</code> directory back to <code>wp-content</code> or manually update the <code>wp-config.php</code> file with the necessary modifications. Instructions for making this modification can be found <a href="%2$s">here</a>.', 'it-l10n-better-wp-security' ), $dir_name, 'https://codex.wordpress.org/Editing_wp-config.php#Moving_wp-content_folder' ) );
				
				$this->show_error( sprintf( __( 'Details on the error that prevented the <code>wp-config.php</code> file from updating is as follows: %1$s (%2$s)', 'it-l10n-better-wp-security' ), $append_result->get_error_message(), $append_result->get_error_code() ) );
			}
			
			return;
		}


		$backup = get_site_option( 'itsec_backup' );

		if ( $backup !== false && isset( $backup['location'] ) ) {

			$backup['location'] = str_replace( WP_CONTENT_DIR, $dir, $backup['location'] );
			update_site_option( 'itsec_backup', $backup );

		}

		$global = get_site_option( 'itsec_global' );

		if ( $global !== false && ( isset( $global['log_location'] ) || isset( $global['nginx_file'] ) ) ) {

			if ( isset( $global['log_location'] ) ) {
				$global['log_location'] = str_replace( WP_CONTENT_DIR, $dir, $global['log_location'] );
			}

			if ( isset( $global['nginx_file'] ) ) {
				$global['nginx_file'] = str_replace( WP_CONTENT_DIR, $dir, $global['nginx_file'] );
			}

			update_site_option( 'itsec_global', $global );

		}

		$this->show_network_admin_notice();
	}
	
	// TODO: Created from old code. Needs to be rebuilt.
	protected function show_error( $message ) {
		add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, 'error' );
		
		$this->last_error = $message;
	}
	
	// TODO: Created from old code. Needs to be rebuilt.
	protected function show_network_admin_notice() {
		if ( is_multisite() ) {
			if ( empty( $this->last_error ) ) {
				$this->core->show_network_admin_notice( false );
			} else {
				$error_handler = new WP_Error();
				$error_handler->add( 'error', $this->last_error );
				
				$this->core->show_network_admin_notice( $error_handler );
			}
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

		$vars['content_directory'] = array(
			'enabled' => '0:b',
		);

		return $vars;

	}

}
