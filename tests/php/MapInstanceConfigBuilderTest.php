<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Frontend\MapInstanceConfigBuilder;

function assertSameMapConfig(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true) . PHP_EOL
        );
        exit(1);
    }
}

$builder = new MapInstanceConfigBuilder();

$config = $builder->build(
    [
        'id' => 'store-map',
        'provider' => 'google',
        'category' => 'retail',
        'region' => 'houston',
        'location_ids' => '4,9',
        'scrollwheel' => 'off',
        'viewport_mode' => 'manual',
        'tile_preset' => 'carto_light',
        'popup_trigger' => 'hover',
        'cluster_radius' => '125',
        'enable_clustering' => 'on',
    ],
    [
        'default_provider' => 'leaflet',
        'default_viewport_mode' => 'auto_fit',
        'default_latitude' => '29.7604',
        'default_longitude' => '-95.3698',
        'default_zoom' => 9,
        'default_map_height' => '480px',
        'default_auto_fit_padding' => 24,
        'default_tile_preset' => 'openstreetmap',
        'tile_url' => 'https://tiles.example.com/{z}/{x}/{y}.png',
        'google_maps_api_key' => '',
        'default_enable_zoom_control' => true,
        'enable_scroll_wheel' => true,
        'default_enable_double_click_zoom' => true,
        'default_popup_trigger' => 'click',
        'default_popup_max_width' => 320,
        'default_close_on_map_click' => true,
        'default_auto_close_popup' => true,
        'default_open_all_popups' => false,
        'default_enable_clustering' => true,
        'default_cluster_radius' => 100,
    ],
    'https://example.com/wp-json/rubicon-maps/v1/locations'
);

assertSameMapConfig('store-map', $config['instanceId'], 'Explicit IDs should be preserved for linked map/list instances.');
assertSameMapConfig('leaflet', $config['provider'], 'Google should fall back to Leaflet when no API key is configured.');
assertSameMapConfig('manual', $config['viewportMode'], 'Explicit viewport mode should be preserved.');
assertSameMapConfig('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', $config['tileUrl'], 'Configured tile presets should map to canonical tile URLs.');
assertSameMapConfig('retail', $config['category'], 'Category filters should remain in the map config.');
assertSameMapConfig('houston', $config['region'], 'Region filters should remain in the map config.');
assertSameMapConfig('4,9', $config['locationIds'], 'Explicit location IDs should remain in the map config.');
assertSameMapConfig(false, $config['scrollWheelZoom'], 'String off values should disable scroll-wheel zoom.');
assertSameMapConfig('hover', $config['popupTrigger'], 'Popup trigger overrides should flow into the frontend config.');
assertSameMapConfig(true, $config['enableClustering'], 'Clustering should honor explicit on values.');
assertSameMapConfig(125, $config['clusterRadius'], 'Cluster radius overrides should flow into the frontend config.');

$config = $builder->build(
    [
        'id' => '',
        'provider' => 'google',
        'scrollwheel' => '',
    ],
    [
        'default_provider' => 'leaflet',
        'default_viewport_mode' => 'auto_fit',
        'default_latitude' => '33.0000',
        'default_longitude' => '-96.0000',
        'default_zoom' => 11,
        'default_map_height' => '60vh',
        'default_auto_fit_padding' => 32,
        'default_tile_preset' => 'custom',
        'tile_url' => '',
        'google_maps_api_key' => 'abc123',
        'default_enable_zoom_control' => true,
        'enable_scroll_wheel' => false,
        'default_enable_double_click_zoom' => true,
        'default_popup_trigger' => 'click',
        'default_popup_max_width' => 320,
        'default_close_on_map_click' => true,
        'default_auto_close_popup' => true,
        'default_open_all_popups' => false,
        'default_enable_clustering' => true,
        'default_cluster_radius' => 100,
    ],
    'https://example.com/wp-json/rubicon-maps/v1/locations',
    'generated-map-id'
);

assertSameMapConfig('generated-map-id', $config['instanceId'], 'Generated IDs should be accepted when the renderer provides them.');
assertSameMapConfig('google', $config['provider'], 'Google should remain active when an API key is configured.');
assertSameMapConfig('33.0000', $config['lat'], 'Default latitude should be used when an instance override is absent.');
assertSameMapConfig('-96.0000', $config['lng'], 'Default longitude should be used when an instance override is absent.');
assertSameMapConfig(11, $config['zoom'], 'Default zoom should be used when an instance override is absent.');
assertSameMapConfig('60vh', $config['height'], 'Default height should be used when an instance override is absent.');
assertSameMapConfig('auto_fit', $config['viewportMode'], 'Default viewport mode should be used when an instance override is absent.');
assertSameMapConfig(32, $config['autoFitPadding'], 'Default auto-fit padding should be used when an instance override is absent.');
assertSameMapConfig(false, $config['scrollWheelZoom'], 'Global scroll-wheel settings should apply when an instance override is absent.');
assertSameMapConfig('click', $config['popupTrigger'], 'Default popup trigger should be used when an instance override is absent.');
assertSameMapConfig(100, $config['clusterRadius'], 'Default cluster radius should be used when an instance override is absent.');

$config = $builder->build(
    [
        'provider' => 'bing',
    ],
    [
        'default_provider' => 'leaflet',
        'default_viewport_mode' => 'auto_fit',
        'default_latitude' => '40.0000',
        'default_longitude' => '-75.0000',
        'default_zoom' => 10,
        'default_map_height' => '500px',
        'default_auto_fit_padding' => 24,
        'default_tile_preset' => 'openstreetmap',
        'tile_url' => 'https://tiles.example.com/{z}/{x}/{y}.png',
        'google_maps_api_key' => 'abc123',
        'default_enable_zoom_control' => true,
        'enable_scroll_wheel' => true,
        'default_enable_double_click_zoom' => true,
        'default_popup_trigger' => 'click',
        'default_popup_max_width' => 320,
        'default_close_on_map_click' => true,
        'default_auto_close_popup' => true,
        'default_open_all_popups' => false,
        'default_enable_clustering' => true,
        'default_cluster_radius' => 100,
    ],
    'https://example.com/wp-json/rubicon-maps/v1/locations',
    'generated-provider-id'
);

