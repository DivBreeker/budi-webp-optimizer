<?php
/**
 * Master Image Converter.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUDI_WebP_Converter {

	/**
	 * Convert a single attachment to WebP according to plugin settings.
	 *
	 * @param int        $attachment_id
	 * @param array|null $override_options
	 * @return BUDI_WebP_Conversion_Result
	 */
	public static function convert( $attachment_id, $override_options = array() ) {
		$result                = new BUDI_WebP_Conversion_Result();
		$result->attachment_id = $attachment_id;

		$settings = wp_parse_args(
			$override_options,
			BUDI_WebP_Settings::get_settings()
		);

		$quality = BUDI_WebP_Quality::get_quality( $settings['quality'] );
		$mode    = isset( $settings['conversion_mode'] ) ? $settings['conversion_mode'] : 'keep';
		$backup  = ! empty( $settings['keep_backup'] );
		$thumbs  = ! empty( $settings['convert_thumbnails'] );

		$file_path = get_attached_file( $attachment_id );
		if ( ! $file_path || ! file_exists( $file_path ) ) {
			$result->error_message = __( 'Attachment source file does not exist on server.', 'budi-webp-optimizer' );
			update_post_meta( $attachment_id, '_budi_webp_failed', $result->error_message );
			BUDI_WebP_Logger::log( "Failed converting attachment #{$attachment_id}: Source file not found.", 'error' );
			return $result;
		}

		$result->original_path = $file_path;
		$result->original_size = filesize( $file_path );

		// Check if already in WebP format
		$mime_type       = get_post_mime_type( $attachment_id );
		$is_already_webp = ( 'image/webp' === $mime_type || ( $file_path && 'webp' === strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) ) ) );

		if ( $is_already_webp ) {
			update_post_meta( $attachment_id, '_budi_webp_converted', 1 );
			delete_post_meta( $attachment_id, '_budi_webp_failed' );

			$result->success       = true;
			$result->original_path = $file_path;
			$result->webp_path     = $file_path;
			$result->original_size = filesize( $file_path );
			$result->webp_size     = $result->original_size;
			$result->saved_bytes   = 0;
			$result->saved_percent = 0;

			delete_transient( 'budi_webp_stats_summary' );
			BUDI_WebP_Logger::log( "Attachment #{$attachment_id} is already in WebP format.", 'info' );
			return $result;
		}

		// Validate MIME type
		if ( ! in_array( $mime_type, array( 'image/jpeg', 'image/png' ), true ) ) {
			$result->error_message = __( 'Only JPEG and PNG images are supported for WebP conversion.', 'budi-webp-optimizer' );
			update_post_meta( $attachment_id, '_budi_webp_failed', $result->error_message );
			BUDI_WebP_Logger::log( "Failed converting attachment #{$attachment_id}: Unsupported MIME type ({$mime_type}).", 'error' );
			return $result;
		}

		$destination_webp = BUDI_WebP_Utils::get_webp_path( $file_path );

		// Encode main image
		$encoded = BUDI_WebP_WebP_Encoder::encode( $file_path, $destination_webp, $quality );

		if ( is_wp_error( $encoded ) ) {
			$result->error_message = $encoded->get_error_message();
			update_post_meta( $attachment_id, '_budi_webp_failed', $result->error_message );
			BUDI_WebP_Logger::log( "Failed converting attachment #{$attachment_id}: " . $result->error_message, 'error' );
			return $result;
		}

		$result->webp_path = $destination_webp;
		$result->webp_size = filesize( $destination_webp );

		// Calculate savings
		$result->saved_bytes   = max( 0, $result->original_size - $result->webp_size );
		$result->saved_percent = BUDI_WebP_Formatting::calculate_savings_percent( $result->original_size, $result->webp_size );

		if ( 'keep' === $mode ) {
			// Mode 1: Keep original in Backups and use WebP across site
			if ( $backup && ! BUDI_WebP_Backup_Manager::has_backup( $attachment_id ) ) {
				$backed_up = BUDI_WebP_Backup_Manager::create_backup( $attachment_id );
				if ( is_wp_error( $backed_up ) ) {
					BUDI_WebP_Logger::log( "Warning: Could not create backup for attachment #{$attachment_id}: " . $backed_up->get_error_message(), 'warning' );
				}
			}

			// Switch attachment to WebP
			$replace_res = BUDI_WebP_Media_Replacer::replace( $attachment_id, $destination_webp, false, false );
			if ( is_wp_error( $replace_res ) ) {
				$result->error_message = $replace_res->get_error_message();
				update_post_meta( $attachment_id, '_budi_webp_failed', $result->error_message );
				BUDI_WebP_Logger::log( "Failed replacing attachment #{$attachment_id}: " . $result->error_message, 'error' );
				return $result;
			}

			// Process thumbnails to WebP
			if ( $thumbs ) {
				$result->thumbnails = BUDI_WebP_Thumbnail_Handler::process_thumbnails( $attachment_id, $quality, 'replace', false );
			}
		} else {
			// Mode 2: Replace original with WebP (auto-deletes original without keeping backup)
			$replace_res = BUDI_WebP_Media_Replacer::replace( $attachment_id, $destination_webp, false, true );
			if ( is_wp_error( $replace_res ) ) {
				$result->error_message = $replace_res->get_error_message();
				update_post_meta( $attachment_id, '_budi_webp_failed', $result->error_message );
				BUDI_WebP_Logger::log( "Failed replacing attachment #{$attachment_id}: " . $result->error_message, 'error' );
				return $result;
			}

			// Process thumbnails to WebP and auto-delete original thumbnails
			if ( $thumbs ) {
				$result->thumbnails = BUDI_WebP_Thumbnail_Handler::process_thumbnails( $attachment_id, $quality, 'replace', true );
			}
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

		update_post_meta( $attachment_id, '_budi_webp_data', $webp_meta );
		update_post_meta( $attachment_id, '_budi_webp_converted', 1 );
		delete_post_meta( $attachment_id, '_budi_webp_failed' );

		// Invalidate cache
		delete_transient( 'budi_webp_stats_summary' );

		$result->success = true;
		$filename = basename( $file_path );
		BUDI_WebP_Logger::log( "Converted attachment #{$attachment_id} ({$filename}) to WebP. Saved {$result->saved_percent}% (" . BUDI_WebP_Formatting::format_bytes( $result->saved_bytes ) . ")", 'success' );

		return $result;
	}
}
