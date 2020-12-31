<?php

/**
 * rest api embed categories to post
 *
 * @author              Grygorii Shevchenko <shevchenko.grygorii@gmail.com>
 *
 * @wordpress-plugin
 * Plugin Name:         Embed category object to post(rest-api)
 * Description:         Embeds category object to post(rest-api)
 * Version:             1.0.0
 * Author:              Grygorii Shevchenko

 */


add_action('init', 'rest_api_embed_categories_to_post', 12);

function rest_api_embed_categories_to_post()
{

  $post_types = get_post_types(array('public' => true), 'objects');

  foreach ($post_types as $post_type) {

    $post_type_name     = $post_type->name;
    $show_in_rest       = (isset($post_type->show_in_rest) && $post_type->show_in_rest) ? true : false;

    // Only proceed if the post type is set to be accessible over the REST API
    // and supports featured images.
    if ($show_in_rest) {

      // Compatibility with the REST API v2 beta 9+
      if (function_exists('register_rest_field')) {
        register_rest_field(
          $post_type_name,
          'categories',
          array(
            'get_callback' => 'rest_api_categories_get_field',
            'schema'       => null,
          )
        );
      } elseif (function_exists('register_api_field')) {
        register_api_field(
          $post_type_name,
          'categories',
          array(
            'get_callback' => 'rest_api_categories_get_field',
            'schema'       => null,
          )
        );
      }
    }
  }
}

function rest_api_categories_get_field($object, $field_nameby_namet)
{

  $category_ids = $object['categories'];
  $category_names = array();

  foreach ($category_ids as $category_id) {
    $category_name = get_category($category_id);
    $category_names[] = $category_name;
  }

  return apply_filters('categories_by_name', $category_names, $object['id']);
}
