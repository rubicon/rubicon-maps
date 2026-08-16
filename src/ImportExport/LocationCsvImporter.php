<?php
// SPDX-License-Identifier: GPL-2.0-or-later

namespace RubiconMaps\ImportExport;

use RubiconMaps\Admin\MetaBox;
use RubiconMaps\Support\Plugin;
use WP_Error;

use function absint;
use function attachment_url_to_postid;
use function fclose;
use function fgetcsv;
use function fopen;
use function get_post_type;
use function is_wp_error;
use function update_post_meta;
use function wp_insert_post;
use function wp_set_object_terms;
use function wp_update_post;

if (!defined('ABSPATH')) {
    exit;
}

final class LocationCsvImporter
{
    /**
     * @return array{created:int,updated:int,skipped:int,warnings:int,errors:array<int, string>}
     */
    public function importFromFile(string $path): array
    {
        $summary = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'warnings' => 0,
            'errors' => [],
        ];

        $handle = fopen($path, 'rb');

        if (false === $handle) {
            $summary['errors'][] = __('Unable to open the uploaded CSV file.', 'rubicon-maps');
            return $summary;
        }

        $headers = fgetcsv($handle);

        if (!is_array($headers) || [] === $headers) {
            fclose($handle);
            $summary['errors'][] = __('The uploaded CSV file is missing a header row.', 'rubicon-maps');
            return $summary;
        }

        while (($row = fgetcsv($handle)) !== false) {
            if ([] === array_filter($row, static fn ($value): bool => '' !== trim((string) $value))) {
                continue;
            }

            $normalized = LocationCsvTransformer::normalizeImportRow(
                array_combine($headers, array_pad($row, count($headers), '')) ?: []
            );

            if ('' === $normalized['title']) {
                $summary['skipped']++;
                $summary['errors'][] = __('Skipped a CSV row because it did not include a title.', 'rubicon-maps');
                continue;
            }

            $result = $this->upsertLocation($normalized);

            if ($result instanceof WP_Error) {
                $summary['skipped']++;
                $summary['errors'][] = $result->get_error_message();
                continue;
            }

            $summary[$result]++;
        }

        fclose($handle);

        return $summary;
    }

    /**
     * @param array<string, mixed> $location
     */
    private function upsertLocation(array $location): string|WP_Error
    {
        $postId = (int) ($location['id'] ?? 0);
        $existingId = $postId > 0 && Plugin::POST_TYPE_LOCATION === get_post_type($postId) ? $postId : 0;

        $postarr = [
            'post_type' => Plugin::POST_TYPE_LOCATION,
            'post_title' => $location['title'],
            'post_excerpt' => $location['excerpt'],
            'post_content' => $location['content'],
            'menu_order' => (int) ($location['menu_order'] ?? 0),
            'post_status' => $location['status'],
        ];

        if ($existingId > 0) {
            $postarr['ID'] = $existingId;
            $postId = wp_update_post($postarr, true);
            $resultKey = 'updated';
        } else {
            $postId = wp_insert_post($postarr, true);
            $resultKey = 'created';
        }

        if (is_wp_error($postId)) {
            return $postId;
        }

        foreach (MetaBox::FIELDS as $metaKey => $type) {
            $sourceKey = 'marker_icon' === $metaKey ? 'marker_icon_id' : $metaKey;

            if (!array_key_exists($sourceKey, $location)) {
                continue;
            }

            update_post_meta($postId, $metaKey, $location[$sourceKey]);
        }

        wp_set_object_terms($postId, $location['categories'], Plugin::TAXONOMY_CATEGORY);
        wp_set_object_terms($postId, $location['regions'], Plugin::TAXONOMY_REGION);

        $thumbnailId = $this->resolveAttachmentId((string) ($location['featured_image'] ?? ''));
        if ($thumbnailId > 0) {
            update_post_meta($postId, '_thumbnail_id', $thumbnailId);
        }

        return $resultKey;
    }

    private function resolveAttachmentId(string $value): int
    {
        $value = trim($value);

        if ('' === $value) {
            return 0;
        }

        if (ctype_digit($value)) {
            return absint($value);
        }

        return function_exists('attachment_url_to_postid') ? absint(attachment_url_to_postid($value)) : 0;
    }
}
