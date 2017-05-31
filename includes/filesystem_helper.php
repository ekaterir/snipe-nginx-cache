<?php

class Filesystem_Helper {

  private $path = '';
  private static $filesystem = null;
  
  private function __construct( $path ) {
    $this->set_path( $path );
  }

  private function initialize_filesystem() {  

    global $wp_filesystem;

    ob_start();
    $credentials = request_filesystem_credentials( '', '', false, $this->path, null, true );
    $data = ob_get_clean();
 
    if ( false === $credentials || ( ! WP_Filesystem( $credentials, $this->path, true ) )) {
        return new WP_Error( 'filesystem', __( 'Filesystem API could not be initialized.', 'fastcgi-cache' ) );
    }
    if ( ! $wp_filesystem->exists( $this->path ) ) {
	return new WP_Error( 'filesystem', __( '"Cache Zone Path" does not exist.', 'fastcgi-cache' ) );
    }
    if ( ! $wp_filesystem->is_dir( $this->path ) ) {
	return new WP_Error( 'filesystem', __( '"Cache Zone Path" is not a directory.', 'fastcgi-cache' ) );
    }
    if ( ! $wp_filesystem->is_writable( $this->path ) ) {
	return new WP_Error( 'filesystem', __( '"Cache Zone Path" is not writable.', 'fastcgi-cache' ) );
    }
    return;
  }

  private function set_path( $path ) {
    if (!$path)
	return;
    
    $this->path = rtrim($path, '/') . '/';
    return;
  }

  public static function delete_directory( $path, $permalink, $recursive = false ) {
    if (!$path)
	return false;

    global $wp_filesystem;

    if ( self::$filesystem == null || $this->path !== $path ) {
        self::$filesystem = new Filesystem_Helper( $path );
        self::$filesystem->initialize_filesystem();
    } 

    return $wp_filesystem->rmdir( self::$filesystem->get_nginx_cache_path( $permalink ), $recursive );
  }

  private function get_nginx_cache_path ( $permalink ) {
	$url = wp_parse_url( $permalink );
	$nginx_cache_path = md5($url['scheme'] . 'GET' . $url['host'] . $url['path']); 
        return $this->path . substr($nginx_cache_path, -1) . '/' . substr($nginx_cache_path, -3, 2) . '/' . $nginx_cache_path;
  }
}
