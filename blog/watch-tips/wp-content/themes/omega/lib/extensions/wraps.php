<?php

function omega_wrap_open() {
  echo omega_apply_atomic( 'wrap_open', '<div class="wrap">');
}

function omega_wrap_close() {
  echo omega_apply_atomic( 'wrap_close', '</div>');
}

add_action('omega_header', 'omega_wrap_open', 7 );
add_action('omega_header', 'omega_wrap_close' );

add_action('omega_before_main', 'omega_wrap_open', 7 );
add_action('omega_after_main', 'omega_wrap_close' );

add_action('omega_before_primary_menu', 'omega_wrap_open', 7 );
add_action('omega_after_primary_menu', 'omega_wrap_close' );

add_action('omega_footer', 'omega_wrap_open', 7 );
add_action('omega_footer', 'omega_wrap_close' );

?>