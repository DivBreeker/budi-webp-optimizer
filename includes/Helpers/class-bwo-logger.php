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

	/**
	 * Log a message if debug logging is active.
	 *
	 * @param string $message
	 * @param string $level (info|warning|error)
	 * @param array  $context
	 */
	public static function log( $message, $level = 'info', $context = array() ) {
		$settings = get_option( 'bwo_settings', array() );
		$debug    = ! empty( $settings['debug_log'] );

		if ( ! $debug && 'error' !== $level ) {
			return;
		}

		$formatted = sprintf(
			'[BWO %s][%s] %s %s',
			gmdate( 'Y-m-d H:i:s' ),
			strtoupper( $level ),
			$message,
			! empty( $context ) ? wp_json_encode( $context ) : ''
		);

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( $formatted ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}

		// Keep a lightweight rotating buffer of the last 50 logs in options for the Reports/Debug tab
		$logs   = get_option( 'bwo_recent_logs', array() );
		$logs[] = array(
			'time'    => time(),
			'level'   => $level,
			'message' => $message,
			'context' => $context,
		);

		if ( count( $logs ) > 50 ) {
			$logs = array_slice( $logs, -50 );
		}

		update_option( 'bwo_recent_logs', $logs, false );
	}
}
