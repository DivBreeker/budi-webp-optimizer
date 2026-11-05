<?php
/**
 * Main Plugin Orchestrator.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUDI_WebP_Plugin {

	protected static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function run() {
		$this->load_dependencies();
		$this->init_hooks();
	}

	private function load_dependencies() {
		// Handled via main plugin bootstrap
	}

	private function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Frontend WebP content delivery filter.
		BUDI_WebP_Frontend::init();

		if ( is_admin() ) {
			BUDI_WebP_Admin_Menu::init();
			BUDI_WebP_Admin::init();
		}
	}

	public function load_textdomain() {
		// WordPress.org automatically loads translations since WP 4.6.
		// load_plugin_textdomain() is intentionally omitted for WP.org hosted plugins.
		// If you are running this plugin outside WordPress.org, enable the line below:
		// load_plugin_textdomain( 'budi-webp-optimizer', false, dirname( BUDI_WEBP_PLUGIN_BASENAME ) . '/languages' );
	}
}
