<?php

namespace RubiconMaps\Support;

if (!defined('ABSPATH')) {
    exit;
}

final class Plugin
{
    public const OPTION_GROUP = 'rtv_rm_settings';
    public const OPTION_NAME = 'rtv_rm_options';
    public const MENU_PAGE_SLUG = 'rubicon-maps';
    public const SETTINGS_PAGE_SLUG = 'rubicon-maps-settings';
    public const POST_TYPE_LOCATION = 'rtv_rm_location';
    public const TAXONOMY_CATEGORY = 'rtv_rm_category';
    public const TAXONOMY_REGION = 'rtv_rm_region';
    public const UPDATE_URI = 'https://git.daxdavis.com/rubicon/rubicon-maps';
    public const UPDATE_HOSTNAME = 'git.daxdavis.com';
    public const UPDATE_API_RELEASE_LATEST = 'https://git.daxdavis.com/api/v1/repos/rubicon/rubicon-maps/releases/latest';
    public const REST_NAMESPACE = 'rubicon-maps/v1';
    public const SHORTCODE_MAP = 'rubicon_maps';
    public const SHORTCODE_LIST = 'rubicon_maps_list';
    public const DIVI5_SCRIPT_HANDLE = 'rtv-rm-divi5-builder';
    public const DIVI5_SCRIPT_RELATIVE_PATH = 'divi-5/visual-builder/build/rubicon-maps-divi5.js';

    /**
     * Return the current plugin version from the bootstrapped constant.
     */
    public static function version(): string
    {
        return defined('RTV_RM_VERSION') ? RTV_RM_VERSION : '1.0.1';
    }

    public static function path(string $relativePath = ''): string
    {
        $basePath = defined('RTV_RM_PLUGIN_PATH') ? RTV_RM_PLUGIN_PATH : dirname(__DIR__, 2) . '/';

        if ('' === $relativePath) {
            return untrailingslashit($basePath);
        }

        return untrailingslashit($basePath) . '/' . ltrim($relativePath, '/');
    }

    public static function url(string $relativePath = ''): string
    {
        $baseUrl = defined('RTV_RM_PLUGIN_URL') ? RTV_RM_PLUGIN_URL : plugins_url('/', RTV_RM_PLUGIN_FILE);

        if ('' === $relativePath) {
            return untrailingslashit($baseUrl);
        }

        return untrailingslashit($baseUrl) . '/' . ltrim($relativePath, '/');
    }

    public static function isDivi5Enabled(): bool
    {
        return function_exists('et_builder_d5_enabled') && et_builder_d5_enabled();
    }

    public static function locationMenuSlug(): string
    {
        return 'edit.php?post_type=' . self::POST_TYPE_LOCATION;
    }
}
