<?php

namespace RubiconMaps\Frontend;

final class MapInstanceConfigBuilder
{
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

        return [
            'instanceId' => trim((string) ($atts['id'] ?? '')) ?: $generatedId,
            'provider' => $provider,
            'lat' => $this->stringOrFallback($atts['lat'] ?? null, (string) ($settings['default_latitude'] ?? '')),
            'lng' => $this->stringOrFallback($atts['lng'] ?? null, (string) ($settings['default_longitude'] ?? '')),
            'zoom' => $this->intOrFallback($atts['zoom'] ?? null, (int) ($settings['default_zoom'] ?? 9)),
            'height' => $this->stringOrFallback($atts['height'] ?? null, (string) ($settings['default_map_height'] ?? '480px')),
            'tileUrl' => $this->normalizedTileUrl((string) ($settings['tile_url'] ?? '')),
            'category' => (string) ($atts['category'] ?? ''),
            'region' => (string) ($atts['region'] ?? ''),
            'locationIds' => (string) ($atts['location_ids'] ?? ''),
            'endpoint' => $endpoint,
            'scrollWheelZoom' => $this->resolveScrollWheelSetting($atts['scrollwheel'] ?? '', (bool) ($settings['enable_scroll_wheel'] ?? true)),
        ];
    }

    private function normalizedProvider(mixed $value, string $fallback, bool $googleConfigured): string
    {
        $provider = strtolower($this->stringOrFallback($value, $fallback));
        $fallback = strtolower(trim($fallback));

        if (!in_array($provider, ['leaflet', 'google'], true)) {
            $provider = in_array($fallback, ['leaflet', 'google'], true) ? $fallback : 'leaflet';
        }

        if ('google' === $provider && !$googleConfigured) {
            return 'leaflet';
        }

        return $provider;
    }

    private function resolveScrollWheelSetting(mixed $value, bool $fallback): bool
    {
        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            '0', 'false', 'off', 'no' => false,
            '1', 'true', 'on', 'yes' => true,
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

    private function normalizedTileUrl(string $tileUrl): string
    {
        $tileUrl = trim($tileUrl);

        if ('' === $tileUrl) {
            return 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        }

        foreach (['{z}', '{x}', '{y}'] as $requiredPlaceholder) {
            if (!str_contains($tileUrl, $requiredPlaceholder)) {
                return 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            }
        }

        return $tileUrl;
    }
}
