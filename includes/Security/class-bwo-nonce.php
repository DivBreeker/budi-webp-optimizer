<?php
/**
 * Nonce validation helper.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Nonce {

	const ACTION = 'bwo_admin_nonce_action';
	const KEY    = 'bwo_nonce';

	/**
	 * Create nonce token.
	 *
	 * @return string
	 */
	public static function create() {
		return wp_create_nonce( self::ACTION );
	}

	/**
	 * Verify request nonce.
	 *
	 * @param string|null $nonce
	 * @return bool
	 */
	public static function verify( $nonce = null ) {
		if ( null === $nonce ) {
			if ( isset( $_REQUEST[ self::KEY ] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST[ self::KEY ] ) );
			} elseif ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) );
			}
		}

		return (bool) wp_verify_nonce( $nonce, self::ACTION );
	}

	/**
	 * Verify or die with JSON error.
	 */
	public static function verify_or_die() {
		if ( ! self::verify() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Security check failed. Please refresh the page and try again.', 'budi-webp-optimizer' ) ), 403 );
		}
	}
}
