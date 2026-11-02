<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package BUDI_WebP_Optimizer
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$clean_on_uninstall = get_option( 'budi_webp_clean_on_uninstall', get_option( 'bwo_clean_on_uninstall', 0 ) );

if ( ! empty( $clean_on_uninstall ) ) {
	// Delete options
	delete_option( 'budi_webp_settings' );
	delete_option( 'budi_webp_stats_cache' );
	delete_option( 'budi_webp_clean_on_uninstall' );
	delete_option( 'budi_webp_queue_state' );
	delete_option( 'budi_webp_activity_logs' );
	delete_option( 'budi_webp_backups_cleared' );
	delete_transient( 'budi_webp_stats_summary' );

	// Legacy cleanup
	delete_option( 'bwo_settings' );
	delete_option( 'bwo_stats_cache' );
	delete_option( 'bwo_clean_on_uninstall' );
	delete_option( 'bwo_queue_state' );
	delete_option( 'bwo_recent_logs' );
	delete_transient( 'bwo_stats_summary' );

	// Note: We deliberately do NOT delete media files or backups unless specifically directed,
	// to avoid catastrophic data loss on client websites.
}
