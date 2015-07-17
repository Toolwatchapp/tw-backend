<?php
/**
 * Display the plugin's dashboard information.
 *
 * Sets up and displays the dashboard status, file permissions and other system
 * information on the plugin's dashboard.
 *
 * @since   4.0.0
 *
 * @package iThemes_Security
 */
class ITSEC_Dashboard_Admin {

	/**
	 * Initialize the plugin dashboard
	 *
	 * Initialize areas of the plugin dashboard.
	 *
	 * @since 4.0.0
	 *
	 * @return ITSEC_Dashboard_Admin
	 */
	function __construct() {

		if ( is_admin() ) {

			add_action( 'itsec_add_admin_meta_boxes', array( $this, 'itsec_add_admin_meta_boxes' ) );

		}

	}

	/**
	 * Add meta boxes to the plugin dashboard.
	 *
	 * Adds plugin's metaboxes including status, system information and file
	 * permissions to the plugin dashboard.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function itsec_add_admin_meta_boxes() {

		//System status shows which plugin features have been activated
		add_meta_box(
			'itsec_status',
			__( 'Security Status', 'it-l10n-better-wp-security' ),
			array( $this, 'metabox_normal_status' ),
			'toplevel_page_itsec',
			'advanced',
			'core'
		);

		add_meta_box(
			'itsec_file_permissions',
			__( 'WordPress File Permissions', 'it-l10n-better-wp-security' ),
			array( $this, 'metabox_normal_file_permissions' ),
			'toplevel_page_itsec',
			'advanced',
			'core'
		);

		add_meta_box(
			'itsec_system_info',
			__( 'System Information', 'it-l10n-better-wp-security' ),
			array( $this, 'metabox_normal_system_info' ),
			'toplevel_page_itsec',
			'advanced',
			'core'
		);

	}

	/**
	 * Display the file permissions metabox.
	 *
	 * Builds and displays the table that shows WordPress file permissions as marked up
	 * in the system.php file.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function metabox_normal_file_permissions() {

		require_once( 'content/perms.php' );

	}

	/**
	 * Display security status.
	 *
	 * Builds and displays the table showing the security status as determined
	 * by which features have been configured.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function metabox_normal_status() {

		require_once( 'content/status.php' );

	}

	/**
	 * Display the system information metabox.
	 *
	 * Builds and displays the table that shows system infmormation as marked up
	 * in the system.php file.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function metabox_normal_system_info() {

		require_once( 'content/system.php' );

	}

	/**
	 * Displays required status array.
	 *
	 * Loops through the filterable status array to build the table items for the
	 * security status metabox.
	 *
	 * @since 4.0.0
	 *
	 * @param array  $status_array array of statuses
	 * @param string $button_text  string for button
	 * @param string $button_class string for button
	 *
	 * @return void
	 */
	private function status_loop( $status_array, $button_text, $button_class ) {

		foreach ( $status_array as $status ) {

			if ( isset( $status['advanced'] ) && true === $status['advanced'] ) {

				$page = 'advanced';

			} elseif ( isset( $status['pro'] ) && true === $status['pro'] ) {

				$page = 'pro';

			} else {

				$page = 'settings';

			}

			if ( false === strpos( $status['link'], 'http:' ) && false === strpos( $status['link'], '?page=' ) ) {

				$setting_link = '?page=toplevel_page_itsec_' . $page . $status['link'];

			} else {

				$setting_link = $status['link'];

			}

			printf( '<li><p>%s</p><div class="itsec_status_action"><a class="button-%s" href="%s">%s</a></div></li>', $status['text'], $button_class, $setting_link, $button_text );

		}

	}

}