<?php
class Rubicon_Maps_Settings_Page {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_options_page(
            __('Rubicon Maps Settings', 'rubicon-maps'),
            __('Rubicon Maps', 'rubicon-maps'),
            'manage_options',
            'rubicon-maps-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('rubicon_maps_options', 'rubicon_maps_options');

        add_settings_section('rubicon_maps_general', __('General Settings', 'rubicon-maps'), null, 'rubicon-maps-settings');

        add_settings_field(
            'default_provider',
            __('Default Map Provider', 'rubicon-maps'),
            [$this, 'render_provider_field'],
            'rubicon-maps-settings',
            'rubicon_maps_general'
        );

        add_settings_field(
            'google_maps_api_key',
            __('Google Maps API Key', 'rubicon-maps'),
            [$this, 'render_api_key_field'],
            'rubicon-maps-settings',
            'rubicon_maps_general'
        );

        // TODO: Add all the fields mentioned above...
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Rubicon Maps Settings', 'rubicon-maps'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('rubicon_maps_options');
                do_settings_sections('rubicon-maps-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_provider_field() {
        $options = get_option('rubicon_maps_options');
        ?>
        <select name="rubicon_maps_options[default_provider]">
            <option value="google" <?php selected('google', $options['default_provider'] ?? ''); ?>><?php _e('Google Maps', 'rubicon-maps'); ?></option>
            <option value="leaflet" <?php selected('leaflet', $options['default_provider'] ?? ''); ?>><?php _e('Leaflet', 'rubicon-maps'); ?></option>
        </select>
        <?php
    }

    public function render_api_key_field() {
        $options = get_option('rubicon_maps_options');
        ?>
        <input type="text" name="rubicon_maps_options[google_maps_api_key]" value="<?php echo esc_attr($options['google_maps_api_key'] ?? ''); ?>" size="50" />
        <?php
    }
}

new Rubicon_Maps_Settings_Page();
?>
