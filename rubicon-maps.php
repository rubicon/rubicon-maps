<?php
/**
 * Plugin Name: Rubicon Maps
 * Plugin URI: https://rubicontv.com/rubicon-maps
 * Description: Divi-first location mapping plugin for WordPress with linked map/list modules, REST support, and location management.
 * Version: 0.5.0
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
    define('RUBICON_MAPS_VERSION', '0.5.0');
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/divi-4/divi-4.php';
require_once __DIR__ . '/divi-5/divi-5.php';

RubiconMaps\Loader::instance();
