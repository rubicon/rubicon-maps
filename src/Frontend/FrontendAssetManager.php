<?php

namespace RubiconMaps\Frontend;

use RubiconMaps\Helpers\SettingsHelper;
use RubiconMaps\Support\Plugin;

use function plugins_url;
use function rest_url;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_localize_script;
use function wp_register_script;
use function wp_register_style;

if (!defined('ABSPATH')) {
    exit;
}

final class FrontendAssetManager
{
    private static bool $frontendAssetsEnqueued = false;
    private static bool $leafletAssetsEnqueued = false;
    private static bool $googleAssetsEnqueued = false;
    private static bool $leafletClusterAssetsEnqueued = false;

    public static function enqueueMap(string $provider = 'leaflet'): void
    {
        self::enqueueSharedFrontendAssets();
        self::enqueueProviderAssets($provider);
    }

    public static function enqueueList(): void
    {
        self::enqueueSharedFrontendAssets();
    }

    private static function enqueueSharedFrontendAssets(): void
    {
        if (self::$frontendAssetsEnqueued) {
            return;
        }

        wp_register_style(
            'rtv-rm-frontend',
            plugins_url('/assets/css/rubicon-maps-frontend.css', RTV_RM_PLUGIN_FILE),
            [],
            Plugin::version()
        );

        wp_register_script(
            'rtv-rm-frontend',
            plugins_url('/assets/js/rubicon-maps-frontend.js', RTV_RM_PLUGIN_FILE),
            [],
            Plugin::version(),
            true
        );

        wp_localize_script(
            'rtv-rm-frontend',
            'rubiconMapsConfig',
            [
                'restBase' => rest_url(Plugin::REST_NAMESPACE . '/locations'),
                'defaultProvider' => SettingsHelper::get_option('default_provider', 'leaflet'),
                'leafletMarkerShadow' => plugins_url('/assets/leaflet/marker-shadow.png', RTV_RM_PLUGIN_FILE),
            ]
        );

        wp_enqueue_style('rtv-rm-frontend');
        wp_enqueue_script('rtv-rm-frontend');

        self::$frontendAssetsEnqueued = true;
    }

    private static function enqueueProviderAssets(string $provider): void
    {
        if ('google' === $provider && !self::$googleAssetsEnqueued) {
            $apiKey = SettingsHelper::get_option('google_maps_api_key', '');

            if ('' !== $apiKey) {
                wp_enqueue_script(
                    'rtv-rm-google-maps',
                    'https://maps.googleapis.com/maps/api/js?key=' . rawurlencode($apiKey),
                    [],
                    null,
                    true
                );

                self::$googleAssetsEnqueued = true;
                return;
            }
        }

        if (!self::$leafletAssetsEnqueued) {
            wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');
            wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true);
            self::$leafletAssetsEnqueued = true;
        }

        if (!self::$leafletClusterAssetsEnqueued) {
            wp_enqueue_style('leaflet-markercluster-css', 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css', ['leaflet-css'], '1.5.3');
            wp_enqueue_style('leaflet-markercluster-default-css', 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css', ['leaflet-markercluster-css'], '1.5.3');
            wp_enqueue_script('leaflet-markercluster-js', 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js', ['leaflet-js'], '1.5.3', true);
            self::$leafletClusterAssetsEnqueued = true;
        }
    }
}
