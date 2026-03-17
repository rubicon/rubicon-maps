<?php

namespace RubiconMaps\ImportExport;

final class LocationCsvTransformer
{
    /**
     * @return array<int, string>
     */
    public static function headers(): array
    {
        return [
            'id',
            'title',
            'excerpt',
            'content',
            'street',
            'city',
            'state',
            'zip',
            'country',
            'latitude',
            'longitude',
            'phone',
            'email',
            'website',
            'categories',
            'regions',
            'featured_image',
            'marker_icon_id',
            'menu_order',
            'status',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function templateRow(): array
    {
        return array_fill_keys(self::headers(), '');
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    public static function normalizeImportRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $normalized[self::normalizeKey((string) $key)] = is_scalar($value) ? trim((string) $value) : '';
        }

        return [
            'id' => self::normalizeInt($normalized['id'] ?? ''),
            'title' => $normalized['title'] ?? '',
            'excerpt' => $normalized['excerpt'] ?? '',
            'content' => $normalized['content'] ?? '',
            'street' => $normalized['street'] ?? '',
            'city' => $normalized['city'] ?? '',
            'state' => $normalized['state'] ?? '',
            'zip' => $normalized['zip'] ?? '',
            'country' => $normalized['country'] ?? '',
            'latitude' => $normalized['latitude'] ?? '',
            'longitude' => $normalized['longitude'] ?? '',
            'phone' => $normalized['phone'] ?? '',
            'email' => $normalized['email'] ?? '',
            'website' => $normalized['website'] ?? '',
            'categories' => self::normalizeSlugList($normalized['categories'] ?? ''),
            'regions' => self::normalizeSlugList($normalized['regions'] ?? ''),
            'featured_image' => $normalized['featured_image'] ?? '',
            'marker_icon_id' => self::normalizeInt($normalized['marker_icon_id'] ?? ''),
            'menu_order' => self::normalizeInt($normalized['menu_order'] ?? ''),
            'status' => self::normalizeStatus($normalized['status'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $location
     * @return array<string, string>
     */
    public static function buildExportRow(array $location): array
    {
        $addressParts = $location['address_parts'] ?? [];

        return [
            'id' => self::stringValue($location['id'] ?? ''),
            'title' => self::stringValue($location['title'] ?? ''),
            'excerpt' => self::stringValue($location['excerpt'] ?? ''),
            'content' => self::stringValue($location['description'] ?? $location['content'] ?? ''),
            'street' => self::stringValue($addressParts['street'] ?? ''),
            'city' => self::stringValue($addressParts['city'] ?? ''),
            'state' => self::stringValue($addressParts['state'] ?? ''),
            'zip' => self::stringValue($addressParts['zip'] ?? ''),
            'country' => self::stringValue($addressParts['country'] ?? ''),
            'latitude' => self::stringValue($location['latitude'] ?? ''),
            'longitude' => self::stringValue($location['longitude'] ?? ''),
            'phone' => self::stringValue($location['phone'] ?? ''),
            'email' => self::stringValue($location['email'] ?? ''),
            'website' => self::stringValue($location['website'] ?? ''),
            'categories' => self::flattenTerms($location['categories'] ?? []),
            'regions' => self::flattenTerms($location['regions'] ?? []),
            'featured_image' => self::stringValue($location['image_url'] ?? ''),
            'marker_icon_id' => self::stringValue($location['marker_icon_id'] ?? ''),
            'menu_order' => self::stringValue($location['menu_order'] ?? ''),
            'status' => self::stringValue($location['status'] ?? 'publish'),
        ];
    }

    private static function normalizeKey(string $key): string
    {
        $key = strtolower(trim($key));
        $key = str_replace(['-', ' '], '_', $key);

        return $key;
    }

    private static function normalizeInt(string $value): int
    {
        $value = (int) trim($value);

        return $value > 0 ? $value : 0;
    }

    /**
     * @return array<int, string>
     */
    private static function normalizeSlugList(string $value): array
    {
        $normalized = [];

        foreach (explode(',', $value) as $item) {
            $slug = trim(strtolower((string) $item));
            $slug = preg_replace('/[^a-z0-9_-]+/', '-', $slug) ?? '';
            $slug = trim($slug, '-');

            if ('' === $slug || in_array($slug, $normalized, true)) {
                continue;
            }

            $normalized[] = $slug;
        }

        return $normalized;
    }

    /**
     * @param array<int, mixed> $terms
     */
    private static function flattenTerms(array $terms): string
    {
        $slugs = [];

        foreach ($terms as $term) {
            $slug = '';

            if (is_array($term)) {
                $slug = trim((string) ($term['slug'] ?? ''));
            } elseif (is_object($term) && isset($term->slug)) {
                $slug = trim((string) $term->slug);
            }

            if ('' === $slug || in_array($slug, $slugs, true)) {
                continue;
            }

            $slugs[] = $slug;
        }

        return implode(',', $slugs);
    }

    private static function normalizeStatus(string $status): string
    {
        $status = trim(strtolower($status));

        if (in_array($status, ['draft', 'pending', 'private', 'publish'], true)) {
            return $status;
        }

        return 'publish';
    }

    private static function stringValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return is_scalar($value) ? trim((string) $value) : '';
    }
}
