<?php
/**
 * Image Optimization & Metadata Stripping Engine.
 * Lightweight, native file size reduction for WordPress uploads.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUDI_WebP_Image_Optimizer {

	/**
	 * Initialize hooks for image optimization and auto-resize.
	 */
	public static function init() {
		// Hook into image upload process before thumbnail generation
		add_filter( 'wp_handle_upload', array( __CLASS__, 'handle_upload' ), 10, 2 );

		// Hook into core big image threshold filter to align with our configured max dimension
		add_filter( 'big_image_size_threshold', array( __CLASS__, 'filter_big_image_threshold' ), 10, 4 );
	}

	/**
	 * Align WordPress big image threshold with user-selected max dimension.
	 *
	 * @param int|false $threshold
	 * @param array     $imagesize
	 * @param string    $file
	 * @param int       $attachment_id
	 * @return int|false
	 */
	public static function filter_big_image_threshold( $threshold, $imagesize, $file, $attachment_id ) {
		$settings = BUDI_WebP_Settings::get_settings();
		if ( empty( $settings['auto_resize'] ) ) {
			return $threshold;
		}

		$max_dimension = ! empty( $settings['max_dimension'] ) ? absint( $settings['max_dimension'] ) : 2048;
		return $max_dimension > 0 ? $max_dimension : $threshold;
	}

	/**
	 * Process and optimize newly uploaded files directly on upload.
	 *
	 * @param array  $upload Array containing 'file', 'url', 'type'.
	 * @param string $context Context of the upload.
	 * @return array
	 */
	public static function handle_upload( $upload, $context = 'upload' ) {
		if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) ) {
			return $upload;
		}

		$file_path = $upload['file'];
		$mime_type = ! empty( $upload['type'] ) ? $upload['type'] : '';

		if ( ! in_array( $mime_type, array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
			return $upload;
		}

		$settings      = BUDI_WebP_Settings::get_settings();
		$strip_meta    = ! empty( $settings['strip_metadata'] );
		$auto_resize   = ! empty( $settings['auto_resize'] );
		$max_dimension = ! empty( $settings['max_dimension'] ) ? absint( $settings['max_dimension'] ) : 2048;
		$quality       = ! empty( $settings['quality'] ) ? absint( $settings['quality'] ) : 85;

		// 1. Auto-resize if oversized
		if ( $auto_resize && $max_dimension > 0 ) {
			self::resize_oversized_image( $file_path, $max_dimension, $quality );
		}

		// 2. Strip metadata & optimize file size
		if ( $strip_meta ) {
			self::strip_and_optimize( $file_path, $mime_type, $quality );
		}

		return $upload;
	}

	/**
	 * Proportionally resize an image down if its dimensions exceed max dimension.
	 *
	 * @param string $file_path
	 * @param int    $max_dimension
	 * @param int    $quality
	 * @return bool
	 */
	public static function resize_oversized_image( $file_path, $max_dimension, $quality = 85 ) {
		if ( ! file_exists( $file_path ) ) {
			return false;
		}

		$image_info = @getimagesize( $file_path );
		if ( ! $image_info ) {
			return false;
		}

		$width  = $image_info[0];
		$height = $image_info[1];

		if ( $width <= $max_dimension && $height <= $max_dimension ) {
			return false;
		}

		if ( ! function_exists( 'wp_get_image_editor' ) ) {
			return false;
		}

		$editor = wp_get_image_editor( $file_path );
		if ( is_wp_error( $editor ) ) {
			return false;
		}

		$editor->set_quality( $quality );
		$resized = $editor->resize( $max_dimension, $max_dimension, false ); // proportional, no crop
		if ( ! is_wp_error( $resized ) ) {
			$saved = $editor->save( $file_path );
			if ( ! is_wp_error( $saved ) ) {
				BUDI_WebP_Logger::log(
					sprintf( 'Auto-resized oversized upload %s from %dx%d down to max %dpx.', basename( $file_path ), $width, $height, $max_dimension ),
					'info'
				);
				return true;
			}
		}

		return false;
	}

	/**
	 * Strip EXIF/IPTC metadata and losslessly compress JPEG/PNG/WebP.
	 *
	 * @param string $file_path
	 * @param string $mime_type
	 * @param int    $quality
	 * @return bool
	 */
	public static function strip_and_optimize( $file_path, $mime_type = '', $quality = 85 ) {
		if ( ! file_exists( $file_path ) ) {
			return false;
		}

		$initial_size = filesize( $file_path );
		if ( $initial_size <= 0 ) {
			return false;
		}

		if ( empty( $mime_type ) ) {
			$info      = @getimagesize( $file_path );
			$mime_type = $info ? $info['mime'] : '';
		}

		// Method 1: Imagick (Excellent metadata stripping)
		if ( extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) ) {
			try {
				$imagick = new Imagick( $file_path );
				$imagick->stripImage(); // Removes EXIF, GPS, camera profiles

				if ( 'image/jpeg' === $mime_type ) {
					$imagick->setImageCompressionQuality( $quality );
					if ( defined( 'Imagick::INTERLACE_PLANE' ) ) {
						$imagick->setInterlaceScheme( Imagick::INTERLACE_PLANE ); // Progressive JPEG
					}
				} elseif ( 'image/png' === $mime_type ) {
					$imagick->setOption( 'png:compression-level', '9' );
					$imagick->setOption( 'png:compression-filter', '5' );
				} elseif ( 'image/webp' === $mime_type ) {
					$imagick->setImageCompressionQuality( $quality );
				}

				$temp_file = $file_path . '.budi_opt_tmp';
				$imagick->writeImage( $temp_file );
				$imagick->clear();
				$imagick->destroy();

				if ( file_exists( $temp_file ) ) {
					$new_size = filesize( $temp_file );
					// Only keep if size did not increase and file is valid
					if ( $new_size > 0 && $new_size <= $initial_size ) {
						@rename( $temp_file, $file_path );
						$saved_bytes = $initial_size - $new_size;
						if ( $saved_bytes > 0 ) {
							BUDI_WebP_Logger::log(
								sprintf( 'Stripped metadata & optimized %s: saved %d bytes.', basename( $file_path ), $saved_bytes ),
								'info'
							);
						}
						return true;
					}
					@unlink( $temp_file );
				}
			} catch ( Exception $e ) {
				// Fall through to GD
			}
		}

		// Method 2: GD Extension (Naturally strips EXIF on re-save)
		if ( extension_loaded( 'gd' ) ) {
			switch ( $mime_type ) {
				case 'image/jpeg':
					$image = @imagecreatefromjpeg( $file_path );
					if ( $image ) {
						$temp_file = $file_path . '.budi_opt_tmp';
						if ( function_exists( 'imageinterlace' ) ) {
							@imageinterlace( $image, 1 ); // Progressive JPEG
						}
						@imagejpeg( $image, $temp_file, $quality );
						imagedestroy( $image );
						if ( file_exists( $temp_file ) ) {
							$new_size = filesize( $temp_file );
							if ( $new_size > 0 && $new_size <= $initial_size ) {
								@rename( $temp_file, $file_path );
								return true;
							}
							@unlink( $temp_file );
						}
					}
					break;

				case 'image/png':
					$image = @imagecreatefrompng( $file_path );
					if ( $image ) {
						$temp_file = $file_path . '.budi_opt_tmp';
						imagealphablending( $image, false );
						imagesavealpha( $image, true );
						@imagepng( $image, $temp_file, 9 );
						imagedestroy( $image );
						if ( file_exists( $temp_file ) ) {
							$new_size = filesize( $temp_file );
							if ( $new_size > 0 && $new_size <= $initial_size ) {
								@rename( $temp_file, $file_path );
								return true;
							}
							@unlink( $temp_file );
						}
					}
					break;
			}
		}

		return false;
	}
}
