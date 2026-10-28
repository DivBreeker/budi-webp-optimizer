<?php
/**
 * Admin Menu registration.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Admin_Menu {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
	}

	public static function register_menus() {
		$icon_svg = 'data:image/svg+xml;base64,' . base64_encode(
			'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10b981"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>'
		);

		// Main Menu (points to Dashboard)
		$main_page = add_menu_page(
			__( 'BUDI WebP Optimizer', 'budi-webp-optimizer' ),
			__( 'WebP Optimizer', 'budi-webp-optimizer' ),
			'manage_options',
			'budi-webp-optimizer',
			array( __CLASS__, 'render_dashboard_page' ),
			$icon_svg,
			58
		);

		// Submenus
		add_submenu_page(
			'budi-webp-optimizer',
			__( 'Dashboard - BUDI WebP Optimizer', 'budi-webp-optimizer' ),
			__( 'Dashboard', 'budi-webp-optimizer' ),
			'manage_options',
			'budi-webp-optimizer',
			array( __CLASS__, 'render_dashboard_page' )
		);

		add_submenu_page(
			'budi-webp-optimizer',
			__( 'Media Library - BUDI WebP Optimizer', 'budi-webp-optimizer' ),
			__( 'Media Library', 'budi-webp-optimizer' ),
			'manage_options',
			'bwo-media-library',
			array( __CLASS__, 'render_media_library_page' )
		);

		add_submenu_page(
			'budi-webp-optimizer',
			__( 'Bulk Convert - BUDI WebP Optimizer', 'budi-webp-optimizer' ),
			__( 'Bulk Convert', 'budi-webp-optimizer' ),
			'manage_options',
			'bwo-bulk-convert',
			array( __CLASS__, 'render_bulk_convert_page' )
		);

		add_submenu_page(
			'budi-webp-optimizer',
			__( 'Settings - BUDI WebP Optimizer', 'budi-webp-optimizer' ),
			__( 'Settings', 'budi-webp-optimizer' ),
			'manage_options',
			'bwo-settings',
			array( __CLASS__, 'render_settings_page' )
		);

		add_submenu_page(
			'budi-webp-optimizer',
			__( 'Backups - BUDI WebP Optimizer', 'budi-webp-optimizer' ),
			__( 'Backups', 'budi-webp-optimizer' ),
			'manage_options',
			'bwo-backups',
			array( __CLASS__, 'render_backups_page' )
		);

		add_submenu_page(
			'budi-webp-optimizer',
			__( 'Reports - BUDI WebP Optimizer', 'budi-webp-optimizer' ),
			__( 'Reports', 'budi-webp-optimizer' ),
			'manage_options',
			'bwo-reports',
			array( __CLASS__, 'render_reports_page' )
		);
	}

	public static function render_dashboard_page() {
		require_once BWO_PLUGIN_DIR . 'templates/dashboard.php';
	}

	public static function render_media_library_page() {
		require_once BWO_PLUGIN_DIR . 'templates/media-optimizer.php';
	}

	public static function render_bulk_convert_page() {
		require_once BWO_PLUGIN_DIR . 'templates/dashboard.php';
	}

	public static function render_settings_page() {
		require_once BWO_PLUGIN_DIR . 'templates/settings.php';
	}

	public static function render_backups_page() {
		require_once BWO_PLUGIN_DIR . 'templates/backups.php';
	}

	public static function render_reports_page() {
		require_once BWO_PLUGIN_DIR . 'templates/reports.php';
	}
}
