<?php

require_once plugin_dir_path( __FILE__ ) . 'common_utils.php';

/**
 * Handle clearing cache when updating/creating/deleting a comment.
 */
class Cache_Sniper_Nginx_Comments {

  use CSNX_Common_Utils;

  public function __construct() {
    require_once plugin_dir_path( __FILE__ ) . 'filesystem_helper.php'; 
    add_action( 'comment_post', [ $this, 'csnx_delete_current_page_cache_on_comments' ] );
    add_action( 'edit_comment', [ $this, 'csnx_delete_current_page_cache_on_comments' ] );
    add_action( 'delete_comment', [ $this, 'csnx_delete_current_page_cache_on_comments' ] );
    add_action( 'trackback_post', [ $this, 'csnx_delete_current_page_cache_on_comments' ] );
    add_action( 'pingback_post', [ $this, 'csnx_delete_current_page_cache_on_comments' ] );
    add_action( 'wp_set_comment_status', [ $this, 'csnx_delete_current_page_cache_on_comments' ] );
  } 

  /**
   * Delete cache when comments are created/updated/deleted
   */
  public function csnx_delete_current_page_cache_on_comments( $comment ) {
    
    if ( get_option( $this->get_cache_clear_on_comments_setting() ) != 1)
      return false;

    if ( !is_object( $comment ) )
      $comment = get_comment( $comment );

    $post_id = $comment->comment_post_ID;
    
    $permalink = get_permalink( $post_id );
    $path = $this->get_option_cache_path();
    $levels = $this->get_option_cache_levels();
    $filesystem = CSNX_Filesystem_Helper::get_instance();
    $cache_path = $filesystem->get_nginx_cache_path( $path, $permalink, $levels );
    return $filesystem->delete( $cache_path );
  }

}

