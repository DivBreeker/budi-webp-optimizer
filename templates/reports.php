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
$logs    = get_option( 'bwo_recent_logs', array() );
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
		</div>
	</header>

	<div class="bwo-title-bar">
		<div class="bwo-title-meta">
			<div class="bwo-title-heading">
				<h1><?php esc_html_e( 'Optimization & Performance Reports', 'budi-webp-optimizer' ); ?></h1>
			</div>
			<p class="bwo-subtitle"><?php esc_html_e( 'Comprehensive breakdown of file compression, server diagnostics, and activity logs.', 'budi-webp-optimizer' ); ?></p>
		</div>
	</div>

	<div class="bwo-report-grid">
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
				<li><span><?php esc_html_e( 'PHP GD WebP:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo $server['gd_webp'] ? 'Supported' : 'Unavailable'; ?></strong></li>
				<li><span><?php esc_html_e( 'PHP Imagick WebP:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo $server['imagick_webp'] ? 'Supported' : 'Unavailable'; ?></strong></li>
				<li><span><?php esc_html_e( 'Memory Limit:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['memory_limit'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Max Execution Time:', 'budi-webp-optimizer' ); ?></span> <strong><?php echo esc_html( $server['max_execution_time'] ); ?>s</strong></li>
			</ul>
		</div>
	</div>

	<!-- Activity Log Table -->
	<div class="bwo-card bwo-table-card" style="margin-top: 24px;">
		<h3 class="bwo-card-title" style="padding: 20px 24px 0 24px;"><?php esc_html_e( 'Recent Activity Logs', 'budi-webp-optimizer' ); ?></h3>
		<div class="bwo-table-responsive">
			<table class="bwo-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Timestamp', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Level', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Message', 'budi-webp-optimizer' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $logs ) ) : ?>
						<tr>
							<td colspan="3" class="bwo-td-empty"><?php esc_html_e( 'No recent activity logs found.', 'budi-webp-optimizer' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( array_reverse( $logs ) as $log ) : ?>
							<tr>
								<td style="white-space:nowrap;"><?php echo esc_html( BWO_Formatting::format_date( $log['time'] ) ); ?></td>
								<td><span class="bwo-badge bwo-badge-<?php echo esc_attr( $log['level'] ); ?>"><?php echo esc_html( strtoupper( $log['level'] ) ); ?></span></td>
								<td><?php echo esc_html( $log['message'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
