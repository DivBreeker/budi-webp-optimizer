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

$clean_on_uninstall = get_option( 'bwo_clean_on_uninstall', 0 );

if ( ! empty( $clean_on_uninstall ) ) {
	// Delete options
	delete_option( 'bwo_settings' );
	delete_option( 'bwo_stats_cache' );
	delete_option( 'bwo_clean_on_uninstall' );
	delete_option( 'bwo_queue_state' );

	// Note: We deliberately do NOT delete media files or backups unless specifically directed,
	// to avoid catastrophic data loss on client websites.
}
