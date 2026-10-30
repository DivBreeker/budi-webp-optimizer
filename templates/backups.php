<?php
/**
 * Backups subpage template.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
$backup_rows = $wpdb->get_results(
	"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_bwo_backup_data' ORDER BY post_id DESC LIMIT 100",
	ARRAY_A
);

$total_backup_size = 0;
if ( ! empty( $backup_rows ) ) {
	foreach ( $backup_rows as $b_row ) {
		$b_data = maybe_unserialize( $b_row['meta_value'] );
		if ( ! empty( $b_data['original_size'] ) ) {
			$total_backup_size += (int) $b_data['original_size'];
		}
	}
}
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
				<h1><?php esc_html_e( 'Image Backups & Restore Points', 'budi-webp-optimizer' ); ?></h1>
				<span class="bwo-version-badge" id="bwo-backup-count-badge"><?php echo esc_html( count( $backup_rows ) ); ?> <?php esc_html_e( 'Backups', 'budi-webp-optimizer' ); ?></span>
			</div>
			<p class="bwo-subtitle"><?php esc_html_e( 'Original unmodified image copies saved before conversion. Restore anytime with 1-click or permanently delete to free server disk space.', 'budi-webp-optimizer' ); ?></p>
		</div>
		<div class="bwo-title-actions">
			<?php if ( ! empty( $backup_rows ) ) : ?>
				<button type="button" class="bwo-btn bwo-btn-danger" id="bwo-btn-clear-all-backups">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
					<span><?php esc_html_e( 'Clear All Backups', 'budi-webp-optimizer' ); ?></span>
				</button>
			<?php endif; ?>
		</div>
	</div>

	<!-- Storage Occupied by Backups notice -->
	<div class="bwo-card" style="padding: 16px 24px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
		<div>
			<span style="font-size: 13px; color: #64748b;"><?php esc_html_e( 'Storage used by backups:', 'budi-webp-optimizer' ); ?></span>
			<strong style="font-size: 16px; color: #0f172a; margin-left: 8px;"><?php echo esc_html( BWO_Formatting::format_bytes( $total_backup_size ) ); ?></strong>
		</div>
		<span style="font-size: 12px; color: #94a3b8;"><?php esc_html_e( 'Location: wp-content/uploads/budi-webp-optimizer-backups/', 'budi-webp-optimizer' ); ?></span>
	</div>

	<div class="bwo-card bwo-table-card">
		<div class="bwo-table-responsive">
			<table class="bwo-table" id="bwo-backups-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Image ID', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Original File', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Backup Date', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Original Size', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'budi-webp-optimizer' ); ?></th>
					</tr>
				</thead>
				<tbody id="bwo-backups-tbody">
					<?php if ( empty( $backup_rows ) ) : ?>
						<tr id="bwo-backups-empty-row">
							<td colspan="5" class="bwo-td-empty">
								<?php esc_html_e( 'No backups found. When images are converted with the backup option enabled, recoverable original copies will appear here.', 'budi-webp-optimizer' ); ?>
							</td>
						</tr>
					<?php else : ?>
						<?php foreach ( $backup_rows as $row ) :
							$data = maybe_unserialize( $row['meta_value'] );
							if ( ! is_array( $data ) ) continue;
							$post_id   = (int) $row['post_id'];
							$file_name = ! empty( $data['original_filename'] ) ? $data['original_filename'] : basename( $data['original_file'] );
							$date      = ! empty( $data['timestamp'] ) ? BWO_Formatting::format_date( $data['timestamp'] ) : '—';
							$size      = ! empty( $data['original_size'] ) ? BWO_Formatting::format_bytes( $data['original_size'] ) : '—';
						?>
							<tr id="bwo-backup-row-<?php echo esc_attr( $post_id ); ?>">
								<td>#<?php echo esc_html( $post_id ); ?></td>
								<td><strong><?php echo esc_html( $file_name ); ?></strong></td>
								<td><?php echo esc_html( $date ); ?></td>
								<td><?php echo esc_html( $size ); ?></td>
								<td>
									<div style="display: flex; gap: 8px;">
										<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white bwo-btn-restore" data-id="<?php echo esc_attr( $post_id ); ?>" title="<?php esc_attr_e( 'Restore original JPG/PNG to Media Library', 'budi-webp-optimizer' ); ?>">
											<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
											<span><?php esc_html_e( 'Restore', 'budi-webp-optimizer' ); ?></span>
										</button>
										<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white bwo-btn-delete-backup" data-id="<?php echo esc_attr( $post_id ); ?>" style="color: #ef4444;" title="<?php esc_attr_e( 'Permanently delete this backup file to free disk space', 'budi-webp-optimizer' ); ?>">
											<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
											<span><?php esc_html_e( 'Delete Backup', 'budi-webp-optimizer' ); ?></span>
										</button>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
