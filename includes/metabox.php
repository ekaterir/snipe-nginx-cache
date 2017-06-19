<?php
/**
 * Register metabox.
 */
function nginx_cache_sniper_register_metabox() {
    add_meta_box(
    'nginx_cache_sniper_metabox',
  __( 'Cache Purge' ),
  'nginx_cache_sniper_render_metabox',
  ['post', 'page'],
  'side',
  'low'
    );
}
/**
 * Render metabox.
 */
function nginx_cache_sniper_render_metabox( $post ) {
  $render = Render_Helper::get_instance();
  echo '<p>' . $render->delete_current_page( $post )  . '</p>';
}
add_action( 'add_meta_boxes', 'nginx_cache_sniper_register_metabox' );

