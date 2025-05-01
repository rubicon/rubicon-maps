<?php
namespace RubiconMaps\Divi;

use ET_Builder_Module;

class RubiconMapModule extends ET_Builder_Module {
    public $slug       = 'rubicon_map';
    public $vb_support = 'on';

    function init() {
        $this->name = esc_html__('Rubicon Map', 'rubicon-maps');
        $this->whitelisted_fields = ['map_id', 'category', 'provider', 'lat', 'lng', 'zoom'];
        $this->options_toggles = [
            'general' => ['toggles' => ['settings' => esc_html__('Settings', 'rubicon-maps')]],
        ];
        $this->main_css_element = '%%order_class%%';
        $this->advanced_fields = [];
    }

    function get_fields() {
        return [
            'map_id' => [
                'label'           => esc_html__('Map ID', 'rubicon-maps'),
                'type'            => 'text',
                'description'     => esc_html__('Used to sync map and list by ID.', 'rubicon-maps'),
                'toggle_slug'     => 'settings',
            ],
            'category' => [
                'label'           => esc_html__('Categories (slugs, comma-separated)', 'rubicon-maps'),
                'type'            => 'text',
                'toggle_slug'     => 'settings',
            ],
            'provider' => [
                'label'           => esc_html__('Map Provider', 'rubicon-maps'),
                'type'            => 'select',
                'options'         => ['leaflet' => 'Leaflet', 'google' => 'Google'],
                'toggle_slug'     => 'settings',
            ],
            'lat' => [
                'label'           => esc_html__('Latitude', 'rubicon-maps'),
                'type'            => 'text',
                'default'         => '40.0',
                'toggle_slug'     => 'settings',
            ],
            'lng' => [
                'label'           => esc_html__('Longitude', 'rubicon-maps'),
                'type'            => 'text',
                'default'         => '-100.0',
                'toggle_slug'     => 'settings',
            ],
            'zoom' => [
                'label'           => esc_html__('Zoom', 'rubicon-maps'),
                'type'            => 'text',
                'default'         => '4',
                'toggle_slug'     => 'settings',
            ],
        ];
    }

    function render($attrs, $content = null, $render_slug) {
        return do_shortcode(sprintf(
            '[rubicon_map id="%s" category="%s" provider="%s" lat="%s" lng="%s" zoom="%s"]',
            esc_attr($this->props['map_id']),
            esc_attr($this->props['category']),
            esc_attr($this->props['provider']),
            esc_attr($this->props['lat']),
            esc_attr($this->props['lng']),
            esc_attr($this->props['zoom'])
        ));
    }
}
