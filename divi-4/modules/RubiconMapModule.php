<?php

namespace RubiconMaps\Divi4\Modules;

use ET_Builder_Module;
use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

class RubiconMapModule extends ET_Builder_Module
{
    public $slug = 'rubicon_map';
    public $vb_support = 'on';

    public function init(): void
    {
        $this->name = esc_html__('Rubicon Map', 'rubicon-maps');
        $this->whitelisted_fields = ['map_id', 'category', 'region', 'location_ids', 'provider', 'viewport_mode', 'lat', 'lng', 'zoom', 'height', 'auto_fit_padding', 'tile_preset', 'zoom_control', 'scrollwheel', 'double_click_zoom', 'popup_trigger', 'popup_max_width', 'close_on_map_click', 'auto_close_popup', 'open_all_popups', 'enable_clustering', 'cluster_radius'];
        $this->options_toggles = [
            'general' => ['toggles' => ['filters' => esc_html__('Filters', 'rubicon-maps'), 'display' => esc_html__('Map Display', 'rubicon-maps'), 'settings' => esc_html__('Map Settings', 'rubicon-maps'), 'popup' => esc_html__('Popup Settings', 'rubicon-maps'), 'clustering' => esc_html__('Marker Clustering', 'rubicon-maps')]],
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
                'description' => esc_html__('Used to sync map and list by ID.', 'rubicon-maps'),
                'toggle_slug' => 'filters',
            ],
            'category' => [
                'label' => esc_html__('Categories (slugs, comma-separated)', 'rubicon-maps'),
                'type' => 'text',
                'toggle_slug' => 'filters',
            ],
            'region' => [
                'label' => esc_html__('Regions (slugs, comma-separated)', 'rubicon-maps'),
                'type' => 'text',
                'toggle_slug' => 'filters',
            ],
            'location_ids' => [
                'label' => esc_html__('Specific Location IDs', 'rubicon-maps'),
                'type' => 'text',
                'description' => esc_html__('Optional comma-separated IDs to limit this map instance to explicit locations.', 'rubicon-maps'),
                'toggle_slug' => 'filters',
            ],
            'provider' => [
                'label' => esc_html__('Map Provider', 'rubicon-maps'),
                'type' => 'select',
                'options' => ['leaflet' => 'Leaflet'],
                'toggle_slug' => 'filters',
            ],
            'viewport_mode' => [
                'label' => esc_html__('Viewport Mode', 'rubicon-maps'),
                'type' => 'select',
                'options' => ['auto_fit' => 'Auto-fit displayed locations', 'manual' => 'Manual center and zoom'],
                'default' => 'auto_fit',
                'toggle_slug' => 'display',
            ],
            'lat' => [
                'label' => esc_html__('Latitude', 'rubicon-maps'),
                'type' => 'text',
                'default' => '29.7604',
                'toggle_slug' => 'display',
            ],
            'lng' => [
                'label' => esc_html__('Longitude', 'rubicon-maps'),
                'type' => 'text',
                'default' => '-95.3698',
                'toggle_slug' => 'display',
            ],
            'zoom' => [
                'label' => esc_html__('Zoom', 'rubicon-maps'),
                'type' => 'text',
                'default' => '9',
                'toggle_slug' => 'display',
            ],
            'height' => [
                'label' => esc_html__('Height', 'rubicon-maps'),
                'type' => 'text',
                'default' => '480px',
                'toggle_slug' => 'display',
            ],
            'auto_fit_padding' => [
                'label' => esc_html__('Auto-fit Padding', 'rubicon-maps'),
                'type' => 'text',
                'default' => '24',
                'toggle_slug' => 'display',
            ],
            'tile_preset' => [
                'label' => esc_html__('Tile Preset', 'rubicon-maps'),
                'type' => 'select',
                'options' => ['openstreetmap' => 'OpenStreetMap', 'carto_light' => 'CARTO Light', 'carto_dark' => 'CARTO Dark', 'opentopomap' => 'OpenTopoMap', 'custom' => 'Custom Tile URL'],
                'default' => 'openstreetmap',
                'toggle_slug' => 'settings',
            ],
            'zoom_control' => [
                'label' => esc_html__('Show Zoom Control', 'rubicon-maps'),
                'type' => 'yes_no_button',
                'options' => ['off' => 'No', 'on' => 'Yes'],
                'default' => 'on',
                'toggle_slug' => 'settings',
            ],
            'scrollwheel' => [
                'label' => esc_html__('Scroll Wheel Zoom', 'rubicon-maps'),
                'type' => 'yes_no_button',
                'options' => ['off' => 'No', 'on' => 'Yes'],
                'default' => 'on',
                'toggle_slug' => 'settings',
            ],
            'double_click_zoom' => [
                'label' => esc_html__('Double-click Zoom', 'rubicon-maps'),
                'type' => 'yes_no_button',
                'options' => ['off' => 'No', 'on' => 'Yes'],
                'default' => 'on',
                'toggle_slug' => 'settings',
            ],
            'popup_trigger' => [
                'label' => esc_html__('Popup Trigger', 'rubicon-maps'),
                'type' => 'select',
                'options' => ['click' => 'On Click', 'hover' => 'On Hover'],
                'default' => 'click',
                'toggle_slug' => 'popup',
            ],
            'popup_max_width' => [
                'label' => esc_html__('Popup Max Width', 'rubicon-maps'),
                'type' => 'text',
                'default' => '320',
                'toggle_slug' => 'popup',
            ],
            'close_on_map_click' => [
                'label' => esc_html__('Close Popup On Map Click', 'rubicon-maps'),
                'type' => 'yes_no_button',
                'options' => ['off' => 'No', 'on' => 'Yes'],
                'default' => 'on',
                'toggle_slug' => 'popup',
            ],
            'auto_close_popup' => [
                'label' => esc_html__('Auto-close Popup', 'rubicon-maps'),
                'type' => 'yes_no_button',
                'options' => ['off' => 'No', 'on' => 'Yes'],
                'default' => 'on',
                'toggle_slug' => 'popup',
            ],
            'open_all_popups' => [
                'label' => esc_html__('Open All Popups', 'rubicon-maps'),
                'type' => 'yes_no_button',
                'options' => ['off' => 'No', 'on' => 'Yes'],
                'default' => 'off',
                'toggle_slug' => 'popup',
            ],
            'enable_clustering' => [
                'label' => esc_html__('Enable Marker Clustering', 'rubicon-maps'),
                'type' => 'yes_no_button',
                'options' => ['off' => 'No', 'on' => 'Yes'],
                'default' => 'on',
                'toggle_slug' => 'clustering',
            ],
            'cluster_radius' => [
                'label' => esc_html__('Cluster Radius', 'rubicon-maps'),
                'type' => 'text',
                'default' => '100',
                'toggle_slug' => 'clustering',
            ],
        ];
    }

    public function render($attrs, $content, $render_slug): string
    {
        return do_shortcode(sprintf(
            '[%s id="%s" sync_id="%s" category="%s" region="%s" location_ids="%s" provider="%s" viewport_mode="%s" lat="%s" lng="%s" zoom="%s" height="%s" auto_fit_padding="%s" tile_preset="%s" zoom_control="%s" scrollwheel="%s" double_click_zoom="%s" popup_trigger="%s" popup_max_width="%s" close_on_map_click="%s" auto_close_popup="%s" open_all_popups="%s" enable_clustering="%s" cluster_radius="%s"]',
            Plugin::SHORTCODE_MAP,
            esc_attr($this->props['map_id']),
            esc_attr($this->props['map_id']),
            esc_attr($this->props['category']),
            esc_attr($this->props['region']),
            esc_attr($this->props['location_ids']),
            esc_attr($this->props['provider']),
            esc_attr($this->props['viewport_mode']),
            esc_attr($this->props['lat']),
            esc_attr($this->props['lng']),
            esc_attr($this->props['zoom']),
            esc_attr($this->props['height']),
            esc_attr($this->props['auto_fit_padding']),
            esc_attr($this->props['tile_preset']),
            esc_attr($this->props['zoom_control']),
            esc_attr($this->props['scrollwheel']),
            esc_attr($this->props['double_click_zoom']),
            esc_attr($this->props['popup_trigger']),
            esc_attr($this->props['popup_max_width']),
            esc_attr($this->props['close_on_map_click']),
            esc_attr($this->props['auto_close_popup']),
            esc_attr($this->props['open_all_popups']),
            esc_attr($this->props['enable_clustering']),
            esc_attr($this->props['cluster_radius'])
        ));
    }
}
