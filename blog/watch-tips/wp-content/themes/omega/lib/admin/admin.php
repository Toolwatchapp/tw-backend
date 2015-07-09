<?php
/**
 * Theme administration functions used with other components of the framework admin.  This file is for 
 * setting up any basic features and holding additional admin helper functions.
 */

/* Add the admin setup function to the 'admin_menu' hook. */
add_action( 'admin_menu', 'omega_admin_setup' );

/**
 * Sets up the adminstration functionality for the framework and themes.
 *
 * @since 0.9.0
 * @return void
 */
function omega_admin_setup() {

	/* Registers admin stylesheets for the framework. */
	add_action( 'admin_enqueue_scripts', 'omega_admin_register_styles', 1 );

	/* Loads admin stylesheets for the framework. */
	add_action( 'admin_enqueue_scripts', 'omega_admin_enqueue_styles' );
}

/**
 * Registers the framework's `admin-widgets.css` stylesheet file.  The function does not load the stylesheet.  
 * It merely registers it with WordPress.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function omega_admin_register_styles() {

	/* Use the .min stylesheet if SCRIPT_DEBUG is turned off. */
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_register_style( 'omega-admin', trailingslashit( OMEGA_CSS ) . "admin.css" );

	wp_register_style( 'omega-admin-widgets', trailingslashit( OMEGA_CSS ) . "admin-widgets{$suffix}.css" );

	wp_register_script( 'omega-admin', esc_url( trailingslashit( OMEGA_JS ) . "admin.js" ), array( 'jquery' ), '20130528', true );
}

/**
 * Loads the `admin-widgets.css` file when viewing the widgets screen.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function omega_admin_enqueue_styles( $hook_suffix ) {

	wp_enqueue_style( 'omega-admin' );

	if ( current_theme_supports( 'omega-widgets' ) && 'widgets.php' == $hook_suffix )
		wp_enqueue_style( 'omega-admin-widgets' );	   
    
}

/**
 * Function for getting an array of available custom templates with a specific header. Ideally, this function 
 * would be used to grab custom singular post (any post type) templates.  It is a recreation of the WordPress
 * page templates function because it doesn't allow for other types of templates.
 *
 * @since 0.7.0
 * @param string $post_type The name of the post type to get templates for.
 * @return array $post_templates The array of templates.
 */
function omega_get_post_templates( $post_type = 'post' ) {
	global $omega;

	/* If templates have already been called, just return them. */
	if ( !empty( $omega->post_templates ) && isset( $omega->post_templates[ $post_type ] ) )
		return $omega->post_templates[ $post_type ];

	/* Else, set up an empty array to house the templates. */
	else
		$omega->post_templates = array();

	/* Set up an empty post templates array. */
	$post_templates = array();

	/* Get the post type object. */
	$post_type_object = get_post_type_object( $post_type );

	/* Get the theme (parent theme if using a child theme) object. */
	$theme = wp_get_theme( get_template() );

	/* Get the theme PHP files one level deep. */
	$files = (array) $theme->get_files( 'php', 1 );

	/* If a child theme is active, get its files and merge with the parent theme files. */
	if ( is_child_theme() ) {
		$child = wp_get_theme();
		$child_files = (array) $child->get_files( 'php', 1 );
		$files = array_merge( $files, $child_files );
	}

	/* Loop through each of the PHP files and check if they are post templates. */
	foreach ( $files as $file => $path ) {

		/* Get file data based on the post type singular name (e.g., "Post Template", "Book Template", etc.). */
		$headers = get_file_data(
			$path,
			array( 
				"{$post_type_object->name} Template" => "{$post_type_object->name} Template",
			)
		);

		/* Continue loop if the header is empty. */
		if ( empty( $headers["{$post_type_object->name} Template"] ) )
			continue;

		/* Add the PHP filename and template name to the array. */
		$post_templates[ $file ] = $headers["{$post_type_object->name} Template"];
	}

	/* Add the templates to the global $omega object. */
	$omega->post_templates[ $post_type ] = array_flip( $post_templates );

	/* Return array of post templates. */
	return $omega->post_templates[ $post_type ];
}

?>