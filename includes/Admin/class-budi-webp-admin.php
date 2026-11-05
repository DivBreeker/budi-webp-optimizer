<?php
/**
 * Admin Controller and AJAX Handlers.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUDI_WebP_Admin {

	public static function init() {
		// Only enqueue on BWO pages
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

		// AJAX endpoints
		add_action( 'wp_ajax_budi_webp_scan_library', array( __CLASS__, 'ajax_scan_library' ) );
		add_action( 'wp_ajax_budi_webp_get_images', array( __CLASS__, 'ajax_get_images' ) );
		add_action( 'wp_ajax_budi_webp_convert_single', array( __CLASS__, 'ajax_convert_single' ) );
		add_action( 'wp_ajax_budi_webp_restore_single', array( __CLASS__, 'ajax_restore_single' ) );
		add_action( 'wp_ajax_budi_webp_delete_backup', array( __CLASS__, 'ajax_delete_backup' ) );
		add_action( 'wp_ajax_budi_webp_clear_all_backups', array( __CLASS__, 'ajax_clear_all_backups' ) );
		add_action( 'wp_ajax_budi_webp_start_bulk', array( __CLASS__, 'ajax_start_bulk' ) );
		add_action( 'wp_ajax_budi_webp_process_batch', array( __CLASS__, 'ajax_process_batch' ) );
		add_action( 'wp_ajax_budi_webp_cancel_bulk', array( __CLASS__, 'ajax_cancel_bulk' ) );
		add_action( 'wp_ajax_budi_webp_save_settings', array( __CLASS__, 'ajax_save_settings' ) );
		add_action( 'wp_ajax_budi_webp_get_preview', array( __CLASS__, 'ajax_get_preview' ) );
		add_action( 'wp_ajax_budi_webp_clear_logs', array( __CLASS__, 'ajax_clear_logs' ) );
		add_action( 'wp_ajax_budi_webp_get_logs', array( __CLASS__, 'ajax_get_logs' ) );
		add_action( 'wp_ajax_budi_webp_sync_post_content', array( __CLASS__, 'ajax_sync_post_content' ) );

		// Auto convert on upload if setting enabled
		add_filter( 'wp_generate_attachment_metadata', array( __CLASS__, 'handle_auto_convert_on_upload' ), 10, 2 );

		// Add WebP column to WP Media Library
		add_filter( 'manage_media_columns', array( __CLASS__, 'add_media_columns' ) );
		add_action( 'manage_media_custom_column', array( __CLASS__, 'render_media_columns' ), 10, 2 );
	}

	/**
	 * Enqueue styles and scripts only on our plugin pages.
	 *
	 * @param string $hook
	 */
	public static function enqueue_assets( $hook ) {
		$plugin_pages = array(
			'toplevel_page_budi-webp-optimizer',
			'webp-optimizer_page_budi-webp-media-library',
			'webp-optimizer_page_budi-webp-bulk-convert',
			'webp-optimizer_page_budi-webp-settings',
			'webp-optimizer_page_budi-webp-backups',
			'webp-optimizer_page_budi-webp-reports',
			'admin_page_budi-webp-media-library',
			'admin_page_budi-webp-bulk-convert',
			'admin_page_budi-webp-settings',
			'admin_page_budi-webp-backups',
			'admin_page_budi-webp-reports',
		);

		$is_plugin_page = in_array( $hook, $plugin_pages, true ) || ( false !== strpos( $hook, 'budi-webp' ) );

		if ( ! $is_plugin_page ) {
			return;
		}

		wp_enqueue_style(
			'budi-webp-admin-css',
			BUDI_WEBP_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			BUDI_WEBP_VERSION
		);

		wp_enqueue_script(
			'budi-webp-admin-js',
			BUDI_WEBP_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			BUDI_WEBP_VERSION,
			true
		);

		$summary  = BUDI_WebP_Media_Scanner::get_summary_stats();
		$settings = BUDI_WebP_Settings::get_settings();
		$server   = BUDI_WebP_Server_Check::get_report();

		wp_localize_script(
			'budi-webp-admin-js',
			'budiWebpData',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => BUDI_WebP_Nonce::create(),
				'stats'    => $summary,
				'settings' => $settings,
				'server'   => $server,
				'strings'  => array(
					'confirmConvertAll'   => __( 'Are you sure you want to convert all eligible images to WebP? This will process in the background.', 'budi-webp-optimizer' ),
					'confirmRestore'      => __( 'Are you sure you want to restore this image from backup?', 'budi-webp-optimizer' ),
					'confirmDeleteBackup' => __( 'Are you sure you want to delete this backup file? This action cannot be undone.', 'budi-webp-optimizer' ),
					'confirmClearBackups' => __( 'Are you sure you want to delete ALL original backups from the server? This will free disk space but you will no longer be able to restore original images.', 'budi-webp-optimizer' ),
					'confirmClearLogs'    => __( 'Are you sure you want to clear all activity logs?', 'budi-webp-optimizer' ),
					'converting'          => __( 'Converting...', 'budi-webp-optimizer' ),
					'restoring'           => __( 'Restoring...', 'budi-webp-optimizer' ),
					'deleting'            => __( 'Deleting...', 'budi-webp-optimizer' ),
					'success'             => __( 'Success', 'budi-webp-optimizer' ),
					'failed'              => __( 'Failed', 'budi-webp-optimizer' ),
					'scanning'            => __( 'Scanning Media Library...', 'budi-webp-optimizer' ),
					'settingsSaved'       => __( 'Settings saved successfully!', 'budi-webp-optimizer' ),
				),
			)
		);
	}

	/**
	 * AJAX: Scan library and refresh counts.
	 */
	public static function ajax_scan_library() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		$stats = BUDI_WebP_Media_Scanner::get_summary_stats( true );
		BUDI_WebP_Logger::log(
			sprintf(
				'Media Library scanned: %d total, %d converted, %d pending, %s saved',
				$stats['total_images'],
				$stats['converted_images'],
				$stats['pending_images'],
				$stats['storage_saved_f']
			)
		);

		wp_send_json_success( array( 'stats' => $stats ) );
	}

	/**
	 * AJAX: Query paginated image list.
	 */
	public static function ajax_get_images() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified via BUDI_WebP_Nonce::verify_or_die() above.
		$paged    = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
		$per_page = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10;
		$search   = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
		$status   = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : 'all';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$data = BUDI_WebP_Media_Scanner::query_images(
			array(
				'paged'    => $paged,
				'per_page' => $per_page,
				'search'   => $search,
				'status'   => $status,
			)
		);

		wp_send_json_success( $data );
	}

	/**
	 * AJAX: Convert single image.
	 */
	public static function ajax_convert_single() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified via BUDI_WebP_Nonce::verify_or_die() above.
		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : 0;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		if ( ! $attachment_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'budi-webp-optimizer' ) ) );
		}

		$result = BUDI_WebP_Converter::convert( $attachment_id );
		if ( $result->success ) {
			$attachment = new BUDI_WebP_Attachment( $attachment_id );
			wp_send_json_success(
				array(
					'message'    => __( 'Image successfully converted to WebP.', 'budi-webp-optimizer' ),
					'attachment' => $attachment->to_table_array(),
					'stats'      => BUDI_WebP_Media_Scanner::get_summary_stats( true ),
				)
			);
		} else {
			wp_send_json_error( array( 'message' => $result->error_message ) );
		}
	}

	/**
	 * AJAX: Restore single image from backup.
	 */
	public static function ajax_restore_single() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified via BUDI_WebP_Nonce::verify_or_die() above.
		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : 0;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		if ( ! $attachment_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'budi-webp-optimizer' ) ) );
		}

		$result = BUDI_WebP_Restore_Manager::restore( $attachment_id );
		if ( true === $result ) {
			$attachment = new BUDI_WebP_Attachment( $attachment_id );
			wp_send_json_success(
				array(
					'message'    => __( 'Original image restored successfully.', 'budi-webp-optimizer' ),
					'attachment' => $attachment->to_table_array(),
					'stats'      => BUDI_WebP_Media_Scanner::get_summary_stats( true ),
				)
			);
		} else {
			$msg = is_wp_error( $result ) ? $result->get_error_message() : __( 'Restore failed.', 'budi-webp-optimizer' );
			wp_send_json_error( array( 'message' => $msg ) );
		}
	}

	/**
	 * AJAX: Delete backup of a single attachment.
	 */
	public static function ajax_delete_backup() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified via BUDI_WebP_Nonce::verify_or_die() above.
		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : 0;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		if ( ! $attachment_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'budi-webp-optimizer' ) ) );
		}

		$deleted = BUDI_WebP_Backup_Manager::delete_backup( $attachment_id );
		if ( $deleted ) {
			wp_send_json_success(
				array(
					'message' => __( 'Backup deleted successfully.', 'budi-webp-optimizer' ),
					'stats'   => BUDI_WebP_Media_Scanner::get_summary_stats( true ),
				)
			);
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to delete backup file.', 'budi-webp-optimizer' ) ) );
		}
	}

	/**
	 * AJAX: Clear all backups from server.
	 */
	public static function ajax_clear_all_backups() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		$count = BUDI_WebP_Backup_Manager::delete_all_backups();
		/* translators: %d: Number of backup records cleared */
		$message = sprintf( __( 'All original backups removed (%d records cleared).', 'budi-webp-optimizer' ), $count );
		wp_send_json_success(
			array(
				'message' => $message,
				'stats'   => BUDI_WebP_Media_Scanner::get_summary_stats( true ),
			)
		);
	}

	/**
	 * AJAX: Synchronize post_content image URLs to WebP permanently in the database.
	 */
	public static function ajax_sync_post_content() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		$res = BUDI_WebP_Content_Updater::sync_all_posts();
		/* translators: 1: number of updated URLs, 2: number of updated posts */
		$message = sprintf(
			__( 'Successfully synced %1$d image URLs across %2$d posts to WebP.', 'budi-webp-optimizer' ),
			$res['updated_urls'],
			$res['updated_posts']
		);

		wp_send_json_success(
			array(
				'message'       => $message,
				'updated_urls'  => $res['updated_urls'],
				'updated_posts' => $res['updated_posts'],
			)
		);
	}

	/**
	 * AJAX: Start bulk conversion.
	 */
	public static function ajax_start_bulk() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified via BUDI_WebP_Nonce::verify_or_die() above.
		$mode = isset( $_POST['mode'] ) ? sanitize_key( $_POST['mode'] ) : 'all'; // 'all', 'selected', 'pending_only', 'failed_only'
		$ids  = array();

		if ( 'selected' === $mode && ! empty( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {
			$ids = array_map( 'absint', $_POST['ids'] );
		} else {
			$ids = BUDI_WebP_Media_Scanner::get_eligible_ids_for_bulk( $mode );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( empty( $ids ) ) {
			wp_send_json_error( array( 'message' => __( 'No eligible images found for conversion.', 'budi-webp-optimizer' ) ) );
		}

		$queue_state = BUDI_WebP_Job_Queue::initialize_queue( $ids );
		BUDI_WebP_Logger::log( sprintf( 'Started bulk WebP conversion queue with %d images (Scope: %s)', count( $ids ), strtoupper( $mode ) ) );

		/* translators: %d: Number of images queued for conversion */
		$message = sprintf( __( 'Queued %d images for conversion.', 'budi-webp-optimizer' ), count( $ids ) );
		wp_send_json_success(
			array(
				'message' => $message,
				'queue'   => $queue_state,
			)
		);
	}

	/**
	 * AJAX: Process a batch from bulk conversion queue.
	 */
	public static function ajax_process_batch() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		$settings   = BUDI_WebP_Settings::get_settings();
		$batch_size = isset( $settings['batch_size'] ) ? $settings['batch_size'] : 5;
		$progress   = BUDI_WebP_Job_Queue::process_batch( $batch_size );

		if ( ! empty( $progress['completed'] ) ) {
			$progress['stats'] = BUDI_WebP_Media_Scanner::get_summary_stats( true );
			BUDI_WebP_Logger::log(
				sprintf(
					'Bulk WebP conversion completed: %d total, %d successful, %d failed',
					$progress['total'],
					$progress['successful'],
					$progress['failed']
				),
				$progress['failed'] > 0 ? 'warning' : 'success'
			);
		}

		wp_send_json_success( $progress );
	}

	/**
	 * AJAX: Cancel bulk queue.
	 */
	public static function ajax_cancel_bulk() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		BUDI_WebP_Job_Queue::cancel_queue();
		BUDI_WebP_Logger::log( 'Bulk conversion queue was cancelled by user.', 'warning' );
		wp_send_json_success( array( 'message' => __( 'Bulk conversion cancelled.', 'budi-webp-optimizer' ) ) );
	}

	/**
	 * AJAX: Save settings.
	 */
	public static function ajax_save_settings() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		// phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput -- Nonce verified above; sanitization handled inside BUDI_WebP_Settings::update_settings().
		$raw_settings = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : array();
		// phpcs:enable WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput
		$updated = BUDI_WebP_Settings::update_settings( $raw_settings );

		wp_send_json_success(
			array(
				'message'  => __( 'Settings saved successfully.', 'budi-webp-optimizer' ),
				'settings' => $updated,
			)
		);
	}

	/**
	 * AJAX: Get preview data for comparison modal.
	 */
	public static function ajax_get_preview() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified via BUDI_WebP_Nonce::verify_or_die() above.
		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : 0;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		if ( ! $attachment_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'budi-webp-optimizer' ) ) );
		}

		$attachment = new BUDI_WebP_Attachment( $attachment_id );
		$meta       = get_post_meta( $attachment_id, '_budi_webp_data', true );
		$backup     = BUDI_WebP_Backup_Manager::get_backup( $attachment_id );

		$orig_url = wp_get_attachment_url( $attachment_id );
		$webp_url = '';

		if ( ! empty( $meta['webp_file'] ) && file_exists( $meta['webp_file'] ) ) {
			$webp_url = BUDI_WebP_Utils::get_webp_url( $orig_url );
		}

		// If replaced mode, the current attachment URL IS the WebP, and original was backed up
		if ( ! empty( $backup ) && ! empty( $backup['backup_path'] ) ) {
			$upload_dir = wp_upload_dir();
			$backup_url = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $backup['backup_path'] );
			$orig_url   = $backup_url;
			$webp_url   = wp_get_attachment_url( $attachment_id );
		}

		wp_send_json_success(
			array(
				'attachment'    => $attachment->to_table_array(),
				'original_url'  => $orig_url,
				'webp_url'      => $webp_url ? $webp_url : $orig_url,
				'original_size' => BUDI_WebP_Formatting::format_bytes( $attachment->original_size ),
				'webp_size'     => $attachment->webp_size > 0 ? BUDI_WebP_Formatting::format_bytes( $attachment->webp_size ) : '—',
				'savings'       => $attachment->savings_percent > 0 ? round( $attachment->savings_percent ) . '%' : '—',
			)
		);
	}

	/**
	 * AJAX: Clear activity logs.
	 */
	public static function ajax_clear_logs() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		BUDI_WebP_Logger::clear_logs();
		BUDI_WebP_Logger::log( 'Activity logs cleared by user.' );

		wp_send_json_success( array( 'message' => __( 'Logs cleared.', 'budi-webp-optimizer' ) ) );
	}

	/**
	 * AJAX: Fetch recent activity logs.
	 */
	public static function ajax_get_logs() {
		BUDI_WebP_Permissions::verify_or_die();
		BUDI_WebP_Nonce::verify_or_die();

		$logs = BUDI_WebP_Logger::get_logs();
		$formatted = array();
		foreach ( array_reverse( $logs ) as $log ) {
			$formatted[] = array(
				'time'    => BUDI_WebP_Formatting::format_date( $log['time'] ),
				'level'   => strtoupper( $log['level'] ),
				'message' => $log['message'],
			);
		}

		wp_send_json_success( array( 'logs' => $formatted ) );
	}

	/**
	 * Hook: Auto convert when new image is uploaded.
	 */
	public static function handle_auto_convert_on_upload( $metadata, $attachment_id ) {
		$settings = BUDI_WebP_Settings::get_settings();
		if ( empty( $settings['auto_convert'] ) ) {
			return $metadata;
		}

		$mime = get_post_mime_type( $attachment_id );
		if ( in_array( $mime, array( 'image/jpeg', 'image/png' ), true ) ) {
			BUDI_WebP_Converter::convert( $attachment_id );
		}

		return $metadata;
	}

	/**
	 * Add WebP column to WP Media Library.
	 */
	public static function add_media_columns( $columns ) {
		$columns['budi_webp'] = __( 'WebP Optimizer', 'budi-webp-optimizer' );
		return $columns;
	}

	/**
	 * Render WebP column content.
	 */
	public static function render_media_columns( $column_name, $post_id ) {
		if ( 'budi_webp' !== $column_name ) {
			return;
		}

		$mime_type = get_post_mime_type( $post_id );
		$is_webp   = ( 'image/webp' === $mime_type );
		$is_conv   = get_post_meta( $post_id, '_budi_webp_converted', true );
		$is_fail   = get_post_meta( $post_id, '_budi_webp_failed', true );
		$data      = get_post_meta( $post_id, '_budi_webp_data', true );

		if ( $is_conv || $is_webp ) {
			$pct = isset( $data['saved_percent'] ) ? round( $data['saved_percent'] ) : 0;
			if ( $pct > 0 ) {
				printf(
					'<span style="color:#15803d; font-weight:600;">✓ %s (-%s%%)</span>',
					esc_html__( 'WebP', 'budi-webp-optimizer' ),
					esc_html( $pct )
				);
			} else {
				printf(
					'<span style="color:#15803d; font-weight:600;">✓ %s</span>',
					esc_html__( 'WebP', 'budi-webp-optimizer' )
				);
			}
		} elseif ( $is_fail ) {
			printf(
				'<span style="color:#b91c1c; font-weight:600;">✗ %s</span>',
				esc_html__( 'Failed', 'budi-webp-optimizer' )
			);
		} else {
			printf(
				'<span style="color:#c2410c;">— %s</span>',
				esc_html__( 'Pending', 'budi-webp-optimizer' )
			);
		}
	}
}
