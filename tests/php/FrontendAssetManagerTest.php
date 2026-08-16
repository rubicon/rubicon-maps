<?php
// SPDX-License-Identifier: GPL-2.0-or-later

declare(strict_types=1);

define('ABSPATH', __DIR__ . '/../../');
define('RTV_RM_PLUGIN_FILE', __DIR__ . '/../../rubicon-maps.php');

$GLOBALS['rubicon_test_enqueued_scripts'] = [];
$GLOBALS['rubicon_test_enqueued_styles'] = [];
$GLOBALS['rubicon_test_localized_scripts'] = [];
$GLOBALS['rubicon_test_options'] = [];

function plugins_url(string $path = '', string $plugin = ''): string
{
    return 'https://example.com/wp-content/plugins/rubicon-maps' . $path;
}

function rest_url(string $path = ''): string
{
    return 'https://example.com/wp-json/' . ltrim($path, '/');
}

function wp_register_style(string $handle, string $src, array $deps = [], string $ver = ''): void
{
    $GLOBALS['rubicon_test_registered_styles'][$handle] = compact('src', 'deps', 'ver');
    $GLOBALS['rubicon_test_register_style_calls'][$handle] = ($GLOBALS['rubicon_test_register_style_calls'][$handle] ?? 0) + 1;
}

function wp_register_script(string $handle, string $src, array $deps = [], string $ver = '', bool $inFooter = false): void
{
    $GLOBALS['rubicon_test_registered_scripts'][$handle] = compact('src', 'deps', 'ver', 'inFooter');
    $GLOBALS['rubicon_test_register_script_calls'][$handle] = ($GLOBALS['rubicon_test_register_script_calls'][$handle] ?? 0) + 1;
}

function wp_enqueue_style(string $handle, string $src = '', array $deps = [], string $ver = ''): void
{
    $GLOBALS['rubicon_test_enqueued_styles'][$handle] = compact('src', 'deps', 'ver');
}

function wp_enqueue_script(string $handle, string $src = '', array $deps = [], string|null $ver = '', bool $inFooter = false): void
{
    $GLOBALS['rubicon_test_enqueued_scripts'][$handle] = compact('src', 'deps', 'ver', 'inFooter');
}

function wp_localize_script(string $handle, string $objectName, array $l10n): void
{
    $GLOBALS['rubicon_test_localized_scripts'][$handle][] = compact('objectName', 'l10n');
}

function get_option(string $option, mixed $default = false): mixed
{
    return $GLOBALS['rubicon_test_options'][$option] ?? $default;
}

