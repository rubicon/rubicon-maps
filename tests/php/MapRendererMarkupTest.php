<?php
// SPDX-License-Identifier: GPL-2.0-or-later

declare(strict_types=1);

define('ABSPATH', __DIR__ . '/../../');
define('RTV_RM_PLUGIN_FILE', __DIR__ . '/../../rubicon-maps.php');

function esc_attr(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES);
}

function esc_url(string $value): string
{
    return $value;
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

function wp_kses_post(string $value): string
{
    return $value;
}

function rest_url(string $path = ''): string
{
    return 'https://example.com/wp-json/' . ltrim($path, '/');
}

function wp_json_encode(mixed $value): string|false
{
    return json_encode($value);
}

function wp_unique_id(): string
{
    return 'test-uid';
}

function get_option(string $option, mixed $default = false): mixed
{
    return [
        'rtv_rm_options' => [],
    ][$option] ?? $default;
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

function plugins_url(string $path = '', string $plugin = ''): string
{
    return 'https://example.com/wp-content/plugins/rubicon-maps' . $path;
}

function apply_filters(string $tag, mixed $value): mixed
{
    return $value;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Frontend\MapRenderer;

function assertMapRendererContains(string $needle, string $haystack, string $message): void
{
    if (!str_contains($haystack, $needle)) {
        fwrite(STDERR, $message . PHP_EOL . 'Markup: ' . $haystack . PHP_EOL);
        exit(1);
    }
}

$renderer = new MapRenderer();

$markup = $renderer->render([]);

assertMapRendererContains('data-state="loading"', $markup, 'Map root should carry data-state="loading" initially.');
assertMapRendererContains('rubicon-maps__status', $markup, 'The map container should include an aria-live status node.');
assertMapRendererContains('aria-live="polite"', $markup, 'The map status node should be announced politely.');
assertMapRendererContains('rubicon-maps__canvas', $markup, 'The map canvas element should remain present.');

echo 'MapRendererMarkupTest passed.' . PHP_EOL;
