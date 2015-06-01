<?php

/* Register custom sections, settings, and controls. */
add_action( 'customize_register', 'omega_customize_logo_register' );


/**
 * Registers custom sections, settings, and controls for the $wp_customize instance.
 *
 * @since 0.3.2
 * @access private
 * @param object $wp_customize
 */
function omega_customize_logo_register( $wp_customize ) {

	/* Add the footer section. */
	$wp_customize->add_section(
		'title_tagline',
		array(
			'title'      => esc_html__( 'Branding', 'omega' ),
			'priority'   => 1,
			'capability' => 'edit_theme_options'
		)
	);

	/* Add the 'custom_logo' setting. */
	$wp_customize->add_setting(
		"custom_logo",
		array(
			'default'              => '',
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_text_field',
			//'sanitize_js_callback' => 'omega_customize_sanitize',
			//'transport'            => 'postMessage',
		)
	);

	/* Add the textarea control for the 'custom_css' setting. */
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'custom_logo',
			array(
				'label'    => esc_html__( 'Logo', 'omega' ),
				'section'  => 'title_tagline',
				'settings' => "custom_logo",
			)
		)
	);

	/* Add the 'custom_favicon' setting. */
	$wp_customize->add_setting(
		"custom_favicon",
		array(
			'default'              => '',
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_text_field',
			//'sanitize_js_callback' => 'omega_customize_sanitize',
			//'transport'            => 'postMessage',
		)
	);

	/* Add the textarea control for the 'custom_css' setting. */
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'custom_favicon',
			array(
				'label'    => esc_html__( 'Favicon', 'omega' ),
				'section'  => 'title_tagline',
				'settings' => "custom_favicon",
			)
		)
	);

}

add_action( 'wp_head', 'omega_favicon' );
add_action( 'admin_head', 'omega_favicon' );

function omega_favicon() {
	if ( $favicon = get_theme_mod( 'custom_favicon' ) )
        echo '<link rel="shortcut icon" href="'.  esc_url( $favicon )  .'"/>'."\n";
}