<?php
namespace RubiconMaps\Taxonomy;

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
            'edit_item'         => __('Edit Region', 'rubicon-maps'),
            'update_item'       => __('Update Region', 'rubicon-maps'),
            'add_new_item'      => __('Add New Region', 'rubicon-maps'),
            'new_item_name'     => __('New Region Name', 'rubicon-maps'),
            'menu_name'         => __('Regions', 'rubicon-maps'),
        ];

        register_taxonomy('rubicon_maps_region', ['rubicon_maps_location'], [
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'regions'],
            'show_in_rest'      => true,
        ]);
    }
}
