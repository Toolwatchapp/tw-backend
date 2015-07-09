<?php
/**
 * Functions for outputting common site data in the `<head>` area of a site.
 */

/* Adds common theme items to <head>. */
add_action( 'wp_head', 'omega_meta_viewport', 1 );
add_action( 'wp_head', 'omega_link_pingback', 3 );

/* Filter the WordPress title. */
add_filter( 'wp_title', 'omega_wp_title', 1, 3 );

/**
 * Adds the meta viewport to the header.
 *
 * @since  0.9.0
 * @access public
 */
function omega_meta_viewport() {
	echo '<meta name="viewport" content="width=device-width" />' . "\n";
}

/**
 * Adds the pingback link to the header.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function omega_link_pingback() {
	if ( 'open' === get_option( 'default_ping_status' ) )
		echo '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
}

/**
 * Filters the `wp_title` output early.
 *
 * @since  0.9.0
 * @access publc
 * @param  string  $title
 * @param  string  $separator
 * @param  string  $seplocation
 * @return string
 */
function omega_wp_title( $title, $separator, $seplocation ) {

	if ( is_front_page() )
		$doctitle = get_bloginfo( 'name' ) . $separator . ' ' . get_bloginfo( 'description' );

	elseif ( is_home() || is_singular() )
		$doctitle = single_post_title( '', false );

	elseif ( is_category() ) 
		$doctitle = single_cat_title( '', false );

	elseif ( is_tag() )
		$doctitle = single_tag_title( '', false );

	elseif ( is_tax() )
		$doctitle = single_term_title( '', false );

	elseif ( is_post_type_archive() )
		$doctitle = post_type_archive_title( '', false );

	elseif ( is_author() )
		$doctitle = get_the_author_meta( 'display_name', get_query_var( 'author' ) );

	elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
		$doctitle = omega_single_minute_hour_title( '', false );

	elseif ( get_query_var( 'minute' ) )
		$doctitle = omega_single_minute_title( '', false );

	elseif ( get_query_var( 'hour' ) )
		$doctitle = omega_single_hour_title( '', false );

	elseif ( is_day() )
		$doctitle = omega_single_day_title( '', false );

	elseif ( get_query_var( 'w' ) )
		$doctitle = omega_single_week_title( '', false );

	elseif ( is_month() )
		$doctitle = single_month_title( ' ', false );

	elseif ( is_year() )
		$doctitle = omega_single_year_title( '', false );

	elseif ( is_archive() )
		$doctitle = omega_single_archive_title( '', false );

	elseif ( is_search() )
		$doctitle = omega_search_title( '', false );

	elseif ( is_404() )
		$doctitle = omega_404_title( '', false );

	else 
		$doctitle = '';
	
	/* If the current page is a paged page. */
	if ( ( ( $page = get_query_var( 'paged' ) ) || ( $page = get_query_var( 'page' ) ) ) && $page > 1 )
		/* Translators: 1 is the page title. 2 is the page number. */
		$doctitle = sprintf( __( '%1$s Page %2$s', 'omega' ), $doctitle . $separator, number_format_i18n( absint( $page ) ) );

	/* Trim separator + space from beginning and end. */
	$doctitle = trim( $doctitle, "{$separator} " );

	return $doctitle;
}