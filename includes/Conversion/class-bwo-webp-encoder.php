<?php
/**
 * Low-level WebP Encoder.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_WebP_Encoder {

	/**
	 * Encode single file to WebP.
	 *
	 * @param string $source_path
	 * @param string $destination_path
	 * @param int    $quality
	 * @return true|WP_Error
	 */
	public static function encode( $source_path, $destination_path, $quality = 85 ) {
		$validation = BWO_Image_Validator::validate( $source_path );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		$quality = BWO_Quality::sanitize( $quality );

		// Method 1: WordPress Image Editor (Recommended Native Way)
		if ( function_exists( 'wp_get_image_editor' ) ) {
			$editor = wp_get_image_editor( $source_path );
			if ( ! is_wp_error( $editor ) ) {
				$editor->set_quality( $quality );
				$saved = $editor->save( $destination_path, 'image/webp' );
				if ( ! is_wp_error( $saved ) && file_exists( $destination_path ) && filesize( $destination_path ) > 0 ) {
					return true;
				}
			}
		}

		// Method 2: GD Extension
		if ( extension_loaded( 'gd' ) && function_exists( 'imagewebp' ) ) {
			$info = @getimagesize( $source_path );
			if ( $info ) {
				$mime  = $info['mime'];
				$image = null;

				switch ( $mime ) {
					case 'image/jpeg':
						$image = @imagecreatefromjpeg( $source_path );
						break;
					case 'image/png':
						$image = @imagecreatefrompng( $source_path );
						if ( $image ) {
							imagepalettetotruecolor( $image );
							imagealphablending( $image, true );
							imagesavealpha( $image, true );
						}
						break;
					case 'image/webp':
						$image = @imagecreatefromwebp( $source_path );
						break;
				}

				if ( $image ) {
					$success = @imagewebp( $image, $destination_path, $quality );
					imagedestroy( $image );
					if ( $success && file_exists( $destination_path ) && filesize( $destination_path ) > 0 ) {
						return true;
					}
				}
			}
		}

		// Method 3: Imagick Extension
		if ( extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) ) {
			try {
				$imagick = new Imagick( $source_path );
				$imagick->setImageFormat( 'webp' );
				$imagick->setImageCompressionQuality( $quality );
				$imagick->setOption( 'webp:method', '6' ); // Best compression method
				$imagick->writeImage( $destination_path );
				$imagick->clear();
				$imagick->destroy();

				if ( file_exists( $destination_path ) && filesize( $destination_path ) > 0 ) {
					return true;
				}
			} catch ( Exception $e ) {
				return new WP_Error( 'bwo_imagick_failed', $e->getMessage() );
			}
		}

		return new WP_Error( 'bwo_encoding_failed', __( 'Server failed to encode image to WebP. Check GD or Imagick support.', 'budi-webp-optimizer' ) );
	}
}
