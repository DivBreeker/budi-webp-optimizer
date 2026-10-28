<?php
/**
 * Quality settings helper.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Quality {

	const DEFAULT_QUALITY = 85;
	const MIN_QUALITY     = 50;
	const MAX_QUALITY     = 100;

	/**
	 * Get sanitized quality percentage from settings or input.
	 *
	 * @param int|null $custom_quality
	 * @return int
	 */
	public static function get_quality( $custom_quality = null ) {
		if ( null !== $custom_quality && is_numeric( $custom_quality ) ) {
			return self::sanitize( $custom_quality );
		}

		$settings = get_option( 'bwo_settings', array() );
		$quality  = isset( $settings['quality'] ) ? (int) $settings['quality'] : self::DEFAULT_QUALITY;

		return self::sanitize( $quality );
	}

	/**
	 * Sanitize quality value between min and max.
	 *
	 * @param int $val
	 * @return int
	 */
	public static function sanitize( $val ) {
		$val = absint( $val );
		if ( $val < self::MIN_QUALITY ) {
			return self::MIN_QUALITY;
		}
		if ( $val > self::MAX_QUALITY ) {
			return self::MAX_QUALITY;
		}
		return $val;
	}
}
