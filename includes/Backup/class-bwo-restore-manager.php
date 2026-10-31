<?php
/**
 * Safe Restore and Rollback Manager.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Restore_Manager {

	/**
	 * Restore an attachment to its original file and metadata.
	 *
	 * @param int $attachment_id
	 * @return bool|WP_Error
	 */
	public static function restore( $attachment_id ) {
		$backup = BWO_Backup_Manager::get_backup( $attachment_id );
		if ( empty( $backup ) ) {
			return new WP_Error( 'bwo_no_backup', __( 'No backup found for this attachment.', 'budi-webp-optimizer' ) );
		}

		$backup_file = $backup['backup_path'];
		if ( ! file_exists( $backup_file ) ) {
			return new WP_Error( 'bwo_backup_file_missing', __( 'Backup file on server is missing.', 'budi-webp-optimizer' ) );
		}

		$original_file = $backup['original_file'];

		// If current attached file is a webp generated file, delete it
		$current_file = get_attached_file( $attachment_id );
		if ( $current_file && file_exists( $current_file ) && $current_file !== $original_file ) {
			if ( 'webp' === strtolower( pathinfo( $current_file, PATHINFO_EXTENSION ) ) ) {
				wp_delete_file( $current_file );
			}
		}

		// Also check adjacent webp file if it was "Keep original" mode
		$adjacent_webp = BWO_Utils::get_webp_path( $original_file );
		if ( file_exists( $adjacent_webp ) ) {
			wp_delete_file( $adjacent_webp );
		}

		// Restore original main file from backup
		$copied = @copy( $backup_file, $original_file );
		if ( ! $copied ) {
			return new WP_Error( 'bwo_restore_copy_failed', __( 'Failed to copy original file back from backup.', 'budi-webp-optimizer' ) );
		}

		// Restore intermediate thumbnails from backup if saved
		if ( ! empty( $backup['thumbnails'] ) && is_array( $backup['thumbnails'] ) ) {
			$orig_dir = dirname( $original_file );
			foreach ( $backup['thumbnails'] as $size_key => $thumb_filename ) {
				$thumb_backup = trailingslashit( $backup['backup_folder'] ) . $thumb_filename;
				$thumb_dest   = trailingslashit( $orig_dir ) . $thumb_filename;
				if ( file_exists( $thumb_backup ) ) {
					@copy( $thumb_backup, $thumb_dest );
				}
				// Remove any generated webp variant
				$webp_thumb = BWO_Utils::get_webp_path( $thumb_dest );
				if ( file_exists( $webp_thumb ) ) {
					wp_delete_file( $webp_thumb );
				}
			}
		}

		// Restore attachment metadata and post mime type
		if ( ! empty( $backup['metadata'] ) ) {
			wp_update_attachment_metadata( $attachment_id, $backup['metadata'] );
		}
		if ( ! empty( $backup['relative_file'] ) ) {
			update_post_meta( $attachment_id, '_wp_attached_file', $backup['relative_file'] );
		}
		if ( ! empty( $backup['original_mime'] ) ) {
			wp_update_post(
				array(
					'ID'             => $attachment_id,
					'post_mime_type' => $backup['original_mime'],
				)
			);
		}

		// Clean plugin post meta
		delete_post_meta( $attachment_id, '_bwo_webp_data' );
		delete_post_meta( $attachment_id, '_bwo_converted' );
		delete_post_meta( $attachment_id, '_bwo_failed' );

		// Clean up backup directory files
		BWO_Backup_Manager::delete_backup( $attachment_id );

		// Invalidate cache
		delete_transient( 'bwo_stats_summary' );

		BWO_Logger::log( "Successfully restored original attachment #{$attachment_id}" );
		return true;
	}
}
