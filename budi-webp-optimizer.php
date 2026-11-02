<?php
/**
 * Plugin Name:       BUDI WebP Optimizer
 * Plugin URI:        https://github.com/DivBreeker/budi-webp-optimizer
 * Description:       Convert smarter. Load faster. Ultra-lightweight WordPress WebP image optimizer with safe backup rollback and WooCommerce support.
 * Version:           1.0.6
 * Author:            BUDI Studio
 * Author URI:        https://budistudio.vercel.app/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       budi-webp-optimizer
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version and paths.
 */
define( 'BUDI_WEBP_VERSION', '1.0.6' );
define( 'BUDI_WEBP_PLUGIN_NAME', 'budi-webp-optimizer' );
define( 'BUDI_WEBP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BUDI_WEBP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BUDI_WEBP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoloader or explicit include of core plugin classes.
 */
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Helpers/class-budi-webp-formatting.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Helpers/class-budi-webp-logger.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Helpers/class-budi-webp-utils.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Security/class-budi-webp-permissions.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Security/class-budi-webp-nonce.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Security/class-budi-webp-file-safety.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Compatibility/class-budi-webp-server-check.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Conversion/class-budi-webp-conversion-result.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Conversion/class-budi-webp-quality.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Conversion/class-budi-webp-image-validator.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Conversion/class-budi-webp-webp-encoder.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Conversion/class-budi-webp-converter.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Backup/class-budi-webp-backup-manager.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Backup/class-budi-webp-restore-manager.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Media/class-budi-webp-attachment.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Media/class-budi-webp-thumbnail-handler.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Media/class-budi-webp-media-replacer.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Media/class-budi-webp-media-scanner.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Queue/class-budi-webp-job-queue.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Admin/class-budi-webp-settings.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Admin/class-budi-webp-admin-menu.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/Admin/class-budi-webp-admin.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/class-budi-webp-activator.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/class-budi-webp-deactivator.php';
require_once BUDI_WEBP_PLUGIN_DIR . 'includes/class-budi-webp-plugin.php';

/**
 * Activation and Deactivation Hooks
 */
register_activation_hook( __FILE__, array( 'BUDI_WebP_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'BUDI_WebP_Deactivator', 'deactivate' ) );

/**
 * Initialize and run plugin.
 */
function budi_webp_optimizer_run() {
	BUDI_WebP_Plugin::get_instance()->run();
}
budi_webp_optimizer_run();
