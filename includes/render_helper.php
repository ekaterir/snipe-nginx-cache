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

  /**
   * Render admin bar.
   */
  public function admin_bar() {
    global $wp_admin_bar;
    $title = '';
    $id = '';
    $filesystem = Filesystem_Helper::get_instance();
    $cache_path = $filesystem->get_nginx_cache_path( get_option( 'nginx_cache_sniper_path' ), '' );
    if ( $filesystem->is_valid_path( $cache_path ) ) {
      $title = self::CLEAR_ENTIRE_CACHE;
      $id = 'delete_entire_cache';
    } else {
      $title = self::ENTIRE_CACHE_CLEARED;
      $id = 'no_cache';
    }

    $wp_admin_bar->add_node([
      'id'    => 'fastcgi_cache',
      'title' => self::PLUGIN_NAME
    ]);

    $wp_admin_bar->add_menu([
      'id'    => $id,
      'title' => $title,
      'parent'=> 'fastcgi_cache'
    ]);
  }

  /**
   * Print settings form.
   */
  public function settings_form() {
    $cache_path_setting = 'nginx_cache_sniper_path';
    $cache_clear_on_update_setting = 'nginx_cache_sniper_auto_clear';
    $cache_path_value = esc_attr( get_option( $cache_path_setting ));
    $cache_clear_on_update_checked_attr = checked( get_option( $cache_clear_on_update_setting ), 1, false);
    $render_plugin_name = self::PLUGIN_NAME; 
    echo '<form class="form-table" method="post" action="options.php">';
    settings_fields( 'nginx-cache-sniper' );
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
            <tr>
              <th scope="row">
                Clear Cache
              </th>
              <td>
	        <label for="$cache_clear_on_update_setting">
		  <input name="$cache_clear_on_update_setting" type="checkbox" id="$cache_clear_on_update_setting" value="1" $cache_clear_on_update_checked_attr />
		  Automatically clear cache on content update
                </label>
              </td>
            </tr>
	  </tbody>
        </table>
        <p><i>For more info on using this plugin with a pre-configured Nginx stack running in AWS, follow this <a target='_blank' href='https://www.thorntech.com/products/wpcloudstack'>link</a>.</i></p>
EOT;
    submit_button();
    echo '</form></div>'; 
  }
}

