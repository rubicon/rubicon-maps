<?php

namespace RubiconMaps\Frontend;

use RubiconMaps\Support\Plugin;

final class LocationQueryArgsBuilder
{
    /**
     * Build canonical location query args from map/list filters.
     *
     * @param array<string, mixed> $filters Query filters from shortcode, module, or REST input.
     * @return array<string, mixed>
     */
    public function build(array $filters): array
    {
        $categories = $this->normalizeList($filters['category'] ?? $filters['categories'] ?? []);
        $regions = $this->normalizeList($filters['region'] ?? $filters['regions'] ?? []);
        $locationIds = $this->normalizeIds($filters['location_ids'] ?? $filters['locations'] ?? []);
        $postsPerPage = isset($filters['posts_per_page']) ? (int) $filters['posts_per_page'] : -1;

        $args = [
            'post_type' => Plugin::POST_TYPE_LOCATION,
            'post_status' => ['publish'],
            'posts_per_page' => 0 === $postsPerPage ? -1 : $postsPerPage,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
        ];

        if ([] !== $locationIds) {
            $args['post__in'] = $locationIds;
        }

        $taxQuery = ['relation' => 'AND'];

        if ([] !== $categories) {
            $taxQuery[] = [
                'taxonomy' => Plugin::TAXONOMY_CATEGORY,
                'field' => 'slug',
                'terms' => $categories,
            ];
        }

        if ([] !== $regions) {
            $taxQuery[] = [
                'taxonomy' => Plugin::TAXONOMY_REGION,
                'field' => 'slug',
                'terms' => $regions,
            ];
        }

        if (1 < count($taxQuery)) {
            $args['tax_query'] = $taxQuery;
        }

        return $args;
    }

    /**
     * Normalize comma-separated or array-based slugs.
     *
     * @param array<int, mixed>|string $value
     * @return array<int, string>
     */
    private function normalizeList(array|string $value): array
    {
        $items = $this->normalizeRawItems($value);
        $normalized = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $item = $item['slug'] ?? $item['value'] ?? $item['id'] ?? '';
            }

            $slug = trim((string) $item);

            if ('' === $slug) {
                continue;
            }

            if (!in_array($slug, $normalized, true)) {
                $normalized[] = $slug;
            }
        }

        return $normalized;
    }

    /**
     * Normalize comma-separated or array-based post IDs.
     *
     * @param array<int, mixed>|string $value
     * @return array<int, int>
     */
    private function normalizeIds(array|string $value): array
    {
        $items = $this->normalizeRawItems($value);
        $normalized = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $item = $item['id'] ?? $item['value'] ?? '';
            }

            $id = (int) trim((string) $item);

            if ($id <= 0 || in_array($id, $normalized, true)) {
                continue;
            }

            $normalized[] = $id;
        }

        return $normalized;
    }

    /**
     * Normalize legacy comma-separated strings and structured JSON arrays into a flat item list.
     *
     * @param array<int, mixed>|string $value
     * @return array<int, mixed>
     */
    private function normalizeRawItems(array|string $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $value = trim($value);

        if ('' === $value) {
            return [];
        }

        if (str_starts_with($value, '[')) {
            $decoded = json_decode($value, true);

            if (JSON_ERROR_NONE === json_last_error() && is_array($decoded)) {
                return $decoded;
            }
        }

        return explode(',', $value);
    }
}
