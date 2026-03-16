<?php
namespace RubiconMaps\Helpers;

use RubiconMaps\Support\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsHelper {

	public static function get_option( $key, $default = '' ) {
		$options = get_option( Plugin::OPTION_NAME, [] );
		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}
}
