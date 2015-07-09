<?php
/**
 * Deprecated functions that should be avoided in favor of newer functions. Also handles removed 
 * functions to avoid errors. Developers should not use these functions in their parent themes and users 
 * should not use these functions in their child themes.  The functions below will all be removed at some 
 * point in a future release.  If your theme is using one of these, you should use the listed alternative or 
 * remove it from your theme if necessary.
 */

/**
 * @since 0.9.0
 * @deprecated 0.9.0
 */
function hybrid_get_setting( $option = '' ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'omega_get_setting()' );
	omega_get_setting( $option );
}

/**
 * @since 0.9.0
 * @deprecated 0.9.0
 */
function post_format_tools_post_has_content( $id = 0 ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'omega_post_has_content()' );
	omega_post_has_content( $id );
}

/**
 * @since 0.9.0
 * @deprecated 0.9.0
 */
function post_format_tools_url_grabber() {
	_deprecated_function( __FUNCTION__, '0.9.0', 'omega_get_the_post_format_url()' );
	omega_get_the_post_format_url();
}

/**
 * @since 0.9.0
 * @deprecated 0.9.0
 */
function post_format_tools_get_image_attachment_count() {
	_deprecated_function( __FUNCTION__, '0.9.0', 'omega_get_gallery_image_count()' );
	omega_get_gallery_image_count();
}

/**
 * @since 0.9.0
 * @deprecated 0.9.0
 */
function post_format_tools_get_video( $deprecated = '' ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'omega_media_grabber()' );
	omega_media_grabber();
}

/**
 * @since 0.8.0
 * @deprecated 0.9.0
 */