assertSameMapConfig('leaflet', $config['provider'], 'Unsupported instance providers should fall back to the configured default provider.');

$config = $builder->build(
    [
        'provider' => 'default',
        'viewport_mode' => 'default',
        'tile_preset' => 'default',
        'zoom_control' => 'default',
        'scrollwheel' => 'default',
        'double_click_zoom' => 'default',
        'popup_trigger' => 'default',
        'close_on_map_click' => 'default',
        'auto_close_popup' => 'default',
        'open_all_popups' => 'default',
        'enable_clustering' => 'default',
        'zoom' => '',
        'height' => '',
        'auto_fit_padding' => '',
        'popup_max_width' => '',
        'cluster_radius' => '',
    ],
    [
        'default_provider' => 'leaflet',
        'default_viewport_mode' => 'manual',
        'default_latitude' => '31.0000',
        'default_longitude' => '-97.0000',
        'default_zoom' => 7,
        'default_map_height' => '55vh',
        'default_auto_fit_padding' => 18,
        'default_tile_preset' => 'carto_dark',
        'tile_url' => 'https://tiles.example.com/{z}/{x}/{y}.png',
        'google_maps_api_key' => '',
        'default_enable_zoom_control' => false,
        'enable_scroll_wheel' => false,
        'default_enable_double_click_zoom' => false,
        'default_popup_trigger' => 'hover',
        'default_popup_max_width' => 420,
        'default_close_on_map_click' => false,
        'default_auto_close_popup' => false,
        'default_open_all_popups' => true,
        'default_enable_clustering' => false,
        'default_cluster_radius' => 160,
    ],
    'https://example.com/wp-json/rubicon-maps/v1/locations'
);

assertSameMapConfig('leaflet', $config['provider'], 'Default provider sentinel values should use the plugin default provider.');
assertSameMapConfig('manual', $config['viewportMode'], 'Default viewport sentinel values should use the plugin default viewport mode.');
assertSameMapConfig(7, $config['zoom'], 'Blank zoom values should fall back to the plugin default zoom.');
assertSameMapConfig('55vh', $config['height'], 'Blank height values should fall back to the plugin default map height.');
assertSameMapConfig(18, $config['autoFitPadding'], 'Blank auto-fit padding values should fall back to the plugin default padding.');
assertSameMapConfig('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', $config['tileUrl'], 'Default tile preset sentinel values should use the plugin default tile preset.');
assertSameMapConfig(false, $config['zoomControl'], 'Default zoom control sentinel values should use the plugin default toggle.');
assertSameMapConfig(false, $config['scrollWheelZoom'], 'Default scroll-wheel sentinel values should use the plugin default toggle.');
assertSameMapConfig(false, $config['doubleClickZoom'], 'Default double-click sentinel values should use the plugin default toggle.');
assertSameMapConfig('hover', $config['popupTrigger'], 'Default popup trigger sentinel values should use the plugin default popup trigger.');
assertSameMapConfig(420, $config['popupMaxWidth'], 'Blank popup width values should fall back to the plugin default width.');
assertSameMapConfig(false, $config['closeOnMapClick'], 'Default close-on-map-click sentinel values should use the plugin default toggle.');
assertSameMapConfig(false, $config['autoClosePopup'], 'Default auto-close sentinel values should use the plugin default toggle.');
assertSameMapConfig(true, $config['openAllPopups'], 'Default open-all sentinel values should use the plugin default toggle.');
assertSameMapConfig(false, $config['enableClustering'], 'Default clustering sentinel values should use the plugin default toggle.');
assertSameMapConfig(160, $config['clusterRadius'], 'Blank cluster radius values should fall back to the plugin default radius.');

$config = $builder->build(
    [
        'category' => '["retail","wholesale","retail"]',
        'region' => '["texas","houston","texas"]',
        'location_ids' => '[4,9,4]',
    ],
    [
        'default_provider' => 'leaflet',
        'default_viewport_mode' => 'auto_fit',
        'default_latitude' => '29.7604',
        'default_longitude' => '-95.3698',
        'default_zoom' => 9,
        'default_map_height' => '480px',
        'default_auto_fit_padding' => 24,
        'default_tile_preset' => 'openstreetmap',
        'tile_url' => 'https://tiles.example.com/{z}/{x}/{y}.png',
        'google_maps_api_key' => '',
        'default_enable_zoom_control' => true,
        'enable_scroll_wheel' => false,
        'default_enable_double_click_zoom' => true,
        'default_popup_trigger' => 'click',
        'default_popup_max_width' => 320,
        'default_close_on_map_click' => true,
        'default_auto_close_popup' => true,
        'default_open_all_popups' => false,
        'default_enable_clustering' => true,
        'default_cluster_radius' => 100,
    ],
    'https://example.com/wp-json/rubicon-maps/v1/locations'
);

assertSameMapConfig('retail,wholesale', $config['category'], 'Structured category selections should be normalized for the frontend runtime.');
assertSameMapConfig('texas,houston', $config['region'], 'Structured region selections should be normalized for the frontend runtime.');
assertSameMapConfig('4,9', $config['locationIds'], 'Structured location selections should be normalized for the frontend runtime.');

echo 'MapInstanceConfigBuilderTest passed.' . PHP_EOL;
