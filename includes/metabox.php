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
        $new_action = '';
	$filesystem = Filesystem_Helper::get_instance();
	$cache_zone_path = get_option( 'nginx_cache_sniper_path' );
	$permalink = get_permalink( $post );
        $cache_path = $filesystem->get_nginx_cache_path( $cache_zone_path, $permalink );
        if ( $filesystem->is_valid_path( $cache_path ) ) {
	    $new_action = sprintf( '<a href="#" class="cache-purge-inline" id="%1$d">%2$s</a>', $post->ID, esc_html( __( 'Purge cache for this page' ) ) );
        } else {
	    $new_action = sprintf( '<span>%1$s</span>', esc_html( __( 'Cache purged' ) ) );
        }
  echo '<p>' . $new_action . '</p>';
}
add_action( 'add_meta_boxes', 'nginx_cache_sniper_register_metabox' );

