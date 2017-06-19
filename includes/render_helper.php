<?php

class Render_Helper {

  const PLUGIN_NAME = 'Nginx Cache Sniper';
  const CLEAR_ENTIRE_CACHE = 'Clear entire cache';
  const ENTIRE_CACHE_CLEARED = 'Entire cache cleared';
  const CLEAR_PAGE_CACHE = 'Clear cache for this page';
  const PAGE_CACHE_CLEARED = 'Cache cleared';
 
  /**
   * Render_Helper $render
   */ 
  private static $render = null;
 
  /**
   * Get instance.
   * @return Render_Helper $render
   */ 
  public static function get_instance() {
    if ( self::$render == null ) {
        self::$render = new self;
    }  
    return self::$render; 
  }

  private function __construct() {
  }

  /**
   * Render text for the current page cache clearing.
   */
  public function delete_current_page( $post ) {
    $filesystem = Filesystem_Helper::get_instance();
    $cache_zone_path = get_option( 'nginx_cache_sniper_path' );
    $permalink = get_permalink( $post );
    $cache_path = $filesystem->get_nginx_cache_path( $cache_zone_path, $permalink );
    if ( $filesystem->is_valid_path( $cache_path ) ) {
      return sprintf( '<a href="#" class="cache-purge-inline" id="%1$d">%2$s</a>', $post->ID, esc_html( __( self::CLEAR_PAGE_CACHE ) ) );
    }
    return sprintf( '<span>%1$s</span>', esc_html( __( self::PAGE_CACHE_CLEARED ) ) );
  }

}

Render_Helper::get_instance();
