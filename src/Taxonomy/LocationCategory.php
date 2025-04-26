<?php

namespace RubiconMaps\Taxonomy;

class LocationCategory {
    public static function register() {
        register_taxonomy('location_category', 'location', [
            'labels' => [
                'name' => __('Location Categories', 'rubicon-maps'),
                'singular_name' => __('Location Category', 'rubicon-maps'),
            ],
            'hierarchical' => true,
            'show_in_rest' => true,
        ]);
    }
}
