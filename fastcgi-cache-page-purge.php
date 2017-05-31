<?php
/**
 * Plugin Name: FastCGI Cache Page Purge
 * Plugin URI: ###
 * Description: ###
 * Version: 1.0.0
 * Author: Thorn Technologies LLC
 * License: MIT
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( is_admin() ) {
  new FastCGI_Cache_Page_Purge();
}

class FastCGI_Cache_Page_Purge {
  public function __construct() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/filesystem_helper.php'; 
    require_once plugin_dir_path( __FILE__ ) . 'includes/settings_manager.php'; 
    require_once plugin_dir_path( __FILE__ ) . 'includes/row_actions.php';
  } 
}
