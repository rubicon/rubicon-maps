<?php
namespace RubiconMaps\Admin;

use RubiconMaps\Support\Plugin;

class MetaBox {
    const FIELDS = [
        'street'      => 'text',
        'city'        => 'text',
        'state'       => 'text',
        'zip'         => 'text',
        'country'     => 'text',
        'latitude'    => 'text',
        'longitude'   => 'text',
        'phone'       => 'text',
        'email'       => 'text',
        'website'     => 'url',
        'marker_icon' => 'media',
    ];

    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'register_meta_box']);
        add_action('save_post_' . Plugin::POST_TYPE_LOCATION, [__CLASS__, 'save_meta_box_data']);
    }

    public static function register_meta_box() {
        add_meta_box(
            'rtv_rm_location_meta',
            __('Location Details', 'rubicon-maps'),
            [__CLASS__, 'render_meta_box'],
            Plugin::POST_TYPE_LOCATION,
            'normal',
            'default'
        );
    }

    public static function render_meta_box($post) {
        wp_nonce_field('rtv_rm_save_meta', 'rtv_rm_meta_nonce');
        echo '<div class="rubicon-admin-panel">';
        echo '<div class="rubicon-admin-panel__header">';
        echo '<div><p class="rubicon-admin-panel__eyebrow">' . esc_html__('Location data', 'rubicon-maps') . '</p><h2 class="rubicon-admin-panel__title">' . esc_html__('Map-ready details', 'rubicon-maps') . '</h2></div>';
        echo '<p class="rubicon-admin-panel__copy">' . esc_html__('Use the search helper to fill the structured address fields, then refine anything that needs to be location-specific.', 'rubicon-maps') . '</p>';
        echo '</div>';
        echo '<div class="rubicon-geocode-search"><label for="rubicon-geocode-query"><strong>' . esc_html__('Find address', 'rubicon-maps') . '</strong></label><div class="rubicon-geocode-search__controls"><input type="text" id="rubicon-geocode-query" class="regular-text" autocomplete="off" placeholder="' . esc_attr__('Search address…', 'rubicon-maps') . '" /><button type="button" class="button button-secondary" id="rubicon-geocode-button">' . esc_html__('Lookup', 'rubicon-maps') . '</button></div><div class="rubicon-geocode-results" id="rubicon-geocode-results" hidden></div><p class="description" id="rubicon-geocode-status">' . esc_html__('Start typing to see matching addresses and populate the structured address and coordinates.', 'rubicon-maps') . '</p></div>';
        echo '<div class="rubicon-admin-grid">';

        foreach (self::FIELDS as $key => $type) {
            $value = get_post_meta($post->ID, $key, true);
            echo '<div class="rubicon-field-wrap">';

            switch ($type) {
                case 'text':
                case 'url':
                    printf(
                        '<label for="%1$s"><strong>%2$s</strong></label><input type="%3$s" id="%1$s" name="%1$s" value="%4$s" class="regular-text" />',
                        esc_attr($key),
                        esc_html(ucwords(str_replace('_', ' ', $key))),
                        esc_attr($type),
                        esc_attr($value)
                    );
                    break;

                case 'media':
                    $image_url = $value ? wp_get_attachment_url($value) : '';
                    printf(
                        '<label for="%1$s"><strong>%2$s</strong></label>
                        <input type="hidden" id="%1$s" name="%1$s" value="%3$s" />
                        <img id="%1$s-preview" src="%4$s" class="rubicon-image-preview%6$s" />
                        <button type="button" class="button rubicon-upload" data-target="%1$s">%5$s</button>',
                        esc_attr($key),
                        esc_html__('Marker Icon', 'rubicon-maps'),
                        esc_attr($value),
                        esc_url($image_url),
                        esc_html__('Choose Image', 'rubicon-maps'),
                        $image_url ? '' : ' is-hidden'
                    );
                    break;
            }

            echo '</div>';
        }

        echo '</div>';
        echo '<p><strong>' . esc_html__('Popup Content', 'rubicon-maps') . '</strong><br>' .
             esc_html__('Use the WordPress title, excerpt, featured image, and main content editor to control each location card, popup, and detail view.', 'rubicon-maps') . '</p>';
        echo '</div>';
    }

    public static function save_meta_box_data($post_id) {
        if (!isset($_POST['rtv_rm_meta_nonce']) || !wp_verify_nonce($_POST['rtv_rm_meta_nonce'], 'rtv_rm_save_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (!current_user_can('edit_post', $post_id)) return;

        foreach (self::FIELDS as $key => $type) {
            if (isset($_POST[$key])) {
                switch ($type) {
                    case 'url':
                        $val = esc_url_raw($_POST[$key]);
                        break;
                    default:
                        $val = sanitize_text_field($_POST[$key]);
                }
                update_post_meta($post_id, $key, $val);
            }
        }
    }
}
