<?php
    add_filter( 'post_row_actions', 'modify_list_row_actions', 10, 2 );
    add_filter( 'page_row_actions', 'modify_list_row_actions', 10 ,2 );
    add_action( 'admin_enqueue_scripts', 'load_row_actions_js' );
    add_action( 'wp_ajax_delete_current_page_cache', 'delete_current_page_cache' );

    function modify_list_row_actions( $actions, $post ) {
	$actions = array_merge( $actions, [
		'cache_purge' => sprintf( '<a href="#" class="cache-purge-inline" id="%1$d">%2$s</a>', $post->ID, esc_html( __( 'Purge cache for this page' ) ) )
	]); 
        return $actions;
    }
    
    function load_row_actions_js() {
        wp_enqueue_script("fastcgi-cache", plugins_url("fastcgi-cache-page-purge/js/row_actions.js"), array(), time(), true); 
    }

    function delete_current_page_cache() {
      if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {
	  if ( $_GET["post"] ) {
	      $permalink = get_permalink( $_GET['post'] );
	      $path = get_option( 'fastcgi_cache_path' );
	      try {
		  $filesystem = Filesystem_Helper::get_instance();
	          $directory_deleted = $filesystem->delete_directory($path, $permalink, true);
	      } catch (\Exception $e) {
	      	  die(json_encode(['error' => $e->getMessage()]));
	      }
	      die(json_encode([$directory_deleted]));
	  }    
      }
    }


