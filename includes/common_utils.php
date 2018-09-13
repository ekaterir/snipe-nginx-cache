<?php

trait CSNX_Common_Utils {

  private $cache_clear_on_comments_setting = 'nginx_cache_sniper_auto_clear_comments';
  private $home_page_cache_clear_on_comments_setting = 'nginx_cache_sniper_auto_clear_home_page_comments';
  private $cache_path_setting = 'nginx_cache_sniper_path';
  private $cache_levels = 'nginx_cache_sniper_levels';
  private $plugin_name = 'cache-sniper-nginx';
  private $cache_clear_on_update_setting = 'nginx_cache_sniper_auto_clear';
  private $home_page_cache_clear_on_update_setting = 'nginx_cache_sniper_auto_clear_home_page';
  private $plugin_url = 'snipe-nginx-cache';

  public function get_cache_clear_on_comments_setting() {
    return $this->cache_clear_on_comments_setting;
  }

  public function get_home_page_cache_clear_on_update_setting() {
    return $this->home_page_cache_clear_on_update_setting;
  }

  public function get_home_page_cache_clear_on_comments_setting() {
    return $this->home_page_cache_clear_on_comments_setting;
  }

  public function get_cache_path_setting() {
    return $this->cache_path_setting;
  }

  public function get_cache_levels_setting() {
    return $this->cache_levels;
  }

  public function get_plugin_name() {
    return $this->plugin_name;
  }

  public function get_plugin_url() {
    return $this->plugin_url;
  }

  public function get_cache_clear_on_update_setting() {
    return $this->cache_clear_on_update_setting;
  }

  public function get_option_cache_levels() {
    return get_option( $this->get_cache_levels_setting(), '1:2' );
  }

  public function get_option_cache_path() {
    return get_option( $this->get_cache_path_setting(), '/var/lib/nginx/cache' );
  }
}
