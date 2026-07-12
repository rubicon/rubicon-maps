<?php

namespace RubiconMaps\Support;

use function add_action;
use function add_filter;
use function current_user_can;
use function get_site_transient;
use function is_array;
use function is_string;
use function json_decode;
use function method_exists;
use function plugin_basename;
use function set_site_transient;
use function str_contains;
use function str_ends_with;
use function str_starts_with;
use function substr;
use function trim;
use function wp_remote_get;
use function wp_remote_retrieve_body;
use function wp_remote_retrieve_response_code;
use function wp_update_plugins;

if (!defined('ABSPATH')) {
    exit;
}

final class PluginUpdater
{
    private const CACHE_KEY = 'rtv_rm_update_release';
    private const CACHE_TTL = 600;

    public static function init(): void
    {
        add_filter('update_plugins_' . Plugin::UPDATE_HOSTNAME, [self::class, 'filterUpdate'], 10, 4);
        add_action('load-plugins.php', [self::class, 'primeUpdateTransient']);
    }

    /**
     * @param array<string, mixed>|false $update
     * @param array<string, mixed> $pluginData
     * @param array<int, string> $locales
     * @return array<string, mixed>|false
     */
    public static function filterUpdate($update, array $pluginData, string $pluginFile, array $locales)
    {
        if (plugin_basename(RTV_RM_PLUGIN_FILE) !== $pluginFile) {
            return $update;
        }

        $release = self::latestRelease();
        $fallbackVersion = (string) ($pluginData['Version'] ?? Plugin::version());
        $fallback = [
            'id' => Plugin::UPDATE_URI,
            'slug' => 'rubicon-maps',
            'plugin' => $pluginFile,
            'version' => $fallbackVersion,
            'new_version' => $fallbackVersion,
            'url' => Plugin::UPDATE_URI,
            'package' => '',
            'tested' => (string) ($pluginData['Tested up to'] ?? ''),
            'requires_php' => (string) ($pluginData['RequiresPHP'] ?? $pluginData['Requires PHP'] ?? ''),
            'autoupdate' => true,
        ];

        if (null === $release) {
            return $fallback;
        }

        $newVersion = self::normalizeVersion((string) ($release['tag_name'] ?? ''));
        if ('' === $newVersion) {
            $newVersion = $fallbackVersion;
        }

        return [
            'id' => Plugin::UPDATE_URI,
            'slug' => 'rubicon-maps',
            'plugin' => $pluginFile,
            'version' => $newVersion,
            'new_version' => $newVersion,
            'url' => self::stringValue($release['html_url'] ?? Plugin::UPDATE_URI),
            'package' => self::packageUrl($release),
            'tested' => (string) ($pluginData['Tested up to'] ?? ''),
            'requires_php' => (string) ($pluginData['RequiresPHP'] ?? $pluginData['Requires PHP'] ?? ''),
            'autoupdate' => true,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function latestRelease(): ?array
    {
        $cached = get_site_transient(self::CACHE_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        $response = wp_remote_get(
            Plugin::UPDATE_API_RELEASE_LATEST,
            [
                'timeout' => 10,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );

        if (!is_array($response) && (!is_object($response) || !method_exists($response, 'get_error_code'))) {
            return null;
        }

        if (is_object($response) && method_exists($response, 'get_error_code')) {
            return null;
        }

        if (200 !== (int) wp_remote_retrieve_response_code($response)) {
            return null;
        }

        $decoded = json_decode((string) wp_remote_retrieve_body($response), true);
        if (!is_array($decoded)) {
            return null;
        }

        set_site_transient(self::CACHE_KEY, $decoded, self::CACHE_TTL);

        return $decoded;
    }

    public static function primeUpdateTransient(): void
    {
        if (!current_user_can('update_plugins')) {
            return;
        }

        $transient = get_site_transient('update_plugins');
        $pluginFile = plugin_basename(RTV_RM_PLUGIN_FILE);

        if (
            is_object($transient)
            && (
                isset($transient->response[$pluginFile])
                || isset($transient->no_update[$pluginFile])
            )
        ) {
            return;
        }

        wp_update_plugins();
    }

    /**
     * @param array<string, mixed> $release
     */
    private static function packageUrl(array $release): string
    {
        $assets = $release['assets'] ?? [];

        if (!is_array($assets)) {
            return '';
        }

        foreach ($assets as $asset) {
            if (!is_array($asset)) {
                continue;
            }

            $name = self::stringValue($asset['name'] ?? '');
            $downloadUrl = self::stringValue($asset['browser_download_url'] ?? '');

            if ('' === $downloadUrl) {
                continue;
            }

            if (str_ends_with($name, '.zip') && str_contains($name, 'rubicon-maps')) {
                return $downloadUrl;
            }
        }

        foreach ($assets as $asset) {
            if (!is_array($asset)) {
                continue;
            }

            $downloadUrl = self::stringValue($asset['browser_download_url'] ?? '');
            if (str_ends_with($downloadUrl, '.zip')) {
                return $downloadUrl;
            }
        }

        return '';
    }

    private static function normalizeVersion(string $version): string
    {
        $version = trim($version);

        if (str_starts_with($version, 'v')) {
            return substr($version, 1);
        }

        return $version;
    }

    private static function stringValue(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }
}
