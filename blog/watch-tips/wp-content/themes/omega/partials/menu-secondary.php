<?php
/**
 * Secondary Menu Template
 */
?>	
<nav class="nav-secondary" <?php omega_attr( 'menu' ); ?>>
	
	<?php do_action( 'omega_before_secondary_menu' ); ?>

	<?php 
	wp_nav_menu( array(
		'theme_location' => 'secondary',
		'container'      => '',
		'menu_class'     => 'menu omega-nav-menu menu-secondary'
		)); 
	?>

	<?php do_action( 'omega_after_secondary_menu' ); ?>

	
</nav><!-- .nav-secondary -->
