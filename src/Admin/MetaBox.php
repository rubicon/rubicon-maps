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
        add_action('save_post_rubicon_maps_location', [__CLASS__, 'save_meta_box_data']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_scripts']);
    }

    public static function register_meta_box() {
        add_meta_box(
            'rubicon_location_meta',
            __('Location Details', 'rubicon-maps'),
            [__CLASS__, 'render_meta_box'],
            Plugin::POST_TYPE_LOCATION,
            'normal',
            'default'
        );
    }

    public static function render_meta_box($post) {
        wp_nonce_field('rubicon_maps_save_meta', 'rubicon_maps_meta_nonce');

        foreach (self::FIELDS as $key => $type) {
            $value = get_post_meta($post->ID, $key, true);
            echo '<div class="rubicon-field-wrap" style="margin-bottom:16px;">';

            switch ($type) {
                case 'text':
                case 'url':
                    printf(
                        '<label for="%1$s"><strong>%2$s</strong></label><br><input type="%3$s" id="%1$s" name="%1$s" value="%4$s" class="regular-text" />',
                        esc_attr($key),
                        esc_html(ucwords(str_replace('_', ' ', $key))),
                        esc_attr($type),
                        esc_attr($value)
                    );
                    break;

                case 'media':
                    $image_url = $value ? wp_get_attachment_url($value) : '';
                    printf(
                        '<label for="%1$s"><strong>%2$s</strong></label><br>
                        <input type="hidden" id="%1$s" name="%1$s" value="%3$s" />
                        <img id="%1$s-preview" src="%4$s" style="max-width:100px;display:block;margin-top:8px;" />
                        <button type="button" class="button rubicon-upload" data-target="%1$s">%5$s</button>',
                        esc_attr($key),
                        esc_html__('Marker Icon', 'rubicon-maps'),
                        esc_attr($value),
                        esc_url($image_url),
                        esc_html__('Choose Image', 'rubicon-maps')
                    );
                    break;
            }

            echo '</div>';
        }

        echo '<p><strong>' . esc_html__('Popup Content', 'rubicon-maps') . '</strong><br>' .
             esc_html__('This is the main content of the post and will appear in the map popup.', 'rubicon-maps') . '</p>';
    }

    public static function enqueue_admin_scripts() {
        wp_enqueue_media();
        wp_enqueue_script('rubicon-admin-meta', plugins_url('../../assets/js/admin-meta.js', __FILE__), [], Plugin::version(), true);
    }

    public static function save_meta_box_data($post_id) {
        if (!isset($_POST['rubicon_maps_meta_nonce']) || !wp_verify_nonce($_POST['rubicon_maps_meta_nonce'], 'rubicon_maps_save_meta')) {
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
