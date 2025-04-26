<?php
/**
 * Plugin Name: Rubicon Maps
 * Plugin URI: https://rubicontv.com/rubicon-maps
 * Description: Easily build beautiful, responsive, and customizable maps for Divi and WordPress — powered by Google Maps and Leaflet.
 * Version: 1.0.3
 * Author: RubiconTV
 * Author URI: https://rubicontv.com
 * Text Domain: rubicon-maps
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

RubiconMaps\Loader::instance();
