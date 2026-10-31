=== BUDI WebP Optimizer ===
Contributors: budistudio, divbreeker
Tags: webp, image optimizer, performance, compression, media library
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convert smarter. Load faster. Ultra-lightweight WordPress WebP image optimizer with safe backup rollback and WooCommerce support.

== Description ==

**BUDI WebP Optimizer** is a lightweight, high-performance image optimization plugin developed by BUDI Studio for WordPress agencies, WooCommerce store owners, and web developers.

Convert your existing JPEG and PNG media library to modern, lightweight WebP formats with zero bloat and absolute safety for your existing layouts, attachment IDs, and WooCommerce galleries.

### Why BUDI WebP Optimizer?

* **100% Lightweight**: Built exclusively on native WordPress APIs (`wp_get_image_editor()`) and standard server WebP encoders (GD / Imagick). Zero heavy third-party vendor bloat or external API dependencies.
* **Modern Dashboard**: Complete control over your library with live conversion stats, animated donut gauge, storage savings calculator, and quick actions.
* **Two Safe Conversion Modes**:
  * **Keep Original & Create WebP**: Keeps your original file untouched and creates adjacent WebP files for seamless delivery.
  * **Replace Original with WebP**: Safely switches the attachment image format to WebP, preserving Attachment IDs, updating thumbnails, and maintaining full WooCommerce and Elementor compatibility.
* **Recoverable Backups & 1-Click Rollback**: Optional automatic backup of original image files before replacement into a protected directory with instant 1-click restore functionality.
* **Delete Backup to Reclaim Disk Space**: Users can permanently delete stored backup files per image or in bulk to free up server disk space.
* **Dedicated Management Hubs**: Dedicated views for Master Dashboard, Media Library Optimizer, Bulk Conversion Center, Backup Management, and Diagnostic Reports.
* **Bulk & Selective Conversion**: Convert your entire media library in controlled background AJAX batches or select specific images on demand.
* **Fine Quality Control**: Tune WebP compression quality from 50% to 100% (presets: 75% smaller file, 85% balanced, 96% highest quality).
* **Before / After Preview**: Compare original image and converted WebP side-by-side with file size reduction metrics.
* **Zero Frontend Bloat**: No frontend CSS or JavaScript is enqueued on visitor page loads. Pure website performance.

== Installation ==

1. Upload the `budi-webp-optimizer` folder to your `/wp-content/plugins/` directory, or upload the ZIP file directly via `Plugins > Add New > Upload Plugin`.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **WebP Optimizer > Dashboard** to review server compatibility and optimize your images.

== Frequently Asked Questions ==

= Does this plugin require an external API key or third-party cloud service? =
No. BUDI WebP Optimizer runs 100% locally on your WordPress web server using native PHP GD or Imagick extensions. Your images are never sent to external third-party servers.

= Will my WooCommerce product galleries and Elementor layouts break? =
No. In "Replace Original with WebP" mode, the plugin preserves the exact WordPress Attachment ID and regenerates all intermediate registered thumbnail sizes, ensuring WooCommerce products, Elementor widgets, and theme featured images continue working seamlessly.

= Can I restore my original JPG and PNG files? =
Yes. If you enable the "Keep a recoverable backup of original files" option, the plugin saves an untouched original copy in a protected folder (`wp-content/uploads/budi-webp-optimizer-backups/`). You can restore any image back to original with a single click from the Backups or Media Library page.

= What happens if I uncheck the backup option? =
If the backup option is unchecked, original copies will not be saved into server backup storage during replacement, maximizing available server disk space immediately.

= Can I delete backups after verifying image quality? =
Yes. You can delete individual image backups or click "Clear All Backups" in the Backups screen to permanently remove backup files and reclaim server disk storage.

= What image formats are supported? =
JPEG (`.jpg`, `.jpeg`) and PNG (`.png`) formats are supported for WebP conversion.

= Does this plugin inject CSS or JavaScript on my website frontend? =
No. Admin scripts and styles are strictly loaded on the plugin's WordPress admin pages. Zero scripts or stylesheets are loaded on your public website.

== Screenshots ==

1. Master Dashboard with real-time conversion statistics, animated SVG donut progress chart, and storage savings.
2. Dedicated Media Library Optimizer with live search, status filters (All, Converted, Pending, Failed), and bulk selection tools.
3. Bulk WebP Conversion Center with scope selection (All Images, Pending Only, Retry Failed) and real-time background batch progress tracker.
4. Recoverable Backups and 1-Click Rollback management screen with Delete Backup functionality.
5. Before & After side-by-side visual comparison preview modal with exact bytes saved.
6. Diagnostic Reports screen with server WebP environment status and real-time Activity Logs.

== Changelog ==

= 1.0.1 =
* Added Delete Backup feature to permanently remove backups and reclaim server disk space.
* Added Clear All Backups option for full backup cleanup.
* Dedicated Media Library Optimizer screen with search, status filters, and bulk actions.
* Dedicated Bulk Conversion screen with scope selection (All, Pending Only, Retry Failed).
* Fixed live Activity Logs display on Dashboard and Reports screens.
* Enhanced settings form persistence for WebP quality slider and backup toggle.
* Fixed backup creation checkmark behavior so unchecking strictly prevents backup storage.

= 1.0.0 =
* Initial official release by BUDI Studio.
* Real-time Media Library scanner with batch AJAX processing.
* Keep Original and Replace Original conversion modes.
* Safe original backups and instant rollback restore.
* High-fidelity modern dashboard matching BUDI Studio brand identity.

== Upgrade Notice ==

= 1.0.1 =
Recommended upgrade: Includes dedicated Media Library and Bulk Convert hubs, plus Delete Backup functionality to free server disk space.
