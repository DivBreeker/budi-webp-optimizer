<?php
/**
 * Formatting helper class.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Formatting {

	/**
	 * Format bytes into human readable format (KB, MB, GB).
	 *
	 * @param int|float $bytes
	 * @param int       $precision
	 * @return string
	 */
	public static function format_bytes( $bytes, $precision = 1 ) {
		if ( ! is_numeric( $bytes ) || $bytes <= 0 ) {
			return '0 B';
		}

		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		$bytes = max( $bytes, 0 );
		$pow   = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow   = min( $pow, count( $units ) - 1 );

		$bytes /= ( 1 << ( 10 * $pow ) );

		return round( $bytes, $precision ) . ' ' . $units[ $pow ];
	}

	/**
	 * Format percentage savings.
	 *
	 * @param int|float $original_size
	 * @param int|float $new_size
	 * @return float
	 */
	public static function calculate_savings_percent( $original_size, $new_size ) {
		if ( ! $original_size || $original_size <= 0 || $new_size >= $original_size ) {
			return 0.0;
		}

		$saved = $original_size - $new_size;
		return round( ( $saved / $original_size ) * 100, 1 );
	}

	/**
	 * Format date for admin display.
	 *
	 * @param string|int $timestamp
	 * @return string
	 */
	public static function format_date( $timestamp ) {
		if ( is_string( $timestamp ) && ! is_numeric( $timestamp ) ) {
			$timestamp = strtotime( $timestamp );
		}
		if ( ! $timestamp ) {
			return '—';
		}
		return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp );
	}
}
