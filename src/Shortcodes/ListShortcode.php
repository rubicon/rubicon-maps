<?php
namespace RubiconMaps\Shortcodes;

use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

class ListShortcode {
    public function __construct() {
        add_shortcode('rubicon_maps_list', [$this, 'render']);
    }

    public function render($atts) {
        $atts = shortcode_atts([
            'id' => 'rubicon-map',
            'category' => '',
            'region' => '',
        ], $atts);

        $query_args = [
            'post_type' => 'rubicon_maps_location',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ];

        if (!empty($atts['category'])) {
            $query_args['tax_query'][] = [
                'taxonomy' => 'location_category',
                'field'    => 'slug',
                'terms'    => explode(',', $atts['category']),
            ];
        }

        if (!empty($atts['region'])) {
            $query_args['tax_query'][] = [
                'taxonomy' => 'rubicon_maps_region',
                'field'    => 'slug',
                'terms'    => explode(',', $atts['region']),
            ];
        }

        $query = new WP_Query($query_args);
        ob_start();

        if ($query->have_posts()) {
            echo '<ul class="rubicon-maps-list" data-map-id="' . esc_attr($atts['id']) . '">';
            while ($query->have_posts()) {
                $query->the_post();
                echo '<li class="rubicon-location-item" data-lat="' . esc_attr(get_post_meta(get_the_ID(), 'latitude', true)) . '" data-lng="' . esc_attr(get_post_meta(get_the_ID(), 'longitude', true)) . '">';
                echo '<strong>' . esc_html(get_the_title()) . '</strong><br>' . esc_html(get_post_meta(get_the_ID(), 'address', true));
                echo '</li>';
            }
            echo '</ul>';
        }

        wp_reset_postdata();
        return ob_get_clean();
    }
}
