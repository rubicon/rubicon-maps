<?php
namespace RubiconMaps;

use RubiconMaps\PostType\Location;
use RubiconMaps\Taxonomy\LocationCategory;
use RubiconMaps\Admin\SettingsPage;
use RubiconMaps\Shortcodes\LocationList;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
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

        if (\is_admin()) {
            new SettingsPage();
        } else {
            new LocationList();
        }
    }
}