function __(string $text, string $domain = ''): string
{
    return $text;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Frontend\FrontendAssetManager;
use RubiconMaps\Support\Plugin;

function assertTrueAsset(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

function resetAssetManagerState(): void
{
    $GLOBALS['rubicon_test_enqueued_scripts'] = [];
    $GLOBALS['rubicon_test_enqueued_styles'] = [];
    $GLOBALS['rubicon_test_localized_scripts'] = [];
    $GLOBALS['rubicon_test_registered_scripts'] = [];
    $GLOBALS['rubicon_test_registered_styles'] = [];
    $GLOBALS['rubicon_test_register_style_calls'] = [];
    $GLOBALS['rubicon_test_register_script_calls'] = [];

    $reflection = new ReflectionClass(FrontendAssetManager::class);

    foreach (['frontendAssetsEnqueued', 'leafletAssetsEnqueued', 'googleAssetsEnqueued'] as $propertyName) {
        $property = $reflection->getProperty($propertyName);
        $property->setValue(null, false);
    }
}

$GLOBALS['rubicon_test_options'][Plugin::OPTION_NAME] = [
    'default_provider' => 'leaflet',
    'google_maps_api_key' => '',
];

resetAssetManagerState();
FrontendAssetManager::enqueueList();

assertTrueAsset(isset($GLOBALS['rubicon_test_enqueued_styles']['rtv-rm-frontend']), 'List rendering should enqueue shared Rubicon Maps frontend styles.');
assertTrueAsset(isset($GLOBALS['rubicon_test_enqueued_scripts']['rtv-rm-frontend']), 'List rendering should enqueue shared Rubicon Maps frontend scripts.');
assertTrueAsset(!isset($GLOBALS['rubicon_test_enqueued_styles']['leaflet-css']), 'List rendering should not enqueue Leaflet styles.');
assertTrueAsset(!isset($GLOBALS['rubicon_test_enqueued_scripts']['leaflet-js']), 'List rendering should not enqueue Leaflet scripts.');
assertTrueAsset(!isset($GLOBALS['rubicon_test_enqueued_scripts']['rtv-rm-google-maps']), 'List rendering should not enqueue Google Maps scripts.');

assertTrueAsset(isset($GLOBALS['rubicon_test_registered_styles']['rtv-rm-tokens']), 'List rendering should register the shared rtv-rm-tokens stylesheet.');
assertTrueAsset(
    in_array('rtv-rm-tokens', $GLOBALS['rubicon_test_registered_styles']['rtv-rm-frontend']['deps'] ?? [], true),
    'The rtv-rm-frontend stylesheet registration should declare rtv-rm-tokens as a dependency.'
);

assertTrueAsset(isset($GLOBALS['rubicon_test_localized_scripts']['rtv-rm-frontend']), 'rtv-rm-frontend should have localized script data.');

$localizedCalls = $GLOBALS['rubicon_test_localized_scripts']['rtv-rm-frontend'];
$localizedObjectNames = array_column($localizedCalls, 'objectName');

assertTrueAsset(in_array('rubiconMapsConfig', $localizedObjectNames, true), 'rtv-rm-frontend should localize rubiconMapsConfig.');
assertTrueAsset(in_array('rubiconMapsStrings', $localizedObjectNames, true), 'rtv-rm-frontend should localize rubiconMapsStrings.');

$stringsCall = null;
foreach ($localizedCalls as $call) {
    if ('rubiconMapsStrings' === $call['objectName']) {
        $stringsCall = $call;
        break;
    }
}

assertTrueAsset(null !== $stringsCall, 'rubiconMapsStrings localized call should be present.');

$expectedStringKeys = ['emptyStandalone', 'emptySynced', 'loading', 'error', 'retry', 'focusOnLocation'];
$actualStringKeys = array_keys($stringsCall['l10n']);
sort($expectedStringKeys);
sort($actualStringKeys);

assertTrueAsset($expectedStringKeys === $actualStringKeys, 'rubiconMapsStrings l10n payload should contain exactly the expected keys.');

foreach ($stringsCall['l10n'] as $key => $value) {
    assertTrueAsset(is_string($value) && '' !== $value, "rubiconMapsStrings key '{$key}' should be a non-empty string.");
}

$registerStyleCallsAfterFirst = $GLOBALS['rubicon_test_register_style_calls'];
$registerScriptCallsAfterFirst = $GLOBALS['rubicon_test_register_script_calls'];

FrontendAssetManager::enqueueList();

assertTrueAsset(
    $GLOBALS['rubicon_test_register_style_calls'] === $registerStyleCallsAfterFirst,
    'Calling enqueueList() a second time without a reset should not re-register any styles.'
);
assertTrueAsset(
    $GLOBALS['rubicon_test_register_script_calls'] === $registerScriptCallsAfterFirst,
    'Calling enqueueList() a second time without a reset should not re-register any scripts.'
);
assertTrueAsset(
    1 === ($GLOBALS['rubicon_test_register_style_calls']['rtv-rm-tokens'] ?? 0),
    'rtv-rm-tokens should only be registered once across repeated enqueueList() calls.'
);
assertTrueAsset(
    1 === ($GLOBALS['rubicon_test_register_style_calls']['rtv-rm-frontend'] ?? 0),
    'rtv-rm-frontend style should only be registered once across repeated enqueueList() calls.'
);
assertTrueAsset(
    1 === ($GLOBALS['rubicon_test_register_script_calls']['rtv-rm-frontend'] ?? 0),
    'rtv-rm-frontend script should only be registered once across repeated enqueueList() calls.'
);

resetAssetManagerState();
FrontendAssetManager::enqueueMap('google');

assertTrueAsset(isset($GLOBALS['rubicon_test_enqueued_styles']['leaflet-css']), 'Map rendering should fall back to Leaflet assets when Google is requested without an API key.');
assertTrueAsset(isset($GLOBALS['rubicon_test_enqueued_scripts']['leaflet-js']), 'Map rendering should enqueue Leaflet scripts when Google is requested without an API key.');
assertTrueAsset(!isset($GLOBALS['rubicon_test_enqueued_scripts']['rtv-rm-google-maps']), 'Map rendering should not enqueue Google Maps without an API key.');

echo 'FrontendAssetManagerTest passed.' . PHP_EOL;
