<?php
/**
 * Sets up the Omega framework's widgets if the theme supports this feature.  
 * A theme must register support for the 'omega-widgets' feature to use the framework widgets.
 */

/* Register Hybrid widgets. */
add_action( 'widgets_init', 'omega_register_widgets' );

/**
 * Registers the omega frameworks widgets.
 *
 * @since 0.9.0
 * @access public
 * @uses register_widget() Registers individual widgets with WordPress
 * @link http://codex.wordpress.org/Function_Reference/register_widget
 * @return void
 */
function omega_register_widgets() {
	
	$supports = get_theme_support( 'omega-widgets' );

	/* If there are any supported meta boxes, load them. */
	if ( is_array( $supports[0] ) ) {

		if ( in_array( 'featured-posts', $supports[0] ) ) {
			/* Load the archives widget class. */
			require_once( trailingslashit( OMEGA_CLASSES ) . 'widget-featured-posts.php' );
			/* Register the Featured Post widget. */
			register_widget( 'Omega_Featured_Post' );
		}	
		if ( in_array( 'featured-page', $supports[0] ) ) {
			/* Load the archives widget class. */
			require_once( trailingslashit( OMEGA_CLASSES ) . 'widget-featured-page.php' );
			/* Register the Featured Page widget. */
			register_widget( 'Omega_Featured_Page' );
		}	

	}
}