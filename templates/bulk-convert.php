<?php
/**
 * Dedicated Bulk Conversion Center.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$summary  = BWO_Media_Scanner::get_summary_stats();
$settings = BWO_Settings::get_settings();
$server   = BWO_Server_Check::get_report();
?>

<div class="bwo-wrap">
	<!-- Top App Header -->
	<header class="bwo-top-header">
		<div class="bwo-brand-col">
			<div class="bwo-brand-logo">
				<span class="bwo-brand-text">BUDI<span class="bwo-brand-dot">.</span>Studio</span>
			</div>
		</div>
		<div class="bwo-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=budi-webp-optimizer' ) ); ?>" class="bwo-header-link">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
				<span><?php esc_html_e( 'Dashboard', 'budi-webp-optimizer' ); ?></span>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=bwo-media-library' ) ); ?>" class="bwo-header-link">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
				<span><?php esc_html_e( 'Media Library', 'budi-webp-optimizer' ); ?></span>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=bwo-settings' ) ); ?>" class="bwo-header-link">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
				<span><?php esc_html_e( 'Settings', 'budi-webp-optimizer' ); ?></span>
			</a>
		</div>
	</header>

	<!-- Main Title Bar -->
	<div class="bwo-title-bar">
		<div class="bwo-title-meta">
			<div class="bwo-title-heading">
				<h1><?php esc_html_e( 'Bulk WebP Conversion', 'budi-webp-optimizer' ); ?></h1>
				<span class="bwo-version-badge">v<?php echo esc_html( BWO_VERSION ); ?></span>
			</div>
			<p class="bwo-subtitle"><?php esc_html_e( 'Batch convert your image attachments in safe, background AJAX chunks without server timeouts.', 'budi-webp-optimizer' ); ?></p>
		</div>
	</div>

	<?php if ( ! $server['supports_webp'] ) : ?>
		<div class="bwo-alert bwo-alert-danger">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
			<div>
				<strong><?php esc_html_e( 'WebP Encoding Not Supported!', 'budi-webp-optimizer' ); ?></strong>
				<p><?php esc_html_e( 'Server WebP support is required to run bulk conversion.', 'budi-webp-optimizer' ); ?></p>
			</div>
		</div>
	<?php endif; ?>

	<div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 20px; align-items: start;">
		<!-- Left: Bulk Scope & Settings Form -->
		<div class="bwo-card">
			<h2 class="bwo-card-title"><?php esc_html_e( 'Select Optimization Scope', 'budi-webp-optimizer' ); ?></h2>
			<p class="bwo-desc"><?php esc_html_e( 'Choose which images you want to include in this bulk conversion run.', 'budi-webp-optimizer' ); ?></p>

			<div class="bwo-radio-cards" style="grid-template-columns: 1fr; gap: 12px; margin-bottom: 24px;">
				<label class="bwo-radio-card selected">
					<input type="radio" name="bulk_scope" value="all" checked />
					<div class="bwo-radio-content">
						<strong><?php esc_html_e( 'Convert All Eligible Images', 'budi-webp-optimizer' ); ?> (<?php echo esc_html( number_format_i18n( $summary['total_images'] ) ); ?> <?php esc_html_e( 'images', 'budi-webp-optimizer' ); ?>)</strong>
						<span><?php esc_html_e( 'Process every JPEG and PNG in your Media Library to ensure 100% WebP coverage.', 'budi-webp-optimizer' ); ?></span>
					</div>
				</label>

				<label class="bwo-radio-card">
					<input type="radio" name="bulk_scope" value="pending_only" />
					<div class="bwo-radio-content">
						<strong><?php esc_html_e( 'Convert Pending Images Only', 'budi-webp-optimizer' ); ?> (<?php echo esc_html( number_format_i18n( $summary['pending_images'] ) ); ?> <?php esc_html_e( 'pending', 'budi-webp-optimizer' ); ?>)</strong>
						<span><?php esc_html_e( 'Fast and efficient: only processes images that have not yet been optimized.', 'budi-webp-optimizer' ); ?></span>
					</div>
				</label>

				<?php if ( $summary['failed_images'] > 0 ) : ?>
					<label class="bwo-radio-card">
						<input type="radio" name="bulk_scope" value="failed_only" />
						<div class="bwo-radio-content">
							<strong><?php esc_html_e( 'Retry Failed Images Only', 'budi-webp-optimizer' ); ?> (<?php echo esc_html( number_format_i18n( $summary['failed_images'] ) ); ?> <?php esc_html_e( 'failed', 'budi-webp-optimizer' ); ?>)</strong>
							<span><?php esc_html_e( 'Attempts to re-encode only images that previously threw errors.', 'budi-webp-optimizer' ); ?></span>
						</div>
					</label>
				<?php endif; ?>
			</div>

			<div class="bwo-settings-section" style="border-top: 1px solid #f1f5f9; padding-top: 20px; margin-bottom: 20px;">
				<div class="bwo-slider-header">
					<div>
						<h3 style="font-size: 15px; margin: 0; font-weight: 700;"><?php esc_html_e( 'WebP Quality', 'budi-webp-optimizer' ); ?></h3>
						<span style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Compression factor for generated WebP files.', 'budi-webp-optimizer' ); ?></span>
					</div>
					<span class="bwo-slider-val-badge" id="bwo-quality-display"><?php echo esc_html( $settings['quality'] ); ?>%</span>
				</div>
				<input type="range" name="quality" id="bwo-quality-slider" min="50" max="100" value="<?php echo esc_attr( $settings['quality'] ); ?>" class="bwo-range-slider" />
			</div>

			<div style="margin-bottom: 20px;">
				<label class="bwo-switch-row">
					<input type="checkbox" name="bulk_keep_backup" id="bwo-bulk-keep-backup" value="1" <?php checked( $settings['keep_backup'], 1 ); ?> />
					<div>
						<strong><?php esc_html_e( 'Keep recoverable backup of original files', 'budi-webp-optimizer' ); ?></strong>
						<span style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Uncheck if you do NOT want to store backup copies in the server backup directory.', 'budi-webp-optimizer' ); ?></span>
					</div>
				</label>
			</div>

			<button type="button" class="bwo-action-btn-green" id="bwo-btn-trigger-bulk" style="justify-content: center; font-size: 16px; padding: 18px 24px;">
				<div class="bwo-btn-icon-circle">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
				</div>
				<div class="bwo-action-btn-text">
					<strong class="bwo-btn-headline" style="font-size: 16px;"><?php esc_html_e( 'Start Bulk Optimization', 'budi-webp-optimizer' ); ?></strong>
					<span class="bwo-btn-subline"><?php esc_html_e( 'Runs in background batches with live progress tracking', 'budi-webp-optimizer' ); ?></span>
				</div>
			</button>
		</div>

		<!-- Right: Live Progress & Status Card -->
		<div class="bwo-card" id="bwo-bulk-live-card">
			<h2 class="bwo-card-title"><?php esc_html_e( 'Live Optimization Status', 'budi-webp-optimizer' ); ?></h2>
			<p class="bwo-card-subhead" id="bwo-bulk-card-subhead"><?php esc_html_e( 'Status: Ready to start.', 'budi-webp-optimizer' ); ?></p>

			<div style="text-align: center; margin: 24px 0 16px 0;">
				<div class="bwo-bulk-pct" id="bwo-bulk-page-pct">0%</div>
				<span style="font-size: 13px; color: #64748b;" id="bwo-bulk-page-status"><?php esc_html_e( 'Idle', 'budi-webp-optimizer' ); ?></span>
			</div>

			<div class="bwo-progress-bar-track" style="margin-bottom: 24px;">
				<div class="bwo-progress-bar-fill" id="bwo-bulk-page-bar" style="width: 0%;"></div>
			</div>

			<div class="bwo-bulk-details-grid" style="margin-bottom: 20px;">
				<div class="bwo-bdet">
					<span><?php esc_html_e( 'Processed', 'budi-webp-optimizer' ); ?></span>
					<strong id="bwo-bulk-page-processed">0 / 0</strong>
				</div>
				<div class="bwo-bdet">
					<span><?php esc_html_e( 'Successful', 'budi-webp-optimizer' ); ?></span>
					<strong class="bwo-text-success" id="bwo-bulk-page-success">0</strong>
				</div>
				<div class="bwo-bdet">
					<span><?php esc_html_e( 'Failed', 'budi-webp-optimizer' ); ?></span>
					<strong class="bwo-text-danger" id="bwo-bulk-page-failed">0</strong>
				</div>
			</div>

			<div class="bwo-bulk-current-item" style="margin-bottom: 24px;">
				<span class="bwo-current-label"><?php esc_html_e( 'Current file:', 'budi-webp-optimizer' ); ?></span>
				<span class="bwo-current-filename" id="bwo-bulk-page-current">—</span>
			</div>

			<div style="display: flex; gap: 12px; justify-content: center;" id="bwo-bulk-page-controls">
				<button type="button" class="bwo-btn bwo-btn-white" id="bwo-btn-page-pause" style="display: none;"><?php esc_html_e( 'Pause', 'budi-webp-optimizer' ); ?></button>
				<button type="button" class="bwo-btn bwo-btn-danger" id="bwo-btn-page-cancel" style="display: none;"><?php esc_html_e( 'Cancel Queue', 'budi-webp-optimizer' ); ?></button>
			</div>
		</div>
	</div>
</div>

<!-- Modal 2: Bulk Progress Modal (reused if initiated from header) -->
<div class="bwo-modal-backdrop" id="bwo-bulk-modal" style="display: none;">
	<div class="bwo-modal-dialog bwo-bulk-dialog">
		<div class="bwo-modal-header">
			<h3 class="bwo-modal-title"><?php esc_html_e( 'Bulk WebP Conversion', 'budi-webp-optimizer' ); ?></h3>
			<button type="button" class="bwo-modal-close" id="bwo-bulk-close-btn">&times;</button>
		</div>
		<div class="bwo-modal-body">
			<div class="bwo-bulk-hero">
				<div class="bwo-bulk-pct" id="bwo-bulk-pct-display">0%</div>
				<p class="bwo-bulk-status-text" id="bwo-bulk-status-text"><?php esc_html_e( 'Preparing conversion queue...', 'budi-webp-optimizer' ); ?></p>
			</div>

			<div class="bwo-progress-bar-track">
				<div class="bwo-progress-bar-fill" id="bwo-bulk-progress-bar" style="width: 0%;"></div>
			</div>

			<div class="bwo-bulk-details-grid">
				<div class="bwo-bdet">
					<span><?php esc_html_e( 'Processed', 'budi-webp-optimizer' ); ?></span>
					<strong id="bwo-bulk-processed-count">0 / 0</strong>
				</div>
				<div class="bwo-bdet">
					<span><?php esc_html_e( 'Successful', 'budi-webp-optimizer' ); ?></span>
					<strong class="bwo-text-success" id="bwo-bulk-success-count">0</strong>
				</div>
				<div class="bwo-bdet">
					<span><?php esc_html_e( 'Failed', 'budi-webp-optimizer' ); ?></span>
					<strong class="bwo-text-danger" id="bwo-bulk-fail-count">0</strong>
				</div>
			</div>

			<div class="bwo-bulk-current-item">
				<span class="bwo-current-label"><?php esc_html_e( 'Currently processing:', 'budi-webp-optimizer' ); ?></span>
				<span class="bwo-current-filename" id="bwo-bulk-current-filename">—</span>
			</div>
		</div>
		<div class="bwo-modal-footer">
			<button type="button" class="bwo-btn bwo-btn-white" id="bwo-btn-bulk-pause"><?php esc_html_e( 'Pause', 'budi-webp-optimizer' ); ?></button>
			<button type="button" class="bwo-btn bwo-btn-danger" id="bwo-btn-bulk-cancel"><?php esc_html_e( 'Cancel', 'budi-webp-optimizer' ); ?></button>
			<button type="button" class="bwo-btn bwo-btn-green" id="bwo-btn-bulk-finish" style="display: none;"><?php esc_html_e( 'Done', 'budi-webp-optimizer' ); ?></button>
		</div>
	</div>
</div>
