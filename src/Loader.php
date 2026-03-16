<?php
namespace RubiconMaps;

use RubiconMaps\PostType\Location;
use RubiconMaps\Taxonomy\LocationCategory;
use RubiconMaps\Taxonomy\Region;
use RubiconMaps\Admin\SettingsPage;
use RubiconMaps\Admin\MetaBox;
use RubiconMaps\Admin\CategoryMeta;
use RubiconMaps\Rest\LocationsEndpoint;
use RubiconMaps\Shortcodes\ListShortcode;
use RubiconMaps\Shortcodes\MapShortcode;

if (!defined('ABSPATH')) {
    exit;
}

class Loader
{
    private static $instance = null;

    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
            self::$instance->setup_hooks();
        }
        return self::$instance;
    }

    private function setup_hooks()
    {
        add_action('init', [Location::class, 'register']);
        add_action('init', [LocationCategory::class, 'register']);
        add_action('init', [Region::class, 'register']);
        add_action('rest_api_init', [LocationsEndpoint::class, 'register_routes']);

        if (is_admin()) {
            SettingsPage::init();
            MetaBox::init();
            CategoryMeta::init();
        }

        new ListShortcode();
        new MapShortcode();
    }
}
