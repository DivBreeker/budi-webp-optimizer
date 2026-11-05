<?php
/**
 * Database Content URL Synchronizer.
 * Permanently updates old JPG/PNG image URLs to WebP inside post_content.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUDI_WebP_Content_Updater {

	/**
	 * Sync post_content for a single attachment when converted.
	 *
	 * @param int $attachment_id
	 * @return int Number of updated posts
	 */
	public static function sync_single_attachment( $attachment_id ) {
		global $wpdb;

		$attachment_id = absint( $attachment_id );
		if ( ! $attachment_id ) {
			return 0;
		}

		$upload_dir        = wp_upload_dir();
		$base_url          = $upload_dir['baseurl'];
		$base_dir          = $upload_dir['basedir'];
		$base_url_no_proto = preg_replace( '#^https?:#i', '', $base_url );

		$meta_data = wp_get_attachment_metadata( $attachment_id );
		$file      = get_attached_file( $attachment_id );
		if ( empty( $file ) ) {
			return 0;
		}

		$replacements = array();

		// Main file URL pair
		if ( ! empty( $meta_data['file'] ) ) {
			$orig_rel = preg_replace( '/\.webp$/i', '', $meta_data['file'] );
			$orig_url = trailingslashit( $base_url ) . $orig_rel;
			$webp_url = BUDI_WebP_Utils::get_webp_url( $orig_url );
			if ( $orig_url !== $webp_url ) {
				$replacements[ $orig_url ] = $webp_url;
			}
		}

		// Thumbnail URLs pairs
		if ( ! empty( $meta_data['sizes'] ) && is_array( $meta_data['sizes'] ) ) {
			$sub_dir = dirname( $meta_data['file'] );
			$prefix  = ( '.' !== $sub_dir && '' !== $sub_dir ) ? trailingslashit( $base_url ) . trailingslashit( $sub_dir ) : trailingslashit( $base_url );

			foreach ( $meta_data['sizes'] as $size_info ) {
				if ( ! empty( $size_info['file'] ) ) {
					$thumb_file    = $size_info['file'];
					$orig_thumb    = preg_replace( '/\.webp$/i', '', $thumb_file );
					$old_thumb_url = $prefix . $orig_thumb;
					$new_thumb_url = BUDI_WebP_Utils::get_webp_url( $old_thumb_url );
					if ( $old_thumb_url !== $new_thumb_url ) {
						$replacements[ $old_thumb_url ] = $new_thumb_url;
					}
				}
			}
		}

		if ( empty( $replacements ) ) {
			return 0;
		}

		$updated_count = 0;
		foreach ( $replacements as $old_url => $new_url ) {
			$old_base = preg_replace( '#^https?:#i', '', $old_url );
			$new_base = preg_replace( '#^https?:#i', '', $new_url );

			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Permanent content URL replacement requires direct bulk update.
			$affected = $wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->posts} SET post_content = REPLACE(REPLACE(post_content, %s, %s), %s, %s) WHERE post_type NOT IN ('revision', 'attachment') AND (post_content LIKE %s OR post_content LIKE %s)",
					$old_url,
					$new_url,
					$old_base,
					$new_base,
					'%' . $wpdb->esc_like( $old_url ) . '%',
					'%' . $wpdb->esc_like( $old_base ) . '%'
				)
			);
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

			if ( $affected ) {
				$updated_count += (int) $affected;
			}
		}

		return $updated_count;
	}

	/**
	 * Scan all posts and permanently replace JPG/PNG image URLs with WebP in post_content if WebP exists.
	 *
	 * @param int $batch_limit
	 * @return array
	 */
	public static function sync_all_posts( $batch_limit = 5000 ) {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Batch content updater reads raw HTML.
		$posts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_content FROM {$wpdb->posts} WHERE post_type NOT IN ('revision', 'attachment') AND post_content LIKE %s LIMIT %d",
				'%wp-content/uploads/%',
				$batch_limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

		if ( empty( $posts ) ) {
			return array(
				'updated_posts' => 0,
				'updated_urls'  => 0,
			);
		}

		$upload_dir        = wp_upload_dir();
		$base_url          = $upload_dir['baseurl'];
		$base_dir          = $upload_dir['basedir'];
		$base_url_no_proto = preg_replace( '#^https?:#i', '', $base_url );

		$updated_posts = 0;
		$updated_urls  = 0;

		foreach ( $posts as $p ) {
			$content = $p->post_content;
			$changed = false;

			$pattern = '#https?://[^\s"\'<>]+/wp-content/uploads/[^\s"\'<>]+\.(?:jpe?g|png)#i';
			$new_content = preg_replace_callback(
				$pattern,
				function ( $matches ) use ( $base_url, $base_dir, $base_url_no_proto, &$changed, &$updated_urls ) {
					$url                = $matches[0];
					$clean_url_no_proto = preg_replace( '#^https?:#i', '', $url );

					if ( false === strpos( $clean_url_no_proto, $base_url_no_proto ) ) {
						return $url;
					}

					$rel_path        = str_replace( $base_url_no_proto, '', $clean_url_no_proto );
					$local_file      = $base_dir . $rel_path;
					$webp_local_file = BUDI_WebP_Utils::get_webp_path( $local_file );

					if ( file_exists( $webp_local_file ) ) {
						$changed = true;
						$updated_urls++;
						return BUDI_WebP_Utils::get_webp_url( $url );
					}

					return $url;
				},
				$content
			);

			if ( $changed && $new_content !== $content ) {
				// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update(
					$wpdb->posts,
					array( 'post_content' => $new_content ),
					array( 'ID' => $p->ID ),
					array( '%s' ),
					array( '%d' )
				);
				// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				clean_post_cache( $p->ID );
				$updated_posts++;
			}
		}

		BUDI_WebP_Logger::log( "Post content sync completed: Updated {$updated_urls} image URLs across {$updated_posts} posts.", 'success' );

		return array(
			'updated_posts' => $updated_posts,
			'updated_urls'  => $updated_urls,
		);
	}
}
