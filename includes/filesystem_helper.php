<?php

class Filesystem_Helper {

  private static $filesystem = null;
  private $path = '';
  
  public static function get_instance() {
    if ( self::$filesystem == null ) {
        self::$filesystem = new self;
    }  
    return self::$filesystem;
  }

  private function __construct() {
  }

  private function initialize_filesystem() {  

    global $wp_filesystem;

    ob_start();
    $credentials = request_filesystem_credentials( '', '', false, $this->path, null, true );
    $data = ob_get_clean();
 
    if ( false === $credentials || ( ! WP_Filesystem( $credentials, $this->path, true ) )) {
	return false;
    }
    return true;
  }

  private function is_valid_path( $path ) {
    global $wp_filesystem;

    if ( empty( $path ) ) {
	return new WP_Error( 'empty', __( '"Cache Zone Path" is not set.', 'nginx-cache' ) );
    }
    if ( $this->initialize_filesystem() ) {
	if ( ! $wp_filesystem->exists( $path ) ) {
	     return false;
	}
	if ( ! $wp_filesystem->is_dir( $path ) ) {
	     return false;
	}
	if ( ! $wp_filesystem->is_writable( $path ) ) {
	     return false;
	}
	return true;
	}
    return false;
  }

  private function set_path( $path ) {
    if (!$path)
	return;
    
    $this->path = rtrim($path, '/') . '/';
    return;
  }

  public function delete_cache( $path ) {
    return $this->delete_directory( $path, '', true );
  }

  public function delete_directory( $path, $permalink, $recursive = false ) {
    if ( !$path )
	return false;
    $this->set_path( $path );
    global $wp_filesystem;
    $cache_path = $this->get_nginx_cache_path( $permalink );
    if ($this->is_valid_path( $cache_path ))
        return $wp_filesystem->rmdir( $this->get_nginx_cache_path( $permalink ), $recursive );
    return false;
  }

  private function get_nginx_cache_path ( $permalink ) {
        if ( ! $permalink ) {
	    return $this->path;
	}
	$url = wp_parse_url( $permalink );
	$nginx_cache_path = md5($url['scheme'] . 'GET' . $url['host'] . $url['path']); 
        return $this->path . substr($nginx_cache_path, -1) . '/' . substr($nginx_cache_path, -3, 2) . '/' . $nginx_cache_path;
  }
}

Filesystem_Helper::get_instance();
