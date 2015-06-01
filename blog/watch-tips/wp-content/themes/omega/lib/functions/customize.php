<?php
/**
 * Functions for registering and setting theme settings that tie into the WordPress theme customizer.  
 * This file loads additional classes and adds settings to the customizer for the built-in Omega Core 
 * settings.
 */



/**
 * Loads framework-specific customize control classes.  Customize control classes extend the WordPress 
 * WP_Customize_Control class to create unique classes that can be used within the framework.
 *
 * @since 1.4.0
 * @access private
 */
function omega_load_customize_controls() {

	/* Loads the textarea customize control class. */
	require_once( trailingslashit( OMEGA_CLASSES ) . 'customize-control-textarea.php' );

	/* Loads the background image customize control class. */
	require_once( trailingslashit( OMEGA_CLASSES ) . 'customize-control-background-image.php' );

	/* Loads the checkbox customize control class. */
	require_once( trailingslashit( OMEGA_CLASSES ) . 'customize-control-checkbox.php' );

	/* Loads the text customize control class. */
	require_once( trailingslashit( OMEGA_CLASSES ) . 'customize-control-char.php' );
}
/* Load custom control classes. */
add_action( 'customize_register', 'omega_load_customize_controls', 1 );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function omega_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'omega_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function omega_customize_preview_js() {
	wp_enqueue_script( 'omega_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'omega_customize_preview_js' );