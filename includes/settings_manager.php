<?php

new Settings_Manager( 'FastCGI Cache Page Purge', 
		 'Cache Purge',
		 'manage_options', //capabilities
		 'fastcgi-cache'
);

/**
 * Create the tools page and register settings.
 */
class Settings_Manager {

    private $page_title = '';
    private $menu_title = '';
    private $capability = '';
    private $menu_slug = '';

    public function __construct( $page_title, $menu_title, $capability, $menu_slug ) {
		
	$this->page_title = $page_title;
	$this->menu_title = $menu_title;
	$this->capability = $capability;
	$this->menu_slug = $menu_slug;

	add_action( 'admin_init', [ $this, 'register_fastcgi_settings' ] );
	add_action( 'admin_menu', [ $this, 'create_tools_page' ] );
    }

    public function register_fastcgi_settings() {
        register_setting( 'fastcgi-cache', 'fastcgi_cache_path', 'sanitize_text_field' );
    }

    /**
     * Create tools page
     */ 
    public function create_tools_page() {
	add_management_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, [ $this, 'build_form' ] );
    }

    /**
     * Give the page basic outline and print the form
     */
    public function build_form() {
	echo '<div class="wrap">';
	echo '<h2>' . $this->page_title . '</h2>';
	try {
	    $this->form();
	} catch (Exception $e) {
	    echo $e->getMessage();
	}
	echo '</div>';
    }

    /**
     * Print the form
     */
    public function form() {
        $cache_path_value = esc_attr( get_option( 'fastcgi_cache_path' ));
	echo '<form class="form-table" method="post" action="options.php">';
        settings_fields( 'fastcgi-cache' );
        echo <<<EOT
	    <table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					Cache Zone Path
				</th>
				<td>
					<input type="text" class="regular-text code" name="fastcgi_cache_path" placeholder="/data/nginx/cache" value="$cache_path_value" />
					<p class="description">The absolute path to the location of the cache zone, specified in the Nginx <code>fastcgi_cache_path</code>.</p>
				</td>
			</tr>
 		</tbody>
	</table>
EOT;
	submit_button();
	echo '</form>';
    }
}