function get_atomic_template( $template ) {
	_deprecated_function( __FUNCTION__, '0.9.0', '' );

	$templates = array();

	$theme_dir = trailingslashit( THEME_DIR ) . $template;
	$child_dir = trailingslashit( CHILD_THEME_DIR ) . $template;

	if ( is_dir( $child_dir ) || is_dir( $theme_dir ) ) {
		$dir = true;
		$templates[] = "{$template}/index.php";
	}
	else {
		$dir = false;
		$templates[] = "{$template}.php";
	}

	foreach ( omega_get_context() as $context )
		$templates[] = ( ( $dir ) ? "{$template}/{$context}.php" : "{$template}-{$context}.php" );

	return locate_template( array_reverse( $templates ), true, false );
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function do_atomic( $tag = '', $arg = '' ) {
	//_deprecated_function( __FUNCTION__, '0.9.0', 'omega_do_atomic' );
	omega_do_atomic( $tag, $arg );
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function apply_atomic( $tag = '', $value = '' ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'omega_apply_atomic' );
	return omega_apply_atomic( $tag, $value );
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function apply_atomic_shortcode( $tag = '', $value = '' ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'omega_apply_atomic_shortcode' );
	return omega_apply_atomic_shortcode( $tag, $value );
}

/**
 * Dynamic element to wrap the site title in.  If it is the front page, wrap it in an <h1> element.  One other 
 * pages, wrap it in a <div> element. 
 *
 * @since      0.1.0
 * @deprecated 0.9.0
 * @access     public
 * @return     void
 */
function hybrid_site_title() {
	_deprecated_function( __FUNCTION__, '0.9.0', '' );

	/* If viewing the front page of the site, use an <h1> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h1' : 'div';

	/* Get the site title.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $title = get_bloginfo( 'name' ) )
		$title = sprintf( '<%1$s id="site-title"><a href="%2$s" title="%3$s" rel="home"><span>%4$s</span></a></%1$s>', tag_escape( $tag ), home_url(), esc_attr( $title ), $title );

	/* Display the site title and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_title', $title );
}

/**
 * Dynamic element to wrap the site description in.  If it is the front page, wrap it in an <h2> element.  
 * On other pages, wrap it in a <div> element.
 *
 * @since 0.1.0
 * @access public
 * @return void
 */
function hybrid_site_description() {
	_deprecated_function( __FUNCTION__, '0.9.0', '' );

	/* If viewing the front page of the site, use an <h2> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h2' : 'div';

	/* Get the site description.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $desc = get_bloginfo( 'description' ) )
		$desc = sprintf( '<%1$s id="site-description"><span>%2$s</span></%1$s>', tag_escape( $tag ), $desc );

	/* Display the site description and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_description', $desc );
}

/**
 * @since      0.9.0
 * @deprecated 0.9.0
 */
function hybrid_body_attributes() {
	_deprecated_function( __FUNCTION__, '0.9.0', "omega_attr( 'body' )" );
	omega_attr( 'body' );
}

/**
 * @since      0.1.0
 * @deprecated 0.9.0
 */
function hybrid_body_class( $class = '' ) {
	_deprecated_function( __FUNCTION__, '0.9.0', "omega_attr( 'body' )" );
	omega_attr( 'body' );
}

/**
 * @since      0.9.0
 * @deprecated 0.9.0
 */
function hybrid_get_body_class( $class = '' ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'get_body_class' );
	return get_body_class( $class );
}

/**
 * @since      1.4.0
 * @deprecated 0.9.0
 */
function hybrid_footer_content() {
	_deprecated_function( __FUNCTION__, '0.9.0', '' );
}

/**
 * @since      0.9.0
 * @deprecated 0.9.0
 */
function hybrid_post_attributes() {
	_deprecated_function( __FUNCTION__, '0.9.0', "omega_attr( 'post' )" );
	omega_attr( 'post' );
}

/**
 * @since      0.9.0
 * @deprecated 0.9.0
 */
function hybrid_post_class( $class = '', $post_id = null ) {
	_deprecated_function( __FUNCTION__, '0.9.0', "omega_attr( 'post' )" );
	echo join( ' ', get_post_class( $class, $post_id ) );
}

/**
 * @since      0.5.0
 * @deprecated 0.9.0
 */
function hybrid_entry_class( $class = '', $post_id = null ) {
	_deprecated_function( __FUNCTION__, '0.9.0', "omega_attr( 'post' )" );
	echo join( ' ', get_post_class( $class, $post_id ) );
}

/**
 * @since      0.9.0
 * @deprecated 0.9.0
 */
function hybrid_get_post_class( $class = '', $post_id = null ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'get_post_class' );
	return get_post_class( $class, $post_id );
}

/**
 * @since      0.9.0
 * @deprecated 0.9.0
 */
function hybrid_comment_attributes() {
	_deprecated_function( __FUNCTION__, '0.9.0', "omega_attr( 'comment' )" );
	omega_attr( 'comment' );
}

/**
 * @since      0.2.0
 * @deprecated 0.9.0
 */
function hybrid_comment_class( $class = '' ) {
	_deprecated_function( __FUNCTION__, '0.9.0', "omega_attr( 'comment' )" );
	omega_attr( 'comment' );
}

/**
 * @since      0.9.0
 * @deprecated 0.9.0
 */
function hybrid_get_comment_class( $class = '' ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'get_comment_class' );
	return get_comment_class( $class );
}

/**
 * @since      0.2.0
 * @deprecated 0.9.0
 */
function hybrid_avatar() {
	global $comment, $omega;

	_deprecated_function( __FUNCTION__, '0.9.0', 'get_avatar' );

	/* Make sure avatars are allowed before proceeding. */
	if ( !get_option( 'show_avatars' ) )
		return false;

	/* Get the avatar provided by the get_avatar() function. */
	$avatar = get_avatar( $comment, 80, '', get_comment_author( $comment->comment_ID ) );

	/* Display the avatar and allow it to be filtered. Note: Use the get_avatar filter hook where possible. */
	echo apply_filters( 'omega_avatar', $avatar );
}

/**
 * @since      0.1.0
 * @deprecated 0.9.0
 */
function hybrid_document_title() {
	_deprecated_function( __FUNCTION__, '0.9.0', 'wp_title' );
	wp_title();
}

/**
 * @since      0.6.0
 * @deprecated 0.9.0
 */
function hybrid_loginout_link_shortcode() {
	_deprecated_function( __FUNCTION__, '0.9.0', 'wp_loginout' );
	return wp_loginout( '', false );
}

