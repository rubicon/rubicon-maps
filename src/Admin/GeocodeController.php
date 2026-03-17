<?php

namespace RubiconMaps\Admin;

use function add_action;
use function current_user_can;
use function get_bloginfo;
use function is_wp_error;
use function sanitize_text_field;
use function wp_remote_get;
use function wp_remote_retrieve_body;
use function wp_send_json_error;
use function wp_send_json_success;
use function wp_verify_nonce;

if (!defined('ABSPATH')) {
    exit;
}

final class GeocodeController
{
    public const NONCE_ACTION = 'rubicon_maps_geocode';

    public static function init(): void
    {
        add_action('wp_ajax_rubicon_maps_geocode', [self::class, 'handle']);
    }

    public static function handle(): void
    {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('You are not allowed to geocode locations.', 'rubicon-maps')], 403);
        }

        $nonce = sanitize_text_field((string) ($_POST['nonce'] ?? ''));
        if (!wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            wp_send_json_error(['message' => __('Your geocoding session expired. Refresh the page and try again.', 'rubicon-maps')], 403);
        }

        $query = trim(sanitize_text_field((string) ($_POST['query'] ?? '')));
        if (strlen($query) < 3) {
            wp_send_json_error(['message' => __('Enter at least three characters before geocoding.', 'rubicon-maps')], 400);
        }

        $response = wp_remote_get(
            'https://nominatim.openstreetmap.org/search?' . http_build_query(
                [
                    'format' => 'jsonv2',
                    'addressdetails' => 1,
                    'limit' => 1,
                    'q' => $query,
                ]
            ),
            [
                'timeout' => 10,
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => sprintf('%s Rubicon Maps/%s', get_bloginfo('name'), defined('RUBICON_MAPS_VERSION') ? RUBICON_MAPS_VERSION : 'dev'),
                ],
            ]
        );

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => $response->get_error_message()], 500);
        }

        $body = wp_remote_retrieve_body($response);
        $results = json_decode((string) $body, true);

        if (!is_array($results) || [] === $results) {
            wp_send_json_error(['message' => __('No matching address was found.', 'rubicon-maps')], 404);
        }

        $match = $results[0];
        $address = is_array($match['address'] ?? null) ? $match['address'] : [];

        wp_send_json_success(
            [
                'latitude' => sanitize_text_field((string) ($match['lat'] ?? '')),
                'longitude' => sanitize_text_field((string) ($match['lon'] ?? '')),
                'street' => sanitize_text_field(trim((string) (($address['house_number'] ?? '') . ' ' . ($address['road'] ?? '')))),
                'city' => sanitize_text_field((string) ($address['city'] ?? $address['town'] ?? $address['village'] ?? '')),
                'state' => sanitize_text_field((string) ($address['state'] ?? '')),
                'zip' => sanitize_text_field((string) ($address['postcode'] ?? '')),
                'country' => sanitize_text_field((string) ($address['country'] ?? '')),
                'display_name' => sanitize_text_field((string) ($match['display_name'] ?? '')),
            ]
        );
    }
}
