<?php
/**
 * File safety and path validation.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_File_Safety {

	/**
	 * Verify that a given path resides inside the WordPress uploads directory.
	 * Prevents directory traversal attacks.
	 *
	 * @param string $path
	 * @return bool
	 */
	public static function is_path_in_uploads( $path ) {
		$upload_dir = wp_upload_dir();
		$base_dir   = wp_normalize_path( realpath( $upload_dir['basedir'] ) );
		$real_path  = wp_normalize_path( realpath( $path ) );

		if ( ! $real_path ) {
			// Path may not exist yet, validate dirname
			$dir = dirname( $path );
			$real_dir = wp_normalize_path( realpath( $dir ) );
			return $real_dir && strpos( $real_dir, $base_dir ) === 0;
		}

		return strpos( $real_path, $base_dir ) === 0;
	}

	/**
	 * Validate file MIME type.
	 *
	 * @param string $path
	 * @return bool
	 */
	public static function is_safe_image_mime( $path ) {
		if ( ! file_exists( $path ) ) {
			return false;
		}

		$file_info = wp_check_filetype( $path );
		$allowed   = array( 'image/jpeg', 'image/png', 'image/webp' );

		return in_array( $file_info['type'], $allowed, true );
	}
}
