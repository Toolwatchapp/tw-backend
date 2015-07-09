<div class="entry-meta">
	<time <?php omega_attr( 'entry-published' ); ?>><?php echo get_the_date(); ?></time>
	<span <?php omega_attr( 'entry-author' ); ?>><?php echo __('by ', 'omega'); the_author_posts_link(); ?></span>	
	<?php echo omega_post_comments( ); ?>
	<?php edit_post_link( __('Edit', 'omega'), ' | ' ); ?>
</div><!-- .entry-meta -->