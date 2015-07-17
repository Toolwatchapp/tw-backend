<?php

class ITSEC_Ban_Users_Admin {

	private
		$settings,
		$core,
		$module_path;

	function run( $core ) {

		$this->core        = $core;
		$this->settings    = get_site_option( 'itsec_ban_users' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_filter( 'itsec_file_modules', array( $this, 'register_file' ) ); //register tooltip action
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_action( 'itsec_admin_init', array( $this, 'initialize_admin' ) ); //initialize admin area
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) ); //enqueue scripts for admin page
		add_filter( 'itsec_add_dashboard_status', array( $this, 'dashboard_status' ) ); //add information for plugin status
		add_filter( 'itsec_tracking_vars', array( $this, 'tracking_vars' ) );

		//manually save options on multisite
		if ( is_multisite() ) {
			add_action( 'itsec_admin_init', array( $this, 'save_network_options' ) ); //save multisite options
		}


		add_filter( 'itsec_filter_apache_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		add_filter( 'itsec_filter_nginx_server_config_modification', array( $this, 'filter_nginx_server_config_modification' ) );
		add_filter( 'itsec_filter_litespeed_server_config_modification', array( $this, 'filter_litespeed_server_config_modification' ) );
	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function add_admin_meta_boxes() {

		$id    = 'ban_users_options';
		$title = __( 'Banned Users', 'it-l10n-better-wp-security' );

		add_meta_box(
			$id,
			$title,
			array( $this, 'metabox_advanced_settings' ),
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
	 * Add Away mode Javascript
	 *
	 * @return void
	 */
	public function admin_script() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) !== false ) {

			wp_enqueue_script( 'itsec_ban_users_js', $this->module_path . 'js/admin-ban_users.js', array( 'jquery' ), $itsec_globals['plugin_build'] );

		}

	}

