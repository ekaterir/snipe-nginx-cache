<?php

    add_action( 'wp_before_admin_bar_render', 'fastcgi_cache_tweaked_admin_bar' );
    add_action( 'admin_enqueue_scripts', 'load_admin_bar_js' );
    add_action( 'wp_ajax_delete_entire_cache', 'delete_entire_cache' );

    function fastcgi_cache_tweaked_admin_bar() {
	global $wp_admin_bar;
        $title = '';
        $id = '';
        $filesystem = Filesystem_Helper::get_instance();
        $filesystem->set_path(get_option('fastcgi_cache_path'));
        if ( $filesystem->is_valid_path( get_option( 'fastcgi_cache_path' ) ) ) {
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
		'id'    => 'delete_entire_cache',
		'title' => 'Purge Entire Cache',
		'parent'=> 'fastcgi_cache'
	));
    }
 
    function load_admin_bar_js() {
        wp_enqueue_script("fastcgi-cache_admin_bar", plugins_url("fastcgi-cache-page-purge/js/admin_bar.js"), array(), time(), true); 
    }

    function delete_entire_cache() { 
	      $path = get_option( 'fastcgi_cache_path' );
	      $filesystem = Filesystem_Helper::get_instance();
	      $cache_deleted = $filesystem->delete_cache( $path );
	      die(json_encode([$cache_deleted]));
    }
