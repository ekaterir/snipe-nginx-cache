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
	$filesystem->set_path(get_option( 'nginx_cache_sniper_path' ));
	$permalink = get_permalink( $post );
        $cache_path = $filesystem->get_nginx_cache_path( $permalink );
        if ( $filesystem->is_valid_path( $cache_path ) ) {
	    $new_action = sprintf( '<a href="#" class="cache-purge-inline" id="%1$d">%2$s</a>', $post->ID, esc_html( __( 'Purge cache for this page' ) ) );
        } else {
	    $new_action = sprintf( '<span>%1$s</span>', esc_html( __( 'Cache purged' ) ) );
        }
  echo '<p>' . $new_action . '</p>';
}
add_action( 'add_meta_boxes', 'nginx_cache_sniper_register_metabox' );

    function load_metabox_js() {
        wp_enqueue_script("nginx-cache-sniper_metabox", plugins_url("nginx-cache-sniper/js/metabox.js"), [], time(), true); 
    }
    add_action( 'admin_enqueue_scripts', 'load_metabox_js' );
