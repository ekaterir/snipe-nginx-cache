<?php
/**
 * Plugin Name: Nginx Cache Sniper
 * Description: Purge the Nginx FastCGI Cache within WordPress on a global or per-page basis.
 * Version: 1.0.0
 * Author: Thorn Technologies LLC
 * License: MIT
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( is_admin() ) {
  new Nginx_Cache_Sniper();
}

class Nginx_Cache_Sniper {

  private $plugin_name = 'nginx-cache-sniper';

  public function __construct() {
    add_action( 'admin_enqueue_scripts', [ $this, 'load_cache_actions_js' ] );
    require_once plugin_dir_path( __FILE__ ) . 'includes/filesystem_helper.php'; 
    require_once plugin_dir_path( __FILE__ ) . 'includes/options.php'; 
    require_once plugin_dir_path( __FILE__ ) . 'includes/row_actions.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/admin_toolbar.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/metabox.php';
  } 
 
  public function get_plugin_name() {
    return $this->plugin_name;
  }

  public function load_cache_actions_js() {
    wp_enqueue_script("nginx-cache-sniper_cache_actions", plugins_url("nginx-cache-sniper/js/cache_actions.js"), [], time(), true); 
  }

}
