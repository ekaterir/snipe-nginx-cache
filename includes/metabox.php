<?php
/**
 * Register meta box(es).
 */
function fastcgi_cache_register_meta_boxes() {
    add_meta_box(
    'fastcgi_cache_metabox',
  __( 'Cache Purge' ),
  'fastcgi_cache_render_metabox',
  ['post', 'page'],
  'side',
  'low'
    );
}
add_action( 'add_meta_boxes', 'fastcgi_cache_register_meta_boxes' );
/**
 */
function fastcgi_cache_render_metabox( $post ) {
        $new_action = '';
        $filesystem = Filesystem_Helper::get_instance();
	$path = get_option( 'fastcgi_cache_path' );
	$permalink = get_permalink( $post );
        $cache_path = $filesystem->get_nginx_cache_path( $path, $permalink );
        if ( $filesystem->is_valid_path( $cache_path ) ) {
	    $new_action = sprintf( '<a href="#" class="cache-purge-inline" id="%1$d">%2$s</a>', $post->ID, esc_html( __( 'Purge cache for this page' ) ) );
        } else {
	    $new_action = sprintf( '<span>%1$s</span>', esc_html( __( 'Cache purged' ) ) );
        }
  echo '<p>' . $new_action . '</p>';
}
