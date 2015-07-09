<?php
/**
 * Sets $options['upgrade-1-0-2'] to true if user is updating
 */
function omega_upgrade_routine() {

	$options = get_option( 'omega_framework', false );
	
	// If version is set, upgrade routine has already run
	if ( $options['version'] == '1.0.2' ) {
		return;
	}
	
	// If $options exist, user is upgrading
	if ( empty( $options['upgrade-1-0-2']) && get_option( 'omega_theme_settings', false ) ) {
		$options['upgrade-1-0-2'] = true;
	}

	// New version number
	$options['version'] = '1.0.2';

	update_option( 'omega_framework', $options );
}

function omega_upgrade_routine_1_1_0() {

	$options = get_option( 'omega_framework', false );
	
	// If version is set, upgrade routine has already run
	if ( $options['version'] == '1.1.0' ) {
		return;
	}
	
	// If $options exist, user is upgrading
	if ( empty( $options['upgrade-1-1-0']) && get_option( 'omega_theme_settings', false ) ) {
		$options['upgrade-1-1-0'] = true;
	}

	// New version number
	$options['version'] = '1.1.0';

	update_option( 'omega_framework', $options );
}

add_action( 'admin_init', 'omega_upgrade_routine_1_1_0' );

/**
 * Displays notice if user has upgraded theme
 */
function omega_upgrade_notice() {

	if ( current_user_can( 'edit_theme_options' ) ) {
		$options = get_option( 'omega_framework', false );

		if ( !empty( $options['upgrade-1-0-2'] ) && $options['upgrade-1-0-2'] ) {
			echo '<div class="updated"><p>';
				printf( __(
					'Thanks for updating Omega Theme.  Please <a href="%1$s" target="_blank">read about important changes</a> in this version and give your site a quick check. <a href="%2$s">Dismiss notice</a>' ),
					'http://themehall.com/forums/topic/omega-1-0-0-updates',
					'?omega_upgrade_notice_ignore=1' );
			echo '</p></div>';
		}
	}

}

function omega_upgrade_notice_1_1_0() {

	if ( current_user_can( 'edit_theme_options' ) ) {
		$options = get_option( 'omega_framework', false );

		if ( !empty( $options['upgrade-1-1-0'] ) && $options['upgrade-1-1-0'] ) {
			echo '<div class="updated"><p>';
				printf( __(
					'Thanks for updating Omega Theme.  Please <a href="%1$s" target="_blank">read about important changes</a> in this version and give your site a quick check. <a href="%2$s">Dismiss notice</a>' ),
					'https://themehall.com/forums/topic/omega-1-1-0-updates',
					'?omega_upgrade_notice_ignore=1' );
			echo '</p></div>';
		}
	}

}

add_action( 'admin_notices', 'omega_upgrade_notice_1_1_0', 100 );

/**
 * Hides notices if user chooses to dismiss it
 */
function omega_notice_ignores() {

	$options = get_option( 'omega_framework' );

	if ( isset( $_GET['omega_upgrade_notice_ignore'] ) && '1' == $_GET['omega_upgrade_notice_ignore'] ) {
		$options['upgrade-1-1-0'] = false;
		update_option( 'omega_framework', $options );
	}

}
add_action( 'admin_init', 'omega_notice_ignores' );
?>