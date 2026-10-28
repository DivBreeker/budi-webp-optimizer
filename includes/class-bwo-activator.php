<?php
/**
 * Fired during plugin activation.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Activator {

	public static function activate() {
		// Initialize default settings if not exists
		if ( false === get_option( 'bwo_settings' ) ) {
			update_option( 'bwo_settings', BWO_Settings::get_defaults() );
		}

		// Ensure backup directory structure exists
		BWO_Utils::get_backup_dir();

		// Prime summary stats
		BWO_Media_Scanner::get_summary_stats( true );
	}
}
