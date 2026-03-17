<?php
namespace RubiconMaps;

use RubiconMaps\PostType\Location;
use RubiconMaps\Taxonomy\LocationCategory;
use RubiconMaps\Taxonomy\Region;
use RubiconMaps\Admin\AdminAssets;
use RubiconMaps\Admin\AdminMenuState;
use RubiconMaps\Admin\SettingsPage;
use RubiconMaps\Admin\MetaBox;
use RubiconMaps\Admin\CategoryMeta;
use RubiconMaps\Admin\GeocodeController;
use RubiconMaps\Admin\ImportExportPage;
use RubiconMaps\Admin\PluginActionLinks;
use RubiconMaps\Rest\LocationsEndpoint;
use RubiconMaps\Shortcodes\ListShortcode;
use RubiconMaps\Shortcodes\MapShortcode;
use RubiconMaps\Support\PluginLifecycle;
use RubiconMaps\Support\PluginUpdater;

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
        add_action('init', [PluginLifecycle::class, 'maybeUpgrade'], 20);
        add_action('rest_api_init', [LocationsEndpoint::class, 'register_routes']);
        PluginUpdater::init();

        if (is_admin()) {
            AdminAssets::init();
            AdminMenuState::init();
            GeocodeController::init();
            SettingsPage::init();
            MetaBox::init();
            CategoryMeta::init();
            ImportExportPage::init();
            PluginActionLinks::init();
        }

        new ListShortcode();
        new MapShortcode();
    }
}
