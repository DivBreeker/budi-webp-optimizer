<?php
/**
 * Safe Backup Manager.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Backup_Manager {

	const META_BACKUP_DATA = '_bwo_backup_data';

	/**
	 * Create a backup of an attachment and all its intermediate sizes before replacement.
	 *
	 * @param int $attachment_id
	 * @return bool|WP_Error
	 */
	public static function create_backup( $attachment_id ) {
		$file_path = get_attached_file( $attachment_id );
		if ( ! $file_path || ! file_exists( $file_path ) ) {
			return new WP_Error( 'bwo_backup_no_file', __( 'Attachment file does not exist for backup.', 'budi-webp-optimizer' ) );
		}

		$backup_dir = BWO_Utils::get_backup_dir();
		$item_dir   = trailingslashit( $backup_dir ) . $attachment_id;

		if ( ! file_exists( $item_dir ) ) {
			wp_mkdir_p( $item_dir );
		}

		$filename     = basename( $file_path );
		$backup_file  = trailingslashit( $item_dir ) . $filename;
		$copied       = @copy( $file_path, $backup_file );

		if ( ! $copied ) {
			return new WP_Error( 'bwo_backup_copy_failed', __( 'Could not copy original file to backup directory.', 'budi-webp-optimizer' ) );
		}

		// Also backup intermediate thumbnails if any
		$metadata   = wp_get_attachment_metadata( $attachment_id );
		$dir        = dirname( $file_path );
		$thumbs_bak = array();

		if ( ! empty( $metadata['sizes'] ) && is_array( $metadata['sizes'] ) ) {
			foreach ( $metadata['sizes'] as $size_key => $size_info ) {
				if ( ! empty( $size_info['file'] ) ) {
					$thumb_source = trailingslashit( $dir ) . $size_info['file'];
					if ( file_exists( $thumb_source ) ) {
						$thumb_dest = trailingslashit( $item_dir ) . $size_info['file'];
						if ( @copy( $thumb_source, $thumb_dest ) ) {
							$thumbs_bak[ $size_key ] = $size_info['file'];
						}
					}
				}
			}
		}

		$backup_data = array(
			'original_file'      => $file_path,
			'relative_file'      => get_post_meta( $attachment_id, '_wp_attached_file', true ),
			'original_filename'  => $filename,
			'original_mime'      => get_post_mime_type( $attachment_id ),
			'original_size'      => filesize( $file_path ),
			'backup_path'        => $backup_file,
			'backup_folder'      => $item_dir,
			'thumbnails'         => $thumbs_bak,
			'metadata'           => $metadata,
			'timestamp'          => time(),
		);

		update_post_meta( $attachment_id, self::META_BACKUP_DATA, $backup_data );
		BWO_Logger::log( "Original backup created for attachment #{$attachment_id} ({$filename})" );
		return true;
	}

	/**
	 * Check if an attachment has an existing backup.
	 *
	 * @param int $attachment_id
	 * @return bool
	 */
	public static function has_backup( $attachment_id ) {
		$backup = get_post_meta( $attachment_id, self::META_BACKUP_DATA, true );
		if ( empty( $backup ) || empty( $backup['backup_path'] ) ) {
			return false;
		}
		return file_exists( $backup['backup_path'] );
	}

	/**
	 * Get backup info.
	 *
	 * @param int $attachment_id
	 * @return array|null
	 */
	public static function get_backup( $attachment_id ) {
		return get_post_meta( $attachment_id, self::META_BACKUP_DATA, true );
	}

	/**
	 * Delete backup files and metadata for a specific attachment.
	 *
	 * @param int $attachment_id
	 * @return bool
	 */
	public static function delete_backup( $attachment_id ) {
		$backup = self::get_backup( $attachment_id );
		if ( ! empty( $backup['backup_folder'] ) && file_exists( $backup['backup_folder'] ) ) {
			$files = glob( trailingslashit( $backup['backup_folder'] ) . '*' );
			if ( $files ) {
				foreach ( $files as $file ) {
					if ( is_file( $file ) ) {
						@unlink( $file );
					}
				}
			}
			@rmdir( $backup['backup_folder'] );
		}

		delete_post_meta( $attachment_id, self::META_BACKUP_DATA );
		BWO_Logger::log( "Deleted original backup for attachment #{$attachment_id}" );
		return true;
	}

	/**
	 * Delete all stored backups across all attachments.
	 *
	 * @return int Number of backup metas removed
	 */
	public static function delete_all_backups() {
		global $wpdb;

		$backup_dir = BWO_Utils::get_backup_dir();
		if ( file_exists( $backup_dir ) ) {
			$subdirs = glob( trailingslashit( $backup_dir ) . '*', GLOB_ONLYDIR );
			if ( $subdirs ) {
				foreach ( $subdirs as $dir ) {
					$files = glob( trailingslashit( $dir ) . '*' );
					if ( $files ) {
						foreach ( $files as $file ) {
							if ( is_file( $file ) ) {
								@unlink( $file );
							}
						}
					}
					@rmdir( $dir );
				}
			}
		}

		$deleted_count = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
				self::META_BACKUP_DATA
			)
		);

		BWO_Logger::log( "All original image backups deleted from server storage." );
		return $deleted_count;
	}
}
