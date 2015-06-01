<?php
/**
 * General template functions.
 */

/**
 * Outputs the link back to the site.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function omega_site_link() {
	echo omega_get_site_link();
}

/**
 * Returns a link back to the site.
 *
 * @since  0.9.0
 * @access public
 * @return string
 */
function omega_get_site_link() {
	return sprintf( '<a class="site-link" href="%s" rel="home">%s</a>', esc_url( home_url() ), get_bloginfo( 'name' ) );
}

/**
 * Displays a link to WordPress.org.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function omega_wp_link() {
	echo omega_get_wp_link();
}

/**
 * Returns a link to WordPress.org.
 *
 * @since  0.9.0
 * @access public
 * @return string
 */
function omega_get_wp_link() {
	return sprintf( '<a class="wp-link" href="http://wordpress.org" title="%s">%s</a>', esc_attr__( 'State-of-the-art semantic personal publishing platform', 'omega' ), __( 'WordPress', 'omega' ) );
}

/**
 * Displays a link to the parent theme URI.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function omega_theme_link() {
	echo omega_get_theme_link();
}

/**
 * Returns a link to the parent theme URI.
 *
 * @since  0.9.0
 * @access public
 * @return string
 */
function omega_get_theme_link() {
	$theme = wp_get_theme( get_template() );
	$uri   = $theme->get( 'ThemeURI' );
	$name  = $theme->display( 'Name', false, true );

	/* Translators: Theme name. */
	$title = sprintf( __( '%s WordPress Theme', 'omega' ), $name );

	return sprintf( '<a class="theme-link" rel="nofollow" href="%s" title="%s">%s</a>', esc_url( $uri ), esc_attr( $title ), $name );
}

/**
 * Returns a link to the parent theme URI.
 *
 * @since  0.9.0
 * @access public
 * @return string
 */
function omega_get_author_uri() {
	$theme = wp_get_theme();
	$uri   = $theme->get( 'AuthorURI' );
	$name  = $theme->display( 'Author', false, true );

	/* Translators: Theme name. */
	$title = sprintf( __( '%s', 'omega' ), $name );

	$nofollow = is_child_theme() ? 'rel="nofollow"' : '';
	return sprintf( '<a class="theme-link" %s href="%s" title="%s">%s</a>', $nofollow, esc_url( $uri ), esc_attr( $title ), $name );
}

/**
 * Displays a link to the child theme URI.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function omega_child_theme_link() {
	echo omega_get_child_theme_link();
}

/**
 * Returns a link to the child theme URI.
 *
 * @since  0.9.0
 * @access public
 * @return string
 */
function omega_get_child_theme_link() {

	if ( !is_child_theme() )
		return '';

	$theme = wp_get_theme();
	$uri   = $theme->get( 'ThemeURI' );
	$name  = $theme->display( 'Name', false, true );

	/* Translators: Theme name. */
	$title = sprintf( __( '%s WordPress Theme', 'omega' ), $name );

	return sprintf( '<a class="child-link" href="%s" title="%s">%s</a>', esc_url( $uri ), esc_attr( $title ), $name );
}

/**
 * Returns theme name.
 *
 * @since  1.1.2
 * @access public
 * @return string
 */
function omega_get_theme_name() {

	$theme = wp_get_theme();
	return $theme->display( 'Name', false, true );
}

/**
 * Outputs the loop title.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function omega_loop_title() {
	echo omega_get_loop_title();
}

/**
 * Gets the loop title.  This function should only be used on archive-type pages, such as archive, blog, and 
 * search results pages.  It outputs the title of the page.
 *
 * @link   http://core.trac.wordpress.org/ticket/21995
 * @since  0.9.0
 * @access public
 * @return string
 */
function omega_get_loop_title() {

	$loop_title = '';

	if ( is_home() && !is_front_page() )
		$loop_title = get_post_field( 'post_title', get_queried_object_id() );

	elseif ( is_category() ) 
		$loop_title = single_cat_title( '', false );

	elseif ( is_tag() )
		$loop_title = single_tag_title( '', false );

	elseif ( is_tax() )
		$loop_title = single_term_title( '', false );

	elseif ( is_author() )
		$loop_title = get_the_author();

	elseif ( is_search() )
		$loop_title = omega_search_title( '', false );

	elseif ( is_post_type_archive() )
		$loop_title = post_type_archive_title( '', false );

	elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
		$loop_title = omega_single_minute_hour_title( '', false );

	elseif ( get_query_var( 'minute' ) )
		$loop_title = omega_single_minute_title( '', false );

	elseif ( get_query_var( 'hour' ) )
		$loop_title = omega_single_hour_title( '', false );

	elseif ( is_day() )
		$loop_title = omega_single_day_title( '', false );

	elseif ( get_query_var( 'w' ) )
		$loop_title = omega_single_week_title( '', false );

	elseif ( is_month() )
		$loop_title = single_month_title( ' ', false );

	elseif ( is_year() )
		$loop_title = omega_single_year_title( '', false );

	elseif ( is_archive() )
		$loop_title = omega_single_archive_title( '', false );

	return apply_filters( 'omega_loop_title', $loop_title );
}

