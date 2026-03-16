<?php

namespace RubiconMaps\Support;

if (!defined('ABSPATH')) {
    exit;
}

final class Plugin
{
    public const OPTION_GROUP = 'rubicon_maps_settings';
    public const OPTION_NAME = 'rubicon_maps_options';
    public const SETTINGS_PAGE_SLUG = 'rubicon-maps';
    public const POST_TYPE_LOCATION = 'rubicon_maps_location';
    public const TAXONOMY_CATEGORY = 'rubicon_maps_category';
    public const TAXONOMY_REGION = 'rubicon_maps_region';
    public const REST_NAMESPACE = 'rubicon-maps/v1';
    public const SHORTCODE_MAP = 'rubicon_maps';
    public const SHORTCODE_LIST = 'rubicon_maps_list';
    public const DIVI5_SCRIPT_HANDLE = 'rubicon-maps-divi5-builder';
    public const DIVI5_SCRIPT_RELATIVE_PATH = 'divi-5/visual-builder/build/rubicon-maps-divi5.js';

    /**
     * Return the current plugin version from the bootstrapped constant.
     */
    public static function version(): string
    {
        return defined('RUBICON_MAPS_VERSION') ? RUBICON_MAPS_VERSION : '0.5.0';
    }

    public static function path(string $relativePath = ''): string
    {
        $basePath = defined('RUBICON_MAPS_PLUGIN_PATH') ? RUBICON_MAPS_PLUGIN_PATH : dirname(__DIR__, 2) . '/';

        if ('' === $relativePath) {
            return untrailingslashit($basePath);
        }

        return untrailingslashit($basePath) . '/' . ltrim($relativePath, '/');
    }

    public static function url(string $relativePath = ''): string
    {
        $baseUrl = defined('RUBICON_MAPS_PLUGIN_URL') ? RUBICON_MAPS_PLUGIN_URL : plugins_url('/', RUBICON_MAPS_PLUGIN_FILE);

        if ('' === $relativePath) {
            return untrailingslashit($baseUrl);
        }

        return untrailingslashit($baseUrl) . '/' . ltrim($relativePath, '/');
    }

    public static function isDivi5Enabled(): bool
    {
        return function_exists('et_builder_d5_enabled') && et_builder_d5_enabled();
    }
}
