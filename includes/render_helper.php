<?php

class CSNX_Render_Helper {

  use CSNX_Common_Utils;

  const PLUGIN_NAME = 'Nginx Cache Sniper';
  const CLEAR_ENTIRE_CACHE = 'Clear entire cache';
  const ENTIRE_CACHE_CLEARED = 'Entire cache cleared';
  const CLEAR_PAGE_CACHE = 'Clear cache for this page';
  const PAGE_CACHE_CLEARED = 'Cache cleared';
 
  /**
   * CSNX_Render_Helper $render
   */ 
  private static $render = null;
 
  /**
   * Get instance.
   * @return CSNX_Render_Helper $render
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
    $filesystem = CSNX_Filesystem_Helper::get_instance();
    $cache_zone_path = get_option( $this->get_cache_path_setting() );
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
    $filesystem = CSNX_Filesystem_Helper::get_instance();
    $cache_path = $filesystem->get_nginx_cache_path( get_option( $this->get_cache_path_setting() ), '' );
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
    $cache_path_setting = $this->get_cache_path_setting();
    $cache_path_value = esc_attr( get_option( $cache_path_setting ));
    
    $cache_clear_on_update_setting = $this->get_cache_clear_on_update_setting();
    $cache_clear_on_update_checked_attr = checked( get_option( $cache_clear_on_update_setting ), 1, false);
    
    $cache_clear_on_comments_setting = $this->get_cache_clear_on_comments_setting();
    $cache_clear_on_comments_checked_attr = checked( get_option( $cache_clear_on_comments_setting ), 1, false);
    
    $render_plugin_name = self::PLUGIN_NAME;
    
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

            <tr>
              <th>
                Clear Cache
              </th>
              <td>
	        <label for="$cache_clear_on_update_setting">
		  <input name="$cache_clear_on_update_setting" type="checkbox" id="$cache_clear_on_update_setting" value="1" $cache_clear_on_update_checked_attr />
		  Automatically clear page cache on content update
                </label>
              </td>
            </tr>

            <tr>
              <th></th>
              <td>
	        <label for="$cache_clear_on_comments_setting">
		  <input name="$cache_clear_on_comments_setting" type="checkbox" id="$cache_clear_on_comments_setting" value="1" $cache_clear_on_comments_checked_attr />
		  Automatically clear page cache on comment
                </label>
              </td>
            </tr>

	  </tbody>
        </table>
        <p><i>For more info on using this plugin with a pre-configured Nginx stack running in AWS, follow this <a target='_blank' href='https://aws.amazon.com/marketplace/pp/B0771QTMR5'>link</a>.</i></p>
EOT;
    submit_button();
    echo '</form></div>'; 
  }
}

