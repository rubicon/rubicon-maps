<?php
namespace RubiconMaps\Divi;

use ET_Builder_Module;
use RubiconMaps\Support\Plugin;

class RubiconLocationListModule extends ET_Builder_Module {
    public $slug       = 'rubicon_location_list';
    public $vb_support = 'on';

    function init() {
        $this->name = esc_html__('Rubicon Location List', 'rubicon-maps');
        $this->whitelisted_fields = ['map_id', 'category', 'region', 'location_ids'];
        $this->options_toggles = [
            'general' => ['toggles' => ['filters' => esc_html__('Filters', 'rubicon-maps')]],
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
                'description'     => esc_html__('Used to sync list with map module by ID.', 'rubicon-maps'),
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
                'description'     => esc_html__('Optional comma-separated IDs to limit this list instance to explicit locations.', 'rubicon-maps'),
                'toggle_slug'     => 'filters',
            ],
        ];
    }

    function render($attrs, $content, $render_slug) {
        return do_shortcode(sprintf(
            '[%s id="%s" category="%s" region="%s" location_ids="%s"]',
            Plugin::SHORTCODE_LIST,
            esc_attr($this->props['map_id']),
            esc_attr($this->props['category']),
            esc_attr($this->props['region']),
            esc_attr($this->props['location_ids'])
        ));
    }
}
