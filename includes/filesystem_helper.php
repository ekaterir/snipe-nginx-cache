<?php

class Filesystem_Helper {

  private $path = '';
  
  public function __construct( $path = '' ) {
	$this->set_path( $path );
  }

  private function set_path( $path ) {
    if (!$path)
	return;

    
    $this->path = $path;
  }

  public function is_valid_path( $path ) {

	// Some rules for path validation

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

  private function initialize_filesystem( $path ) {
    ob_start();
    $credentials = request_filesystem_credentials( $path );
    $data = ob_get_clean(); 
    if ( false === $credentials ) {
	return $this->check_connection( $data );
    }
 
    if ( ! WP_Filesystem( $credentials ) ) {
        ob_start();
        request_filesystem_credentials( $path, '', true ); // Failed to connect, Error and request again.
        $data = ob_get_clean();
	return $this->check_connection( $data );
    }

    if ( ! is_object($wp_filesystem) )
        return new WP_Error('fs_unavailable', __('Could not access filesystem.'));
 
    if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
        return new WP_Error('fs_error', __('Filesystem error.'), $wp_filesystem->errors);
  }

  public function delete_directory( $path, $recursive = false ) {
    if (!$path)
	return false;
    global $wp_filesystem;
    $this->initialize_filesystem( $path );
 
    return $wp_filesystem->delete( $path, $recursive );
  }

  public function get_nginx_cache_path ( $permalink ) {
	  $url = wp_parse_url($permalink);
	  $new_string = $url['scheme'] . 'GET' . $url['host'] . $url['path']; 
	  $new_string = md5($new_string);
          $new_path = $path . substr($new_string, -1) . '/' . substr($new_string, -3, 2) . '/' . $new_string;
	
  }
}
