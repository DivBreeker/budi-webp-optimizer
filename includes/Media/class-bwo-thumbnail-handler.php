<?php
/**
 * Thumbnail Handler for intermediate sizes.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Thumbnail_Handler {

	/**
	 * Convert intermediate thumbnail sizes of an attachment to WebP.
	 *
	 * @param int    $attachment_id
	 * @param int    $quality
	 * @param string $mode ('keep' or 'replace')
	 * @return array Processed sizes info
	 */
	public static function process_thumbnails( $attachment_id, $quality = 85, $mode = 'keep' ) {
		$metadata = wp_get_attachment_metadata( $attachment_id );
		if ( empty( $metadata['sizes'] ) || ! is_array( $metadata['sizes'] ) ) {
			return array();
		}

		$main_file = get_attached_file( $attachment_id );
		$dir       = dirname( $main_file );
		$results   = array();
		$updated_sizes = $metadata['sizes'];

		foreach ( $metadata['sizes'] as $size_key => $size_info ) {
			if ( empty( $size_info['file'] ) ) {
				continue;
			}

			$source_thumb = trailingslashit( $dir ) . $size_info['file'];
			if ( ! file_exists( $source_thumb ) ) {
				continue;
			}

			$orig_size = filesize( $source_thumb );
			$dest_webp = BWO_Utils::get_webp_path( $source_thumb );

			$encoded = BWO_WebP_Encoder::encode( $source_thumb, $dest_webp, $quality );

			if ( ! is_wp_error( $encoded ) && file_exists( $dest_webp ) ) {
				$webp_size = filesize( $dest_webp );

				$results[ $size_key ] = array(
					'original_file' => $size_info['file'],
					'webp_file'     => basename( $dest_webp ),
					'original_size' => $orig_size,
					'webp_size'     => $webp_size,
				);

				// In 'replace' mode, we update the metadata sizes to point to the WebP thumbnail
				if ( 'replace' === $mode ) {
					$updated_sizes[ $size_key ]['file']      = basename( $dest_webp );
					$updated_sizes[ $size_key ]['mime-type'] = 'image/webp';
				}
			}
		}

		if ( 'replace' === $mode && ! empty( $results ) ) {
			$metadata['sizes'] = $updated_sizes;
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}

		return $results;
	}
}
