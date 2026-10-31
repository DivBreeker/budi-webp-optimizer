<?php
/**
 * Reports subpage template.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$summary = BWO_Media_Scanner::get_summary_stats();
$server  = BWO_Server_Check::get_report();
$logs    = BWO_Logger::get_logs();
?>

<div class="bwo-wrap">
	<header class="bwo-top-header">
		<div class="bwo-brand-col">
			<div class="bwo-brand-logo">
				<span class="bwo-brand-text">BUDI<span class="bwo-brand-dot">.</span>Studio</span>
			</div>
		</div>
		<div class="bwo-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=budi-webp-optimizer' ) ); ?>" class="bwo-header-link">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
				<span><?php esc_html_e( 'Back to Dashboard', 'budi-webp-optimizer' ); ?></span>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=bwo-media-library' ) ); ?>" class="bwo-header-link">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
				<span><?php esc_html_e( 'Media Library', 'budi-webp-optimizer' ); ?></span>
			</a>
		</div>
	</header>

	<div class="bwo-title-bar">
		<div class="bwo-title-meta">
			<div class="bwo-title-heading">
				<h1><?php esc_html_e( 'Optimization & Performance Reports', 'budi-webp-optimizer' ); ?></h1>
				<span class="bwo-version-badge">v<?php echo esc_html( BWO_VERSION ); ?></span>
			</div>
			<p class="bwo-subtitle"><?php esc_html_e( 'Comprehensive breakdown of file compression, server diagnostics, and activity logs.', 'budi-webp-optimizer' ); ?></p>
		</div>
	</div>

	<div class="bwo-report-grid" style="padding: 0; margin-bottom: 24px;">
		<div class="bwo-card">
			<h3 class="bwo-card-title"><?php esc_html_e( 'Storage Performance Summary', 'budi-webp-optimizer' ); ?></h3>
			<ul class="bwo-report-list">
				<li><span><?php esc_html_e( 'Total Media Images:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( number_format_i18n( $summary['total_images'] ) ); ?></strong></li>
				<li><span><?php esc_html_e( 'Images Converted to WebP:', 'budi-webp-optimizer' ); ?></span> <strong class="bwo-text-success"><?php echo esc_html( number_format_i18n( $summary['converted_images'] ) ); ?></strong></li>
				<li><span><?php esc_html_e( 'Original Storage Occupied:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $summary['original_size_f'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Current Storage Occupied:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $summary['current_size_f'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Total Server Space Saved:', 'budi-webp-optimizer' ); ?></span> <strong class="bwo-text-success bwo-font-lg"><?php echo esc_html( $summary['storage_saved_f'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Average Size Reduction:', 'budi-webp-optimizer' ); ?></span> <strong class="bwo-text-success"><?php echo esc_html( $summary['reduction_label'] ); ?></strong></li>
			</ul>
		</div>

		<div class="bwo-card">
			<h3 class="bwo-card-title"><?php esc_html_e( 'Server Environment Diagnostics', 'budi-webp-optimizer' ); ?></h3>
			<ul class="bwo-report-list">
				<li><span><?php esc_html_e( 'WebP Engine:', 'budi-webp-optimizer' ); ?></span> <strong class="bwo-text-success"><?php echo esc_html( $server['active_engine'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'PHP Version:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['php_version'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'GD Extension:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['gd_webp'] ? __( 'Supported', 'budi-webp-optimizer' ) : __( 'Unavailable', 'budi-webp-optimizer' ) ); ?></strong></li>
				<li><span><?php esc_html_e( 'PHP Imagick WebP:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['imagick_webp'] ? __( 'Supported', 'budi-webp-optimizer' ) : __( 'Unavailable', 'budi-webp-optimizer' ) ); ?></strong></li>
				<li><span><?php esc_html_e( 'Memory Limit:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['memory_limit'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Max Execution Time:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['max_execution_time'] ); ?>s</strong></li>
			</ul>
		</div>
	</div>

	<!-- Activity Log Table Card -->
	<div class="bwo-card bwo-table-card">
		<div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px 12px 24px;">
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
			<table class="bwo-table" id="bwo-reports-logs-table">
				<thead>
					<tr>
						<th style="width: 170px;"><?php esc_html_e( 'Timestamp', 'budi-webp-optimizer' ); ?></th>
						<th style="width: 90px;"><?php esc_html_e( 'Level', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Activity Event', 'budi-webp-optimizer' ); ?></th>
					</tr>
				</thead>
				<tbody id="bwo-reports-logs-tbody">
					<?php if ( empty( $logs ) ) : ?>
						<tr>
							<td colspan="3" class="bwo-td-empty"><?php esc_html_e( 'No recent activity logs found. Optimization actions will appear here in real-time.', 'budi-webp-optimizer' ); ?></td>
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
