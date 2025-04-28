<?php
namespace RubiconMaps\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class SettingsPage
{

    public function __construct()
    {
        \add_action('admin_menu', [$this, 'add_settings_page']);
        \add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page()
    {
        \add_options_page(
            __('Rubicon Maps Settings', 'rubicon-maps'),
            __('Rubicon Maps', 'rubicon-maps'),
            'manage_options',
            'rubicon-maps-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings()
    {
        \register_setting('rubicon_maps_options', 'rubicon_maps_options', [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);

        \add_settings_section(
            'rubicon_maps_general',
            __('General Settings', 'rubicon-maps'),
            null,
            'rubicon-maps-settings'
        );

        \add_settings_field(
            'default_provider',
            __('Default Map Provider', 'rubicon-maps'),
            [$this, 'render_provider_field'],
            'rubicon-maps-settings',
            'rubicon_maps_general'
        );

        \add_settings_field(
            'google_maps_api_key',
            __('Google Maps API Key', 'rubicon-maps'),
            [$this, 'render_google_api_key_field'],
            'rubicon-maps-settings',
            'rubicon_maps_general'
        );

        \add_settings_field(
            'default_latitude',
            __('Default Latitude', 'rubicon-maps'),
            [$this, 'render_default_latitude_field'],
            'rubicon-maps-settings',
            'rubicon_maps_general'
        );

        \add_settings_field(
            'default_longitude',
            __('Default Longitude', 'rubicon-maps'),
            [$this, 'render_default_longitude_field'],
            'rubicon-maps-settings',
            'rubicon_maps_general'
        );

        \add_settings_field(
            'default_zoom',
            __('Default Zoom Level', 'rubicon-maps'),
            [$this, 'render_default_zoom_field'],
            'rubicon-maps-settings',
            'rubicon_maps_general'
        );

        \add_settings_field(
            'enable_clustering',
            __('Enable Marker Clustering', 'rubicon-maps'),
            [$this, 'render_clustering_field'],
            'rubicon-maps-settings',
            'rubicon_maps_general'
        );
    }

    public function sanitize_settings($input)
    {
        $output = [];

        $output['default_provider'] = in_array($input['default_provider'] ?? '', ['google', 'leaflet'], true) ? $input['default_provider'] : 'google';

        $output['google_maps_api_key'] = sanitize_text_field($input['google_maps_api_key'] ?? '');

        $output['default_latitude'] = sanitize_text_field($input['default_latitude'] ?? '');
        $output['default_longitude'] = sanitize_text_field($input['default_longitude'] ?? '');

        $output['default_zoom'] = absint($input['default_zoom'] ?? 10);

        $output['enable_clustering'] = isset($input['enable_clustering']) ? 1 : 0;

        return $output;
    }

    public function render_settings_page()
    {
        ?> <div class="wrap">
    <h1><?php \esc_html_e('Rubicon Maps Settings', 'rubicon-maps'); ?></h1>
    <form method="post" action="options.php"> <?php
                \settings_fields('rubicon_maps_options');
                \do_settings_sections('rubicon-maps-settings');
                \submit_button();
                ?> </form>
</div> <?php
    }

    public function render_provider_field()
    {
        $options = \get_option('rubicon_maps_options', []);
        ?> <select name="rubicon_maps_options[default_provider]">
    <option value="google" <?php \selected('google', $options['default_provider'] ?? ''); ?>>
        <?php \esc_html_e('Google Maps', 'rubicon-maps'); ?></option>
    <option value="leaflet" <?php \selected('leaflet', $options['default_provider'] ?? ''); ?>>
        <?php \esc_html_e('Leaflet', 'rubicon-maps'); ?></option>
</select> <?php
    }

    public function render_google_api_key_field()
    {
        $options = \get_option('rubicon_maps_options', []);
        ?> <input type="text" name="rubicon_maps_options[google_maps_api_key]"
    value="<?php echo \esc_attr($options['google_maps_api_key'] ?? ''); ?>" size="50" /> <?php
    }

    public function render_default_latitude_field()
    {
        $options = \get_option('rubicon_maps_options', []);
        ?> <input type="text" name="rubicon_maps_options[default_latitude]"
    value="<?php echo \esc_attr($options['default_latitude'] ?? ''); ?>" size="20" /> <?php
    }

    public function render_default_longitude_field()
    {
        $options = \get_option('rubicon_maps_options', []);
        ?> <input type="text" name="rubicon_maps_options[default_longitude]"
    value="<?php echo \esc_attr($options['default_longitude'] ?? ''); ?>" size="20" /> <?php
    }

    public function render_default_zoom_field()
    {
        $options = \get_option('rubicon_maps_options', []);
        ?> <input type="number" name="rubicon_maps_options[default_zoom]"
    value="<?php echo \esc_attr($options['default_zoom'] ?? 10); ?>" min="0" max="20" /> <?php
    }

    public function render_clustering_field()
    {
        $options = \get_option('rubicon_maps_options', []);
        ?> <input type="checkbox" name="rubicon_maps_options[enable_clustering]" value="1"
    <?php \checked(1, $options['enable_clustering'] ?? 0); ?> /> <?php
    }
}