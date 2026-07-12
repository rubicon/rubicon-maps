<?php

declare(strict_types=1);

define('ABSPATH', __DIR__ . '/../../');
define('RTV_RM_PLUGIN_FILE', __DIR__ . '/../../rubicon-maps.php');

function esc_attr(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES);
}

function esc_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES);
}

function esc_html__(string $value, string $domain = ''): string
{
    return $value;
}

function __(string $value, string $domain = ''): string
{
    return $value;
}

function esc_url(string $value): string
{
    return $value;
}

function wp_kses_post(string $value): string
{
    return $value;
}

function wp_unique_id(): string
{
    return 'test-uid';
}

final class WP_Query
{
    public function __construct(array $args = [])
    {
    }

    public function have_posts(): bool
    {
        return false;
    }

    public function the_post(): void
    {
    }
}

function get_option(string $option, mixed $default = false): mixed
{
    return [
        'rtv_rm_options' => [
            'default_map_height' => '480px',
        ],
    ][$option] ?? $default;
}

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
}

function wp_register_script(string $handle, string $src, array $deps = [], string $ver = '', bool $inFooter = false): void
{
}

function wp_enqueue_style(string $handle, string $src = '', array $deps = [], string $ver = ''): void
{
}

function wp_enqueue_script(string $handle, string $src = '', array $deps = [], string $ver = '', bool $inFooter = false): void
{
}

function wp_localize_script(string $handle, string $objectName, array $l10n): void
{
}

function wp_reset_postdata(): void
{
}

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Frontend\LocationListRenderer;

function assertRendererContains(string $needle, string $haystack, string $message): void
{
    if (!str_contains($haystack, $needle)) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$renderer = new LocationListRenderer();

$syncedMarkup = $renderer->render([
    'id' => 'rtv_map_synced',
    'sync_id' => 'rtv_map_synced',
    'use_fixed_height' => 'on',
    'height' => '420px',
]);

assertRendererContains('data-sync-mode="follow-map"', $syncedMarkup, 'Synced lists should render in follow-map mode.');
assertRendererContains('data-sync-id="rtv_map_synced"', $syncedMarkup, 'Synced lists should expose the saved sync ID.');
assertRendererContains('rubicon-location-list--scrollable', $syncedMarkup, 'Fixed-height synced lists should include the scrollable modifier class.');
assertRendererContains('style="height:420px"', $syncedMarkup, 'Explicit list heights should be preserved.');

$standaloneMarkup = $renderer->render([
    'id' => 'rtv_map_standalone',
    'use_fixed_height' => 'on',
    'height' => '',
]);

assertRendererContains('data-sync-mode="standalone"', $standaloneMarkup, 'Standalone lists should remain in standalone mode.');
assertRendererContains('data-default-height="480px"', $standaloneMarkup, 'Standalone lists should expose the plugin default height for runtime fallbacks.');
assertRendererContains('style="height:480px"', $standaloneMarkup, 'Standalone fixed-height lists without a saved height should fall back to the plugin default map height.');

$emptyStandaloneMarkup = $renderer->render([
    'id' => 'rtv_map_empty',
]);

assertRendererContains('data-state="empty"', $emptyStandaloneMarkup, 'Standalone lists with no matches should carry data-state="empty".');
assertRendererContains('aria-live="polite"', $emptyStandaloneMarkup, 'The list container should include an aria-live status node.');

assertRendererContains('data-state="loading"', $syncedMarkup, 'Synced lists should carry data-state="loading" initially.');

$thumbnailReflection = new ReflectionMethod(LocationListRenderer::class, 'renderListItem');

$thumbnailMarkup = $thumbnailReflection->invoke($renderer, [
    'id' => 42,
    'latitude' => '30.0',
    'longitude' => '-97.0',
    'title' => 'Test Location',
    'image_url' => 'https://example.com/photo.jpg',
], true);

assertRendererContains('rubicon-location-list__thumb', $thumbnailMarkup, 'List items should render a thumbnail when show_thumbnail is truthy and image_url is present.');

echo 'LocationListRendererTest passed.' . PHP_EOL;
