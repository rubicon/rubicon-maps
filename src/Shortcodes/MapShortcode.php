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
            'provider' => '',
            'lat' => '',
            'lng' => '',
            'zoom' => '',
            'category' => '',
            'region' => '',
            'location_ids' => '',
            'height' => '',
            'scrollwheel' => '',
        ], $atts, Plugin::SHORTCODE_MAP));
    }

    private function getRenderer(): MapRenderer
    {
        return $this->renderer ?? new MapRenderer();
    }
}
