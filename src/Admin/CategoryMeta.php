<?php
namespace RubiconMaps\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class CategoryMeta {
    public static function init() {
        add_action('location_category_add_form_fields', [__CLASS__, 'add_category_fields']);
        add_action('location_category_edit_form_fields', [__CLASS__, 'edit_category_fields']);
        add_action('created_location_category', [__CLASS__, 'save_category_meta']);
        add_action('edited_location_category', [__CLASS__, 'save_category_meta']);
    }

    public static function add_category_fields() {
        ?>
        <div class="form-field">
            <label for="cat_marker_icon"><?php _e('Marker Icon', 'rubicon-maps'); ?></label>
            <input type="text" name="cat_marker_icon" id="cat_marker_icon" value="" />
        </div>
        <div class="form-field">
            <label for="cat_popup_name"><?php _e('Popup Name', 'rubicon-maps'); ?></label>
            <input type="text" name="cat_popup_name" id="cat_popup_name" value="" />
        </div>
        <div class="form-field">
            <label for="cat_popup_desc"><?php _e('Popup Description', 'rubicon-maps'); ?></label>
            <textarea name="cat_popup_desc" id="cat_popup_desc" rows="5" cols="40"></textarea>
        </div>
        <?php
    }

    public static function edit_category_fields($term) {
        $marker_icon = get_term_meta($term->term_id, 'cat_marker_icon', true);
        $popup_name = get_term_meta($term->term_id, 'cat_popup_name', true);
        $popup_desc = get_term_meta($term->term_id, 'cat_popup_desc', true);
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="cat_marker_icon"><?php _e('Marker Icon', 'rubicon-maps'); ?></label></th>
            <td><input type="text" name="cat_marker_icon" id="cat_marker_icon" value="<?php echo esc_attr($marker_icon); ?>" /></td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="cat_popup_name"><?php _e('Popup Name', 'rubicon-maps'); ?></label></th>
            <td><input type="text" name="cat_popup_name" id="cat_popup_name" value="<?php echo esc_attr($popup_name); ?>" /></td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="cat_popup_desc"><?php _e('Popup Description', 'rubicon-maps'); ?></label></th>
            <td><textarea name="cat_popup_desc" id="cat_popup_desc" rows="5" cols="40"><?php echo esc_textarea($popup_desc); ?></textarea></td>
        </tr>
        <?php
    }

    public static function save_category_meta($term_id) {
        if (isset($_POST['cat_marker_icon'])) {
            update_term_meta($term_id, 'cat_marker_icon', sanitize_text_field($_POST['cat_marker_icon']));
        }
        if (isset($_POST['cat_popup_name'])) {
            update_term_meta($term_id, 'cat_popup_name', sanitize_text_field($_POST['cat_popup_name']));
        }
        if (isset($_POST['cat_popup_desc'])) {
            update_term_meta($term_id, 'cat_popup_desc', wp_kses_post($_POST['cat_popup_desc']));
        }
    }
}
