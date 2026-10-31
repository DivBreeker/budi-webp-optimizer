<?php
/**
 * Media Scanner and Statistics Provider.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Media_Scanner {

	/**
	 * Get summary statistics for dashboard cards and progress meter.
	 * Cached in transient for speed, invalidated upon conversion or manual scan.
	 *
	 * @param bool $force_refresh
	 * @return array
	 */
	public static function get_summary_stats( $force_refresh = false ) {
		$cached = get_transient( 'bwo_stats_summary' );
		if ( ! $force_refresh && false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		global $wpdb;

		// Count total eligible images (JPG/PNG or attachments converted by BWO)
		$all_attachments = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} 
				WHERE post_type = 'attachment' 
				AND (post_mime_type IN (%s, %s, %s) OR ID IN (
					SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_bwo_converted' AND meta_value = '1'
				))",
				'image/jpeg',
				'image/png',
				'image/jpg'
			)
		);

		$total_count     = count( $all_attachments );
		$converted_count = 0;
		$failed_count    = 0;
		$total_orig_size = 0;
		$total_curr_size = 0;
		$total_saved     = 0;

		if ( $total_count > 0 ) {
			// Get converted IDs
			$converted_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
					'_bwo_converted',
					'1'
				)
			);
			$converted_ids_map = array_flip( $converted_ids );
			$converted_count   = count( $converted_ids );

			// Get failed IDs
			$failed_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s",
					'_bwo_failed'
				)
			);
			$failed_count = count( $failed_ids );

			// Compute storage savings from _bwo_webp_data
			$webp_data_rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s",
					'_bwo_webp_data'
				),
				ARRAY_A
			);

			if ( ! empty( $webp_data_rows ) ) {
				foreach ( $webp_data_rows as $row ) {
					$data = maybe_unserialize( $row['meta_value'] );
					if ( is_array( $data ) ) {
						$orig = isset( $data['original_size'] ) ? (int) $data['original_size'] : 0;
						$webp = isset( $data['webp_size'] ) ? (int) $data['webp_size'] : 0;

						$total_orig_size += $orig;
						$total_curr_size += ( $webp > 0 ? $webp : $orig );
						if ( $orig > $webp ) {
							$total_saved += ( $orig - $webp );
						}
					}
				}
			}
		}

		$pending_count = max( 0, $total_count - $converted_count - $failed_count );
		$percent_done  = $total_count > 0 ? round( ( $converted_count / $total_count ) * 100, 1 ) : 0.0;
		$avg_reduction = $total_orig_size > 0 ? round( ( $total_saved / $total_orig_size ) * 100, 1 ) : 0.0;

		$stats = array(
			'total_images'       => $total_count,
			'converted_images'   => $converted_count,
			'pending_images'     => $pending_count,
			'failed_images'      => $failed_count,
			'percent_completed'  => $percent_done,
			'percent_donut'      => round( $percent_done ),
			'storage_saved'      => $total_saved,
			'storage_saved_f'    => BWO_Formatting::format_bytes( $total_saved ),
			'original_size'      => $total_orig_size,
			'original_size_f'    => BWO_Formatting::format_bytes( $total_orig_size ),
			'current_size'       => $total_curr_size,
			'current_size_f'     => BWO_Formatting::format_bytes( $total_curr_size ),
			'average_reduction'  => $avg_reduction,
			'reduction_label'    => $avg_reduction > 0 ? $avg_reduction . '% average reduction' : '0% average reduction',
			'last_updated'       => time(),
		);

		set_transient( 'bwo_stats_summary', $stats, HOUR_IN_SECONDS );
		return $stats;
	}

	/**
	 * Query images for the dashboard table with filtering, search, and pagination.
	 *
	 * @param array $args
	 * @return array
	 */
	public static function query_images( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'paged'    => 1,
			'per_page' => 10,
			'search'   => '',
			'status'   => 'all', // 'all', 'converted', 'pending', 'failed'
		);
		$r = wp_parse_args( $args, $defaults );

		$paged    = max( 1, absint( $r['paged'] ) );
		$per_page = max( 1, min( 100, absint( $r['per_page'] ) ) );
		$offset   = ( $paged - 1 ) * $per_page;

		$where = "WHERE p.post_type = 'attachment' AND (p.post_mime_type IN ('image/jpeg', 'image/png', 'image/jpg') OR pm_conv.meta_value = '1')";
		$joins = "LEFT JOIN {$wpdb->postmeta} pm_conv ON (p.ID = pm_conv.post_id AND pm_conv.meta_key = '_bwo_converted')
		          LEFT JOIN {$wpdb->postmeta} pm_fail ON (p.ID = pm_fail.post_id AND pm_fail.meta_key = '_bwo_failed')";

		if ( ! empty( $r['search'] ) ) {
			$search_like = '%' . $wpdb->esc_like( sanitize_text_field( $r['search'] ) ) . '%';
			$where .= $wpdb->prepare( " AND (p.post_title LIKE %s OR p.guid LIKE %s)", $search_like, $search_like );
		}

		if ( 'converted' === $r['status'] ) {
			$where .= " AND pm_conv.meta_value = '1'";
		} elseif ( 'pending' === $r['status'] ) {
			$where .= " AND pm_conv.meta_value IS NULL AND pm_fail.meta_value IS NULL";
		} elseif ( 'failed' === $r['status'] ) {
			$where .= " AND pm_fail.meta_value IS NOT NULL";
		}

		$total_items = (int) $wpdb->get_var( "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p {$joins} {$where}" );

		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT p.ID FROM {$wpdb->posts} p {$joins} {$where} ORDER BY p.ID DESC LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);

		$items = array();
		foreach ( $ids as $id ) {
			$att = new BWO_Attachment( $id );
			$items[] = $att->to_table_array();
		}

		return array(
			'items'       => $items,
			'total_items' => $total_items,
			'total_pages' => ceil( $total_items / $per_page ),
			'paged'       => $paged,
		);
	}

	/**
	 * Get eligible attachment IDs for bulk processing.
	 *
	 * @param string $mode ('all', 'pending_only', 'failed_only')
	 * @return array List of IDs
	 */
	public static function get_eligible_ids_for_bulk( $mode = 'all' ) {
		global $wpdb;

		if ( 'pending_only' === $mode ) {
			return $wpdb->get_col(
				$wpdb->prepare(
					"SELECT p.ID FROM {$wpdb->posts} p
					LEFT JOIN {$wpdb->postmeta} pm_conv ON (p.ID = pm_conv.post_id AND pm_conv.meta_key = '_bwo_converted')
					LEFT JOIN {$wpdb->postmeta} pm_fail ON (p.ID = pm_fail.post_id AND pm_fail.meta_key = '_bwo_failed')
					WHERE p.post_type = 'attachment' 
					AND p.post_mime_type IN (%s, %s, %s)
					AND pm_conv.meta_value IS NULL
					ORDER BY p.ID DESC",
					'image/jpeg',
					'image/png',
					'image/jpg'
				)
			);
		} elseif ( 'failed_only' === $mode ) {
			return $wpdb->get_col(
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s ORDER BY post_id DESC",
					'_bwo_failed'
				)
			);
		}

		// All eligible
		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT p.ID FROM {$wpdb->posts} p
				LEFT JOIN {$wpdb->postmeta} pm_conv ON (p.ID = pm_conv.post_id AND pm_conv.meta_key = '_bwo_converted')
				WHERE p.post_type = 'attachment' 
				AND (p.post_mime_type IN (%s, %s, %s) OR pm_conv.meta_value = '1')
				ORDER BY p.ID DESC",
				'image/jpeg',
				'image/png',
				'image/jpg'
			)
		);
	}
}
