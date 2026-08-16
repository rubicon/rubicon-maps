<?php
/**
 * Plugin Name: Rubicon Maps
 * Plugin URI: https://rubicontv.com/rubicon-maps
 * Update URI: https://github.com/rubicon/rubicon-maps
 * Description: Divi-first maps for WordPress that let you manage real locations, not wrestle shortcode spaghetti, with linked maps, lists, imports, and builder-ready controls.
 * Version: 2.0.0
 * Requires at least: 5.8
 * Tested up to: 6.9.4
 * Requires PHP: 8.1
 * Author: Rubicon
 * Author URI: https://rubicontv.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * SPDX-License-Identifier: GPL-2.0-or-later
 * Text Domain: rubicon-maps
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('RTV_RM_PLUGIN_FILE')) {
    define('RTV_RM_PLUGIN_FILE', __FILE__);
}

if (!defined('RTV_RM_PLUGIN_PATH')) {
    define('RTV_RM_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

if (!defined('RTV_RM_PLUGIN_URL')) {
    define('RTV_RM_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('RTV_RM_VERSION')) {
    define('RTV_RM_VERSION', '2.0.0');
}

// No manual load_plugin_textdomain() call: WordPress auto-loads translations for a
// matching text domain (WP 4.6+). Add one only if targeting WordPress.org's domain matching.

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/divi-5/divi-5.php';

register_activation_hook(__FILE__, [RubiconMaps\Support\PluginLifecycle::class, 'activate']);
register_deactivation_hook(__FILE__, [RubiconMaps\Support\PluginLifecycle::class, 'deactivate']);

RubiconMaps\Loader::instance();
