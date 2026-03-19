<?php
namespace RubiconMaps\Shortcodes;

use RubiconMaps\Frontend\MapRenderer;
use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

class MapShortcode {
    public function __construct(private readonly ?MapRenderer $renderer = null) {
        add_shortcode(Plugin::SHORTCODE_MAP, [$this, 'render']);
    }

    public function render($atts) {
        return $this->getRenderer()->render(shortcode_atts([
            'id' => '',
            'sync_id' => '',
            'provider' => '',
            'viewport_mode' => '',
            'lat' => '',
            'lng' => '',
            'zoom' => '',
            'category' => '',
            'region' => '',
            'location_ids' => '',
            'height' => '',
            'auto_fit_padding' => '',
            'tile_preset' => '',
            'zoom_control' => '',
            'scrollwheel' => '',
            'double_click_zoom' => '',
            'popup_trigger' => '',
            'popup_max_width' => '',
            'close_on_map_click' => '',
            'auto_close_popup' => '',
            'open_all_popups' => '',
            'enable_clustering' => '',
            'cluster_radius' => '',
        ], $atts, Plugin::SHORTCODE_MAP));
    }

    private function getRenderer(): MapRenderer
    {
        return $this->renderer ?? new MapRenderer();
    }
}
