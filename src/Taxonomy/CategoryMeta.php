<?php
namespace RubiconMaps\Taxonomy;

class CategoryMeta {

	public static function init() {
		add_action('location_category_add_form_fields', [__CLASS__, 'add_term_fields']);
		add_action('location_category_edit_form_fields', [__CLASS__, 'edit_term_fields'], 10, 2);
		add_action('created_location_category', [__CLASS__, 'save_term_meta']);
		add_action('edited_location_category', [__CLASS__, 'save_term_meta']);
		add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_media_script']);
	}

	public static function enqueue_media_script() {
		wp_enqueue_media();
		wp_enqueue_script('rubicon-cat-meta', plugins_url('../../assets/js/admin-cat-meta.js', __FILE__), ['jquery'], null, true);
	}

	public static function add_term_fields($taxonomy) {
		?>
		<div class="form-field">
			<label for="cat_marker_icon"><?php _e('Marker Icon', 'rubicon-maps'); ?></label>
			<input type="hidden" name="cat_marker_icon" id="cat_marker_icon" value="" />
			<img id="cat_marker_icon-preview" src="" style="max-width:100px;display:none;margin-top:8px;" />
			<button type="button" class="button rubicon-upload" data-target="cat_marker_icon"><?php _e('Choose Image', 'rubicon-maps'); ?></button>
		</div>

		<div class="form-field">
			<label for="cat_popup_name"><?php _e('Popup Name Override', 'rubicon-maps'); ?></label>
			<input type="text" name="cat_popup_name" id="cat_popup_name" value="" />
		</div>

		<div class="form-field">
			<label for="cat_popup_desc"><?php _e('Popup Description', 'rubicon-maps'); ?></label>
			<?php wp_editor('', 'cat_popup_desc', [
				'textarea_name' => 'cat_popup_desc',
				'media_buttons' => true,
				'textarea_rows' => 5,
			]); ?>
		</div>
		<?php
	}

	public static function edit_term_fields($term, $taxonomy) {
		$marker = get_term_meta($term->term_id, 'cat_marker_icon', true);
		$name   = get_term_meta($term->term_id, 'cat_popup_name', true);
		$desc   = get_term_meta($term->term_id, 'cat_popup_desc', true);
		?>
		<tr class="form-field">
			<th scope="row"><label for="cat_marker_icon"><?php _e('Marker Icon', 'rubicon-maps'); ?></label></th>
			<td>
				<input type="hidden" name="cat_marker_icon" id="cat_marker_icon" value="<?php echo esc_attr($marker); ?>" />
				<img id="cat_marker_icon-preview" src="<?php echo esc_url(wp_get_attachment_url($marker)); ?>" style="max-width:100px;<?php echo $marker ? '' : 'display:none;'; ?>margin-top:8px;" />
				<button type="button" class="button rubicon-upload" data-target="cat_marker_icon"><?php _e('Choose Image', 'rubicon-maps'); ?></button>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="cat_popup_name"><?php _e('Popup Name Override', 'rubicon-maps'); ?></label></th>
			<td><input type="text" name="cat_popup_name" id="cat_popup_name" value="<?php echo esc_attr($name); ?>" /></td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="cat_popup_desc"><?php _e('Popup Description', 'rubicon-maps'); ?></label></th>
			<td>
				<?php wp_editor($desc, 'cat_popup_desc', [
					'textarea_name' => 'cat_popup_desc',
					'media_buttons' => true,
					'textarea_rows' => 5,
				]); ?>
			</td>
		</tr>
		<?php
	}

	public static function save_term_meta($term_id) {
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
