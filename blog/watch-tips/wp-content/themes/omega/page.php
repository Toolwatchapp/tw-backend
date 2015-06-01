<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
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
