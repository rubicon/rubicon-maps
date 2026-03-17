<?php

namespace RubiconMaps\Support;

use RubiconMaps\PostType\Location;
use RubiconMaps\Taxonomy\LocationCategory;
use RubiconMaps\Taxonomy\Region;

use function flush_rewrite_rules;
use function get_option;
use function update_option;

if (!defined('ABSPATH')) {
    exit;
}

final class PluginLifecycle
{
    private const VERSION_OPTION = 'rtv_maps_plugin_version';

    public static function activate(): void
    {
        self::registerContentTypes();
        flush_rewrite_rules();
        update_option(self::VERSION_OPTION, Plugin::version());
    }

    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }

    public static function maybeUpgrade(): void
    {
        if ((string) get_option(self::VERSION_OPTION, '') === Plugin::version()) {
            return;
        }

        self::registerContentTypes();
        flush_rewrite_rules(false);
        update_option(self::VERSION_OPTION, Plugin::version());
    }

    private static function registerContentTypes(): void
    {
        Location::register();
        LocationCategory::register();
        Region::register();
    }
}