/**
 * Outputs the loop description.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function omega_loop_description() {
	echo omega_get_loop_description();
}

/**
 * Gets the loop description.  This function should only be used on archive-type pages, such as archive, blog, and 
 * search results pages.  It outputs the description of the page.
 *
 * @link   http://core.trac.wordpress.org/ticket/21995
 * @since  0.9.0
 * @access public
 * @return string
 */
function omega_get_loop_description() {

	$loop_desc = '';

	if ( is_home() && !is_front_page() )
		$loop_desc = get_post_field( 'post_content', get_queried_object_id(), 'raw' );

	elseif ( is_category() )
		$loop_desc = get_term_field( 'description', get_queried_object_id(), 'category', 'raw' );

	elseif ( is_tag() )
		$loop_desc = get_term_field( 'description', get_queried_object_id(), 'post_tag', 'raw' );

	elseif ( is_tax() )
		$loop_desc = get_term_field( 'description', get_queried_object_id(), get_query_var( 'taxonomy' ), 'raw' );

	elseif ( is_author() )
		$loop_desc = get_the_author_meta( 'description', get_query_var( 'author' ) );

	elseif ( is_search() )
		$loop_desc = sprintf( __( 'You are browsing the search results for &#8220;%s&#8221;', 'omega' ), get_search_query() );

	elseif ( is_post_type_archive() )
		$loop_desc = get_post_type_object( get_query_var( 'post_type' ) )->description;

	elseif ( is_time() )
		$loop_desc = __( 'You are browsing the site archives by time.', 'omega' );

	elseif ( is_day() )
		$loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'omega' ), omega_single_day_title( '', false ) );

	elseif ( is_month() )
		$loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'omega' ), single_month_title( ' ', false ) );

	elseif ( is_year() )
		$loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'omega' ), omega_single_year_title( '', false ) );

	elseif ( is_archive() )
		$loop_desc = __( 'You are browsing the site archives.', 'omega' );

	return apply_filters( 'omega_loop_description', $loop_desc );
}

/**
 * Retrieve the general archive title.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function omega_single_archive_title( $prefix = '', $display = true ) {

	$title = $prefix . __( 'Archives', 'omega' );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the year archive title.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function omega_single_year_title( $prefix = '', $display = true ) {

	$title = $prefix . get_the_date( _x( 'Y', 'yearly archives date format', 'omega' ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the week archive title.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function omega_single_week_title( $prefix = '', $display = true ) {

	/* Translators: 1 is the week number and 2 is the year. */
	$title = $prefix . sprintf( __( 'Week %1$s of %2$s', 'omega' ), get_the_time( _x( 'W', 'weekly archives date format', 'omega' ) ), get_the_time( _x( 'Y', 'yearly archives date format', 'omega' ) ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the day archive title.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function omega_single_day_title( $prefix = '', $display = true ) {

	$title = $prefix . get_the_date( _x( 'F j, Y', 'daily archives date format', 'omega' ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the hour archive title.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function omega_single_hour_title( $prefix = '', $display = true ) {

	$title = $prefix . get_the_time( _x( 'g a', 'hour archives time format', 'omega' ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the minute archive title.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function omega_single_minute_title( $prefix = '', $display = true ) {

	/* Translators: Minute archive title. %s is the minute time format. */
	$title = $prefix . sprintf( __( 'Minute %s', 'omega' ), get_the_time( _x( 'i', 'minute archives time format', 'omega' ) ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the minute + hour archive title.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function omega_single_minute_hour_title( $prefix = '', $display = true ) {

	$title = $prefix . get_the_time( _x( 'g:i a', 'minute and hour archives time format', 'omega' ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the search results title.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function omega_search_title( $prefix = '', $display = true ) {

	/* Translators: %s is the search query. The HTML entities are opening and closing curly quotes. */
	$title = $prefix . sprintf( __( 'Search results for &#8220;%s&#8221;', 'omega' ), get_search_query() );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the 404 page title.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function omega_404_title( $prefix = '', $display = true ) {

	$title = __( '404 Not Found', 'omega' );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Produces the date of post publication.
 *
 * Supported attributes are:
 *   after (output after link, default is empty string),
 *   before (output before link, default is empty string),
 *   format (date format, default is value in date_format option field),
 *   label (text following 'before' output, but before date).
 *
 * Output passes through 'omega_get_post_date' filter before returning.
 *
 * @since 1.1.0
 * @access public
 * @param  string  $after
 * @param  string  $before
 * @param  string  $format
 * @param  string  $label
 * @return string
 */

function omega_get_post_date( $after = '', $before = '', $format = '', $label = '' ) {

	if ($format == '') $format = get_option( 'date_format' );

	$display = ( 'relative' === $format ) ? omega_human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'omega' ) : get_the_time( $format );

	$output = sprintf( '<time %s>', omega_get_attr( 'entry-published' ) ) . $before . $label . $display . $after . '</time>';

	return apply_filters( 'omega_get_post_date', $output, $after, $before, $format, $label  );

}