	/**
	 * echos Banned Agents field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function ban_users_agent_list() {

		$agent_list = '';

		//Convert and show the agent list
		if ( isset( $this->settings['agent_list'] ) && is_array( $this->settings['agent_list'] ) && sizeof( $this->settings['agent_list'] ) >= 1 ) {

			$agent_list = implode( PHP_EOL, $this->settings['agent_list'] );

		} elseif ( isset( $this->settings['agent_list'] ) && ! is_array( $this->settings['agent_list'] ) && strlen( $this->settings['agent_list'] ) > 1 ) {

			$agent_list = $this->settings['agent_list'];

		}

		$content = '<textarea id="itsec_ban_users_agent_list" name="itsec_ban_users[agent_list]" rows="10" cols="50">' . $agent_list . PHP_EOL . '</textarea>';
		$content .= '<p>' . __( 'Use the guidelines below to enter user agents that will not be allowed access to your site.', 'it-l10n-better-wp-security' ) . '</p>';
		$content .= '<ul>';
		$content .= '<li>' . __( 'Enter only 1 user agent per line.', 'it-l10n-better-wp-security' ) . '</li>';
		$content .= '</ul>';

		echo $content;

	}

	/**
	 * echos hackrepair list Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function ban_users_default() {

		if ( isset( $this->settings['default'] ) && $this->settings['default'] === true ) {
			$default = 1;
		} else {
			$default = 0;
		}

		$content = '<input type="checkbox" id="itsec_ban_users_default" name="itsec_ban_users[default]" value="1" ' . checked( 1, $default, false ) . '/>';
		$content .= '<label for="itsec_ban_users_default"> ' . __( 'Enable HackRepair.com\'s blacklist feature', 'it-l10n-better-wp-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'As a getting-started point you can include the excellent blacklist developed by Jim Walker of <a href="http://hackrepair.com/blog/how-to-block-bots-from-seeing-your-website-bad-bots-and-drive-by-hacks-explained" target="_blank">HackRepair.com</a>.', 'it-l10n-better-wp-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Enabled Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function ban_users_enabled() {

		if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {
			$enabled = 1;
		} else {
			$enabled = 0;
		}

		$content = '<input type="checkbox" id="itsec_ban_users_enabled" name="itsec_ban_users[enabled]" value="1" ' . checked( 1, $enabled, false ) . '/>';
		$content .= '<label for="itsec_ban_users_enabled"> ' . __( 'Enable ban users', 'it-l10n-better-wp-security' ) . '</label>';

		echo $content;

	}

	/**
	 * echos Banned Hosts field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function ban_users_host_list() {

		$host_list = '';

		//Convert and show the host list
		if ( isset( $this->settings['host_list'] ) && is_array( $this->settings['host_list'] ) && sizeof( $this->settings['host_list'] ) >= 1 ) {

			$host_list = implode( PHP_EOL, $this->settings['host_list'] );

		} elseif ( isset( $this->settings['host_list'] ) && ! is_array( $this->settings['host_list'] ) && strlen( $this->settings['host_list'] ) > 1 ) {

			$host_list = $this->settings['host_list'];

		}

		echo '<textarea id="itsec_ban_users_host_list" name="itsec_ban_users[host_list]" rows="10" cols="50">' . $host_list . PHP_EOL . '</textarea>';
		echo '<p>' . __( 'Use the guidelines below to enter hosts that will not be allowed access to your site. Note you cannot ban yourself.', 'it-l10n-better-wp-security' ) . '</p>';
		echo '<ul>';
		echo '<li>' . __( 'You may ban users by individual IP address or IP address range.', 'it-l10n-better-wp-security' ) . '</li>';
		echo '<li>' . __( 'Individual IP addesses must be in IPV4 standard format (i.e. ###.###.###.### or ###.###.###.###/##). Wildcards (*) or a netmask is allowed to specify a range of ip addresses.', 'it-l10n-better-wp-security' ) . '</li>';
		echo '<li>' . __( 'If using a wildcard (*) you must start with the right-most number in the ip field. For example ###.###.###.* and ###.###.*.* are permitted but ###.###.*.### is not.', 'it-l10n-better-wp-security' ) . '</li>';
		echo '<li><a href="http://ip-lookup.net/domain-lookup.php" target="_blank">' . __( 'Lookup IP Address.', 'it-l10n-better-wp-security' ) . '</a></li>';
		echo '<li>' . __( 'Enter only 1 IP address or 1 IP address range per line.', 'it-l10n-better-wp-security' ) . '</li>';
		echo '</ul>';

	}

	public function filter_apache_server_config_modification( $modification ) {
		$modification .= $this->get_server_config_default_blacklist_rules( 'apache' );
		$modification .= $this->get_server_config_ban_hosts_rules( 'apache' );
		$modification .= $this->get_server_config_ban_user_agents_rules( 'apache' );
		
		return $modification;
	}
	
	public function filter_nginx_server_config_modification( $modification ) {
		$modification .= $this->get_server_config_default_blacklist_rules( 'nginx' );
		$modification .= $this->get_server_config_ban_hosts_rules( 'nginx' );
		$modification .= $this->get_server_config_ban_user_agents_rules( 'nginx' );
		
		return $modification;
	}
	
	public function filter_litespeed_server_config_modification( $modification ) {
		$modification .= $this->get_server_config_default_blacklist_rules( 'litespeed' );
		$modification .= $this->get_server_config_ban_hosts_rules( 'litespeed' );
		$modification .= $this->get_server_config_ban_user_agents_rules( 'litespeed' );
		
		return $modification;
	}
	
	protected function get_server_config_default_blacklist_rules( $server_type ) {
		if ( true !== $this->settings['default'] ) {
			return '';
		}
		
		
		$rules = '';
		
		require_once( trailingslashit( $GLOBALS['itsec_globals']['plugin_dir'] ) . 'core/lib/class-itsec-lib-file.php' );
		
		$file = plugin_dir_path( __FILE__ ) . "lists/hackrepair-$server_type.inc";
		
		if ( ITSEC_Lib_File::is_file( $file ) ) {
			$default_list = ITSEC_Lib_File::read( $file );
			
			if ( ! empty( $default_list ) ) {
				$default_list = preg_replace( '/^/m', "\t", $default_list );
				
				$rules .= "\n";
				$rules .= "\t# " . __( 'Enable HackRepair.com\'s blacklist feature - Security > Settings > Banned Users > Default Blacklist', 'it-l10n-better-wp-security' ) . "\n";
				$rules .= $default_list;
			}
		}
		
		return $rules;
	}
	
	protected function get_server_config_ban_hosts_rules( $server_type ) {
		if ( true !== $this->settings['enabled']  ) {
			return '';
		}
		if ( ! is_array( $this->settings['host_list'] ) || empty( $this->settings['host_list'] ) ) {
			return '';
		}
		
		
		
		if ( ! class_exists( 'ITSEC_Ban_Users' ) ) {
			require( dirname( __FILE__ ) . '/class-itsec-ban-users.php' );
		}
		
		
		$host_rules = '';
		$set_env_rules = '';
		$deny_rules = '';
		$require_rules = '';
		
		// process hosts list
		foreach ( $this->settings['host_list'] as $host ) {
			$host = ITSEC_Lib::ip_wild_to_mask( $host );
			$host = trim( $host );
			
			if ( empty( $host ) ) {
				continue;
			}
			
			if ( ITSEC_Ban_Users::is_ip_whitelisted( $host ) ) {
				/**
				 * @todo warn the user the ip to be banned is whitelisted
				 */
				continue;
			}
			
			
			if ( in_array( $server_type, array( 'apache', 'litespeed' ) ) ) {
				$converted_host = ITSEC_Lib::ip_mask_to_range( $host );
				$converted_host = trim( $converted_host );
				
				if ( empty( $converted_host ) ) {
					continue;
				}
				
				
				$set_env_host = str_replace( '.', '\\.', $converted_host );
				
				$set_env_rules .= "\tSetEnvIF REMOTE_ADDR \"^$set_env_host$\" DenyAccess\n"; // Ban IP
				$set_env_rules .= "\tSetEnvIF X-FORWARDED-FOR \"^$set_env_host$\" DenyAccess\n"; // Ban IP from a proxy
				$set_env_rules .= "\tSetEnvIF X-CLUSTER-CLIENT-IP \"^$set_env_host$\" DenyAccess\n"; // Ban IP from a load balancer
				$set_env_rules .= "\n";
				
				
				$require_host = str_replace( '.[0-9]+', '', $converted_host );
				
				$require_rules .= "\t\t\tRequire not ip $require_host\n";
				$deny_rules .= "\t\tDeny from $require_host\n";
			} else if ( 'nginx' === $server_type ) {
				$host_rules .= "\tdeny $host;\n";
			}
		}
		
		
		$rules = '';
		
		if ( 'apache' === $server_type ) {
			if ( ! empty( $set_env_rules ) ) {
				$rules .= "\n";
				$rules .= "\t# " . __( 'Ban Hosts - Security > Settings > Banned Users', 'it-l10n-better-wp-security' ) . "\n";
				$rules .= $set_env_rules;
				$rules .= "\t<IfModule mod_authz_core.c>\n";
				$rules .= "\t\t<RequireAll>\n";
				$rules .= "\t\t\tRequire all granted\n";
				$rules .= "\t\t\tRequire not env DenyAccess\n";
				$rules .= $require_rules;
				$rules .= "\t\t</RequireAll>\n";
				$rules .= "\t</IfModule>\n";
				$rules .= "\t<IfModule !mod_authz_core.c>\n";
				$rules .= "\t\tOrder allow,deny\n";
				$rules .= "\t\tAllow from all\n";
				$rules .= "\t\tDeny from env=DenyAccess\n";
				$rules .= $deny_rules;
				$rules .= "\t</IfModule>\n";
			}
		} else if ( 'litespeed' === $server_type ) {
			if ( ! empty( $set_env_rules ) ) {
				$rules .= "\n";
				$rules .= "\t# " . __( 'Ban Hosts - Security > Settings > Banned Users', 'it-l10n-better-wp-security' ) . "\n";
				$rules .= $set_env_rules;
				$rules .= "\t<IfModule mod_litespeed.c>\n";
				$rules .= "\t\tOrder allow,deny\n";
				$rules .= "\t\tAllow from all\n";
				$rules .= "\t\tDeny from env=DenyAccess\n";
				$rules .= $deny_rules;
				$rules .= "\t</IfModule>\n";
			}
		} else if ( 'nginx' === $server_type ) {
			if ( ! empty( $host_rules ) ) {
				$rules .= "\n";
				$rules .= "# " . __( 'Ban Hosts - Security > Settings > Banned Users', 'it-l10n-better-wp-security' ) . "\n";
				$rules .= $host_rules;
			}
		}
		
		return $rules;
	}
	
	protected function get_server_config_ban_user_agents_rules( $server_type ) {
		if ( true !== $this->settings['enabled']  ) {
			return '';
		}
		if ( ! is_array( $this->settings['agent_list'] ) || empty( $this->settings['agent_list'] ) ) {
			return '';
		}
		
		
		$agent_rules = '';
		$rewrite_rules = '';
		
		foreach ( $this->settings['agent_list'] as $index => $agent ) {
			$agent = trim( $agent );
			
			if ( empty( $agent ) ) {
				continue;
			}
			
			
			$agent = preg_quote( $agent );
			
			if ( in_array( $server_type, array( 'apache', 'litespeed' ) ) ) {
				$agent = str_replace( ' ', '\\ ', $agent );
				$rewrite_rules .= "\t\tRewriteCond %{HTTP_USER_AGENT} ^$agent [NC,OR]\n";
			} else if ( 'nginx' === $server_type ) {
				$agent = str_replace( '"', '\\"', $agent );
				$agent_rules .= "if (\$http_user_agent ~* \"^$agent\") { return 403; }\n";
			}
		}
		
		if ( in_array( $server_type, array( 'apache', 'litespeed' ) ) && ! empty( $rewrite_rules ) ) {
			$rewrite_rules = preg_replace( "/\[NC,OR\]\n$/", "[NC]\n", $rewrite_rules );
			
			$agent_rules .= "\t<IfModule mod_rewrite.c>\n";
			$agent_rules .= "\t\tRewriteEngine On\n";
			$agent_rules .= $rewrite_rules;
			$agent_rules .= "\t\tRewriteRule ^.* - [F]\n";
			$agent_rules .= "\t</IfModule>\n";
		}
		
		
		$rules = '';
		
		if ( ! empty( $agent_rules ) ) {
			$rules .= "\n";
			$rules .= "\t# " . __( 'Ban User Agents - Security > Settings > Banned Users', 'it-l10n-better-wp-security' ) . "\n";
			$rules .= $agent_rules;
		}
		
		return $rules;
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

			$status_array = 'safe-low';
			$status       = array(
				'text' => __( 'You are blocking known bad hosts and agents with the ban users tool.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_ban_users_enabled',
			);

		} else {

			$status_array = 'low';
			$status       = array(
				'text' => __( 'You are not blocking any users that are known to be a problem. Consider turning on the Ban Users feature.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_ban_users_enabled',
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

		//default blacklist section
		add_settings_section(
			'ban_users_default',
			__( 'Default Blacklist', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		//Enabled section
		add_settings_section(
			'ban_users_enabled',
			__( 'Configure Ban Users', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		//primary settings section
		add_settings_section(
			'ban_users_settings',
			__( 'Configure Ban Users', 'it-l10n-better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		//default list field
		add_settings_field(
			'itsec_ban_users[default]',
			__( 'Default Blacklist', 'it-l10n-better-wp-security' ),
			array( $this, 'ban_users_default' ),
			'security_page_toplevel_page_itsec_settings',
			'ban_users_default'
		);

		//enabled field
		add_settings_field(
			'itsec_ban_users[enabled]',
			__( 'Ban Users', 'it-l10n-better-wp-security' ),
			array( $this, 'ban_users_enabled' ),
			'security_page_toplevel_page_itsec_settings',
			'ban_users_enabled'
		);

		//host list field
		add_settings_field(
			'itsec_ban_users[host_list]',
			__( 'Ban Hosts', 'it-l10n-better-wp-security' ),
			array( $this, 'ban_users_host_list' ),
			'security_page_toplevel_page_itsec_settings',
			'ban_users_settings'
		);

		//agent _list field
		add_settings_field(
			'itsec_ban_users[agent_list]',
			__( 'Ban User Agents', 'it-l10n-better-wp-security' ),
			array( $this, 'ban_users_agent_list' ),
			'security_page_toplevel_page_itsec_settings',
			'ban_users_settings'
		);

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_ban_users',
			array( $this, 'sanitize_module_input' )
		);

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function metabox_advanced_settings() {

		echo '<p>' . __( 'This feature allows you to completely ban hosts and user agents from your site without having to manage any configuration of your server. Any IP addresses or user agents found in the lists below will not be allowed any access to your site.', 'it-l10n-better-wp-security' ) . '</p>';

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'ban_users_default', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'ban_users_enabled', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'ban_users_settings', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save All Changes', 'it-l10n-better-wp-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

		echo '</form>';

	}

	/**
	 * Register ban users for file writer
	 *
	 * @param  array $file_modules array of file writer modules
	 *
	 * @return array                   array of file writer modules
	 */
	public function register_file( $file_modules ) {

		$file_modules['ban-users'] = array(
			'rewrite' => array( $this, 'save_rewrite_rules' ),
		);

		return $file_modules;

	}

	/**
	 * Sanitize and validate input
	 *
	 * @param  Array $input array of input fields
	 *
	 * @return Array         Sanitized array
	 */
	public function sanitize_module_input( $input ) {

		global $itsec_globals;

		$no_errors = false; //start out assuming they entered a bad IP somewhere

		//Sanitize checkbox features
		$input['enabled'] = ( isset( $input['enabled'] ) && intval( $input['enabled'] == 1 ) ? true : false );
		$input['default'] = ( isset( $input['default'] ) && intval( $input['default'] == 1 ) ? true : false );

		//process agent list
		if ( isset( $input['agent_list'] ) && ! is_array( $input['agent_list'] ) ) {

			$agents = explode( PHP_EOL, $input['agent_list'] );

		} elseif ( isset( $input['agent_list'] ) ) {

			$agents = $input['agent_list'];

		} else {

			$agents = array();

		}

		$good_agents = array();

		foreach ( $agents as $agent ) {
			$good_agents[] = trim( sanitize_text_field( $agent ) );
		}

		$input['agent_list'] = $good_agents;

		//Process hosts list
		if ( isset( $input['host_list'] ) && ! is_array( $input['host_list'] ) ) {

			$addresses = explode( PHP_EOL, $input['host_list'] );

		} elseif ( isset( $input['host_list'] ) ) {

			$addresses = $input['host_list'];

		} else {

			$addresses = array();

		}

		$bad_ips   = array();
		$white_ips = array();
		$raw_ips   = array();

		foreach ( $addresses as $index => $address ) {

			if ( strlen( trim( $address ) ) > 0 ) {

				if ( ITSEC_Lib::validates_ip_address( $address ) === false ) {

					$bad_ips[] = trim( filter_var( $address, FILTER_SANITIZE_STRING ) );

				}

				if ( ! class_exists( 'ITSEC_Ban_Users' ) ) {
					require( dirname( __FILE__ ) . '/class-itsec-ban-users.php' );
				}

				if ( ITSEC_Ban_Users::is_ip_whitelisted( $address, null, true ) ) {

					$white_ips[] = trim( filter_var( $address, FILTER_SANITIZE_STRING ) );

				}

				$raw_ips[] = trim( filter_var( $address, FILTER_SANITIZE_STRING ) );

			} else {
				unset( $addresses[$index] );
			}

		}

		$raw_ips = array_unique( $raw_ips );

		if ( sizeof( $bad_ips ) > 0 ) {

			$input['enabled'] = false; //disable ban users list

			$type = 'error';

			if ( $no_errors === true ) {
				$message = sprintf( '%s<br /><br />', __( 'Note that the ban users feature has been disabled until the following errors are corrected:', 'it-l10n-better-wp-security' ) );
			}

			foreach ( $bad_ips as $bad_ip ) {
				$message .= sprintf( '%s %s<br />', $bad_ip, __( 'is not a valid address in the ban users box.', 'it-l10n-better-wp-security' ) );
			}

			add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

		} else {

			$no_errors = true;

		}

		if ( sizeof( $white_ips ) > 0 ) {

			$input['enabled'] = false; //disable ban users list

			$type = 'error';

			if ( $no_errors === true ) {
				$message = sprintf( '%s<br /><br />', __( 'Note that the ban users feature has been disabled until the following errors are corrected:', 'it-l10n-better-wp-security' ) );
			}

			foreach ( $white_ips as $white_ip ) {
				$message .= sprintf( '%s %s<br />', $white_ip, __( 'is not a valid address as it has been white listed.', 'it-l10n-better-wp-security' ) );
			}

			add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

		} else {

			$no_errors = true;

		}

		$input['host_list'] = $raw_ips;

		if ( $no_errors === true ) {

			if (
				! isset( $type ) &&
				(
					$input['host_list'] !== $this->settings['host_list'] ||
					$input['enabled'] !== $this->settings['enabled'] ||
					$input['default'] !== $this->settings['default'] ||
					$input['agent_list'] !== $this->settings['agent_list']
				) ||
				isset( $itsec_globals['settings']['write_files'] ) && $itsec_globals['settings']['write_files'] === true
			) {

				add_site_option( 'itsec_rewrites_changed', true );

			}

		}

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
	 * Prepare and save options in network settings
	 *
	 * @return void
	 */
	public function save_network_options() {

		if ( isset( $_POST['itsec_ban_users'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'it-l10n-better-wp-security' ) );
			}

			update_site_option( 'itsec_ban_users', $_POST['itsec_ban_users'] ); //we must manually save network options

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

		$vars['itsec_ban_users'] = array(
			'enabled' => '0:b',
			'default' => '0:b',
		);

		return $vars;

	}
}
