=== BUDI WebP Optimizer ===
Contributors: budistudio
Tags: webp, image optimizer, speed, performance, compression, media library
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convert smarter. Load faster. A lightweight, high-performance image optimization plugin by BUDI Studio designed to reduce image file sizes and preserve visual quality.

== Description ==

**BUDI WebP Optimizer** is a lightweight, high-performance image optimization and conversion plugin built specifically for WordPress developers, WooCommerce store owners, and agencies by BUDI Studio.

Convert your existing JPEG and PNG media library to modern, lightweight WebP formats with zero bloat and absolute safety for your existing layouts, attachment IDs, and WooCommerce galleries.

### Key Highlights

* **100% Lightweight**: Built exclusively on native WordPress APIs (`wp_get_image_editor()`) and standard server WebP encoders (GD / Imagick). Zero heavy external PHP vendor bloat.
* **Pixel-Perfect Modern Dashboard**: Complete control over your library with live conversion stats, donut charts, storage savings, and quick action cards.
* **Two Conversion Modes**:
  * **Keep Original & Create WebP**: Keeps your original file untouched and creates adjacent WebP files for seamless delivery.
  * **Replace Original with WebP**: Safely replaces the attachment source with WebP, preserving attachment IDs, updating thumbnails, and maintaining full WooCommerce and Elementor compatibility.
* **Recoverable Backups & 1-Click Restore**: Automatic backup of original image files before replacement with instant 1-click restore functionality.
* **Bulk & Selective Conversion**: Convert your entire media library in controlled background AJAX batches or select specific images on demand.
* **Quality Control**: Finely tune WebP compression quality from 50% to 100% (recommended default: 85% to 96%).
* **Before / After Preview**: Compare original image and converted WebP side-by-side with file size reduction metrics.
* **Zero Frontend Bloat**: No frontend CSS or JavaScript is enqueued. Your visitors experience pure, blazing-fast speed.

== Installation ==

1. Upload the `budi-webp-optimizer` folder to your `/wp-content/plugins/` directory, or upload the ZIP file directly via `Plugins > Add New > Upload Plugin`.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **WebP Optimizer > Dashboard** to review server compatibility and optimize your images.

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
