<?php
/**
 * Plugin Name: Rubicon Maps
 * Plugin URI: https://rubicontv.com/rubicon-maps
 * Update URI: https://git.daxdavis.com/rubicon/rubicon-maps
 * Description: Divi-first maps for WordPress that let you manage real locations, not wrestle shortcode spaghetti, with linked maps, lists, imports, and builder-ready controls.
 * Version: 1.0.0
 * Author: Rubicon
 * Author URI: https://rubicontv.com
 * Text Domain: rubicon-maps
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('RUBICON_MAPS_PLUGIN_FILE')) {
    define('RUBICON_MAPS_PLUGIN_FILE', __FILE__);
}

if (!defined('RUBICON_MAPS_PLUGIN_PATH')) {
    define('RUBICON_MAPS_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

if (!defined('RUBICON_MAPS_PLUGIN_URL')) {
    define('RUBICON_MAPS_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('RUBICON_MAPS_VERSION')) {
    define('RUBICON_MAPS_VERSION', '1.0.0');
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/divi-4/divi-4.php';
require_once __DIR__ . '/divi-5/divi-5.php';

register_activation_hook(__FILE__, [RubiconMaps\Support\PluginLifecycle::class, 'activate']);
register_deactivation_hook(__FILE__, [RubiconMaps\Support\PluginLifecycle::class, 'deactivate']);

RubiconMaps\Loader::instance();
