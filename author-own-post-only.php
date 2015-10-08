<?php
/*
Plugin Name: Author Own Post Only
Plugin URI: http://wordpress.org/plugins/author-own-post-only/
Description: Users can see own posts only.
Author: haruair
Version: 0.0.1
Author URI: http://haruair.com/
*/

class Haruair_AuthorOwnPostOnly {

  public static function filter_query($wp_query) {
    $is_page_for_posts = false;

    if (get_option('show_on_front')) {
      $page_for_posts = get_page(get_option('page_for_posts'));

      if ($page_for_posts->ID) {
        $is_page_for_posts = $wp_query->get('pagename') === $page_for_posts->post_name;
      }
    }

    if (!is_admin() && ( $is_page_for_posts
                        || $wp_query->is_search()
                        || $wp_query->is_archive()
                        || $wp_query->is_single() ) ) {
      $wp_query->set('author', get_current_user_id()); 
    }
  }

  public static function filter_where ( $where, $in_same_cat = false, $excluded_categories = '' ) {
     $id = (int) get_current_user_id();

     if ($where != '') {
      $where .= " AND ";
     }

     $where .= "p.post_author = {$id}";
     return $where;
  }
}

add_action('pre_get_posts', array('Haruair_AuthorOwnPostOnly', 'filter_query'));
add_filter('get_next_post_where', array('Haruair_AuthorOwnPostOnly', 'filter_where'));
add_filter('get_previous_post_where', array('Haruair_AuthorOwnPostOnly', 'filter_where'));
