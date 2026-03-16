<?php
namespace RubiconMaps\Rest;

use RubiconMaps\Frontend\LocationRepository;
use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

class LocationsEndpoint {

    public static function register_routes() {
        \register_rest_route(Plugin::REST_NAMESPACE, '/locations', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_locations'],
            'permission_callback' => '__return_true',
            'args' => [
                'category' => ['sanitize_callback' => 'sanitize_text_field'],
                'region' => ['sanitize_callback' => 'sanitize_text_field'],
                'location_ids' => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ]);
    }

    public static function get_locations($request) {
        $repository = new LocationRepository();

        return rest_ensure_response(
            $repository->getLocations([
                'category' => $request->get_param('category'),
                'region' => $request->get_param('region'),
                'location_ids' => $request->get_param('location_ids'),
                'posts_per_page' => -1,
            ])
        );
    }
}
