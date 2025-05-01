<?php
namespace RubiconMaps\Shortcodes;

if (!defined('ABSPATH')) {
    exit;
}

class MapShortcode {
    public function __construct() {
        add_shortcode('rubicon_maps', [$this, 'render']);
    }

    public function render($atts) {
        $atts = shortcode_atts([
            'id' => 'rubicon-map',
            'provider' => 'leaflet',
            'lat' => '0',
            'lng' => '0',
            'zoom' => '2',
            'category' => '',
            'region' => '',
            'width' => '100%',
            'height' => '400px'
        ], $atts);

        ob_start();
        ?>
        <div id="<?php echo esc_attr($atts['id']); ?>" class="rubicon-maps-container"
            data-provider="<?php echo esc_attr($atts['provider']); ?>"
            data-lat="<?php echo esc_attr($atts['lat']); ?>"
            data-lng="<?php echo esc_attr($atts['lng']); ?>"
            data-zoom="<?php echo esc_attr($atts['zoom']); ?>"
            data-category="<?php echo esc_attr($atts['category']); ?>"
            data-region="<?php echo esc_attr($atts['region']); ?>"
            style="width:<?php echo esc_attr($atts['width']); ?>;height:<?php echo esc_attr($atts['height']); ?>">
            <div id="<?php echo esc_attr($atts['id']); ?>-map" class="rubicon-map-inner"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
