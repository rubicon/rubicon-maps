<?php

namespace RubiconMaps\PostType;

class Location {
    public static function register() {
        register_post_type('location', [
            'labels' => [
                'name' => __('Locations', 'rubicon-maps'),
                'singular_name' => __('Location', 'rubicon-maps'),
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'locations'],
            'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'show_in_rest' => true,
        ]);
    }
}
