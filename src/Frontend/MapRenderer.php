<?php

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
            'default_latitude' => SettingsHelper::get_option('default_latitude', '29.7604'),
            'default_longitude' => SettingsHelper::get_option('default_longitude', '-95.3698'),
            'default_zoom' => SettingsHelper::get_option('default_zoom', 9),
            'default_map_height' => SettingsHelper::get_option('default_map_height', '480px'),
            'tile_url' => SettingsHelper::get_option('tile_url', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
            'google_maps_api_key' => SettingsHelper::get_option('google_maps_api_key', ''),
            'enable_scroll_wheel' => SettingsHelper::get_option('enable_scroll_wheel', true),
        ];
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
            data-rubicon-config="<?php echo esc_attr((string) wp_json_encode($payload)); ?>"
        >
            <div class="rubicon-maps__canvas" style="height:<?php echo esc_attr($payload['height']); ?>"></div>
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
