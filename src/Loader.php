<?php
namespace RubiconMaps;

use RubiconMaps\PostType\Location;
use RubiconMaps\Taxonomy\LocationCategory;
use RubiconMaps\Taxonomy\Region;
use RubiconMaps\Admin\SettingsPage;
use RubiconMaps\Shortcodes\LocationList;
use RubiconMaps\Rest\LocationsEndpoint;

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
            new SettingsPage();
        }

        new LocationList(); // ensure shortcodes are registered on frontend
    }
}
