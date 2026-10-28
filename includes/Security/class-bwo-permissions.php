<?php
/**
 * Permissions verification helper.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Permissions {

	const REQUIRED_CAPABILITY = 'manage_options';

	/**
	 * Verify current user has admin capability.
	 *
	 * @return bool
	 */
	public static function check_admin() {
		return current_user_can( self::REQUIRED_CAPABILITY );
	}

	/**
	 * Die if permission denied.
	 */
	public static function verify_or_die() {
		if ( ! self::check_admin() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized access. Insufficient capabilities.', 'budi-webp-optimizer' ) ), 403 );
		}
	}
}
