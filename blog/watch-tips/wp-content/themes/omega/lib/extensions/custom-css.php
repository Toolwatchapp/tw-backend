<?php

/* Register custom sections, settings, and controls. */
add_action( 'customize_register', 'omega_customize_css_register' );

/* Output CSS into <head>. */
add_action( 'wp_head', 'print_custom_css' );

/* Delete the cached data for this feature. */
add_action( 'update_option_theme_mods_' . get_stylesheet(), 'custom_css_cache_delete' );

/**
 * Deletes the cached style CSS that's output into the header.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */

function custom_css_cache_delete() {
	wp_cache_delete( 'custom_css' );
}


function print_custom_css() {
	/* Get the cached style. */
	$style = wp_cache_get( 'custom_css' );

	/* If the style is available, output it and return. */
	if ( !empty( $style ) ) {
		echo $style;
		return;
	} else {
		/* Put the final style output together. */
		$style = "\n" . '<style type="text/css" id="custom-css">' . trim( get_theme_mod( 'custom_css' ) ) . '</style>' . "\n";

		/* Cache the style, so we don't have to process this on each page load. */
		wp_cache_set( 'custom_css', $style );

		/* Output the custom style. */
		echo $style;
	}
}

/**
 * Registers custom sections, settings, and controls for the $wp_customize instance.
 *
 * @since 0.3.2
 * @access private
 * @param object $wp_customize
 */
function omega_customize_css_register( $wp_customize ) {

	/* Add the section. */
	$wp_customize->add_section(
		'css',
		array(
			'title'      => esc_html__( 'CSS', 'omega' ),
			'priority'   => 150,
			'capability' => 'unfiltered_html'
		)
	);

	/* Add the 'custom_css' setting. */
	$wp_customize->add_setting(
		"custom_css",
		array(
			'default'              => '',
			'type'                 => 'theme_mod',
			'capability' 		   => 'edit_theme_options',
			'sanitize_callback'    => 'omega_custom_css_sanitize',
			'transport'            => 'postMessage',
		)
	);

	/* Add the textarea control for the 'custom_css' setting. */
	$wp_customize->add_control(
		new Omega_Customize_Control_Textarea(
			$wp_customize,
			'custom_css',
			array(
				'label'   		=> '',
				'section'  		=> 'css',
				'placeholder' 	=> '.classname {	background: #fff;}',
				'settings' 		=> "custom_css",
			)
		)
	);

	/* If viewing the customize preview screen, add a script to show a live preview. */
	if ( $wp_customize->is_preview() && !is_admin() ) {
		add_action( 'wp_footer', 'omega_customize_preview_script', 22 );
	}
}

/**
 * Handles changing settings for the live preview of the theme.
 *
 * @since 0.3.2
 * @access private
 */
function omega_customize_preview_script() {

	?>
	<script type="text/javascript">
	( function( $ ){
		// Bind the Live CSS
		wp.customize('custom_css', function( value ) {
			value.bind(function( to ) {
					$( '#custom-css' ).text( to );
			});
		});
	} )( jQuery )
	</script>
	<?php
}

/**
 * sanitize css input
 *
 * @since 1.1.1
 * @access private
 */
function omega_custom_css_sanitize($css) {

	if (''!=$css) {
		$css = str_replace( '<=', '&lt;=', $css );
		$css = wp_kses_split( $css, array(), array() );
		$css = str_replace( '&gt;', '>', $css );
		$css = strip_tags( $css );
	}

	return $css;

}
?>