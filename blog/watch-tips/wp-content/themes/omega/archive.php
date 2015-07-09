<?php
/**
 * The template for displaying Archive pages.
 *
 * @package Omega
 */

get_header(); ?>

	<main  class="<?php echo omega_apply_atomic( 'main_class', 'content' );?>" <?php omega_attr( 'content' ); ?>>

		<?php 
		do_action( 'omega_before_content' );
		do_action( 'omega_content' );
		do_action( 'omega_after_content' );
		?>
		
	</main><!-- .content -->

<?php get_footer(); ?>