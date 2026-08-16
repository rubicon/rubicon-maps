<?php
// SPDX-License-Identifier: GPL-2.0-or-later

namespace RubiconMaps\Frontend;

use RubiconMaps\Helpers\SettingsHelper;

use function esc_attr;
use function esc_url;
use function rest_url;
use function wp_json_encode;

if (!defined('ABSPATH')) {
    exit;
}

final class MapRenderer
{
    public function __construct(private readonly ?MapInstanceConfigBuilder $configBuilder = null)
    {
    }

    /**
     * Render a frontend map shell for shortcodes or Divi modules.
     *
     * @param array<string, mixed> $atts Map instance attributes.
     */
    public function render(array $atts = []): string
    {
        $settings = [
            'default_provider' => SettingsHelper::get_option('default_provider', 'leaflet'),
            'default_viewport_mode' => SettingsHelper::get_option('default_viewport_mode', 'auto_fit'),
            'default_latitude' => SettingsHelper::get_option('default_latitude', '29.7604'),
            'default_longitude' => SettingsHelper::get_option('default_longitude', '-95.3698'),
            'default_zoom' => SettingsHelper::get_option('default_zoom', 9),
            'default_map_height' => SettingsHelper::get_option('default_map_height', '480px'),
            'default_auto_fit_padding' => SettingsHelper::get_option('default_auto_fit_padding', 24),
            'default_tile_preset' => SettingsHelper::get_option('default_tile_preset', 'openstreetmap'),
            'tile_url' => SettingsHelper::get_option('tile_url', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
            'google_maps_api_key' => SettingsHelper::get_option('google_maps_api_key', ''),
            'default_enable_zoom_control' => SettingsHelper::get_option('default_enable_zoom_control', true),
            'enable_scroll_wheel' => SettingsHelper::get_option('enable_scroll_wheel', false),
            'default_enable_double_click_zoom' => SettingsHelper::get_option('default_enable_double_click_zoom', true),
            'default_popup_trigger' => SettingsHelper::get_option('default_popup_trigger', 'click'),
            'default_popup_max_width' => SettingsHelper::get_option('default_popup_max_width', 320),
            'default_close_on_map_click' => SettingsHelper::get_option('default_close_on_map_click', true),
            'default_auto_close_popup' => SettingsHelper::get_option('default_auto_close_popup', true),
            'default_open_all_popups' => SettingsHelper::get_option('default_open_all_popups', false),
            'default_enable_clustering' => SettingsHelper::get_option('default_enable_clustering', true),
            'default_cluster_radius' => SettingsHelper::get_option('default_cluster_radius', 100),
        ];
        $syncId = trim((string) ($atts['sync_id'] ?? ''));
        $instanceId = $this->resolveInstanceId($atts['id'] ?? null);
        $payload = $this->getConfigBuilder()->build(
            $atts,
            $settings,
            esc_url(rest_url('rubicon-maps/v1/locations')),
            $instanceId
        );

        FrontendAssetManager::enqueueMap((string) $payload['provider']);

        ob_start();
        ?>
        <div
            id="<?php echo esc_attr($instanceId); ?>"
            class="rubicon-maps"
            data-rubicon-map="1"
            data-state="loading"
            data-sync-id="<?php echo esc_attr('' !== $syncId ? $syncId : $instanceId); ?>"
            data-rubicon-config="<?php echo esc_attr((string) wp_json_encode($payload)); ?>"
        >
            <div class="rubicon-maps__canvas" style="height:<?php echo esc_attr($payload['height']); ?>"></div>
            <div class="rubicon-maps__status" aria-live="polite"></div>
        </div>
        <?php

        return (string) ob_get_clean();
    }

    private function resolveInstanceId(?string $requestedId): string
    {
        $requestedId = trim((string) $requestedId);

        if ('' !== $requestedId) {
            return $requestedId;
        }

        return 'rubicon-map-' . wp_unique_id();
    }

    private function getConfigBuilder(): MapInstanceConfigBuilder
    {
        return $this->configBuilder ?? new MapInstanceConfigBuilder();
    }
}
