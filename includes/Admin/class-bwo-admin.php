<?php
/**
 * Admin Controller and AJAX Handlers.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Admin {

	public static function init() {
		// Only enqueue on BWO pages
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

		// AJAX endpoints
		add_action( 'wp_ajax_bwo_scan_library', array( __CLASS__, 'ajax_scan_library' ) );
		add_action( 'wp_ajax_bwo_get_images', array( __CLASS__, 'ajax_get_images' ) );
		add_action( 'wp_ajax_bwo_convert_single', array( __CLASS__, 'ajax_convert_single' ) );
		add_action( 'wp_ajax_bwo_restore_single', array( __CLASS__, 'ajax_restore_single' ) );
		add_action( 'wp_ajax_bwo_start_bulk', array( __CLASS__, 'ajax_start_bulk' ) );
		add_action( 'wp_ajax_bwo_process_batch', array( __CLASS__, 'ajax_process_batch' ) );
		add_action( 'wp_ajax_bwo_cancel_bulk', array( __CLASS__, 'ajax_cancel_bulk' ) );
		add_action( 'wp_ajax_bwo_save_settings', array( __CLASS__, 'ajax_save_settings' ) );
		add_action( 'wp_ajax_bwo_get_preview', array( __CLASS__, 'ajax_get_preview' ) );

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
		// Check if we are on one of our pages
		$plugin_pages = array(
			'toplevel_page_budi-webp-optimizer',
			'webp-optimizer_page_bwo-media-library',
			'webp-optimizer_page_bwo-bulk-convert',
			'webp-optimizer_page_bwo-settings',
			'webp-optimizer_page_bwo-backups',
			'webp-optimizer_page_bwo-reports',
		);

		if ( ! in_array( $hook, $plugin_pages, true ) ) {
			return;
		}

		wp_enqueue_style(
			'bwo-admin-css',
			BWO_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			BWO_VERSION
		);

		wp_enqueue_script(
			'bwo-admin-js',
			BWO_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			BWO_VERSION,
			true
		);

		$summary = BWO_Media_Scanner::get_summary_stats();
		$settings = BWO_Settings::get_settings();
		$server   = BWO_Server_Check::get_report();

		wp_localize_script(
			'bwo-admin-js',
			'bwoData',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => BWO_Nonce::create(),
				'stats'     => $summary,
				'settings'  => $settings,
				'server'    => $server,
				'strings'   => array(
					'confirmConvertAll' => __( 'Are you sure you want to convert all eligible images to WebP? This will process in the background.', 'budi-webp-optimizer' ),
					'confirmRestore'    => __( 'Are you sure you want to restore the original image?', 'budi-webp-optimizer' ),
					'converting'        => __( 'Converting...', 'budi-webp-optimizer' ),
					'restoring'         => __( 'Restoring...', 'budi-webp-optimizer' ),
					'success'           => __( 'Success', 'budi-webp-optimizer' ),
					'failed'            => __( 'Failed', 'budi-webp-optimizer' ),
					'scanning'          => __( 'Scanning Media Library...', 'budi-webp-optimizer' ),
					'settingsSaved'     => __( 'Settings saved successfully!', 'budi-webp-optimizer' ),
				),
			)
		);
	}

	/**
	 * AJAX: Scan library and refresh counts.
	 */
	public static function ajax_scan_library() {
		BWO_Permissions::verify_or_die();
		BWO_Nonce::verify_or_die();

		$stats = BWO_Media_Scanner::get_summary_stats( true );
		wp_send_json_success( array( 'stats' => $stats ) );
	}

	/**
	 * AJAX: Query paginated image list.
	 */
	public static function ajax_get_images() {
		BWO_Permissions::verify_or_die();
		BWO_Nonce::verify_or_die();

		$paged    = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
		$per_page = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10;
		$search   = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
		$status   = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : 'all';

		$data = BWO_Media_Scanner::query_images(
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
		BWO_Permissions::verify_or_die();
		BWO_Nonce::verify_or_die();

		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : 0;
		if ( ! $attachment_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'budi-webp-optimizer' ) ) );
		}

		$result = BWO_Converter::convert( $attachment_id );
		if ( $result->success ) {
			$attachment = new BWO_Attachment( $attachment_id );
			wp_send_json_success(
				array(
					'message'    => __( 'Image successfully converted to WebP.', 'budi-webp-optimizer' ),
					'attachment' => $attachment->to_table_array(),
					'stats'      => BWO_Media_Scanner::get_summary_stats( true ),
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
		BWO_Permissions::verify_or_die();
		BWO_Nonce::verify_or_die();

		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : 0;
		if ( ! $attachment_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'budi-webp-optimizer' ) ) );
		}

		$result = BWO_Restore_Manager::restore( $attachment_id );
		if ( true === $result ) {
			$attachment = new BWO_Attachment( $attachment_id );
			wp_send_json_success(
				array(
					'message'    => __( 'Original image restored successfully.', 'budi-webp-optimizer' ),
					'attachment' => $attachment->to_table_array(),
					'stats'      => BWO_Media_Scanner::get_summary_stats( true ),
				)
			);
		} else {
			$msg = is_wp_error( $result ) ? $result->get_error_message() : __( 'Restore failed.', 'budi-webp-optimizer' );
			wp_send_json_error( array( 'message' => $msg ) );
		}
	}

	/**
	 * AJAX: Start bulk conversion.
	 */
	public static function ajax_start_bulk() {
		BWO_Permissions::verify_or_die();
		BWO_Nonce::verify_or_die();

		$mode = isset( $_POST['mode'] ) ? sanitize_key( $_POST['mode'] ) : 'all'; // 'all', 'selected', 'pending_only'
		$ids  = array();

		if ( 'selected' === $mode && ! empty( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {
			$ids = array_map( 'absint', $_POST['ids'] );
		} else {
			$ids = BWO_Media_Scanner::get_eligible_ids_for_bulk( $mode );
		}

		if ( empty( $ids ) ) {
			wp_send_json_error( array( 'message' => __( 'No eligible images found for conversion.', 'budi-webp-optimizer' ) ) );
		}

		$queue_state = BWO_Job_Queue::initialize_queue( $ids );
		wp_send_json_success(
			array(
				'message' => sprintf( __( 'Queued %d images for conversion.', 'budi-webp-optimizer' ), count( $ids ) ),
				'queue'   => $queue_state,
			)
		);
	}

	/**
	 * AJAX: Process a batch from bulk conversion queue.
	 */
	public static function ajax_process_batch() {
		BWO_Permissions::verify_or_die();
		BWO_Nonce::verify_or_die();

		$settings   = BWO_Settings::get_settings();
		$batch_size = isset( $settings['batch_size'] ) ? $settings['batch_size'] : 5;
		$progress   = BWO_Job_Queue::process_batch( $batch_size );

		if ( ! empty( $progress['completed'] ) ) {
			$progress['stats'] = BWO_Media_Scanner::get_summary_stats( true );
		}

		wp_send_json_success( $progress );
	}

	/**
	 * AJAX: Cancel bulk queue.
	 */
	public static function ajax_cancel_bulk() {
		BWO_Permissions::verify_or_die();
		BWO_Nonce::verify_or_die();

		BWO_Job_Queue::cancel_queue();
		wp_send_json_success( array( 'message' => __( 'Bulk conversion cancelled.', 'budi-webp-optimizer' ) ) );
	}

	/**
	 * AJAX: Save settings.
	 */
	public static function ajax_save_settings() {
		BWO_Permissions::verify_or_die();
		BWO_Nonce::verify_or_die();

		$settings = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? $_POST['settings'] : array();
		$updated  = BWO_Settings::update_settings( $settings );

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
		BWO_Permissions::verify_or_die();
		BWO_Nonce::verify_or_die();

		$attachment_id = isset( $_POST['attachment_id'] ) ? absint( $_POST['attachment_id'] ) : 0;
		if ( ! $attachment_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'budi-webp-optimizer' ) ) );
		}

		$attachment = new BWO_Attachment( $attachment_id );
		$meta       = get_post_meta( $attachment_id, '_bwo_webp_data', true );
		$backup     = BWO_Backup_Manager::get_backup( $attachment_id );

		$orig_url = wp_get_attachment_url( $attachment_id );
		$webp_url = '';

		if ( ! empty( $meta['webp_file'] ) && file_exists( $meta['webp_file'] ) ) {
			$webp_url = BWO_Utils::get_webp_url( $orig_url );
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
				'original_size' => BWO_Formatting::format_bytes( $attachment->original_size ),
				'webp_size'     => $attachment->webp_size > 0 ? BWO_Formatting::format_bytes( $attachment->webp_size ) : '—',
				'savings'       => $attachment->savings_percent > 0 ? round( $attachment->savings_percent ) . '%' : '—',
			)
		);
	}

	/**
	 * Hook: Auto convert when new image is uploaded.
	 */
	public static function handle_auto_convert_on_upload( $metadata, $attachment_id ) {
		$settings = BWO_Settings::get_settings();
		if ( empty( $settings['auto_convert'] ) ) {
			return $metadata;
		}

		$mime = get_post_mime_type( $attachment_id );
		if ( in_array( $mime, array( 'image/jpeg', 'image/png' ), true ) ) {
			BWO_Converter::convert( $attachment_id );
		}

		return $metadata;
	}

	/**
	 * Add WebP column to WP Media Library.
	 */
	public static function add_media_columns( $columns ) {
		$columns['bwo_webp'] = __( 'WebP Optimizer', 'budi-webp-optimizer' );
		return $columns;
	}

	/**
	 * Render WebP column content.
	 */
	public static function render_media_columns( $column_name, $post_id ) {
		if ( 'bwo_webp' !== $column_name ) {
			return;
		}

		$is_conv = get_post_meta( $post_id, '_bwo_converted', true );
		$is_fail = get_post_meta( $post_id, '_bwo_failed', true );
		$data    = get_post_meta( $post_id, '_bwo_webp_data', true );

		if ( $is_conv ) {
			$pct = isset( $data['saved_percent'] ) ? round( $data['saved_percent'] ) : 0;
			echo '<span style="color:#15803d; font-weight:600;">✓ ' . esc_html__( 'WebP', 'budi-webp-optimizer' ) . ' (-' . esc_html( $pct ) . '%)</span>';
		} elseif ( $is_fail ) {
			echo '<span style="color:#b91c1c; font-weight:600;">✗ ' . esc_html__( 'Failed', 'budi-webp-optimizer' ) . '</span>';
		} else {
			echo '<span style="color:#c2410c;">— ' . esc_html__( 'Pending', 'budi-webp-optimizer' ) . '</span>';
		}
	}
}
