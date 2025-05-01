<?php
/**
 * Plugin Name: Rubicon Maps
 * Plugin URI: https://rubicontv.com/rubicon-maps
 * Description: Powerful mapping plugin with Gutenberg, Divi, and REST integration. Supports custom post types, categories, clustering, and autocomplete.
 * Version: 0.3.7
 * Author: Rubicon
 * Author URI: https://rubicontv.com
 * Text Domain: rubicon-maps
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('RUBICON_MAPS_PLUGIN_FILE')) {
    define('RUBICON_MAPS_PLUGIN_FILE', __FILE__);
}

if (!defined('RUBICON_MAPS_VERSION')) {
    define('RUBICON_MAPS_VERSION', '0.3.6');
}

require_once __DIR__ . '/vendor/autoload.php';

RubiconMaps\Loader::instance();
