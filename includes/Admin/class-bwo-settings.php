<?php
/**
 * Settings Manager.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Settings {

	const OPTION_NAME = 'bwo_settings';

	public static function get_defaults() {
		return array(
			'quality'            => 85,
			'conversion_mode'    => 'keep', // 'keep' (Keep original and create WebP) or 'replace' (Replace original with WebP)
			'keep_backup'        => 1,
			'auto_convert'       => 1,
			'convert_thumbnails' => 1,
			'batch_size'         => 5,
			'debug_log'          => 0,
			'clean_on_uninstall' => 0,
		);
	}

	public static function get_settings() {
		$saved = get_option( self::OPTION_NAME, array() );
		return wp_parse_args( $saved, self::get_defaults() );
	}

	public static function get( $key, $default = null ) {
		$settings = self::get_settings();
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	public static function update_settings( $new_settings ) {
		$current  = self::get_settings();
		$sanitized = array();

		if ( isset( $new_settings['quality'] ) ) {
			$sanitized['quality'] = BWO_Quality::sanitize( $new_settings['quality'] );
		} else {
			$sanitized['quality'] = $current['quality'];
		}

		if ( isset( $new_settings['conversion_mode'] ) && in_array( $new_settings['conversion_mode'], array( 'keep', 'replace' ), true ) ) {
			$sanitized['conversion_mode'] = sanitize_key( $new_settings['conversion_mode'] );
		} else {
			$sanitized['conversion_mode'] = $current['conversion_mode'];
		}

		$sanitized['keep_backup']        = ! empty( $new_settings['keep_backup'] ) ? 1 : 0;
		$sanitized['auto_convert']       = ! empty( $new_settings['auto_convert'] ) ? 1 : 0;
		$sanitized['convert_thumbnails'] = ! empty( $new_settings['convert_thumbnails'] ) ? 1 : 0;
		$sanitized['debug_log']          = ! empty( $new_settings['debug_log'] ) ? 1 : 0;
		$sanitized['clean_on_uninstall'] = ! empty( $new_settings['clean_on_uninstall'] ) ? 1 : 0;

		if ( isset( $new_settings['batch_size'] ) ) {
			$sanitized['batch_size'] = max( 1, min( 20, absint( $new_settings['batch_size'] ) ) );
		} else {
			$sanitized['batch_size'] = $current['batch_size'];
		}

		update_option( self::OPTION_NAME, $sanitized );
		return $sanitized;
	}
}
