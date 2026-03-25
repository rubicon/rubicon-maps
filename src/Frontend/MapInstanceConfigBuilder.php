<?php

namespace RubiconMaps\Frontend;

final class MapInstanceConfigBuilder
{
    private const DEFAULT_TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

    /**
     * @var array<string, string>
     */
    private const TILE_PRESETS = [
        'openstreetmap' => self::DEFAULT_TILE_URL,
        'carto_light' => 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
        'carto_dark' => 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
        'opentopomap' => 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
    ];

    /**
     * @param array<string, mixed> $atts
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    public function build(array $atts, array $settings, string $endpoint, string $generatedId = ''): array
    {
        $provider = $this->normalizedProvider(
            $atts['provider'] ?? null,
            (string) ($settings['default_provider'] ?? 'leaflet'),
            '' !== trim((string) ($settings['google_maps_api_key'] ?? ''))
        );
        $viewportMode = $this->normalizedViewportMode(
            $atts['viewport_mode'] ?? null,
            (string) ($settings['default_viewport_mode'] ?? 'auto_fit'),
            $atts
        );
        $tilePreset = $this->normalizedTilePreset(
            $atts['tile_preset'] ?? null,
            (string) ($settings['default_tile_preset'] ?? 'openstreetmap')
        );

        return [
            'instanceId' => trim((string) ($atts['id'] ?? '')) ?: $generatedId,
            'syncId' => trim((string) ($atts['sync_id'] ?? '')),
            'provider' => $provider,
            'viewportMode' => $viewportMode,
            'lat' => $this->stringOrFallback($atts['lat'] ?? null, (string) ($settings['default_latitude'] ?? '')),
            'lng' => $this->stringOrFallback($atts['lng'] ?? null, (string) ($settings['default_longitude'] ?? '')),
            'zoom' => $this->intOrFallback($atts['zoom'] ?? null, (int) ($settings['default_zoom'] ?? 9)),
            'height' => $this->stringOrFallback($atts['height'] ?? null, (string) ($settings['default_map_height'] ?? '480px')),
            'autoFitPadding' => $this->intOrFallback($atts['auto_fit_padding'] ?? null, (int) ($settings['default_auto_fit_padding'] ?? 24)),
            'tilePreset' => $tilePreset,
            'tileUrl' => $this->resolvedTileUrl($tilePreset, (string) ($settings['tile_url'] ?? '')),
            'category' => $this->normalizeCsvList($atts['category'] ?? ''),
            'region' => $this->normalizeCsvList($atts['region'] ?? ''),
            'locationIds' => $this->normalizeCsvIdList($atts['location_ids'] ?? ''),
            'endpoint' => $endpoint,
            'zoomControl' => $this->resolveBooleanSetting($atts['zoom_control'] ?? '', (bool) ($settings['default_enable_zoom_control'] ?? true)),
            'scrollWheelZoom' => $this->resolveBooleanSetting($atts['scrollwheel'] ?? '', (bool) ($settings['enable_scroll_wheel'] ?? true)),
            'doubleClickZoom' => $this->resolveBooleanSetting($atts['double_click_zoom'] ?? '', (bool) ($settings['default_enable_double_click_zoom'] ?? true)),
            'popupTrigger' => $this->normalizedPopupTrigger($atts['popup_trigger'] ?? null, (string) ($settings['default_popup_trigger'] ?? 'click')),
            'popupMaxWidth' => $this->intOrFallback($atts['popup_max_width'] ?? null, (int) ($settings['default_popup_max_width'] ?? 320)),
            'closeOnMapClick' => $this->resolveBooleanSetting($atts['close_on_map_click'] ?? '', (bool) ($settings['default_close_on_map_click'] ?? true)),
            'autoClosePopup' => $this->resolveBooleanSetting($atts['auto_close_popup'] ?? '', (bool) ($settings['default_auto_close_popup'] ?? true)),
            'openAllPopups' => $this->resolveBooleanSetting($atts['open_all_popups'] ?? '', (bool) ($settings['default_open_all_popups'] ?? false)),
            'enableClustering' => $this->resolveBooleanSetting($atts['enable_clustering'] ?? '', (bool) ($settings['default_enable_clustering'] ?? true)),
            'clusterRadius' => $this->intOrFallback($atts['cluster_radius'] ?? null, (int) ($settings['default_cluster_radius'] ?? 100)),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function tilePresetUrls(): array
    {
        return self::TILE_PRESETS;
    }

    private function normalizedProvider(mixed $value, string $fallback, bool $googleConfigured): string
    {
        $provider = strtolower($this->stringOrFallback($value, $fallback));
        $fallback = strtolower(trim($fallback));

        if ('default' === $provider) {
            $provider = $fallback;
        }

        if (!in_array($provider, ['leaflet', 'google'], true)) {
            $provider = in_array($fallback, ['leaflet', 'google'], true) ? $fallback : 'leaflet';
        }

        if ('google' === $provider && !$googleConfigured) {
            return 'leaflet';
        }

        return $provider;
    }

    private function normalizedViewportMode(mixed $value, string $fallback, array $atts): string
    {
        $normalized = strtolower(trim((string) $value));
        $fallback = strtolower(trim($fallback));

        if ('default' === $normalized || '' === $normalized) {
            $normalized = $fallback;
        }

        if (in_array($normalized, ['auto_fit', 'manual'], true)) {
            return $normalized;
        }

        $hasManualCoordinates = '' !== trim((string) ($atts['lat'] ?? ''))
            && '' !== trim((string) ($atts['lng'] ?? ''));

        if ($hasManualCoordinates) {
            return 'manual';
        }

        return in_array($fallback, ['auto_fit', 'manual'], true) ? $fallback : 'auto_fit';
    }

    private function normalizedTilePreset(mixed $value, string $fallback): string
    {
        $normalized = strtolower(trim((string) $value));
        $fallback = strtolower(trim($fallback));

        if ('default' === $normalized || '' === $normalized) {
            $normalized = $fallback;
        }

        if ('custom' === $normalized || array_key_exists($normalized, self::TILE_PRESETS)) {
            return $normalized;
        }

        if ('custom' === $fallback || array_key_exists($fallback, self::TILE_PRESETS)) {
            return $fallback;
        }

        return 'openstreetmap';
    }

    private function normalizedPopupTrigger(mixed $value, string $fallback): string
    {
        $normalized = strtolower(trim((string) $value));
        $fallback = strtolower(trim($fallback));

        if (in_array($normalized, ['click', 'hover'], true)) {
            return $normalized;
        }

        return in_array($fallback, ['click', 'hover'], true) ? $fallback : 'click';
    }

    private function resolveBooleanSetting(mixed $value, bool $fallback): bool
    {
        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            '0', 'false', 'off', 'no' => false,
            '1', 'true', 'on', 'yes' => true,
            'default', '' => $fallback,
            default => $fallback,
        };
    }

    private function stringOrFallback(mixed $value, string $fallback): string
    {
        $value = trim((string) $value);

        return '' !== $value ? $value : $fallback;
    }

    private function intOrFallback(mixed $value, int $fallback): int
    {
        $value = trim((string) $value);

        if ('' === $value) {
            return $fallback;
        }

        $normalized = (int) $value;

        return $normalized > 0 ? $normalized : $fallback;
    }

    private function resolvedTileUrl(string $tilePreset, string $tileUrl): string
    {
        if ('custom' !== $tilePreset && array_key_exists($tilePreset, self::TILE_PRESETS)) {
            return self::TILE_PRESETS[$tilePreset];
        }

        return $this->normalizedTileUrl($tileUrl);
    }

    private function normalizeCsvList(mixed $value): string
    {
        if (is_array($value)) {
            $items = $value;
        } else {
            $normalized = trim((string) $value);

            if ('' === $normalized) {
                return '';
            }

            if (str_starts_with($normalized, '[')) {
                $decoded = json_decode($normalized, true);

                if (JSON_ERROR_NONE === json_last_error() && is_array($decoded)) {
                    $items = $decoded;
                } else {
                    $items = explode(',', $normalized);
                }
            } else {
                $items = explode(',', $normalized);
            }
        }

        $values = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $item = $item['slug'] ?? $item['value'] ?? $item['id'] ?? '';
            }

            $item = trim((string) $item);

            if ('' === $item || in_array($item, $values, true)) {
                continue;
            }

            $values[] = $item;
        }

        return implode(',', $values);
    }

    private function normalizeCsvIdList(mixed $value): string
    {
        if (is_array($value)) {
            $items = $value;
        } else {
            $normalized = trim((string) $value);

            if ('' === $normalized) {
                return '';
            }

            if (str_starts_with($normalized, '[')) {
                $decoded = json_decode($normalized, true);

                if (JSON_ERROR_NONE === json_last_error() && is_array($decoded)) {
                    $items = $decoded;
                } else {
                    $items = explode(',', $normalized);
                }
            } else {
                $items = explode(',', $normalized);
            }
        }

        $values = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $item = $item['id'] ?? $item['value'] ?? '';
            }

            $item = (int) trim((string) $item);

            if ($item <= 0 || in_array($item, $values, true)) {
                continue;
            }

            $values[] = $item;
        }

        return implode(',', $values);
    }

    private function normalizedTileUrl(string $tileUrl): string
    {
        $tileUrl = trim($tileUrl);

        if ('' === $tileUrl) {
            return self::DEFAULT_TILE_URL;
        }

        foreach (['{z}', '{x}', '{y}'] as $requiredPlaceholder) {
            if (!str_contains($tileUrl, $requiredPlaceholder)) {
                return self::DEFAULT_TILE_URL;
            }
        }

        return $tileUrl;
    }
}
