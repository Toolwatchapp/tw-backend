<?php
/* Register footer widget areas. */
add_action( 'widgets_init', 'omega_register_footer_widget_areas', 20 );

/**
 * Register footer widget areas based on the number of widget areas the user wishes to create with `add_theme_support()`.
 *
 * @since 0.3.4
 *
 * @uses omega_register_sidebar() Register footer widget areas.
 *
 * @return null Return early if there's no theme support.
 */
function omega_register_footer_widget_areas() {

	$footer_widgets = get_theme_support( 'omega-footer-widgets' );

	if ( ! $footer_widgets || ! isset( $footer_widgets[0] ) || ! is_numeric( $footer_widgets[0] ) )
		return;

	$footer_widgets = (int) $footer_widgets[0];

	$counter = 1;

	while ( $counter <= $footer_widgets ) {

		/* Set up some default sidebar arguments. */
		$defaults = array(
			'id'            => sprintf( 'footer-%d', $counter ),
			'name'          => sprintf( __( 'Footer %d', 'omega' ), $counter ),
			'description'   => sprintf( __( 'Footer %d widget area.', 'omega' ), $counter )
		);

		omega_register_sidebar( $defaults );

		$counter++;
	}

}


add_action( 'omega_before_footer', 'omega_footer_widget_areas' );
/**
 * Echo the markup necessary to facilitate the footer widget areas.
 *
 * Check for a numerical parameter given when adding theme support - if none is found, then the function returns early.
 *
 * The child theme must style the widget areas.
 *
 * Applies the `omega_footer_widget_areas` filter.
 *
 * @since 0.3.4
 *
 * @uses omega_structural_wrap() Optionally adds wrap with footer-widgets context.
 *
 * @return null Return early if number of widget areas could not be determined, or nothing is added to the first widget area.
 */
function omega_footer_widget_areas() {

	$footer_widgets = get_theme_support( 'omega-footer-widgets' );

	if ( ! $footer_widgets || ! isset( $footer_widgets[0] ) || ! is_numeric( $footer_widgets[0] ) )
		return;

	$footer_widgets = (int) $footer_widgets[0];

	//* Check to see if first widget area has widgets. If not, do nothing. No need to check all footer widget areas.
	if ( ! is_active_sidebar( 'footer-1' ) )
		return;

	$inside  = '';
	$output  = '';
 	$counter = 1;

	while ( $counter <= $footer_widgets ) {

		//* Gotta output buffer.
		ob_start();
		dynamic_sidebar( 'footer-' . $counter );
		$widgets = ob_get_clean();

		$inside .= sprintf( '<div class="footer-widgets-%d widget-area">%s</div>', $counter, $widgets );

		$counter++;

	}

	if ( $inside ) {
	
		$output .= '<div class="footer-widgets"><div class="wrap col-'.$footer_widgets.'">';
		
		$output .= $inside;
		
		$output .= '</div></div>';

	}

	echo apply_filters( 'omega_footer_widget_areas', $output, $footer_widgets );

}