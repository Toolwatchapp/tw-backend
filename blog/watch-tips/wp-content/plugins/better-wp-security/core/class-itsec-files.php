<?php

/**
 * iThemes file handler.
 *
 * Writes to core files including wp-config.php, htaccess and nginx.conf.
 *
 * @package iThemes_Security
 *
 * @since   4.0.0
 */
final class ITSEC_Files {

	/**
	 * The module's that have registered with the file writer
	 *
	 * @since  4.0.0
	 * @access private
	 * @var array
	 */
	private $file_modules;

	/**
	 * The current rewrite rules
	 *
	 * @since  4.0.0
	 * @access private
	 * @var array
	 */
	private $rewrite_rules;

	/**
	 * The current wp-config.php rules
	 *
	 * @since  4.0.0
	 * @access private
	 * @var array
	 */
	private $wpconfig_rules;

	/**
	 * Whether or not rewrite rules have been modified externally
	 *
	 * @since  4.0.0
	 * @access private
	 * @var bool
	 */
	private $rewrites_changed;

	/**
	 * Whether or not wp-config.php rules have been modified externally
	 *
	 * @since  4.0.0
	 * @access private
	 * @var bool
	 */
	private $config_changed;

	/**
	 * Create and manage wp_config.php or .htaccess/nginx rewrites.
	 *
	 * Executes primary file actions at plugins_loaded.
	 *
	 * @since  4.0.0
	 *
	 * @return ITSEC_Files
	 */
	public function __construct() {

		$this->rewrites_changed = false;
		$this->config_changed   = false;
		$this->rewrite_rules    = array();
		$this->wpconfig_rules   = array();

		//Add the metabox
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) );
		add_action( 'plugins_loaded', array( $this, 'file_writer_init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );


		add_filter( 'itsec_filter_can_write_to_files', array( $this, 'can_write_to_files' ) );
	}
	
	/**
	 * Check the setting that allows writing files.
	 *
	 * @since 1.15.0
	 *
	 * @return bool True if files can be written to, false otherwise.
	 */
	public function can_write_to_files() {
		global $itsec_globals;
		
		if ( isset( $itsec_globals ) && isset( $itsec_globals['settings'] ) && isset( $itsec_globals['settings']['write_files'] ) && ( true === $itsec_globals['settings']['write_files'] ) ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Add meta boxes to primary options pages.
	 *
	 * Adds the meta boxes containing rewrite rules that appears on the iThemes Security
	 * Dashboard.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	function add_admin_meta_boxes() {

		add_meta_box(
			'itsec_rewrite',
			__( 'Rewrite Rules', 'it-l10n-better-wp-security' ),
			array( $this, 'rewrite_metabox' ),
			'toplevel_page_itsec',
			'bottom',
			'core'
		);

		add_meta_box(
			'itsec_wpconfig',
			__( 'wp-config.php Rules', 'it-l10n-better-wp-security' ),
			array( $this, 'config_metabox' ),
			'toplevel_page_itsec',
			'bottom',
			'core'
		);

	}

	/**
	 * Processes file writing after saving options.
	 *
	 * Looks to see if rewrites_changed is true and starts file writing process as appropriate
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function admin_init() {
		global $itsec_globals;
		
		if ( true === $this->rewrites_changed ) {
			if ( isset( $itsec_globals['settings']['write_files'] ) && true === $itsec_globals['settings']['write_files'] ) {
				do_action( 'itsec_pre_save_rewrites' );
				
				$rewrites = $this->save_rewrites();
				
				if ( is_array( $rewrites ) ) {
					if ( false === $rewrites['success'] ) {
						add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $rewrites['text'], 'error' );
						
						require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
						$file = ITSEC_Lib_Config_File::get_server_config_file_path();
						
						$message = sprintf( __( 'Unable to update the <code>%1$s</code> file. You may need to manually remove the existing iThemes Security modifications and replace them with the rules found at <a href="%2$s">Security > Dashboard</a> under the "Rewrite Rules" section.', 'it-l10n-better-wp-security' ), $file, admin_url( 'admin.php?page=itsec#itsec_rewrite' ) );
						add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, 'error' );
					} else if ( true !== $rewrites['text'] ) {
						add_settings_error( 'itsec', esc_attr( 'settings_updated' ), __( 'Settings Updated', 'it-l10n-better-wp-security' ) . '<br />' . $rewrites['text'], 'updated' );
					}
				} else {
					add_site_option( 'itsec_manual_update', true );
				}
			} else {
				add_site_option( 'itsec_manual_update', true );
			}
		}
		
		if ( true === $this->config_changed ) {
			if ( isset( $itsec_globals['settings']['write_files'] ) && true === $itsec_globals['settings']['write_files'] ) {
				do_action( 'itsec_pre_save_configs' );
				
				$configs = $this->save_wpconfig();
				
				if ( is_array( $configs ) ) {
					if ( false === $configs['success'] ) {
						add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $configs['text'], 'error' );
						
						$message = sprintf( __( 'Unable to update the <code>%1$s</code> file. You may need to manually remove the existing iThemes Security modifications and replace them with the rules found at <a href="%2$s">Security > Dashboard</a> under the "wp-config.php Rules" section.', 'it-l10n-better-wp-security' ), ABSPATH . 'wp-config.php', admin_url( 'admin.php?page=itsec#itsec_wpconfig' ) );
						add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, 'error' );
					}
					
					if ( 1 == get_site_option( 'itsec_clear_login' ) ) {
						delete_site_option( 'itsec_clear_login' );
						
						wp_clear_auth_cookie();
						
						$redirect_to = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : ITSEC_Lib::get_home_root() . 'wp-login.php?loggedout=true';
						wp_safe_redirect( $redirect_to );
						exit();
					}
				} else {
					add_site_option( 'itsec_manual_update', true );
				}
			} else {
				add_site_option( 'itsec_manual_update', true );
			}
		}
	}

	/**
	 * Calls config metabox action.
	 *
	 * Allows a hook to add to the metabox containing the wp-config.php rules.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function config_metabox() {

		do_action( 'itsec_wpconfig_metabox' );

	}

	/**
	 * Echos content metabox contents.
	 *
	 * Echos the contents of the wp-config.php metabox
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function config_metabox_contents() {
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
		
		$config = ITSEC_Lib_Config_File::get_wp_config();
		
		if ( empty( $config ) ) {
			_e( 'There are no rules to write.', 'it-l10n-better-wp-security' );
		} else {
			echo '<div class="itsec_rewrite_rules">' . highlight_string( $config, true ) . '</div>';
		}
	}

	/**
	 * Execute activation functions.
	 *
	 * Writes necessary information to wp-config and .htaccess upon plugin activation.
	 *
	 * @since  4.0.0
	 *
	 * @return void
	 */
	public function do_activate() {
		$this->save_wpconfig();
		$this->save_rewrites();
	}

	/**
	 * Execute deactivation functions.
	 *
	 * Writes necessary information to wp-config and .htaccess upon plugin deactivation.
	 *
	 * @since  4.0.0
	 *
	 * @return void
	 */
	public function do_deactivate() {
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
		
		ITSEC_Lib_Config_File::reset_wp_config();
		ITSEC_Lib_Config_File::reset_server_config();
	}

	/**
	 * Initialize file writer and rules arrays.
	 *
	 * Sets up initial information such as file locations and more to make
	 * calling quicker.
	 *
	 * @since  4.0.0
	 *
	 * @return void
	 */
	public function file_writer_init() {

		$this->file_modules = apply_filters( 'itsec_file_modules', $this->file_modules );

		if ( '1' == get_site_option( 'itsec_config_changed' ) || '1' == get_site_option( 'itsec_rewrites_changed' ) ) {

			$this->rewrites_changed = get_site_option( 'itsec_rewrites_changed' ) == '1' ? true : false;
			$this->config_changed   = get_site_option( 'itsec_config_changed' ) == '1' ? true : false;

			delete_site_option( 'itsec_rewrites_changed' );
			delete_site_option( 'itsec_config_changed' );

		}

	}

	/**
	 * Attempt to get a lock for atomic operations.
	 *
	 * Tries to get a more robust lock on the file in question. Useful in situations where automatic
	 * file locking doesn't work.
	 *
	 * @since  4.0.0
	 *
	 * @param string $lock_file file name of lock
	 * @param int    $exp       seconds until lock expires
	 *
	 * @return bool true if lock was achieved, else false
	 */
	public function get_file_lock( $lock_file, $exp = 180 ) {

		global $itsec_globals;

		clearstatcache();

		if ( isset( $itsec_globals['settings']['lock_file'] ) && true === $itsec_globals['settings']['lock_file'] ) {
			return true;
		}

		//Make sure the iThemes directory is actually there
		if ( ! @is_dir( $itsec_globals['ithemes_dir'] ) ) {

			@mkdir( $itsec_globals['ithemes_dir'] );
			$handle = @fopen( $itsec_globals['ithemes_dir'] . '/.htaccess', 'w+' );
			@fwrite( $handle, 'Deny from all' );
			@fclose( $handle );

		}

		$lock_file = $itsec_globals['ithemes_dir'] . '/' . sanitize_text_field( $lock_file ) . '.lock';
		$dir_age   = @filectime( $lock_file );

		if ( false === @mkdir( $lock_file ) ) {

			if ( false !== $dir_age ) {

				if ( ( time() - $dir_age ) > intval( $exp ) ) { //see if the lock has expired

					@rmdir( $lock_file );
					@mkdir( $lock_file );

				} else { //couldn't get the lock

					return false;

				}

			} else {

				return false;

			}

		}

		return true; //file lock was achieved

	}
	
	/**
	 * Sorts given arrays py priority key
	 *
	 * Allows for sorting of the rules array by a specified priority deeper in the array
	 *
	 * @since  4.0.0
	 *
	 * @access private
	 *
	 * @param  string $a value a
	 * @param  string $b value b
	 *
	 * @return int    -1 if a less than b, 0 if they're equal or 1 if a is greater
	 */
	private function priority_sort( $a, $b ) {

		if ( isset( $a['priority'] ) && isset( $b['priority'] ) ) {

			if ( $a['priority'] == $b['priority'] ) {
				return 0;
			}

			return $a['priority'] > $b['priority'] ? 1 : - 1;

		} else {

			return 1;

		}

	}

	/**
	 * Process quick ban of host.
	 *
	 * Immediately adds the supplied host to the .htaccess file for banning.
	 *
	 * @since 4.0.0
	 *
	 * @param string $host the host to ban
	 *
	 * @return bool true on success or false on failure
	 */
	public static function quick_ban( $host ) {
		$host = trim( $host );
		
		if ( ! ITSEC_Lib::validates_ip_address( $host ) ) {
			return false;
		}
		
		
		$host_rule = '# ' . __( 'Quick ban IP. Will be updated on next formal rules save.', 'it-l10n-better-wp-security' ) . "\n";
		
		if ( 'nginx' === ITSEC_Lib::get_server() ) {
			$host_rule .= "\tdeny $host;\n";
		} else if ( 'apache' === ITSEC_Lib::get_server() ) {
			$dhost = str_replace( '.', '\\.', $host ); //re-define $dhost to match required output for SetEnvIf-RegEX
			
			$host_rule .= "SetEnvIF REMOTE_ADDR \"^$dhost$\" DenyAccess\n"; //Ban IP
			$host_rule .= "SetEnvIF X-FORWARDED-FOR \"^$dhost$\" DenyAccess\n"; //Ban IP from Proxy-User
			$host_rule .= "SetEnvIF X-CLUSTER-CLIENT-IP \"^$dhost$\" DenyAccess\n"; //Ban IP for Cluster/Cloud-hosted WP-Installs
			$host_rule .= "<IfModule mod_authz_core.c>\n";
			$host_rule .= "\t<RequireAll>\n";
			$host_rule .= "\t\tRequire all granted\n";
			$host_rule .= "\t\tRequire not env DenyAccess\n";
			$host_rule .= "\t\tRequire not ip $host\n";
			$host_rule .= "\t</RequireAll>\n";
			$host_rule .= "</IfModule>\n";
			$host_rule .= "<IfModule !mod_authz_core.c>\n";
			$host_rule .= "\tOrder allow,deny\n";
			$host_rule .= "\tDeny from env=DenyAccess\n";
			$host_rule .= "\tDeny from $host\n";
			$host_rule .= "\tAllow from all\n";
			$host_rule .= "</IfModule>\n";
		}
		
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
		$result = ITSEC_Lib_Config_File::append_server_config( $host_rule );
		
		if ( is_error( $result ) ) {
			return false;
		}
		
		return true;
	}

	/**
	 * Release the lock.
	 *
	 * Releases a file lock to allow others to use it.
	 *
	 * @since  4.0.0
	 *
	 * @param string $lock_file file name of lock
	 *
	 * @return bool true if released, false otherwise
	 */
	public function release_file_lock( $lock_file ) {

		global $itsec_globals;

		if ( isset( $itsec_globals['settings']['lock_file'] ) && true === $itsec_globals['settings']['lock_file'] ) {
			return true;
		}

		$lock_file = $itsec_globals['ithemes_dir'] . '/' . sanitize_text_field( $lock_file ) . '.lock';

		if ( ! is_dir( $lock_file ) ) {

			return true;

		} else {

			if ( ! @rmdir( $lock_file ) ) {

				@chmod( $itsec_globals['ithemes_dir'], 0775 );

				if ( file_exists( $lock_file . '/Thumbs.db' ) ) {
					unlink( $lock_file . '/Thumbs.db' );
				}

				return @rmdir( $lock_file );

			} else {

				return true;

			}

		}

	}

	/**
	 * Calls rewrite metabox action.
	 *
	 * Executes the action to draw the htaccess rewrite rules metabox
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function rewrite_metabox() {

		do_action( 'itsec_rewrite_metabox' );

	}

	/**
	 * Echos rewrite metabox content.
	 *
	 * Echos the rewrite rules in the dashboard.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function rewrite_metabox_contents() {
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
		
		$config = ITSEC_Lib_Config_File::get_server_config();
		
		if ( empty( $config ) ) {
			_e( 'There are no rules to write.', 'it-l10n-better-wp-security' );
		} else {
			echo '<div class="itsec_rewrite_rules">' . highlight_string( $config, true ) . '</div>';
		}
	}

	/**
	 * Saves all rewrite rules to htaccess or similar file.
	 *
	 * Gets a file lock for .htaccess and calls the writing function if successful.
	 *
	 * @since  4.0.0
	 *
	 * @return mixed array or false if writing disabled or error message
	 */
	public function save_rewrites() {
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
		
		$result = ITSEC_Lib_Config_File::update_server_config();
		
		if ( is_wp_error( $result ) ) {
			$retval = array(
				'success' => false,
				'text'    => $result->get_error_message(),
			);
		} else {
			$server = ITSEC_Lib_Utility::get_web_server();
			
			if ( 'nginx' === $server ) {
				$retval = array(
					'success' => true,
					'text'    => __( 'You must restart your NGINX server for the settings to take effect', 'it-l10n-better-wp-security' ),
				);
			} else {
				$retval = array(
					'success' => true,
					'text'    => true,
				);
			}
		}
		
		return $retval;
	}

	/**
	 * Saves all wpconfig rules to wp-config.php.
	 *
	 * Gets a file lock for wp-config.php and calls the writing function if successful.
	 *
	 * @since  4.0.0
	 *
	 * @return mixed array or false if writing disabled or error message
	 */
	public function save_wpconfig() {
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-config-file.php' );
		
		$result = ITSEC_Lib_Config_File::update_wp_config();
		
		if ( is_wp_error( $result ) ) {
			$retval = array(
				'success' => false,
				'text'    => $result->get_error_message(),
			);
		} else {
			$retval = array(
				'success' => true,
				'text'    => true,
			);
		}
		
		return $retval;
	}
}
