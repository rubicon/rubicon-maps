<?php
namespace RubiconMaps\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsHelper {

	public static function get_option( $key, $default = '' ) {
		$options = get_option( 'rubicon_maps_settings', [] );
		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}
}
