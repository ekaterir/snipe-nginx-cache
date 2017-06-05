<?php
/**
 * Register plugin's settings.
 */
function register_nginx_cache_sniper_settings() {
  register_setting( 'nginx-cache-sniper', 'nginx_cache_sniper_path', 'sanitize_text_field' );
}
add_action( 'admin_init', 'register_nginx_cache_sniper_settings' );

/**
 * Add tools page.
 */
function create_tools_page() {
  add_management_page( 'Nginx Cache Sniper', 'Nginx Cache Sniper', 'manage_options', 'nx-cache-sniper', 'build_form' );
}
add_action( 'admin_menu', 'create_tools_page' );

/**
 * Build the form on the tools page.
 */
function build_form() {
  $cache_path_value = esc_attr( get_option( 'nginx_cache_sniper_path' ));
  echo '<form class="form-table" method="post" action="options.php">';
  settings_fields( 'nginx-cache-sniper' );
  echo <<<EOT
  <div class="wrap">
    <h2>Nginx Cache Sniper</h2>
      <table class="form-table">
	<tbody>
	  <tr>
	    <th scope="row">
	      Cache Path
	    </th>
	    <td>
	      <input type="text" class="regular-text code" name="nginx_cache_sniper_path" placeholder="/data/nginx/cache" value="$cache_path_value" />
	      <p class="description">The absolute path to the location of the cache zone, specified in the Nginx <code>fastcgi_cache_path</code>.</p>
	    </td>
	  </tr>
	</tbody>
      </table>
EOT;
  submit_button();
  echo '</form></div>';
}

