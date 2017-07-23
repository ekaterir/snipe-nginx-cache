<?php

class CSNX_Filesystem_Helper {
 
  /**
   * CSNX_Filesystem_Helper $filesystem
   */ 
  private static $filesystem = null;
 
  /**
   * Get instance.
   * @return CSNX_Filesystem_Helper $filesystem
   */ 
  public static function get_instance() {
    if ( self::$filesystem == null ) {
        self::$filesystem = new self;
    }  
    return self::$filesystem; 
  }

  private function __construct() {
  }

  /**
   * Initialize filesystem.
   * @param string $path
   * @return bool
   */
  public function initialize_filesystem( $path ) {
    if ( !is_dir( $path ) ) {
        $path = dirname($path);  
    }
    ob_start();
    $credentials = request_filesystem_credentials( '', '', false, $path, null, true );
    $data = ob_get_clean();
    if ( false === $credentials || ( ! WP_Filesystem( $credentials, $path, true ) )) {
	return false;
    }
    return true;
  }

  /**
   * Is the path valid (exists and writable).
   * @param string $path
   * @return bool
   */
  public function is_valid_path( $path ) {
    global $wp_filesystem;

    if ( !$path ) {
	return false;
    }
    if ( $this->initialize_filesystem( $path ) ) {
	if ( ! $wp_filesystem->exists( $path ) ) {
	     return false;
	}
	if ( ! $wp_filesystem->is_writable( $path ) ) {
	     return false;
	}
	return true;
    }
    return false;
  }

  /**
   * Delete a directory.
   * @param string $path
   * @param bool $recursive
   * @return bool
   */ 
  public function delete( $path, $recursive = false ) {
    if ( !$path )
	return false;
    global $wp_filesystem; 
    if ( $this->is_valid_path( $path ) )
        return $wp_filesystem->rmdir( $path, $recursive );
    return false;
    
  }  
 
  /**
   * Get cache hash key for a page url.
   * fastcgi_cache_key  "$scheme$request_method$host$request_uri"
   * @param string $page_url | Full URL of the page
   * @return string
   */ 
  public function get_cache_hash_key( $page_url ) {
    $url = wp_parse_url( $page_url );
    return md5( $url['scheme'] . 'GET' . $url['host'] . $url['path'] );
  }

  /**
   * Make folders using the last char, and then the next two based on the hash.
   * @param string $cache_key
   * @return string 
   */
  public function get_cache_zone_subfolders( $cache_key ) {
    return substr($cache_key, -1) . '/' . substr($cache_key, -3, 2) . '/' . $cache_key;	
  }

  /**
   * Get complete path to the directory / file.
   * @param string $cache_zone_path
   * @param string $page_url
   * @return string
   */ 
  public function get_nginx_cache_path( $cache_zone_path, $page_url = '' ) {
    if ( ! $page_url ) {
      return $cache_zone_path;
    }
    $cache_zone_path = rtrim($cache_zone_path, '/') . '/';
    $cache_key = $this->get_cache_hash_key( $page_url ); 
    return $cache_zone_path . $this->get_cache_zone_subfolders( $cache_key );
  }
}

