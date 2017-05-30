<?php

    add_filter( 'post_row_actions', 'modify_list_row_actions', 10, 2 );
    add_filter( 'page_row_actions', 'modify_list_row_actions', 10 ,2 );
    add_action( 'admin_enqueue_scripts', 'load_toolbar_js' );
    add_action('wp_ajax_delete_current_page_cache', 'delete_current_page_cache');

    function load_toolbar_js() {
        wp_enqueue_script("fastcgi-cache", plugins_url("fastcgi-cache-page-purge/js/toolbar.js"), array(), time(), true); 
    }

    function delete_current_page_cache() {
	$path = get_option( 'fastcgi_cache_path' );

	$handleFiles = new Filesystem_Helper();
	$handleFiles->delete_directory($path);
      if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	if($_GET["post"]){
	  $permalink = get_permalink($_GET['post']);
	$handleFiles->get_nginx_cache_path($permalink);
	  die(json_encode(array($new_string, "error", "alert", $new_path)));
	}    
      }
    }

    function modify_list_row_actions( $actions, $post ) {
       return Cache_Purge::modify_list_row_actions( $actions, $post );
    }

class Cache_Purge {

  public static function modify_list_row_actions( $actions, $post ) {

	$actions = array_merge( $actions, [
		'cache_purge' => sprintf( '<a href="#" class="cache-purge-inline" id="%1$d">%2$s</a>', $post->ID, esc_html( __( 'Cache Purge' ) ) )
	]);
 
    return $actions;
  }
}
