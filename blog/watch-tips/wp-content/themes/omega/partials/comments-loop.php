<?php if ( have_comments() ) : ?>
	<h3><?php comments_number( '', __( 'One Comment', 'omega' ), __( '% Comments', 'omega' ) ); ?></h3>

	<?php get_template_part( 'partials/comments-loop-nav' ); // Loads the comment-loop-nav.php template. ?>	

	<ol class="comment-list">
		<?php wp_list_comments(
			array(
				'callback'     => 'omega_comments_callback',
				'end-callback' => 'omega_comments_end_callback'
			)
		); ?>
	</ol><!-- .comment-list -->

<?php endif; // have_comments() ?>

<?php get_template_part( 'partials/comments-loop-error' ); // Loads the comments-loop-error.php template. ?>