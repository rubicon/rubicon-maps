<?php
// SPDX-License-Identifier: GPL-2.0-or-later

namespace RubiconMaps\Taxonomy;

use RubiconMaps\Support\Plugin;

class LocationCategory {
    public static function register() {
        register_taxonomy(Plugin::TAXONOMY_CATEGORY, Plugin::POST_TYPE_LOCATION, [
            'labels' => [
                'name' => __('Location Categories', 'rubicon-maps'),
                'singular_name' => __('Location Category', 'rubicon-maps'),
            ],
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'location-category'],
            'show_in_rest' => true,
        ]);
    }
}