/**
 * @since      0.6.0
 * @deprecated 0.9.0
 */
function hybrid_query_counter_shortcode() {
	_deprecated_function( __FUNCTION__, '0.9.0', '' );
	return '';
}

/**
 * @since      0.8.0
 * @deprecated 0.9.0
 */
function hybrid_nav_menu_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'wp_nav_menu' );
	return '';
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function hybrid_entry_edit_link_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'edit_post_link' );

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr, 'entry-edit-link' );

	ob_start();
	edit_post_link( null, $attr['before'], $attr['after'] );
	return ob_get_clean();
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function hybrid_entry_published_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'get_the_date' );

	$attr = shortcode_atts( 
		array( 
			'before' => '', 
			'after' => '', 
			'format' => get_option( 'date_format' ), 
			'human_time' => '' 
		), 
		$attr, 
		'entry-published'
	);

	/* If $human_time is passed in, allow for '%s ago' where '%s' is the return value of human_time_diff(). */
	if ( !empty( $attr['human_time'] ) )
		$time = sprintf( $attr['human_time'], human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );

	/* Else, just grab the time based on the format. */
	else
		$time = get_the_time( $attr['format'] );

	$published = '<time class="published" datetime="' . get_the_time( 'Y-m-d\TH:i:sP' ) . '">' . $time . '</time>';

	return $attr['before'] . $published . $attr['after'];
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function hybrid_entry_comments_link_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'comments_popup_link' );

	$attr = shortcode_atts( 
		array( 
			'zero'      => false, 
			'one'       => false, 
			'more'      => false, 
			'css_class' => 'comments-link', 
			'none'      => '', 
			'before'    => '', 
			'after'     => '' 
		), 
		$attr,
		'entry-comments-link'
	);

	ob_start();
	echo $attr['before'];
	comments_popup_link( $attr['zero'], $attr['one'], $attr['more'], $attr['css_class'], $attr['none'] );
	echo $attr['after'];
	return ob_get_clean();
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function hybrid_entry_author_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'the_author_posts_link' );

	$post_type = get_post_type();

	if ( post_type_supports( $post_type, 'author' ) ) {

		$attr = shortcode_atts(
			array( 
				'before' => '', 
				'after'  => '' 
			), 
			$attr,
			'entry-author'
		);

		$author = '<span class="author vcard"><a class="url fn n" rel="author" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';

		return $attr['before'] . $author . $attr['after'];
	}

	return '';
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function hybrid_entry_terms_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'the_terms' );

	$attr = shortcode_atts( 
		array( 
			'id'        => get_the_ID(), 
			'taxonomy'  => 'post_tag', 
			'separator' => ', ', 
			'before'    => '', 
			'after'     => '' 
		), 
		$attr, 
		'entry-terms'
	);

	$attr['before'] = ( empty( $attr['before'] ) ? '<span class="' . $attr['taxonomy'] . '">' : '<span class="' . $attr['taxonomy'] . '"><span class="before">' . $attr['before'] . '</span>' );
	$attr['after'] = ( empty( $attr['after'] ) ? '</span>' : '<span class="after">' . $attr['after'] . '</span></span>' );

	return get_the_term_list( $attr['id'], $attr['taxonomy'], $attr['before'], $attr['separator'], $attr['after'] );
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function hybrid_entry_title_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'the_title' );

	$attr = shortcode_atts(
		array( 
			'permalink' => true, 
			'tag'       => is_singular() ? 'h1' : 'h2' 
		), 
		$attr,
		'entry-title'
	);

	$tag = tag_escape( $attr['tag'] );
	$class = sanitize_html_class( get_post_type() ) . '-title entry-title';

	if ( false == (bool)$attr['permalink'] )
		$title = the_title( "<{$tag} class='{$class}'>", "</{$tag}>", false );
	else
		$title = the_title( "<{$tag} class='{$class}'><a href='" . get_permalink() . "'>", "</a></{$tag}>", false );

	return $title;
}

/**
 * @since      0.8.0
 * @deprecated 0.9.0
 */
function hybrid_entry_shortlink_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', '' );
	return '';
}

