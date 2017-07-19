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
  private $cache_clear_on_update_setting = 'nginx_cache_sniper_auto_clear';

  public function __construct() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/filesystem_helper.php'; 
    require_once plugin_dir_path( __FILE__ ) . 'includes/render_helper.php';
    add_action( 'admin_enqueue_scripts', [ $this, 'load_cache_actions_js' ] );
    add_action( 'admin_init', [ $this, 'register_nginx_cache_sniper_settings' ] );
    add_action( 'admin_menu', [ $this, 'create_tools_page' ] );
    add_action( 'wp_before_admin_bar_render', [ $this, 'nginx_cache_sniper_tweaked_admin_bar' ] ); 
    add_filter( 'post_row_actions', [ $this, 'modify_list_row_actions' ], 10, 2 );
    add_filter( 'page_row_actions', [ $this, 'modify_list_row_actions' ], 10 ,2 );
    add_action( 'add_meta_boxes', [ $this, 'nginx_cache_sniper_register_metabox' ] );
    add_action( 'wp_ajax_delete_entire_cache', [ $this, 'delete_entire_cache' ] );
    add_action( 'wp_ajax_delete_current_page_cache', [ $this, 'delete_current_page_cache' ] );
    add_action( 'save_post', [ $this, 'delete_current_page_cache_on_update' ] );
  } 

  public function get_plugin_name() {
    return $this->plugin_name;
  }
  
  public function get_cache_path_setting() {
    return $this->cache_path_setting;
  }
 
  public function get_cache_clear_on_update_setting() {
    return $this->cache_clear_on_update_setting;
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
    register_setting( $this->get_plugin_name(), $this->get_cache_clear_on_update_setting(), 'absint' );
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
    $render = Render_Helper::get_instance();
    $render->settings_form();
  }

  /**
   * Add menu to the admin toolbar.
   */
  function nginx_cache_sniper_tweaked_admin_bar() {
    $render = Render_Helper::get_instance();
    $render->admin_bar();
  }

  /**
   * Register metabox.
   */
  public function nginx_cache_sniper_register_metabox() {
    add_meta_box(
      'nginx_cache_sniper_metabox',
      Render_Helper::PLUGIN_NAME,
      [ $this, 'nginx_cache_sniper_render_metabox' ],
      ['post', 'page'],
      'side',
      'low'
    );
  }

  /**
   * Render metabox.
   */
  public function nginx_cache_sniper_render_metabox( $post ) {
    $render = Render_Helper::get_instance();
    echo '<p>' . $render->delete_current_page( $post )  . '</p>';
  }

  /**
   * Add an action to the list of posts and pages.
   */
  public function modify_list_row_actions( $actions, $post ) {
    $render = Render_Helper::get_instance();
    $actions = array_merge( $actions, [
      'cache_purge' => $render->delete_current_page( $post )
    ]); 
    return $actions;
  }

  /**
   * Delete entire cache.
   */
  public function delete_entire_cache() { 
    $path = get_option( $this->get_cache_path_setting() );
    $filesystem = Filesystem_Helper::get_instance();
    $cache_deleted = $filesystem->delete( $path, true );
    die(json_encode([$cache_deleted]));
  }

  /**
   * Delete current page cache.
   */
  public function delete_current_page_cache() {
    if ( isset($_GET["post"]) ) {
      $permalink = get_permalink( $_GET['post'] );
    } else {
      die(json_encode(['error' => 'Page/post was not supplied']));
    }
    $path = get_option( $this->get_cache_path_setting() );
    $filesystem = Filesystem_Helper::get_instance();
    $cache_path = $filesystem->get_nginx_cache_path( $path, $permalink );
    $directory_deleted = $filesystem->delete( $cache_path );
    die(json_encode([$directory_deleted]));
  }

  /**
   * Delete cache on page/post update.
   */
  public function delete_current_page_cache_on_update( $post_id ) {
    // If this is just a revision, don't clear cache.
    if ( wp_is_post_revision( $post_id ) )
      return;

    if ( get_option( $this->get_cache_clear_on_update_setting() ) != 1 )
      return;

    $permalink = get_permalink( $post_id );
    $path = get_option( $this->get_cache_path_setting() );
    $filesystem = Filesystem_Helper::get_instance();
    $cache_path = $filesystem->get_nginx_cache_path( $path, $permalink );
    $filesystem->delete( $cache_path );
  }

}
