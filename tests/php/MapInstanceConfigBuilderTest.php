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
    ],
    [
        'default_provider' => 'leaflet',
        'default_latitude' => '29.7604',
        'default_longitude' => '-95.3698',
        'default_zoom' => 9,
        'default_map_height' => '480px',
        'tile_url' => 'https://tiles.example.com/{z}/{x}/{y}.png',
        'google_maps_api_key' => '',
        'enable_scroll_wheel' => true,
    ],
    'https://example.com/wp-json/rubicon-maps/v1/locations'
);

assertSameMapConfig('store-map', $config['instanceId'], 'Explicit IDs should be preserved for linked map/list instances.');
assertSameMapConfig('leaflet', $config['provider'], 'Google should fall back to Leaflet when no API key is configured.');
assertSameMapConfig('https://tiles.example.com/{z}/{x}/{y}.png', $config['tileUrl'], 'Configured tile URLs should flow into the frontend map config.');
assertSameMapConfig('retail', $config['category'], 'Category filters should remain in the map config.');
assertSameMapConfig('houston', $config['region'], 'Region filters should remain in the map config.');
assertSameMapConfig('4,9', $config['locationIds'], 'Explicit location IDs should remain in the map config.');
assertSameMapConfig(false, $config['scrollWheelZoom'], 'String off values should disable scroll-wheel zoom.');

$config = $builder->build(
    [
        'id' => '',
        'provider' => 'google',
        'scrollwheel' => '',
    ],
    [
        'default_provider' => 'leaflet',
        'default_latitude' => '33.0000',
        'default_longitude' => '-96.0000',
        'default_zoom' => 11,
        'default_map_height' => '60vh',
        'tile_url' => '',
        'google_maps_api_key' => 'abc123',
        'enable_scroll_wheel' => false,
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
assertSameMapConfig(false, $config['scrollWheelZoom'], 'Global scroll-wheel settings should apply when an instance override is absent.');

$config = $builder->build(
    [
        'provider' => 'bing',
    ],
    [
        'default_provider' => 'leaflet',
        'default_latitude' => '40.0000',
        'default_longitude' => '-75.0000',
        'default_zoom' => 10,
        'default_map_height' => '500px',
        'tile_url' => 'https://tiles.example.com/{z}/{x}/{y}.png',
        'google_maps_api_key' => 'abc123',
        'enable_scroll_wheel' => true,
    ],
    'https://example.com/wp-json/rubicon-maps/v1/locations',
    'generated-provider-id'
);

assertSameMapConfig('leaflet', $config['provider'], 'Unsupported instance providers should fall back to the configured default provider.');

echo 'MapInstanceConfigBuilderTest passed.' . PHP_EOL;
