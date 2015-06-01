<?php

/* Register custom sections, settings, and controls. */
add_action( 'customize_register', 'omega_customize_post_register' );


/**
 * Registers custom sections, settings, and controls for the $wp_customize instance.
 *
 * @since 0.3.2
 * @access private
 * @param object $wp_customize
 */
function omega_customize_post_register( $wp_customize ) {

	/* Add the post section. */
	$wp_customize->add_section(
		'post_section',
		array(
			'title'      => esc_html__( 'Posts', 'omega' ),
			'priority'   => 120,
			'capability' => 'edit_theme_options'
		)
	);

	/* Add the 'content_archive' setting. */
	$wp_customize->add_setting(
		"post_excerpt",
		array(
			'default'              => 'excerpts',
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_text_field',
		)
	);

	/* Add the checkbox control for the 'content_archive' setting. */
	$wp_customize->add_control( 
		'post_excerpt',
		array(
			'priority'   => 1,
			'type' => 'select',
			'label'    => '',
			'section'  => 'post_section',
			'settings' => 'post_excerpt',
			'choices' => array(
	            'full' => 'Display full post',
	            'excerpts' => 'Display post excerpts',
	        )	        
		)
	);	

	/* Add the 'no_more_link_scroll' setting. */
	$wp_customize->add_setting(
		"more_scroll",
		array(
			'default'              => '1',
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_text_field',
		)
	);

	/* Add the checkbox control for the 'more_link_scroll' setting. */
	$wp_customize->add_control( 
		new Omega_Customize_Control_Checkbox( 
			$wp_customize,
			'more_scroll',
			array(
				'priority'   => 2,
				'type' => 'checkbox',
				'label'    => esc_html__( 'More Link Page Scroll', 'omega' ),
				'section'  => 'post_section',
				'settings' => 'more_scroll',
				'extra'	=> esc_html__( 'By default, clicking the .more-link anchor opens and scrolls the page to section of the document containing the named anchor. This section is where you put the <!--more--> tag within a post type.', 'omega' ),
			)
		)
	);	

	/* Add the 'content_archive_limit' setting. */
	$wp_customize->add_setting(
		"excerpt_chars_limit",
		array(
			'default'              => '0',
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_text_field',
		)
	);

	/* Add the checkbox control for the 'content_archive_limit' setting. */
	$wp_customize->add_control(
		new Omega_Customize_Control_Char( 
			$wp_customize,
			'excerpt_chars_limit',
			array(
				'priority'   => 3,
				'label'    => esc_html__( 'Limit content to', 'omega' ),
				'section'  => 'post_section',
				'settings' => 'excerpt_chars_limit',
				'extra'    => esc_html__( 'Select "Display post excerpts" will limit the text and strip all formatting from the text displayed. Set 0 characters will display the first 55 words (default)', 'omega' ),
			)
		)
	);

	/* Add the 'more_text' setting. */
	$wp_customize->add_setting(
		"more_text",
		array(
			'default'              => '[Read more...]',
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_text_field',
		)
	);

	/* Add the checkbox control for the 'more_text' setting. */
	$wp_customize->add_control( 
		'more_text',
		array(
			'priority'   => 4,
			'type' => 'text',
			'label'    => esc_html__( 'More Text', 'omega' ),
			'section'  => 'post_section',
			'settings' => 'more_text',
		)
	);	

	/* Add the 'content_archive_thumbnail' setting. */
	$wp_customize->add_setting(
		"post_thumbnail",
		array(
			'default'              => '1',
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_text_field',
		)
	);

	/* Add the checkbox control for the 'content_archive_thumbnail' setting. */
	$wp_customize->add_control( 
		'post_thumbnail',
		array(
			'priority'   => 5,
			'type' => 'checkbox',
			'label'    => esc_html__( 'Include the Featured Image?', 'omega' ),
			'section'  => 'post_section',
			'settings' => 'post_thumbnail'
		)
	);	

	/* Add the 'image_size' setting. */
	$wp_customize->add_setting(
		"image_size",
		array(
			'default'              => 'large',
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_text_field',
		)
	);

	/* Add the checkbox control for the 'image_size' setting. */
	$sizes = omega_get_image_sizes();
	$imagesizes = array();
	foreach ( (array) $sizes as $name => $size ) {
		$imagesizes[esc_attr( $name )] = esc_attr( $name ) . ' (' . absint( $size['width'] ) . ' &#x000D7; ' . absint( $size['height'] ) . ')';
	}
		
	$wp_customize->add_control( 
		'image_size',
		array(
			'priority'   => 6,
			'type' => 'select',
			'label'    => esc_html__( 'Image size', 'omega' ),
			'section'  => 'post_section',
			'settings' => 'image_size',
			'choices' => $imagesizes,
		)
	);

	/* Add the 'single_nav' setting. */
	$wp_customize->add_setting(
		"single_nav",
		array(
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_text_field',
		)
	);

	/* Add the checkbox control for the 'more_link_scroll' setting. */
	$wp_customize->add_control( 
		'single_nav',
		array(
			'priority'   => 7,
			'type' => 'checkbox',
			'label'    => esc_html__( 'Enable single post Prev Next navigation links?', 'omega' ),
			'section'  => 'post_section',
			'settings' => 'single_nav',
		)
	);	

	wp_enqueue_style( 'omega-customizer', trailingslashit( OMEGA_CSS ) . "customizer.css" );
	
}

add_action('customize_controls_print_scripts', 'omega_customize_post_script');

function omega_customize_post_script() {
?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		"use strict";
		
		function init(){
			if ($("#customize-control-post_thumbnail input:checkbox").attr('checked')) {
				$("#customize-control-image_size").removeClass('hidden');
			}
			else {
				$("#customize-control-image_size").addClass('hidden');
			}
			if ($("#customize-control-post_excerpt select").val() != 'full') {
				$('#customize-control-excerpt_chars_limit').removeClass('hidden');
				$('#customize-control-more_text').removeClass('hidden');
				$('#customize-control-more_scroll').addClass('hidden');
			}
			else {
				$('#customize-control-excerpt_chars_limit').addClass('hidden');
				$('#customize-control-more_text').addClass('hidden');
				$('#customize-control-more_scroll').removeClass('hidden');
			}
		}
		init();
		

		$('#customize-control-post_thumbnail input:checkbox').on('click load', function() {
			if ($(this).attr('checked')) {
				$("#customize-control-image_size").removeClass('hidden');
			}
			else {
				$("#customize-control-image_size").addClass('hidden');
			}
		});

		$('#customize-control-post_excerpt select').on('change load', function() {
		  	if (this.value != 'full') {
				$('#customize-control-excerpt_chars_limit').removeClass('hidden');
				$('#customize-control-more_text').removeClass('hidden');
				$('#customize-control-more_scroll').addClass('hidden');
			}
			else {
				$('#customize-control-excerpt_chars_limit').addClass('hidden');
				$('#customize-control-more_text').addClass('hidden');
				$('#customize-control-more_scroll').removeClass('hidden');
			}
		});

	});	
</script>
<?php 		
}