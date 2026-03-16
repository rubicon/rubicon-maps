<?php
namespace RubiconMaps\Shortcodes;

use RubiconMaps\Frontend\LocationListRenderer;
use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

class ListShortcode {
    public function __construct(private readonly ?LocationListRenderer $renderer = null) {
        add_shortcode(Plugin::SHORTCODE_LIST, [$this, 'render']);
    }

    public function render($atts) {
        return $this->getRenderer()->render(shortcode_atts([
            'id' => '',
            'category' => '',
            'region' => '',
            'location_ids' => '',
            'posts_per_page' => -1,
        ], $atts, Plugin::SHORTCODE_LIST));
    }

    private function getRenderer(): LocationListRenderer
    {
        return $this->renderer ?? new LocationListRenderer();
    }
}
