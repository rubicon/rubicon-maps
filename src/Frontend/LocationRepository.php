<?php

namespace RubiconMaps\Frontend;

use RubiconMaps\Support\Plugin;
use WP_Post;
use WP_Query;

use function get_post;
use function get_post_field;
use function get_post_meta;
use function get_permalink;
use function get_term_meta;
use function get_the_post_thumbnail_url;
use function get_the_terms;
use function is_wp_error;
use function wp_reset_postdata;
use function wp_strip_all_tags;
use function wp_trim_words;

if (!defined('ABSPATH')) {
    exit;
}

final class LocationRepository
{
    public function __construct(private readonly ?LocationQueryArgsBuilder $argsBuilder = null)
    {
    }

    /**
     * Query location payloads for frontend or REST responses.
     *
     * @param array<string, mixed> $filters Query filters.
     * @return array<int, array<string, mixed>>
     */
    public function getLocations(array $filters = []): array
    {
        $query = new WP_Query($this->getArgsBuilder()->build($filters));
        $locations = [];

        while ($query->have_posts()) {
            $query->the_post();
            $locations[] = $this->serialize((int) $query->post->ID);
        }

        wp_reset_postdata();

        return $locations;
    }

    /**
     * Convert a location post into a canonical payload used by REST, list rendering, and map JS.
     *
     * @return array<string, mixed>
     */
    public function serialize(int $postId): array
    {
        $post = get_post($postId);

        if (!$post instanceof WP_Post) {
            return [];
        }

        $addressParts = [
            'street' => (string) get_post_meta($postId, 'street', true),
            'city' => (string) get_post_meta($postId, 'city', true),
            'state' => (string) get_post_meta($postId, 'state', true),
            'zip' => (string) get_post_meta($postId, 'zip', true),
            'country' => (string) get_post_meta($postId, 'country', true),
        ];

        $formattedAddress = LocationAddressFormatter::format($addressParts);
        $legacyAddress = trim((string) get_post_meta($postId, 'address', true));

        $excerpt = trim((string) $post->post_excerpt);
        if ('' === $excerpt) {
            $excerpt = wp_trim_words(wp_strip_all_tags((string) get_post_field('post_content', $postId)), 26);
        }

        $markerIconId = (int) get_post_meta($postId, 'marker_icon', true);
        if ($markerIconId <= 0) {
            $categories = $this->getTermPayloads($postId, Plugin::TAXONOMY_CATEGORY);
            foreach ($categories as $category) {
                if (!empty($category['marker_icon_id'])) {
                    $markerIconId = (int) $category['marker_icon_id'];
                    break;
                }
            }
        }

        return [
            'id' => $postId,
            'title' => (string) get_the_title($postId),
            'excerpt' => $excerpt,
            'content' => (string) apply_filters('the_content', (string) get_post_field('post_content', $postId)),
            'description' => (string) get_post_field('post_content', $postId),
            'formatted_address' => '' !== $formattedAddress ? $formattedAddress : $legacyAddress,
            'address_parts' => $addressParts,
            'latitude' => (string) get_post_meta($postId, 'latitude', true),
            'longitude' => (string) get_post_meta($postId, 'longitude', true),
            'phone' => (string) get_post_meta($postId, 'phone', true),
            'email' => (string) get_post_meta($postId, 'email', true),
            'website' => (string) get_post_meta($postId, 'website', true),
            'marker_icon_id' => $markerIconId,
            'categories' => $this->getTermPayloads($postId, Plugin::TAXONOMY_CATEGORY),
            'regions' => $this->getTermPayloads($postId, Plugin::TAXONOMY_REGION),
            'permalink' => get_permalink($postId),
            'image_url' => get_the_post_thumbnail_url($postId, 'medium_large') ?: '',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getTermPayloads(int $postId, string $taxonomy): array
    {
        $terms = get_the_terms($postId, $taxonomy);
        $payloads = [];

        if (is_wp_error($terms) || empty($terms)) {
            return $payloads;
        }

        foreach ($terms as $term) {
            $payload = [
                'id' => (int) $term->term_id,
                'name' => (string) $term->name,
                'slug' => (string) $term->slug,
            ];

            if (Plugin::TAXONOMY_CATEGORY === $taxonomy) {
                $payload['marker_icon_id'] = (int) get_term_meta($term->term_id, 'cat_marker_icon', true);
                $payload['popup_name'] = (string) get_term_meta($term->term_id, 'cat_popup_name', true);
                $payload['popup_desc'] = (string) get_term_meta($term->term_id, 'cat_popup_desc', true);
            }

            $payloads[] = $payload;
        }

        return $payloads;
    }

    private function getArgsBuilder(): LocationQueryArgsBuilder
    {
        return $this->argsBuilder ?? new LocationQueryArgsBuilder();
    }
}
