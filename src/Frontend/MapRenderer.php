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
    /**
     * Render a frontend map shell for shortcodes or Divi modules.
     *
     * @param array<string, mixed> $atts Map instance attributes.
     */
    public function render(array $atts = []): string
    {
        $provider = (string) ($atts['provider'] ?? SettingsHelper::get_option('default_provider', 'leaflet'));
        if ('google' === $provider && '' === SettingsHelper::get_option('google_maps_api_key', '')) {
            $provider = 'leaflet';
        }

        FrontendAssetManager::enqueue($provider);

        $instanceId = $this->resolveInstanceId($atts['id'] ?? null);
        $payload = [
            'instanceId' => $instanceId,
            'provider' => $provider,
            'lat' => (string) ($atts['lat'] ?? SettingsHelper::get_option('default_latitude', '29.7604')),
            'lng' => (string) ($atts['lng'] ?? SettingsHelper::get_option('default_longitude', '-95.3698')),
            'zoom' => (int) ($atts['zoom'] ?? SettingsHelper::get_option('default_zoom', 9)),
            'height' => (string) ($atts['height'] ?? SettingsHelper::get_option('default_map_height', '480px')),
            'category' => (string) ($atts['category'] ?? ''),
            'region' => (string) ($atts['region'] ?? ''),
            'locationIds' => (string) ($atts['location_ids'] ?? ''),
            'endpoint' => esc_url(rest_url('rubicon-maps/v1/locations')),
            'scrollWheelZoom' => !empty($atts['scrollwheel']) ? '1' : (SettingsHelper::get_option('enable_scroll_wheel', true) ? '1' : '0'),
        ];

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
}
