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
  private $cache_path_setting = 'nginx_cache_sniper_path';

  public function __construct() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/filesystem_helper.php'; 
    require_once plugin_dir_path( __FILE__ ) . 'includes/render_helper.php';
    add_action( 'admin_enqueue_scripts', [ $this, 'load_cache_actions_js' ] );
    add_action( 'admin_init', [ $this, 'register_nginx_cache_sniper_settings' ] );
    add_action( 'admin_menu', [ $this, 'create_tools_page' ] );
    add_action( 'wp_before_admin_bar_render', [ $this, 'nginx_cache_sniper_tweaked_admin_bar' ] ); 
    require_once plugin_dir_path( __FILE__ ) . 'includes/row_actions.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/admin_toolbar.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/metabox.php';
  } 
 
  public function get_plugin_name() {
    return $this->plugin_name;
  }
  
  public function get_cache_path_setting() {
    return $this->cache_path_setting;
  }

  /**
   * Load javascript.
   */
  public function load_cache_actions_js() {
    wp_enqueue_script( $this->get_plugin_name() . "_cache_actions", plugins_url( $this->get_plugin_name() . "/js/cache_actions.js" ), [], time(), true ); 
  }

  /**
   * Register plugin's settings.
   */
  public function register_nginx_cache_sniper_settings() {
    register_setting( $this->get_plugin_name(), $this->get_cache_path_setting(), 'sanitize_text_field' );
  }

  /**
   * Add tools page.
   */
  public function create_tools_page() {
    add_management_page( Render_Helper::PLUGIN_NAME, Render_Helper::PLUGIN_NAME, 'manage_options', $this->get_plugin_name(), [ $this, 'build_form' ] );
  }

  /**
   * Build the form on the tools page.
   */
  function build_form() {
    $cache_path_setting = $this->get_cache_path_setting();
    $cache_path_value = esc_attr( get_option( $cache_path_setting ));
    $render_plugin_name = Render_Helper::PLUGIN_NAME; 
    echo '<form class="form-table" method="post" action="options.php">';
    settings_fields( $this->get_plugin_name() );
    echo <<<EOT
    <div class="wrap">
      <h2>$render_plugin_name</h2>
        <table class="form-table">
	  <tbody>
	    <tr>
	      <th scope="row">
	        Cache Path
	      </th>
	      <td>
	        <input type="text" class="regular-text code" name="$cache_path_setting" placeholder="/data/nginx/cache" value="$cache_path_value" />
	        <p class="description">The absolute path to the location of the cache zone, specified in the Nginx <code>fastcgi_cache_path</code>.</p>
	      </td>
	    </tr>
	  </tbody>
        </table>
EOT;
    submit_button();
    echo '</form></div>';
  }

  /**
   * Add menu to the admin toolbar.
   */
  function nginx_cache_sniper_tweaked_admin_bar() {
    global $wp_admin_bar;
    $title = '';
    $id = '';
    $filesystem = Filesystem_Helper::get_instance();
    $cache_path = $filesystem->get_nginx_cache_path( get_option( $this->get_cache_path_setting() ), '' );
    if ( $filesystem->is_valid_path( $cache_path ) ) {
      $title = Render_Helper::CLEAR_ENTIRE_CACHE;
      $id = 'delete_entire_cache';
    } else {
      $title = Render_Helper::ENTIRE_CACHE_CLEARED;
      $id = 'no_cache';
    }

    $wp_admin_bar->add_node([
      'id'    => 'fastcgi_cache',
      'title' => Render_Helper::PLUGIN_NAME
    ]);

    $wp_admin_bar->add_menu([
      'id'    => $id,
      'title' => $title,
      'parent'=> 'fastcgi_cache'
    ]);
  }
}
