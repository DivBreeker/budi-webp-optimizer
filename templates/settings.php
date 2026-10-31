<?php
/**
 * Settings subpage template.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = BWO_Settings::get_settings();
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
				<h1><?php esc_html_e( 'Plugin Settings', 'budi-webp-optimizer' ); ?></h1>
				<span class="bwo-version-badge">v<?php echo esc_html( BWO_VERSION ); ?></span>
			</div>
			<p class="bwo-subtitle"><?php esc_html_e( 'Configure WebP compression levels, conversion modes, and backup behavior.', 'budi-webp-optimizer' ); ?></p>
		</div>
	</div>

	<div class="bwo-card bwo-settings-standalone-card">
		<form id="bwo-standalone-settings-form">
			<div class="bwo-settings-section">
				<h3><?php esc_html_e( 'Conversion Strategy', 'budi-webp-optimizer' ); ?></h3>
				<p class="bwo-desc"><?php esc_html_e( 'Choose how original image files and WordPress attachment records should be modified.', 'budi-webp-optimizer' ); ?></p>

				<div class="bwo-radio-cards">
					<label class="bwo-radio-card <?php echo esc_attr( 'keep' === $settings['conversion_mode'] ? 'selected' : '' ); ?>">
						<input type="radio" name="conversion_mode" value="keep" <?php checked( $settings['conversion_mode'], 'keep' ); ?> />
						<div class="bwo-radio-content">
							<strong><?php esc_html_e( 'Keep original and create WebP', 'budi-webp-optimizer' ); ?></strong>
							<span><?php esc_html_e( 'Original files stay intact. WebP files are generated alongside them. Safest option with zero modifications to uploads.', 'budi-webp-optimizer' ); ?></span>
						</div>
					</label>

					<label class="bwo-radio-card <?php echo esc_attr( 'replace' === $settings['conversion_mode'] ? 'selected' : '' ); ?>">
						<input type="radio" name="conversion_mode" value="replace" <?php checked( $settings['conversion_mode'], 'replace' ); ?> />
						<div class="bwo-radio-content">
							<strong><?php esc_html_e( 'Replace original with WebP', 'budi-webp-optimizer' ); ?></strong>
							<span><?php esc_html_e( 'Replaces original file references with WebP across WordPress, saving maximum server disk storage.', 'budi-webp-optimizer' ); ?></span>
						</div>
					</label>
				</div>
			</div>

			<div class="bwo-settings-section">
				<div class="bwo-slider-header">
					<div>
						<h3><?php esc_html_e( 'WebP Quality', 'budi-webp-optimizer' ); ?></h3>
						<p class="bwo-desc"><?php esc_html_e( 'WebP compression quality (50% to 100%). Default: 85%.', 'budi-webp-optimizer' ); ?></p>
					</div>
					<span class="bwo-slider-val-badge" id="bwo-quality-display-2"><?php echo esc_html( $settings['quality'] ); ?>%</span>
				</div>

				<input type="range" name="quality" id="bwo-quality-slider-2" min="50" max="100" value="<?php echo esc_attr( $settings['quality'] ); ?>" class="bwo-range-slider" />
				
				<div class="bwo-slider-ticks">
					<span class="bwo-tick-btn <?php echo esc_attr( 75 === (int) $settings['quality'] ? 'active' : '' ); ?>" data-val="75"><?php esc_html_e( 'Smaller file (75%)', 'budi-webp-optimizer' ); ?></span>
					<span class="bwo-tick-btn <?php echo esc_attr( 85 === (int) $settings['quality'] ? 'active' : '' ); ?>" data-val="85"><?php esc_html_e( 'Balanced (85%)', 'budi-webp-optimizer' ); ?></span>
					<span class="bwo-tick-btn <?php echo esc_attr( 96 === (int) $settings['quality'] ? 'active' : '' ); ?>" data-val="96"><?php esc_html_e( 'Highest quality (96%)', 'budi-webp-optimizer' ); ?></span>
				</div>
			</div>

			<div class="bwo-settings-section">
				<h3><?php esc_html_e( 'Automation & Backups', 'budi-webp-optimizer' ); ?></h3>

				<label class="bwo-switch-row">
					<input type="checkbox" name="keep_backup" value="1" <?php checked( $settings['keep_backup'], 1 ); ?> />
					<div>
						<strong><?php esc_html_e( 'Keep recoverable backup of original files', 'budi-webp-optimizer' ); ?></strong>
						<span><?php esc_html_e( 'Safely stores untouched JPG/PNG copies in a protected folder for instant 1-click restore. If unchecked, original copies will NOT be backed up to server storage.', 'budi-webp-optimizer' ); ?></span>
					</div>
				</label>

				<label class="bwo-switch-row">
					<input type="checkbox" name="auto_convert" value="1" <?php checked( $settings['auto_convert'], 1 ); ?> />
					<div>
						<strong><?php esc_html_e( 'Auto-convert new uploads', 'budi-webp-optimizer' ); ?></strong>
						<span><?php esc_html_e( 'Instantly generate WebP files whenever new images are uploaded to the WordPress Media Library.', 'budi-webp-optimizer' ); ?></span>
					</div>
				</label>

				<label class="bwo-switch-row">
					<input type="checkbox" name="convert_thumbnails" value="1" <?php checked( $settings['convert_thumbnails'], 1 ); ?> />
					<div>
						<strong><?php esc_html_e( 'Optimize intermediate thumbnail sizes', 'budi-webp-optimizer' ); ?></strong>
						<span><?php esc_html_e( 'Also compress and optimize all thumbnail sizes generated by WordPress and WooCommerce.', 'budi-webp-optimizer' ); ?></span>
					</div>
				</label>
			</div>

			<div class="bwo-settings-section">
				<h3><?php esc_html_e( 'Batch Processing & Diagnostics', 'budi-webp-optimizer' ); ?></h3>

				<div style="margin-bottom: 16px;">
					<label style="display: block; font-size: 14px; font-weight: 600; color: #0f172a; margin-bottom: 4px;">
						<?php esc_html_e( 'Bulk Batch Size', 'budi-webp-optimizer' ); ?>
					</label>
					<select name="batch_size" class="bwo-select" style="padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px;">
						<option value="3" <?php selected( $settings['batch_size'], 3 ); ?>>3 <?php esc_html_e( 'images per batch (Low-resource shared hosts)', 'budi-webp-optimizer' ); ?></option>
						<option value="5" <?php selected( $settings['batch_size'], 5 ); ?>>5 <?php esc_html_e( 'images per batch (Recommended)', 'budi-webp-optimizer' ); ?></option>
						<option value="10" <?php selected( $settings['batch_size'], 10 ); ?>>10 <?php esc_html_e( 'images per batch (Fast VPS / Dedicated)', 'budi-webp-optimizer' ); ?></option>
					</select>
				</div>

				<label class="bwo-switch-row">
					<input type="checkbox" name="debug_log" value="1" <?php checked( $settings['debug_log'], 1 ); ?> />
					<div>
						<strong><?php esc_html_e( 'Enable Detailed Diagnostic Logging', 'budi-webp-optimizer' ); ?></strong>
						<span><?php esc_html_e( 'Logs internal technical details to the PHP error log for troubleshooting.', 'budi-webp-optimizer' ); ?></span>
					</div>
				</label>
			</div>

			<div class="bwo-settings-footer">
				<button type="submit" class="bwo-btn bwo-btn-green" id="bwo-btn-save-settings-standalone">
					<span><?php esc_html_e( 'Save Settings', 'budi-webp-optimizer' ); ?></span>
				</button>
				<span class="bwo-save-status" id="bwo-standalone-save-status"></span>
			</div>
		</form>
	</div>
</div>
