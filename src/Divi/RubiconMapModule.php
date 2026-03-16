<?php
namespace RubiconMaps\Divi;

use ET_Builder_Module;
use RubiconMaps\Support\Plugin;

class RubiconMapModule extends ET_Builder_Module {
    public $slug       = 'rubicon_map';
    public $vb_support = 'on';

    function init() {
        $this->name = esc_html__('Rubicon Map', 'rubicon-maps');
        $this->whitelisted_fields = ['map_id', 'category', 'region', 'location_ids', 'provider', 'lat', 'lng', 'zoom', 'height'];
        $this->options_toggles = [
            'general' => ['toggles' => ['filters' => esc_html__('Filters', 'rubicon-maps'), 'map' => esc_html__('Map', 'rubicon-maps')]],
        ];
        $this->main_css_element = '%%order_class%%';
        $this->advanced_fields = [];
        $this->category = esc_html__('RubiconTV', 'rubicon-maps');
    }

    function get_fields() {
        return [
            'map_id' => [
                'label'           => esc_html__('Map ID', 'rubicon-maps'),
                'type'            => 'text',
                'description'     => esc_html__('Used to sync map and list by ID.', 'rubicon-maps'),
                'toggle_slug'     => 'filters',
            ],
            'category' => [
                'label'           => esc_html__('Categories (slugs, comma-separated)', 'rubicon-maps'),
                'type'            => 'text',
                'toggle_slug'     => 'filters',
            ],
            'region' => [
                'label'           => esc_html__('Regions (slugs, comma-separated)', 'rubicon-maps'),
                'type'            => 'text',
                'toggle_slug'     => 'filters',
            ],
            'location_ids' => [
                'label'           => esc_html__('Specific Location IDs', 'rubicon-maps'),
                'type'            => 'text',
                'description'     => esc_html__('Optional comma-separated IDs to limit this map instance to explicit locations.', 'rubicon-maps'),
                'toggle_slug'     => 'filters',
            ],
            'provider' => [
                'label'           => esc_html__('Map Provider', 'rubicon-maps'),
                'type'            => 'select',
                'options'         => ['leaflet' => 'Leaflet', 'google' => 'Google'],
                'toggle_slug'     => 'map',
            ],
            'lat' => [
                'label'           => esc_html__('Latitude', 'rubicon-maps'),
                'type'            => 'text',
                'default'         => '29.7604',
                'toggle_slug'     => 'map',
            ],
            'lng' => [
                'label'           => esc_html__('Longitude', 'rubicon-maps'),
                'type'            => 'text',
                'default'         => '-95.3698',
                'toggle_slug'     => 'map',
            ],
            'zoom' => [
                'label'           => esc_html__('Zoom', 'rubicon-maps'),
                'type'            => 'text',
                'default'         => '9',
                'toggle_slug'     => 'map',
            ],
            'height' => [
                'label'           => esc_html__('Height', 'rubicon-maps'),
                'type'            => 'text',
                'default'         => '480px',
                'toggle_slug'     => 'map',
            ],
        ];
    }

    function render($attrs, $content, $render_slug) {
        return do_shortcode(sprintf(
            '[%s id="%s" category="%s" region="%s" location_ids="%s" provider="%s" lat="%s" lng="%s" zoom="%s" height="%s"]',
            Plugin::SHORTCODE_MAP,
            esc_attr($this->props['map_id']),
            esc_attr($this->props['category']),
            esc_attr($this->props['region']),
            esc_attr($this->props['location_ids']),
            esc_attr($this->props['provider']),
            esc_attr($this->props['lat']),
            esc_attr($this->props['lng']),
            esc_attr($this->props['zoom']),
            esc_attr($this->props['height'])
        ));
    }
}
