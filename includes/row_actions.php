<?php
    add_filter( 'post_row_actions', 'modify_list_row_actions', 10, 2 );
    add_filter( 'page_row_actions', 'modify_list_row_actions', 10 ,2 );
    add_action( 'admin_enqueue_scripts', 'load_row_actions_js' );
    add_action( 'wp_ajax_delete_current_page_cache', 'delete_current_page_cache' );

    function modify_list_row_actions( $actions, $post ) {
        $new_action = '';
        $filesystem = Filesystem_Helper::get_instance();
	$filesystem->set_path(get_option( 'fastcgi_cache_path' ));
	$permalink = get_permalink( $post );
        $cache_path = $filesystem->get_nginx_cache_path( $permalink );
        if ( $filesystem->is_valid_path( $cache_path ) ) {
	    $new_action = sprintf( '<a href="#" class="cache-purge-inline" id="%1$d">%2$s</a>', $post->ID, esc_html( __( 'Purge cache for this page' ) ) );
        } else {
	    $new_action = sprintf( '<span>%1$s</span>', esc_html( __( 'Cache purged' ) ) );
        }
	$actions = array_merge( $actions, [
		'cache_purge' => $new_action
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
	      $filesystem = Filesystem_Helper::get_instance();
	      $directory_deleted = $filesystem->delete_directory($path, $permalink, true);
	      die(json_encode([$directory_deleted]));
	  }    
      }
    }


