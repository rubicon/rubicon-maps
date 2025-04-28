<?php
namespace RubiconMaps\Shortcodes;

use RubiconMaps\Helpers\SettingsHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class LocationList {

    public function __construct() {
        add_shortcode('rubicon_location_list', [$this, 'render_location_list']);
        add_action('wp_enqueue_scripts', [$this, 'conditionally_enqueue_assets']);
    }

    public function conditionally_enqueue_assets() {
        if ( ! is_singular() ) {
            return; // Only load on single posts/pages for now (optimize later)
        }

        global $post;
        if ( has_shortcode($post->post_content, 'rubicon_location_list') ) {
            $provider = SettingsHelper::get_option('default_provider', 'google');

            if ( 'google' === $provider ) {
                wp_enqueue_script('rubicon-maps-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . SettingsHelper::get_option('google_maps_api_key', ''), [], null, true);
            } elseif ( 'leaflet' === $provider ) {
                wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');
                wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true);
            }

            // Your plugin frontend JS
            wp_enqueue_script('rubicon-maps-frontend', plugins_url('/assets/js/rubicon-maps-frontend.js', RUBICON_MAPS_PLUGIN_FILE), ['jquery'], RUBICON_MAPS_VERSION, true);
            wp_enqueue_style('rubicon-maps-frontend', plugins_url('/assets/css/rubicon-maps-frontend.css', RUBICON_MAPS_PLUGIN_FILE), [], RUBICON_MAPS_VERSION);
        }
    }

    public function render_location_list($atts = [], $content = null) {
        $atts = shortcode_atts([], $atts, 'rubicon_location_list');

        $latitude = SettingsHelper::get_option('default_latitude', '37.7749');
        $longitude = SettingsHelper::get_option('default_longitude', '-122.4194');
        $zoom = SettingsHelper::get_option('default_zoom', 10);
        $provider = SettingsHelper::get_option('default_provider', 'google');

        ob_start();
        ?>
        <div id="rubicon-maps-container" 
            data-lat="<?php echo esc_attr($latitude); ?>" 
            data-lng="<?php echo esc_attr($longitude); ?>" 
            data-zoom="<?php echo esc_attr($zoom); ?>"
            data-provider="<?php echo esc_attr($provider); ?>">
            <div id="rubicon-maps-map" style="width: 100%; height: 400px;"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
