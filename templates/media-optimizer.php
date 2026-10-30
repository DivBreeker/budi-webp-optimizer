<?php
/**
 * Dedicated Media Library Optimizer View.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$summary = BWO_Media_Scanner::get_summary_stats();
$server  = BWO_Server_Check::get_report();
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
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=bwo-bulk-convert' ) ); ?>" class="bwo-header-link">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
				<span><?php esc_html_e( 'Bulk Convert', 'budi-webp-optimizer' ); ?></span>
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
				<h1><?php esc_html_e( 'Media Library Optimizer', 'budi-webp-optimizer' ); ?></h1>
				<span class="bwo-version-badge">v<?php echo esc_html( BWO_VERSION ); ?></span>
			</div>
			<p class="bwo-subtitle"><?php esc_html_e( 'Search, filter, inspect, and optimize individual or selected media attachments in your WordPress library.', 'budi-webp-optimizer' ); ?></p>
		</div>
		<div class="bwo-title-actions">
			<button type="button" class="bwo-btn bwo-btn-white" id="bwo-btn-scan">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="bwo-icon-spin-target"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
				<span><?php esc_html_e( 'Scan Library', 'budi-webp-optimizer' ); ?></span>
			</button>
		</div>
	</div>

	<!-- Mini Stats Summary Bar -->
	<div class="bwo-card bwo-media-summary-bar" style="margin-bottom: 20px; padding: 16px 24px;">
		<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
			<div style="display: flex; gap: 28px; flex-wrap: wrap;">
				<div>
					<span style="font-size: 12px; color: #64748b; display: block; font-weight: 500;"><?php esc_html_e( 'Total Images', 'budi-webp-optimizer' ); ?></span>
					<strong style="font-size: 18px; color: #0f172a;" id="bwo-stat-total"><?php echo esc_html( number_format_i18n( $summary['total_images'] ) ); ?></strong>
				</div>
				<div style="border-left: 1px solid #e2e8f0; padding-left: 24px;">
					<span style="font-size: 12px; color: #64748b; display: block; font-weight: 500;"><?php esc_html_e( 'Converted to WebP', 'budi-webp-optimizer' ); ?></span>
					<strong style="font-size: 18px; color: #10b981;" id="bwo-stat-converted"><?php echo esc_html( number_format_i18n( $summary['converted_images'] ) ); ?> (<?php echo esc_html( $summary['percent_completed'] ); ?>%)</strong>
				</div>
				<div style="border-left: 1px solid #e2e8f0; padding-left: 24px;">
					<span style="font-size: 12px; color: #64748b; display: block; font-weight: 500;"><?php esc_html_e( 'Storage Saved', 'budi-webp-optimizer' ); ?></span>
					<strong style="font-size: 18px; color: #059669;" id="bwo-stat-saved"><?php echo esc_html( $summary['storage_saved_f'] ); ?></strong>
				</div>
				<div style="border-left: 1px solid #e2e8f0; padding-left: 24px;">
					<span style="font-size: 12px; color: #64748b; display: block; font-weight: 500;"><?php esc_html_e( 'Pending Conversion', 'budi-webp-optimizer' ); ?></span>
					<strong style="font-size: 18px; color: #f97316;" id="bwo-stat-pending"><?php echo esc_html( number_format_i18n( $summary['pending_images'] ) ); ?></strong>
				</div>
			</div>
			<div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=bwo-bulk-convert' ) ); ?>" class="bwo-btn bwo-btn-green bwo-btn-sm">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
					<span><?php esc_html_e( 'Go to Bulk Convert', 'budi-webp-optimizer' ); ?></span>
				</a>
			</div>
		</div>
	</div>

	<!-- Main Media Table Card -->
	<div class="bwo-tabs-container">
		<div class="bwo-tabs-header">
			<nav class="bwo-nav-tabs">
				<button type="button" class="bwo-tab-btn active bwo-media-filter-btn" data-status="all">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
					<span><?php esc_html_e( 'All Images', 'budi-webp-optimizer' ); ?></span>
				</button>
				<button type="button" class="bwo-tab-btn bwo-media-filter-btn" data-status="converted">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
					<span><?php esc_html_e( 'Converted Only', 'budi-webp-optimizer' ); ?></span>
				</button>
				<button type="button" class="bwo-tab-btn bwo-media-filter-btn" data-status="pending">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
					<span><?php esc_html_e( 'Pending Only', 'budi-webp-optimizer' ); ?></span>
				</button>
				<button type="button" class="bwo-tab-btn bwo-media-filter-btn" data-status="failed">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
					<span><?php esc_html_e( 'Failed Only', 'budi-webp-optimizer' ); ?></span>
				</button>
			</nav>

			<!-- Search & Filter Controls -->
			<div class="bwo-table-toolbar">
				<div class="bwo-search-wrap">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
					<input type="text" id="bwo-search-input" placeholder="<?php esc_attr_e( 'Search by filename...', 'budi-webp-optimizer' ); ?>" />
				</div>
			</div>
		</div>

		<!-- Bulk Selection Action Bar -->
		<div class="bwo-bulk-bar" id="bwo-bulk-bar" style="display: none;">
			<span class="bwo-bulk-count"><strong id="bwo-selected-count">0</strong> <?php esc_html_e( 'images selected', 'budi-webp-optimizer' ); ?></span>
			<div class="bwo-bulk-btns">
				<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-green" id="bwo-btn-convert-selected">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
					<span><?php esc_html_e( 'Convert Selected', 'budi-webp-optimizer' ); ?></span>
				</button>
				<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white" id="bwo-btn-restore-selected">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
					<span><?php esc_html_e( 'Restore Selected', 'budi-webp-optimizer' ); ?></span>
				</button>
				<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-link" id="bwo-btn-clear-selection">
					<span><?php esc_html_e( 'Deselect All', 'budi-webp-optimizer' ); ?></span>
				</button>
			</div>
		</div>

		<div class="bwo-table-responsive">
			<table class="bwo-table" id="bwo-images-table">
				<thead>
					<tr>
						<th class="bwo-th-check"><input type="checkbox" id="bwo-select-all" /></th>
						<th class="bwo-th-image"><?php esc_html_e( 'Image', 'budi-webp-optimizer' ); ?></th>
						<th class="bwo-th-name"><?php esc_html_e( 'File Name', 'budi-webp-optimizer' ); ?></th>
						<th class="bwo-th-orig"><?php esc_html_e( 'Original Size', 'budi-webp-optimizer' ); ?></th>
						<th class="bwo-th-webp"><?php esc_html_e( 'WebP Size', 'budi-webp-optimizer' ); ?></th>
						<th class="bwo-th-savings"><?php esc_html_e( 'Savings', 'budi-webp-optimizer' ); ?></th>
						<th class="bwo-th-status"><?php esc_html_e( 'Status', 'budi-webp-optimizer' ); ?></th>
						<th class="bwo-th-actions"><?php esc_html_e( 'Actions', 'budi-webp-optimizer' ); ?></th>
					</tr>
				</thead>
				<tbody id="bwo-table-body">
					<tr>
						<td colspan="8" class="bwo-td-loading">
							<div class="bwo-spinner"></div>
							<span><?php esc_html_e( 'Loading image attachments...', 'budi-webp-optimizer' ); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Pagination -->
		<div class="bwo-pagination-wrap" id="bwo-pagination" style="display: none;">
			<div class="bwo-pagination-info" id="bwo-pagination-info"></div>
			<div class="bwo-pagination-btns">
				<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white" id="bwo-pg-prev">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
					<span><?php esc_html_e( 'Previous', 'budi-webp-optimizer' ); ?></span>
				</button>
				<span class="bwo-pg-current" id="bwo-pg-num">1</span>
				<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white" id="bwo-pg-next">
					<span><?php esc_html_e( 'Next', 'budi-webp-optimizer' ); ?></span>
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal 1: Image Comparison / Preview Modal -->
<div class="bwo-modal-backdrop" id="bwo-preview-modal" style="display: none;">
	<div class="bwo-modal-dialog">
		<div class="bwo-modal-header">
			<h3 class="bwo-modal-title"><?php esc_html_e( 'Image Comparison & Inspection', 'budi-webp-optimizer' ); ?></h3>
			<button type="button" class="bwo-modal-close" data-close-modal>&times;</button>
		</div>
		<div class="bwo-modal-body">
			<div class="bwo-preview-stats">
				<div class="bwo-pstat">
					<span><?php esc_html_e( 'Original Size', 'budi-webp-optimizer' ); ?></span>
					<strong id="bwo-pv-orig-size">—</strong>
				</div>
				<div class="bwo-pstat-arrow">→</div>
				<div class="bwo-pstat">
					<span><?php esc_html_e( 'WebP Size', 'budi-webp-optimizer' ); ?></span>
					<strong id="bwo-pv-webp-size" class="bwo-text-success">—</strong>
				</div>
				<div class="bwo-pstat-badge" id="bwo-pv-savings-badge">-0%</div>
			</div>

			<div class="bwo-preview-compare-grid">
				<div class="bwo-preview-side">
					<span class="bwo-preview-tag"><?php esc_html_e( 'Original (JPG/PNG)', 'budi-webp-optimizer' ); ?></span>
					<div class="bwo-preview-img-wrap">
						<img id="bwo-pv-orig-img" src="" alt="Original" />
					</div>
				</div>
				<div class="bwo-preview-side">
					<span class="bwo-preview-tag bwo-tag-green"><?php esc_html_e( 'Optimized (WebP)', 'budi-webp-optimizer' ); ?></span>
					<div class="bwo-preview-img-wrap">
						<img id="bwo-pv-webp-img" src="" alt="WebP" />
					</div>
				</div>
			</div>
		</div>
		<div class="bwo-modal-footer">
			<button type="button" class="bwo-btn bwo-btn-white" data-close-modal><?php esc_html_e( 'Close', 'budi-webp-optimizer' ); ?></button>
		</div>
	</div>
</div>

<!-- Modal 2: Bulk Progress Modal -->
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
