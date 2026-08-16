<?php
// SPDX-License-Identifier: GPL-2.0-or-later
namespace RubiconMaps\Admin;

use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) exit;

class UninstallHelper {
    public static function uninstall() {
        global $wpdb;

        // Delete plugin options
        delete_option(Plugin::OPTION_NAME);

        // Delete custom post types
        $posts = get_posts(['post_type' => Plugin::POST_TYPE_LOCATION, 'numberposts' => -1]);
        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }

        // Delete terms and meta from taxonomies
        $taxonomies = [Plugin::TAXONOMY_CATEGORY, Plugin::TAXONOMY_REGION];
        foreach ($taxonomies as $tax) {
            $terms = get_terms(['taxonomy' => $tax, 'hide_empty' => false]);
            foreach ($terms as $term) {
                wp_delete_term($term->term_id, $tax);
            }
        }

        // Clean up post meta manually if necessary
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('address', 'street', 'city', 'state', 'zip', 'country', 'latitude', 'longitude', 'phone', 'email', 'website', 'marker_icon')");
    }
}
