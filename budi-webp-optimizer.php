<?php
/**
 * Plugin Name:       BUDI WebP Optimizer
 * Plugin URI:        https://budistudio.vercel.app/
 * Description:       Convert smarter. Load faster. Ultra-lightweight WordPress WebP image optimizer with safe backup rollback and WooCommerce support.
 * Version:           1.0.1
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
define( 'BWO_VERSION', '1.0.1' );
define( 'BWO_PLUGIN_NAME', 'budi-webp-optimizer' );
define( 'BWO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BWO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BWO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoloader or explicit include of core plugin classes.
 */
require_once BWO_PLUGIN_DIR . 'includes/Helpers/class-bwo-formatting.php';
require_once BWO_PLUGIN_DIR . 'includes/Helpers/class-bwo-logger.php';
require_once BWO_PLUGIN_DIR . 'includes/Helpers/class-bwo-utils.php';
require_once BWO_PLUGIN_DIR . 'includes/Security/class-bwo-permissions.php';
require_once BWO_PLUGIN_DIR . 'includes/Security/class-bwo-nonce.php';
require_once BWO_PLUGIN_DIR . 'includes/Security/class-bwo-file-safety.php';
require_once BWO_PLUGIN_DIR . 'includes/Compatibility/class-bwo-server-check.php';
require_once BWO_PLUGIN_DIR . 'includes/Conversion/class-bwo-conversion-result.php';
require_once BWO_PLUGIN_DIR . 'includes/Conversion/class-bwo-quality.php';
require_once BWO_PLUGIN_DIR . 'includes/Conversion/class-bwo-image-validator.php';
require_once BWO_PLUGIN_DIR . 'includes/Conversion/class-bwo-webp-encoder.php';
require_once BWO_PLUGIN_DIR . 'includes/Conversion/class-bwo-converter.php';
require_once BWO_PLUGIN_DIR . 'includes/Backup/class-bwo-backup-manager.php';
require_once BWO_PLUGIN_DIR . 'includes/Backup/class-bwo-restore-manager.php';
require_once BWO_PLUGIN_DIR . 'includes/Media/class-bwo-attachment.php';
require_once BWO_PLUGIN_DIR . 'includes/Media/class-bwo-thumbnail-handler.php';
require_once BWO_PLUGIN_DIR . 'includes/Media/class-bwo-media-replacer.php';
require_once BWO_PLUGIN_DIR . 'includes/Media/class-bwo-media-scanner.php';
require_once BWO_PLUGIN_DIR . 'includes/Queue/class-bwo-job-queue.php';
require_once BWO_PLUGIN_DIR . 'includes/Admin/class-bwo-settings.php';
require_once BWO_PLUGIN_DIR . 'includes/Admin/class-bwo-admin-menu.php';
require_once BWO_PLUGIN_DIR . 'includes/Admin/class-bwo-admin.php';
require_once BWO_PLUGIN_DIR . 'includes/class-bwo-activator.php';
require_once BWO_PLUGIN_DIR . 'includes/class-bwo-deactivator.php';
require_once BWO_PLUGIN_DIR . 'includes/class-bwo-plugin.php';

/**
 * Activation and Deactivation Hooks
 */
register_activation_hook( __FILE__, array( 'BWO_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'BWO_Deactivator', 'deactivate' ) );

/**
 * Initialize and run plugin.
 */
function run_budi_webp_optimizer() {
	$plugin = BWO_Plugin::get_instance();
	$plugin->run();
}
run_budi_webp_optimizer();
