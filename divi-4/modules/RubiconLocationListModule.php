<?php

namespace RubiconMaps\Divi4\Modules;

use ET_Builder_Module;
use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

class RubiconLocationListModule extends ET_Builder_Module
{
    public $slug = 'rubicon_location_list';
    public $vb_support = 'on';

    public function init(): void
    {
        $this->name = esc_html__('Rubicon Location List', 'rubicon-maps');
        $this->whitelisted_fields = ['map_id', 'category', 'region', 'location_ids', 'use_fixed_height', 'height'];
        $this->options_toggles = [
            'general' => ['toggles' => ['filters' => esc_html__('Filters', 'rubicon-maps'), 'display' => esc_html__('List Display', 'rubicon-maps')]],
        ];
        $this->main_css_element = '%%order_class%%';
        $this->advanced_fields = [];
        $this->category = esc_html__('RubiconTV', 'rubicon-maps');
    }

    public function get_fields(): array
    {
        return [
            'map_id' => [
                'label' => esc_html__('Map ID', 'rubicon-maps'),
                'type' => 'text',
                'description' => esc_html__('Used to sync list with map module by ID.', 'rubicon-maps'),
                'toggle_slug' => 'filters',
            ],
            'category' => [
                'label' => esc_html__('Categories', 'rubicon-maps'),
                'type' => 'text',
                'description' => esc_html__('Search and select category terms to include in this listing.', 'rubicon-maps'),
                'id' => 'rubicon-list-category-filter',
                'toggle_slug' => 'filters',
            ],
            'region' => [
                'label' => esc_html__('Regions', 'rubicon-maps'),
                'type' => 'text',
                'description' => esc_html__('Search and select region terms to include in this listing.', 'rubicon-maps'),
                'id' => 'rubicon-list-region-filter',
                'toggle_slug' => 'filters',
            ],
            'location_ids' => [
                'label' => esc_html__('Specific Locations', 'rubicon-maps'),
                'type' => 'text',
                'description' => esc_html__('Search and select explicit locations to include in this listing.', 'rubicon-maps'),
                'id' => 'rubicon-list-location-filter',
                'toggle_slug' => 'filters',
            ],
            'use_fixed_height' => [
                'label' => esc_html__('Use Fixed List Height', 'rubicon-maps'),
                'type' => 'yes_no_button',
                'options' => ['off' => 'No', 'on' => 'Yes'],
                'default' => 'off',
                'toggle_slug' => 'display',
            ],
            'height' => [
                'label' => esc_html__('List Height', 'rubicon-maps'),
                'type' => 'text',
                'default' => '',
                'toggle_slug' => 'display',
            ],
        ];
    }

    public function render($attrs, $content, $render_slug): string
    {
        return do_shortcode(sprintf(
            '[%s id="%s" sync_id="%s" category="%s" region="%s" location_ids="%s" use_fixed_height="%s" height="%s"]',
            Plugin::SHORTCODE_LIST,
            esc_attr($this->props['map_id']),
            esc_attr($this->props['map_id']),
            esc_attr($this->props['category']),
            esc_attr($this->props['region']),
            esc_attr($this->props['location_ids']),
            esc_attr($this->props['use_fixed_height']),
            esc_attr($this->props['height'])
        ));
    }
}
