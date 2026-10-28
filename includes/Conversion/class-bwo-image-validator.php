<?php
/**
 * Image Validator.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Image_Validator {

	/**
	 * Validate image for conversion.
	 *
	 * @param string $file_path
	 * @return true|WP_Error
	 */
	public static function validate( $file_path ) {
		if ( empty( $file_path ) ) {
			return new WP_Error( 'bwo_no_file', __( 'No image file specified.', 'budi-webp-optimizer' ) );
		}

		if ( ! file_exists( $file_path ) ) {
			return new WP_Error( 'bwo_file_missing', sprintf( __( 'Image file not found: %s', 'budi-webp-optimizer' ), basename( $file_path ) ) );
		}

		if ( ! is_readable( $file_path ) ) {
			return new WP_Error( 'bwo_file_unreadable', __( 'Image file is not readable by web server.', 'budi-webp-optimizer' ) );
		}

		$size = @filesize( $file_path );
		if ( ! $size || $size < 10 ) {
			return new WP_Error( 'bwo_empty_file', __( 'Image file appears to be empty or corrupted.', 'budi-webp-optimizer' ) );
		}

		if ( ! BWO_File_Safety::is_safe_image_mime( $file_path ) ) {
			return new WP_Error( 'bwo_unsupported_mime', __( 'File MIME type is not a supported image.', 'budi-webp-optimizer' ) );
		}

		return true;
	}
}
