=== Cache Sniper for Nginx ===
Contributors: ekaterir, robertchen617, djrusk
Tags: cache, caching, invalidation, nginx, aws, amazon web services, apache, nginx, purge, flush, server, fastcgi, php, fpm, php-fpm, snipe, individual, page
Requires at least: 4.6
Tested up to: 4.7
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Purge the Nginx FastCGI cache manually, or automatically when content is updated. Supports purging of individual pages. 

== Description ==

Nginx has a built-in FastCGI cache, which acts as a reverse proxy cache at the webserver layer. The Cache Sniper for Nginx WordPress plugin lets you purge this cache from within WordPress. Here are some key features:

* Purge the entire cache from the menu bar
* Manually purge individual pages
* Configure pages to purge automatically when updated
* Configure Settings via WP CLI

For more info on using this plugin with a pre-configured Nginx stack running in AWS, follow this [link](https://www.thorntech.com/products/wpcloudstack).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/cache-sniper-nginx` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the **Plugins** screen in WordPress
1. Go to **Tools** -> **Nginx Cache Sniper** to configure the plugin

**Usage**

1. Clear the entire cache by going to the **Nginx Cache Sniper** drop-down in the top **Menu bar**, and click **Clear entire cache**. 
1. To clear the cache for an individual Post (or Page), click on **Clear cache for this page**, either on the **All Posts** or Post detail pages.
1. When configured to do so, you can also purge the cache for a Post (or Page) by updating it.

**Configuration Settings**

Cache Sniper for Nginx comes with two settings:

1. **Cache Path**: This is the filesystem path where the FastCGI cache is stored on-disk. Set this to the value you used for `fastcgi_cache_path` from your Nginx configuration. **Note**: Nginx needs read/write access to this location.
1. **Automatically clear cache on content update**: Check this box to automatically purge the cache when updated. This only purges the updated page -- it does not clear the entire cache.

**Configuration via WP CLI**

For those scripting out infrastructure, Cache Sniper for Nginx can be configured via WP CLI:

1. `wp plugin activate cache-sniper-nginx`
1. `wp option add nginx_cache_sniper_path '/var/lib/nginx/cache'`
1. `wp option add nginx_cache_sniper_auto_clear 1`

For instructions on setting up FastCGI caching with Nginx, refer to this [Digital Ocean blog post](https://www.digitalocean.com/community/tutorials/how-to-setup-fastcgi-caching-with-nginx-on-your-vps). 

**Server-side configuration**

There are a few things that need to be configured on the server in order for this plugin to work.

1. Be sure to set `levels=1:2` for `fastcgi_cache_path`. For example: `fastcgi_cache_path /var/lib/nginx/cache levels=1:2 keys_zone=CACHE:100m;`
1. The Linux account running Nginx needs read-write permissions to the cache path on disk.

== Frequently Asked Questions ==

= Do I need to recompile Nginx to purge the cache? =

No. Cache Sniper for Nginx works without relying on any custom Nginx modules. This means you can easily keep Nginx updated with your normal package manager. 

== Screenshots ==

1. cache-sniper-screenshot.png

== Changelog ==

= 1.0.0 =
* First version, hope you like it!

== Upgrade Notice ==

= 1.0 =
