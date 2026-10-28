<?php
/**
 * Main Plugin Orchestrator.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Plugin {

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

		if ( is_admin() ) {
			BWO_Admin_Menu::init();
			BWO_Admin::init();
		}
	}

	public function load_textdomain() {
		load_plugin_textdomain(
			'budi-webp-optimizer',
			false,
			dirname( BWO_PLUGIN_BASENAME ) . '/languages'
		);
	}
}
