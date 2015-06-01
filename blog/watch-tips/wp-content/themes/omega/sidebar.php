<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Omega
 */

if ( is_active_sidebar( 'primary' ) ) : ?>	

	<aside class="<?php echo omega_apply_atomic( 'sidebar_class', 'sidebar sidebar-primary widget-area' );?>" <?php omega_attr( 'sidebar' ); ?>>
	
		<?php do_action( 'before_primary' ); ?>

		<?php dynamic_sidebar( 'primary' ); ?>

		<?php do_action( 'after_primary' ); ?>

  	</aside><!-- .sidebar -->

<?php endif;  ?>