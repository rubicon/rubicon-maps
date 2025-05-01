<?php
namespace RubiconMaps\Rest;

if (!defined('ABSPATH')) {
    exit;
}

class LocationsEndpoint {

    public static function register_routes() {
        \register_rest_route('rubicon-maps/v1', '/locations', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_locations'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function get_locations($request) {
        $region_filter = $request->get_param('region');

        $args = [
            'post_type'      => 'rubicon_maps_location',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'tax_query'      => [],
        ];

        if ($region_filter) {
            $args['tax_query'][] = [
                'taxonomy' => 'rubicon_maps_region',
                'field'    => 'slug',
                'terms'    => $region_filter,
            ];
        }

        $query = new \WP_Query($args);
        $locations = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();

                $regions = get_the_terms($post_id, 'rubicon_maps_region');
                $region_data = [];
                if (!is_wp_error($regions) && !empty($regions)) {
                    foreach ($regions as $region) {
                        $region_data[] = [
                            'id'   => $region->term_id,
                            'name' => $region->name,
                            'slug' => $region->slug,
                        ];
                    }
                }

                $locations[] = [
                    'id'         => $post_id,
                    'title'      => get_the_title(),
                    'address'    => get_post_meta($post_id, 'address', true),
                    'latitude'   => get_post_meta($post_id, 'latitude', true),
                    'longitude'  => get_post_meta($post_id, 'longitude', true),
                    'permalink'  => get_permalink(),
                    'region'     => $region_data,
                ];
            }
            wp_reset_postdata();
        }

        return rest_ensure_response($locations);
    }
}
