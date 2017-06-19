<?php


    function delete_entire_cache() { 
	      $path = get_option( 'nginx_cache_sniper_path' );
	      $filesystem = Filesystem_Helper::get_instance();
	      $cache_deleted = $filesystem->delete( $path, true );
	      die(json_encode([$cache_deleted]));
    }
    add_action( 'wp_ajax_delete_entire_cache', 'delete_entire_cache' );
