<?php
namespace RubiconMaps\Admin;

if (!defined('ABSPATH')) exit;

class SettingsPage {
    private static $option_group = 'rubicon_maps_settings';
    private static $option_name = 'rubicon_maps_options';

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function add_menu() {
        add_menu_page(
            __('Rubicon Maps Settings', 'rubicon-maps'),
            __('Rubicon Maps', 'rubicon-maps'),
            'manage_options',
            'rubicon_maps_settings',
            [__CLASS__, 'render_settings_page'],
            'dashicons-location-alt',
            56
        );
    }

    public static function register_settings() {
        register_setting(self::$option_group, self::$option_name, [__CLASS__, 'sanitize']);

        add_settings_section('general', __('General Settings', 'rubicon-maps'), '__return_false', self::$option_name);

        add_settings_field('tile_url', __('Map Tile URL', 'rubicon-maps'), [__CLASS__, 'field_tile_url'], self::$option_name, 'general');
        add_settings_field('default_lat', __('Default Latitude', 'rubicon-maps'), [__CLASS__, 'field_text'], self::$option_name, 'general', ['id' => 'default_lat']);
        add_settings_field('default_lng', __('Default Longitude', 'rubicon-maps'), [__CLASS__, 'field_text'], self::$option_name, 'general', ['id' => 'default_lng']);
        add_settings_field('default_zoom', __('Default Zoom', 'rubicon-maps'), [__CLASS__, 'field_number'], self::$option_name, 'general', ['id' => 'default_zoom']);
        add_settings_field('autocomplete_provider', __('Autocomplete Provider', 'rubicon-maps'), [__CLASS__, 'field_autocomplete'], self::$option_name, 'general');
    }

    public static function sanitize($input) {
        return [
            'tile_url'             => esc_url_raw($input['tile_url'] ?? ''),
            'default_lat'          => sanitize_text_field($input['default_lat'] ?? ''),
            'default_lng'          => sanitize_text_field($input['default_lng'] ?? ''),
            'default_zoom'         => absint($input['default_zoom'] ?? 10),
            'autocomplete_provider'=> in_array($input['autocomplete_provider'] ?? '', ['osm', 'google']) ? $input['autocomplete_provider'] : 'osm',
        ];
    }

    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Rubicon Maps Settings', 'rubicon-maps'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields(self::$option_group);
                do_settings_sections(self::$option_name);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public static function field_tile_url() {
        $options = get_option(self::$option_name);
        ?>
        <input type="text" name="<?php echo self::$option_name; ?>[tile_url]" value="<?php echo esc_attr($options['tile_url'] ?? ''); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Example: https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', 'rubicon-maps'); ?></p>
        <?php
    }

    public static function field_text($args) {
        $options = get_option(self::$option_name);
        $id = $args['id'];
        ?>
        <input type="text" name="<?php echo self::$option_name; ?>[<?php echo $id; ?>]" value="<?php echo esc_attr($options[$id] ?? ''); ?>" class="regular-text" />
        <?php
    }

    public static function field_number($args) {
        $options = get_option(self::$option_name);
        $id = $args['id'];
        ?>
        <input type="number" name="<?php echo self::$option_name; ?>[<?php echo $id; ?>]" value="<?php echo esc_attr($options[$id] ?? 10); ?>" class="small-text" />
        <?php
    }

    public static function field_autocomplete() {
        $options = get_option(self::$option_name);
        ?>
        <select name="<?php echo self::$option_name; ?>[autocomplete_provider]">
            <option value="osm" <?php selected($options['autocomplete_provider'] ?? '', 'osm'); ?>>OpenStreetMap (Nominatim)</option>
            <option value="google" <?php selected($options['autocomplete_provider'] ?? '', 'google'); ?>>Google Maps</option>
        </select>
        <?php
    }
}
