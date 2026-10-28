<?php
/**
 * Server compatibility and environment checker.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Server_Check {

	/**
	 * Check if the server supports WebP encoding.
	 *
	 * @return bool
	 */
	public static function supports_webp() {
		// 1. Check native WordPress Image Editor support
		if ( function_exists( 'wp_image_editor_supports' ) ) {
			if ( wp_image_editor_supports( array( 'methods' => array( 'save' ), 'mime_type' => 'image/webp' ) ) ) {
				return true;
			}
		}

		// 2. Check Imagick directly
		if ( extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) ) {
			try {
				$imagick = new Imagick();
				$formats = $imagick->queryFormats( 'WEBP' );
				if ( ! empty( $formats ) ) {
					return true;
				}
			} catch ( Exception $e ) {
				// Imagick query error
			}
		}

		// 3. Check GD directly
		if ( extension_loaded( 'gd' ) && function_exists( 'imagewebp' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get detailed server capabilities report.
	 *
	 * @return array
	 */
	public static function get_report() {
		$has_gd         = extension_loaded( 'gd' );
		$gd_webp        = $has_gd && function_exists( 'imagewebp' );
		$has_imagick    = extension_loaded( 'imagick' ) && class_exists( 'Imagick' );
		$imagick_webp   = false;

		if ( $has_imagick ) {
			try {
				$im = new Imagick();
				$formats = $im->queryFormats( 'WEBP' );
				$imagick_webp = ! empty( $formats );
			} catch ( Exception $e ) {
				$imagick_webp = false;
			}
		}

		$wp_editor_support = function_exists( 'wp_image_editor_supports' ) ?
			wp_image_editor_supports( array( 'methods' => array( 'save' ), 'mime_type' => 'image/webp' ) ) : false;

		return array(
			'php_version'       => PHP_VERSION,
			'supports_webp'     => self::supports_webp(),
			'wp_editor_support' => $wp_editor_support,
			'has_gd'            => $has_gd,
			'gd_webp'           => $gd_webp,
			'has_imagick'       => $has_imagick,
			'imagick_webp'      => $imagick_webp,
			'memory_limit'      => ini_get( 'memory_limit' ),
			'max_execution_time'=> ini_get( 'max_execution_time' ),
			'active_engine'     => self::get_preferred_engine(),
		);
	}

	/**
	 * Get which engine is actively utilized.
	 *
	 * @return string 'imagick', 'gd', or 'none'
	 */
	public static function get_preferred_engine() {
		if ( extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) ) {
			try {
				$im = new Imagick();
				if ( ! empty( $im->queryFormats( 'WEBP' ) ) ) {
					return 'Imagick';
				}
			} catch ( Exception $e ) {
				// continue to GD
			}
		}

		if ( extension_loaded( 'gd' ) && function_exists( 'imagewebp' ) ) {
			return 'GD';
		}

		return 'None';
	}
}
