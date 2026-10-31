<?php
/**
 * Dashboard template.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$summary  = BWO_Media_Scanner::get_summary_stats();
$server   = BWO_Server_Check::get_report();
$settings = BWO_Settings::get_settings();
$logs     = BWO_Logger::get_logs();
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
			<a href="#help-modal" class="bwo-header-link" id="bwo-btn-help">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
				<span><?php esc_html_e( 'Help', 'budi-webp-optimizer' ); ?></span>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=bwo-settings' ) ); ?>" class="bwo-header-link" id="bwo-btn-header-settings">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
				<span><?php esc_html_e( 'Settings', 'budi-webp-optimizer' ); ?></span>
			</a>
		</div>
	</header>

	<!-- Main Title Banner -->
	<div class="bwo-title-bar">
		<div class="bwo-title-meta">
			<div class="bwo-title-heading">
				<h1><?php esc_html_e( 'WebP Optimizer', 'budi-webp-optimizer' ); ?></h1>
				<span class="bwo-version-badge">v<?php echo esc_html( BWO_VERSION ); ?></span>
			</div>
			<p class="bwo-subtitle"><?php esc_html_e( 'Convert, optimize and manage your images for a faster WordPress website.', 'budi-webp-optimizer' ); ?></p>
		</div>
		<div class="bwo-title-actions">
			<button type="button" class="bwo-btn bwo-btn-white" id="bwo-btn-scan">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="bwo-icon-spin-target"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
				<span><?php esc_html_e( 'Scan Media Library', 'budi-webp-optimizer' ); ?></span>
			</button>
		</div>
	</div>

	<?php if ( ! $server['supports_webp'] ) : ?>
		<div class="bwo-alert bwo-alert-danger">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
			<div>
				<strong><?php esc_html_e( 'WebP Encoding Not Supported!', 'budi-webp-optimizer' ); ?></strong>
				<p><?php esc_html_e( 'Neither PHP GD (with WebP) nor Imagick (with WebP) was detected on your server. Please contact your host to enable WebP support.', 'budi-webp-optimizer' ); ?></p>
			</div>
		</div>
	<?php endif; ?>

	<!-- 4 Stat Summary Cards -->
	<div class="bwo-stats-grid">
		<!-- Card 1: Total Images -->
		<div class="bwo-stat-card">
			<div class="bwo-stat-icon-wrapper bwo-icon-mint">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
			</div>
			<div class="bwo-stat-content">
				<span class="bwo-stat-label"><?php esc_html_e( 'Total Images', 'budi-webp-optimizer' ); ?></span>
				<div class="bwo-stat-value" id="bwo-stat-total"><?php echo esc_html( number_format_i18n( $summary['total_images'] ) ); ?></div>
				<span class="bwo-stat-subtext"><?php esc_html_e( 'JPG, PNG and others', 'budi-webp-optimizer' ); ?></span>
			</div>
		</div>

		<!-- Card 2: Converted to WebP -->
		<div class="bwo-stat-card">
			<div class="bwo-stat-icon-wrapper bwo-icon-blue">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
			</div>
			<div class="bwo-stat-content">
				<span class="bwo-stat-label"><?php esc_html_e( 'Converted to WebP', 'budi-webp-optimizer' ); ?></span>
				<div class="bwo-stat-value" id="bwo-stat-converted"><?php echo esc_html( number_format_i18n( $summary['converted_images'] ) ); ?></div>
				<span class="bwo-stat-subtext bwo-text-success" id="bwo-stat-percent"><?php echo esc_html( $summary['percent_completed'] ); ?>% <?php esc_html_e( 'completed', 'budi-webp-optimizer' ); ?></span>
			</div>
		</div>

		<!-- Card 3: Storage Saved -->
		<div class="bwo-stat-card">
			<div class="bwo-stat-icon-wrapper bwo-icon-teal">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
			</div>
			<div class="bwo-stat-content">
				<span class="bwo-stat-label"><?php esc_html_e( 'Storage Saved', 'budi-webp-optimizer' ); ?></span>
				<div class="bwo-stat-value" id="bwo-stat-saved"><?php echo esc_html( $summary['storage_saved_f'] ); ?></div>
				<span class="bwo-stat-subtext" id="bwo-stat-reduction"><?php echo esc_html( $summary['reduction_label'] ); ?></span>
			</div>
		</div>

		<!-- Card 4: Pending Images -->
		<div class="bwo-stat-card">
			<div class="bwo-stat-icon-wrapper bwo-icon-orange">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
			</div>
			<div class="bwo-stat-content">
				<span class="bwo-stat-label"><?php esc_html_e( 'Pending Images', 'budi-webp-optimizer' ); ?></span>
				<div class="bwo-stat-value" id="bwo-stat-pending"><?php echo esc_html( number_format_i18n( $summary['pending_images'] ) ); ?></div>
				<span class="bwo-stat-subtext"><?php esc_html_e( 'Not yet converted', 'budi-webp-optimizer' ); ?></span>
			</div>
		</div>
	</div>

	<!-- Middle Section: Optimization Progress + Quick Actions -->
	<div class="bwo-middle-grid">
		<!-- Left: Image Optimization Progress -->
		<div class="bwo-card bwo-progress-card">
			<h2 class="bwo-card-title"><?php esc_html_e( 'Image Optimization Progress', 'budi-webp-optimizer' ); ?></h2>
			
			<div class="bwo-progress-body">
				<!-- Donut Gauge Chart -->
				<div class="bwo-donut-container">
					<svg class="bwo-donut-svg" viewBox="0 0 100 100">
						<circle class="bwo-donut-meter" cx="50" cy="50" r="40" stroke="#f1f5f9" stroke-width="8" fill="none" />
						<circle class="bwo-donut-value" id="bwo-donut-circle" cx="50" cy="50" r="40" stroke="#10b981" stroke-width="8" stroke-linecap="round" fill="none"
							stroke-dasharray="251.2"
							stroke-dashoffset="<?php echo esc_attr( 251.2 - ( 251.2 * ( $summary['percent_completed'] / 100 ) ) ); ?>" />
					</svg>
					<div class="bwo-donut-inner">
						<span class="bwo-donut-number" id="bwo-donut-pct"><?php echo esc_html( $summary['percent_donut'] ); ?>%</span>
						<span class="bwo-donut-label"><?php esc_html_e( 'Converted', 'budi-webp-optimizer' ); ?></span>
					</div>
				</div>

				<!-- Counts Legend -->
				<div class="bwo-legend-list">
					<div class="bwo-legend-row">
						<span class="bwo-legend-bullet bwo-bullet-green"></span>
						<span class="bwo-legend-name"><?php esc_html_e( 'Converted to WebP', 'budi-webp-optimizer' ); ?></span>
						<span class="bwo-legend-count" id="bwo-legend-conv"><?php echo esc_html( number_format_i18n( $summary['converted_images'] ) ); ?></span>
					</div>
					<div class="bwo-legend-row">
						<span class="bwo-legend-bullet bwo-bullet-gray"></span>
						<span class="bwo-legend-name"><?php esc_html_e( 'Pending', 'budi-webp-optimizer' ); ?></span>
						<span class="bwo-legend-count" id="bwo-legend-pend"><?php echo esc_html( number_format_i18n( $summary['pending_images'] ) ); ?></span>
					</div>
					<div class="bwo-legend-row">
						<span class="bwo-legend-bullet bwo-bullet-red"></span>
						<span class="bwo-legend-name"><?php esc_html_e( 'Failed', 'budi-webp-optimizer' ); ?></span>
						<span class="bwo-legend-count" id="bwo-legend-fail"><?php echo esc_html( number_format_i18n( $summary['failed_images'] ) ); ?></span>
					</div>
				</div>

				<!-- Size Metrics -->
				<div class="bwo-size-metrics">
					<div class="bwo-metric-item">
						<span class="bwo-metric-label"><?php esc_html_e( 'Original Size', 'budi-webp-optimizer' ); ?></span>
						<span class="bwo-metric-val" id="bwo-metric-orig"><?php echo esc_html( $summary['original_size_f'] ); ?></span>
					</div>
					<div class="bwo-metric-item">
						<span class="bwo-metric-label"><?php esc_html_e( 'Current Size', 'budi-webp-optimizer' ); ?></span>
						<span class="bwo-metric-val" id="bwo-metric-curr"><?php echo esc_html( $summary['current_size_f'] ); ?></span>
					</div>
					<div class="bwo-metric-item">
						<span class="bwo-metric-label"><?php esc_html_e( 'Saved', 'budi-webp-optimizer' ); ?></span>
						<span class="bwo-metric-val bwo-text-success bwo-metric-saved" id="bwo-metric-saved"><?php echo esc_html( $summary['storage_saved_f'] ); ?></span>
					</div>
				</div>
			</div>
		</div>

		<!-- Right: Quick Actions -->
		<div class="bwo-card bwo-actions-card">
			<h2 class="bwo-card-title"><?php esc_html_e( 'Quick Actions', 'budi-webp-optimizer' ); ?></h2>
			<p class="bwo-card-subhead"><?php esc_html_e( 'Choose how you want to optimize your images.', 'budi-webp-optimizer' ); ?></p>

			<div class="bwo-action-buttons">
				<!-- Convert All Images Button Card -->
				<button type="button" class="bwo-action-btn-green" id="bwo-btn-convert-all">
					<div class="bwo-btn-icon-circle">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
					</div>
					<div class="bwo-action-btn-text">
						<strong class="bwo-btn-headline"><?php esc_html_e( 'Convert All Images', 'budi-webp-optimizer' ); ?></strong>
						<span class="bwo-btn-subline"><?php esc_html_e( 'Convert all eligible images in Media Library', 'budi-webp-optimizer' ); ?></span>
					</div>
				</button>

				<!-- Select & Convert Button Card -->
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=bwo-media-library' ) ); ?>" class="bwo-action-btn-white" id="bwo-btn-select-convert" style="text-decoration: none;">
					<div class="bwo-btn-icon-box">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="15" x2="15" y2="15"></line></svg>
					</div>
					<div class="bwo-action-btn-text">
						<strong class="bwo-btn-headline-dark"><?php esc_html_e( 'Select & Convert', 'budi-webp-optimizer' ); ?></strong>
						<span class="bwo-btn-subline-dark"><?php esc_html_e( 'Choose specific images to convert in Media Library', 'budi-webp-optimizer' ); ?></span>
					</div>
				</a>
			</div>
		</div>
	</div>

	<!-- Tabs & Content Section -->
	<div class="bwo-tabs-container">
		<div class="bwo-tabs-header">
			<nav class="bwo-nav-tabs">
				<button type="button" class="bwo-tab-btn active" data-tab="tab-recent">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
					<span><?php esc_html_e( 'Recent Images', 'budi-webp-optimizer' ); ?></span>
				</button>
				<button type="button" class="bwo-tab-btn" data-tab="tab-settings">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
					<span><?php esc_html_e( 'Conversion Settings', 'budi-webp-optimizer' ); ?></span>
				</button>
				<button type="button" class="bwo-tab-btn" data-tab="tab-reports">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
					<span><?php esc_html_e( 'Storage & Activity Report', 'budi-webp-optimizer' ); ?></span>
				</button>
			</nav>

			<!-- Search & Filter Controls -->
			<div class="bwo-table-toolbar">
				<div class="bwo-search-wrap">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
					<input type="text" id="bwo-search-input" placeholder="<?php esc_attr_e( 'Search images...', 'budi-webp-optimizer' ); ?>" />
				</div>
				<div class="bwo-filter-wrap">
					<button type="button" class="bwo-btn bwo-btn-filter" id="bwo-btn-filter-toggle">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
						<span id="bwo-current-filter-label"><?php esc_html_e( 'Filter', 'budi-webp-optimizer' ); ?></span>
					</button>
					<div class="bwo-filter-dropdown" id="bwo-filter-dropdown" style="display: none;">
						<a href="#" data-status="all" class="bwo-filter-opt active"><?php esc_html_e( 'All Images', 'budi-webp-optimizer' ); ?></a>
						<a href="#" data-status="converted" class="bwo-filter-opt"><?php esc_html_e( 'Converted only', 'budi-webp-optimizer' ); ?></a>
						<a href="#" data-status="pending" class="bwo-filter-opt"><?php esc_html_e( 'Pending only', 'budi-webp-optimizer' ); ?></a>
						<a href="#" data-status="failed" class="bwo-filter-opt"><?php esc_html_e( 'Failed only', 'budi-webp-optimizer' ); ?></a>
					</div>
				</div>
			</div>
		</div>

		<!-- Tab 1: Recent Images Table -->
		<div class="bwo-tab-content active" id="tab-recent">
			<!-- Bulk Selection Action Bar -->
			<div class="bwo-bulk-bar" id="bwo-bulk-bar" style="display: none;">
				<span class="bwo-bulk-count"><strong id="bwo-selected-count">0</strong> <?php esc_html_e( 'images selected', 'budi-webp-optimizer' ); ?></span>
				<div class="bwo-bulk-btns">
					<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-green" id="bwo-btn-convert-selected">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
						<span><?php esc_html_e( 'Convert Selected', 'budi-webp-optimizer' ); ?></span>
					</button>
					<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white" id="bwo-btn-restore-selected">
						<span><?php esc_html_e( 'Restore Selected', 'budi-webp-optimizer' ); ?></span>
					</button>
					<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-link" id="bwo-btn-clear-selection">
						<span><?php esc_html_e( 'Deselect', 'budi-webp-optimizer' ); ?></span>
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

		<!-- Tab 2: Conversion Settings -->
		<div class="bwo-tab-content" id="tab-settings">
			<div class="bwo-settings-box">
				<form id="bwo-settings-form">
					<div class="bwo-settings-section">
						<h3><?php esc_html_e( 'Conversion Options', 'budi-webp-optimizer' ); ?></h3>
						<p class="bwo-desc"><?php esc_html_e( 'Choose how the plugin should handle original image files.', 'budi-webp-optimizer' ); ?></p>

						<div class="bwo-radio-cards">
							<label class="bwo-radio-card <?php echo esc_attr( 'keep' === $settings['conversion_mode'] ? 'selected' : '' ); ?>">
								<input type="radio" name="conversion_mode" value="keep" <?php checked( $settings['conversion_mode'], 'keep' ); ?> />
								<div class="bwo-radio-content">
									<strong><?php esc_html_e( 'Keep original and create WebP', 'budi-webp-optimizer' ); ?></strong>
									<span><?php esc_html_e( 'Original image and WebP both remain safely in Media Library. Safest method with zero alteration to existing uploads.', 'budi-webp-optimizer' ); ?></span>
								</div>
							</label>

							<label class="bwo-radio-card <?php echo esc_attr( 'replace' === $settings['conversion_mode'] ? 'selected' : '' ); ?>">
								<input type="radio" name="conversion_mode" value="replace" <?php checked( $settings['conversion_mode'], 'replace' ); ?> />
								<div class="bwo-radio-content">
									<strong><?php esc_html_e( 'Replace original with WebP', 'budi-webp-optimizer' ); ?></strong>
									<span><?php esc_html_e( 'Switch attachment image format directly to WebP, preserving Attachment IDs, updating thumbnails, and preserving WooCommerce/Elementor links.', 'budi-webp-optimizer' ); ?></span>
								</div>
							</label>
						</div>
					</div>

					<!-- Quality Slider -->
					<div class="bwo-settings-section">
						<div class="bwo-slider-header">
							<div>
								<h3><?php esc_html_e( 'WebP Quality', 'budi-webp-optimizer' ); ?></h3>
								<p class="bwo-desc"><?php esc_html_e( 'Balance between smaller file size and visual fidelity.', 'budi-webp-optimizer' ); ?></p>
							</div>
							<span class="bwo-slider-val-badge" id="bwo-quality-display"><?php echo esc_html( $settings['quality'] ); ?>%</span>
						</div>

						<input type="range" name="quality" id="bwo-quality-slider" min="50" max="100" value="<?php echo esc_attr( $settings['quality'] ); ?>" class="bwo-range-slider" />
						
						<div class="bwo-slider-ticks">
							<span class="bwo-tick-btn <?php echo esc_attr( 75 === (int) $settings['quality'] ? 'active' : '' ); ?>" data-val="75"><?php esc_html_e( 'Smaller file (75%)', 'budi-webp-optimizer' ); ?></span>
							<span class="bwo-tick-btn <?php echo esc_attr( 85 === (int) $settings['quality'] ? 'active' : '' ); ?>" data-val="85"><?php esc_html_e( 'Balanced (85%)', 'budi-webp-optimizer' ); ?></span>
							<span class="bwo-tick-btn <?php echo esc_attr( 96 === (int) $settings['quality'] ? 'active' : '' ); ?>" data-val="96"><?php esc_html_e( 'Highest quality (96%)', 'budi-webp-optimizer' ); ?></span>
						</div>
					</div>

					<!-- Checkboxes -->
					<div class="bwo-settings-section">
						<h3><?php esc_html_e( 'Safety & Automation', 'budi-webp-optimizer' ); ?></h3>

						<label class="bwo-switch-row">
							<input type="checkbox" name="keep_backup" value="1" <?php checked( $settings['keep_backup'], 1 ); ?> />
							<div>
								<strong><?php esc_html_e( 'Keep a recoverable backup of original files', 'budi-webp-optimizer' ); ?></strong>
								<span><?php esc_html_e( 'Safely stores untouched JPG/PNG copies in a protected folder for instant 1-click restore. Uncheck if you do NOT want backups saved on your server.', 'budi-webp-optimizer' ); ?></span>
							</div>
						</label>

						<label class="bwo-switch-row">
							<input type="checkbox" name="auto_convert" value="1" <?php checked( $settings['auto_convert'], 1 ); ?> />
							<div>
								<strong><?php esc_html_e( 'Automatic conversion on upload', 'budi-webp-optimizer' ); ?></strong>
								<span><?php esc_html_e( 'Automatically optimize new image uploads upon insertion into Media Library.', 'budi-webp-optimizer' ); ?></span>
							</div>
						</label>

						<label class="bwo-switch-row">
							<input type="checkbox" name="convert_thumbnails" value="1" <?php checked( $settings['convert_thumbnails'], 1 ); ?> />
							<div>
								<strong><?php esc_html_e( 'Optimize intermediate thumbnail sizes', 'budi-webp-optimizer' ); ?></strong>
								<span><?php esc_html_e( 'Also generate WebP versions of all registered WordPress and WooCommerce thumbnail sizes.', 'budi-webp-optimizer' ); ?></span>
							</div>
						</label>
					</div>

					<div class="bwo-settings-footer">
						<button type="submit" class="bwo-btn bwo-btn-green" id="bwo-btn-save-settings">
							<span><?php esc_html_e( 'Save Configuration', 'budi-webp-optimizer' ); ?></span>
						</button>
						<span class="bwo-save-status" id="bwo-save-status"></span>
					</div>
				</form>
			</div>
		</div>

		<!-- Tab 3: Storage & Activity Report -->
		<div class="bwo-tab-content" id="tab-reports">
			<div class="bwo-reports-box">
				<div class="bwo-report-grid">
					<div class="bwo-report-card">
						<h4><?php esc_html_e( 'Server Environment', 'budi-webp-optimizer' ); ?></h4>
						<ul class="bwo-report-list">
							<li><span><?php esc_html_e( 'WebP Support:', 'budi-webp-optimizer' ); ?></span> <strong class="<?php echo esc_attr( $server['supports_webp'] ? 'bwo-text-success' : 'bwo-text-danger' ); ?>"><?php echo esc_html( $server['supports_webp'] ? __( 'Active', 'budi-webp-optimizer' ) : __( 'Not Supported', 'budi-webp-optimizer' ) ); ?></strong></li>
							<li><span><?php esc_html_e( 'Active Encoder:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['active_engine'] ); ?></strong></li>
							<li><span><?php esc_html_e( 'PHP Version:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['php_version'] ); ?></strong></li>
							<li><span><?php esc_html_e( 'GD Extension:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['gd_webp'] ? '✓ WebP Enabled' : ( $server['has_gd'] ? 'Loaded (No WebP)' : 'Missing' ) ); ?></strong></li>
							<li><span><?php esc_html_e( 'Imagick Extension:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['imagick_webp'] ? '✓ WebP Enabled' : ( $server['has_imagick'] ? 'Loaded (No WebP)' : 'Missing' ) ); ?></strong></li>
							<li><span><?php esc_html_e( 'Memory Limit:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['memory_limit'] ); ?></strong></li>
						</ul>
					</div>

					<div class="bwo-report-card">
						<h4><?php esc_html_e( 'Storage Breakdown', 'budi-webp-optimizer' ); ?></h4>
						<ul class="bwo-report-list">
							<li><span><?php esc_html_e( 'Total Library Images:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( number_format_i18n( $summary['total_images'] ) ); ?></strong></li>
							<li><span><?php esc_html_e( 'Optimized Attachments:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( number_format_i18n( $summary['converted_images'] ) ); ?></strong></li>
							<li><span><?php esc_html_e( 'Original Library Size:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $summary['original_size_f'] ); ?></strong></li>
							<li><span><?php esc_html_e( 'Current Optimized Size:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $summary['current_size_f'] ); ?></strong></li>
							<li><span><?php esc_html_e( 'Total Disk Space Saved:', 'budi-webp-optimizer' ); ?></span> <strong class="bwo-text-success"><?php echo esc_html( $summary['storage_saved_f'] ); ?></strong></li>
							<li><span><?php esc_html_e( 'Average Size Reduction:', 'budi-webp-optimizer' ); ?></span> <strong class="bwo-text-success"><?php echo esc_html( $summary['reduction_label'] ); ?></strong></li>
						</ul>
					</div>
				</div>

				<!-- Activity Logs Section -->
				<div class="bwo-card bwo-table-card" style="margin: 0 24px 24px 24px; border: 1px solid #e2e8f0;">
					<div style="display: flex; justify-content: space-between; align-items: center; padding: 18px 24px 12px 24px;">
						<h3 class="bwo-card-title" style="margin: 0;"><?php esc_html_e( 'Recent Activity Logs', 'budi-webp-optimizer' ); ?></h3>
						<div style="display: flex; gap: 8px;">
							<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white" id="bwo-btn-refresh-logs">
								<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
								<span><?php esc_html_e( 'Refresh', 'budi-webp-optimizer' ); ?></span>
							</button>
							<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white" id="bwo-btn-clear-logs" style="color: #64748b;">
								<span><?php esc_html_e( 'Clear Logs', 'budi-webp-optimizer' ); ?></span>
							</button>
						</div>
					</div>

					<div class="bwo-table-responsive">
						<table class="bwo-table" id="bwo-dashboard-logs-table">
							<thead>
								<tr>
									<th style="width: 170px;"><?php esc_html_e( 'Timestamp', 'budi-webp-optimizer' ); ?></th>
									<th style="width: 90px;"><?php esc_html_e( 'Level', 'budi-webp-optimizer' ); ?></th>
									<th><?php esc_html_e( 'Activity Event', 'budi-webp-optimizer' ); ?></th>
								</tr>
							</thead>
							<tbody id="bwo-dashboard-logs-tbody">
								<?php if ( empty( $logs ) ) : ?>
									<tr>
										<td colspan="3" class="bwo-td-empty"><?php esc_html_e( 'No activity logged yet. Conversion and optimization events will appear here.', 'budi-webp-optimizer' ); ?></td>
									</tr>
								<?php else : ?>
									<?php foreach ( array_reverse( $logs ) as $log ) : ?>
										<tr>
											<td style="white-space: nowrap; color: #64748b; font-size: 12px;"><?php echo esc_html( BWO_Formatting::format_date( $log['time'] ) ); ?></td>
											<td><span class="bwo-badge bwo-badge-<?php echo esc_attr( $log['level'] ); ?>"><?php echo esc_html( strtoupper( $log['level'] ) ); ?></span></td>
											<td style="color: #0f172a; font-weight: 500;"><?php echo esc_html( $log['message'] ); ?></td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
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
				<div class="bwo-pstat-badge" id="bwo-pv-savings-badge">-75%</div>
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

<!-- Modal 3: Help / Info Modal -->
<div class="bwo-modal-backdrop" id="bwo-help-modal" style="display: none;">
	<div class="bwo-modal-dialog">
		<div class="bwo-modal-header">
			<h3 class="bwo-modal-title"><?php esc_html_e( 'BUDI WebP Optimizer Documentation', 'budi-webp-optimizer' ); ?></h3>
			<button type="button" class="bwo-modal-close" data-close-modal>&times;</button>
		</div>
		<div class="bwo-modal-body bwo-help-content">
			<h4><?php esc_html_e( 'Why WebP?', 'budi-webp-optimizer' ); ?></h4>
			<p><?php esc_html_e( 'WebP is an ultra-modern image format that provides superior lossless and lossy compression for images on the web. Using WebP, webmasters and web developers can create smaller, richer images that make the web faster.', 'budi-webp-optimizer' ); ?></p>

			<h4><?php esc_html_e( 'Conversion Modes Explained', 'budi-webp-optimizer' ); ?></h4>
			<p><strong><?php esc_html_e( 'Keep Original:', 'budi-webp-optimizer' ); ?></strong> <?php esc_html_e( 'Creates WebP images side-by-side with your original JPG/PNG files without modifying any existing references.', 'budi-webp-optimizer' ); ?></p>
			<p><strong><?php esc_html_e( 'Replace Original:', 'budi-webp-optimizer' ); ?></strong> <?php esc_html_e( 'Replaces the attachment file directly with WebP, saving maximum disk space. An untouched original backup is preserved for effortless 1-click restore.', 'budi-webp-optimizer' ); ?></p>

			<h4><?php esc_html_e( 'Recoverable Backups', 'budi-webp-optimizer' ); ?></h4>
			<p><?php esc_html_e( 'When "Keep a recoverable backup of original files" is checked, original files are stored in a protected folder before conversion. If unchecked, no original backup is saved, freeing up server disk storage immediately.', 'budi-webp-optimizer' ); ?></p>
		</div>
		<div class="bwo-modal-footer">
			<button type="button" class="bwo-btn bwo-btn-white" data-close-modal><?php esc_html_e( 'Got it', 'budi-webp-optimizer' ); ?></button>
		</div>
	</div>
</div>
