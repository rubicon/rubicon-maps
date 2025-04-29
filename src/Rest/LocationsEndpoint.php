<?php
namespace RubiconMaps\Rest;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LocationsEndpoint {

    public static function register_routes() {
        \register_rest_route('rubicon-maps/v1', '/locations', [
            'methods' => 'GET',
            'callback' => [self::class, 'get_locations'],
            'permission_callback' => '__return_true'
        ]);
    }

    public static function get_locations($request) {
        $query = new \WP_Query([
            'post_type' => 'location',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ]);

        $locations = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $locations[] = [
                    'id' => \get_the_ID(),
                    'title' => \get_the_title(),
                    'address' => \get_post_meta(get_the_ID(), 'address', true),
                    'latitude' => \get_post_meta(get_the_ID(), 'latitude', true),
                    'longitude' => \get_post_meta(get_the_ID(), 'longitude', true),
                    'permalink' => \get_permalink()
                ];
            }
            \wp_reset_postdata();
        }

        return \rest_ensure_response($locations);
    }
}
