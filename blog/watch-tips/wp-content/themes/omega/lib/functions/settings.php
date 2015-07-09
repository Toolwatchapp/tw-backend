<?php
/**
 * Functions for dealing with theme settings on both the front end of the site and the admin.  This allows us 
 * to set some default settings and make it easy for theme developers to quickly grab theme settings from 
 * the database.  This file is only loaded if the theme adds support for the 'omega-theme-settings' 
 * feature.
 */

/**
 * Loads the Omega theme settings once and allows the input of the specific field the user would 
 * like to show.  Omega theme settings are added with 'autoload' set to 'yes', so the settings are 
 * only loaded once on each page load.
 *
 * @since  0.7.0
 * @access public
 * @uses   get_option() Gets an option from the database.
 * @global object $omega The global Omega object.
 * @param  string $option The specific theme setting the user wants.
 * @return mixed $settings[$option] Specific setting asked for.
 */
function omega_get_setting( $option = '' ) {
	global $omega;

	/* If no specific option was requested, return false. */
	if ( !$option )
		return false;

	/* Get the default settings. */
	$defaults = omega_get_default_theme_settings();

	/* If the settings array hasn't been set, call get_option() to get an array of theme settings. */
	if ( !isset( $omega->settings ) || !is_array( $omega->settings ) )
		$omega->settings = get_option( 'omega_theme_settings', $defaults );

	/* If the option isn't set but the default is, set the option to the default. */
	if ( !isset( $omega->settings[ $option ] ) && isset( $defaults[ $option ] ) )
		$omega->settings[ $option ] = $defaults[ $option ];

	/* If no option is found at this point, return false. */
	if ( !isset( $omega->settings[ $option ] ) )
		return false;

	/* If the specific option is an array, return it. */
	if ( is_array( $omega->settings[ $option ] ) )
		return $omega->settings[ $option ];

	/* Strip slashes from the setting and return. */
	else
		return wp_kses_stripslashes( $omega->settings[ $option ] );
}

/**
 * Sets up a default array of theme settings for use with the theme.  Theme developers should filter the 
 * "omega_default_theme_settings" hook to define any default theme settings.  WordPress does not 
 * provide a hook for default settings at this time.
 *
 * @since  0.9.0
 * @access public
 * @return array $settings The default theme settings.
 */
function omega_get_default_theme_settings() {
	return apply_filters( 'omega_default_theme_settings', array() );
}

?>