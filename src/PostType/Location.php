<?php
namespace RubiconMaps\PostType;

use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

class Location {
    public static function register() {
        $labels = [
            'name'               => __('Locations', 'rubicon-maps'),
            'singular_name'      => __('Location', 'rubicon-maps'),
            'menu_name'          => __('Locations', 'rubicon-maps'),
            'name_admin_bar'     => __('Location', 'rubicon-maps'),
            'add_new'            => __('Add Location', 'rubicon-maps'),
            'add_new_item'       => __('Add New Location', 'rubicon-maps'),
            'new_item'           => __('New Location', 'rubicon-maps'),
            'edit_item'          => __('Edit Location', 'rubicon-maps'),
            'view_item'          => __('View Location', 'rubicon-maps'),
            'all_items'          => __('All Locations', 'rubicon-maps'),
            'search_items'       => __('Search Locations', 'rubicon-maps'),
            'parent_item_colon'  => __('Parent Locations:', 'rubicon-maps'),
            'not_found'          => __('No locations found.', 'rubicon-maps'),
            'not_found_in_trash' => __('No locations found in Trash.', 'rubicon-maps'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'show_in_menu'       => Plugin::SETTINGS_PAGE_SLUG,
            'menu_icon'          => 'dashicons-location-alt',
            'supports'           => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
            'has_archive'        => false,
            'rewrite'            => ['slug' => 'locations'],
            'show_in_rest'       => true,
            'capability_type'    => 'post',
        ];

        register_post_type(Plugin::POST_TYPE_LOCATION, $args);
    }
}

// Disable Gutenberg for this post type
add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
    if ($post_type === Plugin::POST_TYPE_LOCATION) {
        return false;
    }
    return $use_block_editor;
}, 10, 2);