/**
 * @since      0.9.0
 * @deprecated 0.9.0
 */
function hybrid_entry_permalink_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'get_permalink' );

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr, 'entry-permalink' );

	return $attr['before'] . '<a href="' . esc_url( get_permalink() ) . '" class="permalink">' . __( 'Permalink', 'omega' ) . '</a>' . $attr['after'];
}

/**
 * @since      0.9.0
 * @deprecated 0.9.0
 */
function hybrid_post_format_link_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'get_post_format_link' );

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr, 'post-format-link' );
	$format = get_post_format();
	$url = ( empty( $format ) ? get_permalink() : get_post_format_link( $format ) );

	return $attr['before'] . '<a href="' . esc_url( $url ) . '" class="post-format-link">' . get_post_format_string( $format ) . '</a>' . $attr['after'];
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function hybrid_comment_published_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'get_comment_date' );

	$attr = shortcode_atts(
		array(
			'human_time' => '',
			'before'     => '',
			'after'      => '',
		),
		$attr,
		'comment-published'
	);

	/* If $human_time is passed in, allow for '%s ago' where '%s' is the return value of human_time_diff(). */
	if ( !empty( $attr['human_time'] ) )
		$published = '<time class="published" datetime="' . get_comment_time( 'Y-m-d\TH:i:sP' ) . '">' . sprintf( $attr['human_time'], human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ) . '</time>';

	/* Else, just return the default. */
	else
		$published = '<span class="published"><time class="comment-date" datetime="' . get_comment_time( 'Y-m-d\TH:i:sP' ) . '">' . get_comment_date() . '</time></span>';

	return $attr['before'] . $published . $attr['after'];
}

/**
 * @since      0.8.0
 * @deprecated 0.9.0
 */
function hybrid_comment_author_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'comment_author_link' );

	global $comment;

	$attr = shortcode_atts(
		array(
			'before' => '',
			'after' => '',
			'tag' => 'span' // @deprecated 0.9.0 Back-compatibility. Please don't use this argument.
		),
		$attr,
		'comment-author'
	);

	$author = esc_html( get_comment_author( $comment->comment_ID ) );
	$url = esc_url( get_comment_author_url( $comment->comment_ID ) );

	/* Display link and cite if URL is set. Also, properly cites trackbacks/pingbacks. */
	if ( $url )
		$output = '<cite class="fn" title="' . $url . '"><a href="' . $url . '" title="' . esc_attr( $author ) . '" class="url" rel="external nofollow">' . $author . '</a></cite>';
	else
		$output = '<cite class="fn">' . $author . '</cite>';

	$output = '<' . tag_escape( $attr['tag'] ) . ' class="comment-author vcard">' . $attr['before'] . apply_filters( 'get_comment_author_link', $output ) . $attr['after'] . '</' . tag_escape( $attr['tag'] ) . '><!-- .comment-author .vcard -->';

	return $output;
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function hybrid_comment_permalink_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'get_comment_link' );

	global $comment;

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr, 'comment-permalink' );
	$link = '<a class="permalink" href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . __( 'Permalink', 'omega' ) . '</a>';
	return $attr['before'] . $link . $attr['after'];
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function hybrid_comment_edit_link_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'edit_comment_link' );

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr, 'comment-edit-link' );

	ob_start();
	edit_comment_link( null, $attr['before'], $attr['after'] );
	return ob_get_clean();
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function hybrid_comment_reply_link_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.9.0', 'omega_get_comment_reply_link' );
	return omega_get_comment_reply_link( $attr );
}

/**
 * @since      0.8.0
 * @deprecated 0.9.0
 */
function hybrid_get_transient_expiration() {
	_deprecated_function( __FUNCTION__, '0.9.0', '' );
	return 43200;
}

/* === Removed Functions (note that functions removed prior to the 1.5 branch are gone). === */



/**
 * Message to display for removed functions.
 * @since 0.5.0
 */
function hybrid_function_removed( $func = '' ) {
	die( sprintf( __( '<code>%1$s</code> &mdash; This function has been removed or replaced by another function.', 'omega' ), $func ) );
}

?>