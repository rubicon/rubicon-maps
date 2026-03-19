<?php
namespace RubiconMaps\Taxonomy;

use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

class Region {
    public static function register() {
        $labels = [
            'name'              => __('Regions', 'rubicon-maps'),
            'singular_name'     => __('Region', 'rubicon-maps'),
            'search_items'      => __('Search Regions', 'rubicon-maps'),
            'all_items'         => __('All Regions', 'rubicon-maps'),
            'parent_item'       => __('Parent Region', 'rubicon-maps'),
            'parent_item_colon' => __('Parent Region:', 'rubicon-maps'),
            'edit_item'         => __('Edit Region', 'rubicon-maps'),
            'update_item'       => __('Update Region', 'rubicon-maps'),
            'add_new_item'      => __('Add New Region', 'rubicon-maps'),
            'new_item_name'     => __('New Region Name', 'rubicon-maps'),
            'menu_name'         => __('Regions', 'rubicon-maps'),
        ];

        register_taxonomy(Plugin::TAXONOMY_REGION, [Plugin::POST_TYPE_LOCATION], [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'regions'],
            'show_in_rest'      => true,
        ]);
    }
}
