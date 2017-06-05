<?php


    function nginx_cache_sniper_tweaked_admin_bar() {
	global $wp_admin_bar;
        $title = '';
        $id = '';
        $filesystem = Filesystem_Helper::get_instance();
        $filesystem->set_path( get_option( 'nginx_cache_sniper_path' ) );
        if ( $filesystem->is_valid_path( get_option( 'nginx_cache_sniper_path' ) ) ) {
           $title = 'Purge Entire Cache';
           $id = 'delete_entire_cache';
        } else {
           $title = 'Entire cache was purged';
           $id = 'no_cache';
        }

	$wp_admin_bar->add_node(array(
		'id'    => 'fastcgi_cache',
		'title' => 'Cache Purge'
	));

	$wp_admin_bar->add_menu( array(
                'id'    => $id,
                'title' => $title,
		'parent'=> 'fastcgi_cache'
	));
    }
    add_action( 'wp_before_admin_bar_render', 'nginx_cache_sniper_tweaked_admin_bar' );
 
    function load_admin_bar_js() {
        wp_enqueue_script("nginx-cache-sniper_admin_bar", plugins_url("nginx-cache-sniper/js/admin_bar.js"), [], time(), true); 
    }
    add_action( 'admin_enqueue_scripts', 'load_admin_bar_js' );

    function delete_entire_cache() { 
	      $path = get_option( 'nginx_cache_sniper_path' );
	      $filesystem = Filesystem_Helper::get_instance();
	      $cache_deleted = $filesystem->delete_cache( $path );
	      die(json_encode([$cache_deleted]));
    }
    add_action( 'wp_ajax_delete_entire_cache', 'delete_entire_cache' );
