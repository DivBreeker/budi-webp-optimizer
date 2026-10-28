<?php
/**
 * Master Image Converter.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Converter {

	/**
	 * Convert a single attachment to WebP according to plugin settings.
	 *
	 * @param int        $attachment_id
	 * @param array|null $override_options
	 * @return BWO_Conversion_Result
	 */
	public static function convert( $attachment_id, $override_options = array() ) {
		$result                = new BWO_Conversion_Result();
		$result->attachment_id = $attachment_id;

		$settings = wp_parse_args(
			$override_options,
			get_option(
				'bwo_settings',
				array(
					'quality'           => 85,
					'conversion_mode'   => 'keep', // 'keep' or 'replace'
					'keep_backup'       => 1,
					'convert_thumbnails'=> 1,
				)
			)
		);

		$quality = BWO_Quality::get_quality( $settings['quality'] );
		$mode    = isset( $settings['conversion_mode'] ) ? $settings['conversion_mode'] : 'keep';
		$backup  = ! empty( $settings['keep_backup'] );
		$thumbs  = ! empty( $settings['convert_thumbnails'] );

		$file_path = get_attached_file( $attachment_id );
		if ( ! $file_path || ! file_exists( $file_path ) ) {
			$result->error_message = __( 'Attachment source file does not exist on server.', 'budi-webp-optimizer' );
			update_post_meta( $attachment_id, '_bwo_failed', $result->error_message );
			return $result;
		}

		$result->original_path = $file_path;
		$result->original_size = filesize( $file_path );

		// Validate MIME type
		$mime_type = get_post_mime_type( $attachment_id );
		if ( ! in_array( $mime_type, array( 'image/jpeg', 'image/png' ), true ) ) {
			$result->error_message = __( 'Only JPEG and PNG images are supported for WebP conversion.', 'budi-webp-optimizer' );
			update_post_meta( $attachment_id, '_bwo_failed', $result->error_message );
			return $result;
		}

		$destination_webp = BWO_Utils::get_webp_path( $file_path );

		// Encode main image
		$encoded = BWO_WebP_Encoder::encode( $file_path, $destination_webp, $quality );

		if ( is_wp_error( $encoded ) ) {
			$result->error_message = $encoded->get_error_message();
			update_post_meta( $attachment_id, '_bwo_failed', $result->error_message );
			BWO_Logger::log( "Failed converting attachment #{$attachment_id}: " . $result->error_message, 'error' );
			return $result;
		}

		$result->webp_path = $destination_webp;
		$result->webp_size = filesize( $destination_webp );

		// Check if WebP is actually smaller or if we enforce it anyway
		$result->saved_bytes   = max( 0, $result->original_size - $result->webp_size );
		$result->saved_percent = BWO_Formatting::calculate_savings_percent( $result->original_size, $result->webp_size );

		// If Replace mode is enabled, safely replace attachment file
		if ( 'replace' === $mode ) {
			$replace_res = BWO_Media_Replacer::replace( $attachment_id, $destination_webp, $backup );
			if ( is_wp_error( $replace_res ) ) {
				$result->error_message = $replace_res->get_error_message();
				update_post_meta( $attachment_id, '_bwo_failed', $result->error_message );
				return $result;
			}
		}

		// Process thumbnails if enabled
		if ( $thumbs ) {
			$result->thumbnails = BWO_Thumbnail_Handler::process_thumbnails( $attachment_id, $quality, $mode );
		}

		// Update attachment meta
		$webp_meta = array(
			'converted_at'   => time(),
			'mode'           => $mode,
			'quality'        => $quality,
			'original_size'  => $result->original_size,
			'webp_size'      => $result->webp_size,
			'saved_bytes'    => $result->saved_bytes,
			'saved_percent'  => $result->saved_percent,
			'webp_file'      => $destination_webp,
			'thumbnails'     => $result->thumbnails,
		);

		update_post_meta( $attachment_id, '_bwo_webp_data', $webp_meta );
		update_post_meta( $attachment_id, '_bwo_converted', 1 );
		delete_post_meta( $attachment_id, '_bwo_failed' );

		// Invalidate cache
		delete_transient( 'bwo_stats_summary' );

		$result->success = true;
		BWO_Logger::log( "Converted attachment #{$attachment_id} to WebP. Saved {$result->saved_percent}%" );

		return $result;
	}
}
