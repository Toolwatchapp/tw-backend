<footer class="entry-footer">
	<div class="entry-meta">
		<?php omega_post_terms( array( 'taxonomy' => 'category', 'text' => __( 'Posted in: %s', 'omega' ) ) ); ?>
		<?php omega_post_terms( array( 'taxonomy' => 'post_tag', 'text' => __( 'Tagged: %s', 'omega' ) ) ); ?>		
	</div><!-- .entry-meta -->
</footer>