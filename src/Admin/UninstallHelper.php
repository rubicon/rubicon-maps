<?php
namespace RubiconMaps\Admin;

if (!defined('ABSPATH')) exit;

class UninstallHelper {
    public static function uninstall() {
        global $wpdb;

        // Delete plugin options
        delete_option('rubicon_maps_settings');

        // Delete custom post types
        $posts = get_posts(['post_type' => 'rubicon_maps_location', 'numberposts' => -1]);
        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }

        // Delete terms and meta from taxonomies
        $taxonomies = ['rubicon_maps_category', 'rubicon_maps_region'];
        foreach ($taxonomies as $tax) {
            $terms = get_terms(['taxonomy' => $tax, 'hide_empty' => false]);
            foreach ($terms as $term) {
                wp_delete_term($term->term_id, $tax);
            }
        }

        // Clean up post meta manually if necessary
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('address', 'latitude', 'longitude', 'marker_icon', 'popup_copy')");
    }
}
