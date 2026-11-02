<?php
/**
 * Fired during plugin activation.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUDI_WebP_Activator {

	public static function activate() {
		// Initialize default settings if not exists
		if ( false === get_option( 'budi_webp_settings' ) ) {
			update_option( 'budi_webp_settings', BUDI_WebP_Settings::get_defaults() );
		}

		// Ensure backup directory structure exists
		BUDI_WebP_Utils::get_backup_dir();

		// Prime summary stats
		BUDI_WebP_Media_Scanner::get_summary_stats( true );
	}
}
