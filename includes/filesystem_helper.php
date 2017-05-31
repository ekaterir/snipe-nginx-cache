<?php

class Filesystem_Helper {

  private $path = '';
  private static $filesystem = null;
  
  private function __construct( $path ) {
    $this->set_path( $path );
    $this->initialize_filesystem( $path );
  }

  private function initialize_filesystem() {
    
    ob_start();
    $credentials = request_filesystem_credentials( $this->path );
    $data = ob_get_clean(); 
    if ( false === $credentials ) {
	return $this->check_connection( $data );
    }
 
    if ( ! WP_Filesystem( $credentials ) ) {
        ob_start();
        request_filesystem_credentials( $this->path, '', true ); // Failed to connect, Error and request again.
        $data = ob_get_clean();
	return $this->check_connection( $data );
    }

    if ( ! is_object($wp_filesystem) )
        return new WP_Error('fs_unavailable', __('Could not access filesystem.'));
 
    if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
        return new WP_Error('fs_error', __('Filesystem error.'), $wp_filesystem->errors);
  }

  private function set_path( $path ) {
    if (!$path)
	return;
    
    $this->path = rtrim($path, '/') . '/';
    return;
  }

  private function check_connection( $data ) {
        if ( ! empty($data) ){
            include_once( ABSPATH . 'wp-admin/admin-header.php');
            echo $data;
            include( ABSPATH . 'wp-admin/admin-footer.php');
            exit;
        }
        return;
  }

  public static function delete_directory( $path, $permalink, $recursive = false ) {
    if (!$path)
	return false;
    global $wp_filesystem;
    if ( self::$filesystem == null ) {
        self::$filesystem = new Filesystem_Helper( $path );
    } else {
	self::$filesystem->set_path( $path );
    }
    return $wp_filesystem->delete( self::$filesystem->get_nginx_cache_path( $permalink ), $recursive );
  }

  private function get_nginx_cache_path ( $permalink ) {
	  $url = wp_parse_url( $permalink );
	  $nginx_cache_path = md5($url['scheme'] . 'GET' . $url['host'] . $url['path']); 
          return $this->path . substr($nginx_cache_path, -1) . '/' . substr($nginx_cache_path, -3, 2) . '/' . $nginx_cache_path;
  }
}
