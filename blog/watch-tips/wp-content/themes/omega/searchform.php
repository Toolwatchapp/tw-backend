<?php
/**
 * The template for displaying search forms in Omega
 *
 * @package Omega
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	
	<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search ...', 'placeholder', 'omega' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php _ex( 'Search for:', 'label', 'omega' ); ?>">
	
	<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'omega' ); ?>">
</form>
