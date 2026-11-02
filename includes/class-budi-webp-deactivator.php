<?php
/**
 * Fired during plugin deactivation.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUDI_WebP_Deactivator {

	public static function deactivate() {
		// Clear transient cache
		delete_transient( 'budi_webp_stats_summary' );
		delete_transient( 'bwo_stats_summary' );

		// Clear active bulk queue if any
		delete_option( 'budi_webp_queue_state' );
		delete_option( 'bwo_queue_state' );
	}
}
