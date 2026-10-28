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
	"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_bwo_backup_data' ORDER BY post_id DESC LIMIT 50",
	ARRAY_A
);
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
				<h1><?php esc_html_e( 'Image Backups & Restore Points', 'budi-webp-optimizer' ); ?></h1>
			</div>
			<p class="bwo-subtitle"><?php esc_html_e( 'Original unmodified image copies saved before conversion. Restore anytime with 1-click.', 'budi-webp-optimizer' ); ?></p>
		</div>
	</div>

	<div class="bwo-card bwo-table-card">
		<div class="bwo-table-responsive">
			<table class="bwo-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Image ID', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Original File', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Backup Date', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Original Size', 'budi-webp-optimizer' ); ?></th>
						<th><?php esc_html_e( 'Action', 'budi-webp-optimizer' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $backup_rows ) ) : ?>
						<tr>
							<td colspan="5" class="bwo-td-empty">
								<?php esc_html_e( 'No backups found. When images are converted in "Replace Original" mode, backup copies will appear here.', 'budi-webp-optimizer' ); ?>
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
							<tr>
								<td>#<?php echo esc_html( $post_id ); ?></td>
								<td><strong><?php echo esc_html( $file_name ); ?></strong></td>
								<td><?php echo esc_html( $date ); ?></td>
								<td><?php echo esc_html( $size ); ?></td>
								<td>
									<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white bwo-btn-restore" data-id="<?php echo esc_attr( $post_id ); ?>">
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
										<span><?php esc_html_e( 'Restore Original', 'budi-webp-optimizer' ); ?></span>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
