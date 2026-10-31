<?php
/**
 * Safe Media Replacer.
 * Replaces original image file references while preserving WordPress Attachment IDs.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Media_Replacer {

	/**
	 * Replace attachment with WebP version.
	 *
	 * @param int    $attachment_id
	 * @param string $webp_path
	 * @param bool   $keep_backup
	 * @param bool   $delete_original
	 * @return bool|WP_Error
	 */
	public static function replace( $attachment_id, $webp_path, $keep_backup = false, $delete_original = false ) {
		if ( ! file_exists( $webp_path ) || filesize( $webp_path ) === 0 ) {
			return new WP_Error( 'bwo_invalid_webp', __( 'Generated WebP file is invalid or missing.', 'budi-webp-optimizer' ) );
		}

		$original_file = get_attached_file( $attachment_id );

		// Step 1: Backup original files if backup enabled and not already backed up
		if ( $keep_backup && ! BWO_Backup_Manager::has_backup( $attachment_id ) ) {
			$backed_up = BWO_Backup_Manager::create_backup( $attachment_id );
			if ( is_wp_error( $backed_up ) ) {
				return $backed_up;
			}
		}

		// Step 2: Compute relative path for _wp_attached_file
		$relative_file = get_post_meta( $attachment_id, '_wp_attached_file', true );
		$new_relative  = BWO_Utils::get_webp_path( $relative_file );

		// Step 3: Update post meta and post record
		update_attached_file( $attachment_id, $webp_path );
		update_post_meta( $attachment_id, '_wp_attached_file', $new_relative );

		wp_update_post(
			array(
				'ID'             => $attachment_id,
				'post_mime_type' => 'image/webp',
			)
		);

		// Step 4: If delete_original is true, permanently delete original main file from disk
		if ( $delete_original && $original_file && file_exists( $original_file ) && $original_file !== $webp_path ) {
			wp_delete_file( $original_file );
		}

		BWO_Logger::log( "Replaced attachment #{$attachment_id} with WebP: {$new_relative}" );
		return true;
	}
}
