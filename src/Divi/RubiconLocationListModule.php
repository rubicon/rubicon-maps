<?php
namespace RubiconMaps\Divi;

use ET_Builder_Module;

class RubiconLocationListModule extends ET_Builder_Module {
    public $slug       = 'rubicon_location_list';
    public $vb_support = 'on';

    function init() {
        $this->name = esc_html__('Rubicon Location List', 'rubicon-maps');
        $this->whitelisted_fields = ['map_id', 'category'];
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
                'description'     => esc_html__('Used to sync list with map module by ID.', 'rubicon-maps'),
                'toggle_slug'     => 'settings',
            ],
            'category' => [
                'label'           => esc_html__('Categories (slugs, comma-separated)', 'rubicon-maps'),
                'type'            => 'text',
                'toggle_slug'     => 'settings',
            ],
        ];
    }

    function render($attrs, $content = null, $render_slug) {
        return do_shortcode(sprintf(
            '[rubicon_location_list id="%s" category="%s"]',
            esc_attr($this->props['map_id']),
            esc_attr($this->props['category'])
        ));
    }
}
