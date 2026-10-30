<?php
/**
 * Logger helper class.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Logger {

	const OPTION_LOGS = 'bwo_recent_logs';
	const MAX_LOGS    = 100;

	/**
	 * Log a message to recent activity log buffer and optionally error_log.
	 *
	 * @param string $message
	 * @param string $level (info|warning|error|success)
	 * @param array  $context
	 */
	public static function log( $message, $level = 'info', $context = array() ) {
		$settings = get_option( 'bwo_settings', array() );
		$debug    = ! empty( $settings['debug_log'] );

		// Keep a lightweight rotating buffer of logs in options for the Reports/Debug tab
		$logs = get_option( self::OPTION_LOGS, array() );
		if ( ! is_array( $logs ) ) {
			$logs = array();
		}

		$logs[] = array(
			'time'    => time(),
			'level'   => sanitize_key( $level ),
			'message' => sanitize_text_field( $message ),
			'context' => $context,
		);

		if ( count( $logs ) > self::MAX_LOGS ) {
			$logs = array_slice( $logs, - self::MAX_LOGS );
		}

		update_option( self::OPTION_LOGS, $logs, false );

		// Optional PHP system error log
		if ( ( $debug || 'error' === $level ) && defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			$formatted = sprintf(
				'[BWO %s][%s] %s %s',
				gmdate( 'Y-m-d H:i:s' ),
				strtoupper( $level ),
				$message,
				! empty( $context ) ? wp_json_encode( $context ) : ''
			);
			error_log( $formatted ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Retrieve all recent activity logs.
	 *
	 * @return array
	 */
	public static function get_logs() {
		$logs = get_option( self::OPTION_LOGS, array() );
		return is_array( $logs ) ? $logs : array();
	}

	/**
	 * Clear all activity logs.
	 *
	 * @return bool
	 */
	public static function clear_logs() {
		update_option( self::OPTION_LOGS, array(), false );
		return true;
	}
}
