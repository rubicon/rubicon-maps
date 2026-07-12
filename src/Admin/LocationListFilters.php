<?php

namespace RubiconMaps\Admin;

use RubiconMaps\Support\Plugin;

use function absint;
use function add_action;
use function add_filter;
use function get_term_by;
use function is_admin;
use function sanitize_key;
use function wp_dropdown_categories;

if (!defined('ABSPATH')) {
    exit;
}

final class LocationListFilters
{
    public static function init(): void
    {
        add_action('restrict_manage_posts', [self::class, 'renderFilters']);
        add_action('parse_query', [self::class, 'applyFilters']);
        add_filter('post_row_actions', [self::class, 'injectInlineId'], 10, 2);
    }

    /**
     * @param array<string, string> $actions
     * @return array<string, string>
     */
    public static function prependInlineIdAction(array $actions, int $postId): array
    {
        return ['rtv_rm_location_id' => 'ID: ' . $postId] + $actions;
    }

    /**
     * @param array<string, string> $actions
     * @param object $post
     * @return array<string, string>
     */
    public static function injectInlineId(array $actions, $post): array
    {
        if (!is_object($post) || Plugin::POST_TYPE_LOCATION !== ($post->post_type ?? '')) {
            return $actions;
        }

        return self::prependInlineIdAction($actions, (int) ($post->ID ?? 0));
    }

    /**
     * @param string $postType
     */
    public static function renderFilters($postType): void
    {
        if (!self::isLocationPostType($postType) || !function_exists('wp_dropdown_categories')) {
            return;
        }

        self::renderTaxonomyDropdown(
            Plugin::TAXONOMY_CATEGORY,
            __('All Categories', 'rubicon-maps')
        );

        self::renderTaxonomyDropdown(
            Plugin::TAXONOMY_REGION,
            __('All Regions', 'rubicon-maps')
        );
    }

    /**
     * @param object $query
     */
    public static function applyFilters($query): void
    {
        if (!is_admin() || !is_object($query)) {
            return;
        }

        $postType = $query->query['post_type'] ?? $_GET['post_type'] ?? '';
        if (!self::isLocationPostType($postType)) {
            return;
        }

        $taxQuery = $query->query_vars['tax_query'] ?? [];

        foreach ([Plugin::TAXONOMY_CATEGORY, Plugin::TAXONOMY_REGION] as $taxonomy) {
            $termId = absint($_GET[$taxonomy] ?? 0);

            if ($termId <= 0) {
                continue;
            }

            $taxQuery[] = [
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => [$termId],
            ];

            unset($query->query_vars[$taxonomy], $query->query[$taxonomy]);
        }

        if ([] === $taxQuery) {
            return;
        }

        if (count($taxQuery) > 1) {
            $taxQuery['relation'] = 'AND';
        }

        $query->set('tax_query', $taxQuery);
    }

    private static function isLocationPostType(string $postType): bool
    {
        return Plugin::POST_TYPE_LOCATION === sanitize_key($postType);
    }

    private static function renderTaxonomyDropdown(string $taxonomy, string $allLabel): void
    {
        wp_dropdown_categories([
            'show_option_all' => $allLabel,
            'taxonomy' => $taxonomy,
            'name' => $taxonomy,
            'orderby' => 'name',
            'selected' => absint($_GET[$taxonomy] ?? 0),
            'hierarchical' => true,
            'depth' => 3,
            'show_count' => false,
            'hide_empty' => false,
            'value_field' => 'term_id',
        ]);
    }
}
