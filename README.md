# BUDI WebP Optimizer

<p align="center">
  <img src="assets/images/icon.svg" alt="BUDI WebP Optimizer" width="96" height="96" />
</p>

<p align="center">
  <strong>Convert smarter. Load faster.</strong><br>
  A lightweight, high-performance image optimization plugin by BUDI Studio designed to reduce image file sizes, preserve visual quality, and accelerate WordPress & WooCommerce websites.
</p>

<p align="center">
  <a href="#key-features">Features</a> •
  <a href="#conversion-modes">Modes</a> •
  <a href="#safety--backup">Safety & Rollback</a> •
  <a href="#installation">Installation</a> •
  <a href="#architecture">Architecture</a>
</p>

---

## ⚡ Key Highlights

- **Ultra-Lightweight (~52 KB)**: Zero external composer packages, heavy node modules, or bulky third-party dependencies.
- **Native WordPress Image Processing**: Uses `wp_get_image_editor()` with GD and Imagick server WebP encoders.
- **Zero Frontend Bloat**: Admin scripts and CSS are only enqueued on plugin admin screens. Zero frontend scripts injected.
- **Pixel-Perfect Modern Dashboard**: Real-time stats, animated SVG donut chart, storage savings calculator, and batch AJAX background processing.
- **Preserved Attachment IDs**: Maintains WordPress Attachment IDs across all conversions so WooCommerce product galleries, Elementor layouts, and theme links never break.
- **1-Click Rollback / Restore**: Original unmodified images are automatically backed up to a protected directory. Restore anytime with a single click.

---

## 🛡️ Safe Conversion Modes

### 1. Keep Original and Create WebP
* Original `.jpg` / `.png` files remain untouched in the WordPress uploads directory.
* WebP variants are generated adjacent to the original image and intermediate thumbnail sizes.
* Safest mode for legacy themes and third-party plugins.

### 2. Replace Original with WebP
* Safely changes the attachment format to WebP.
* Automatically creates an untouched original backup in `wp-content/uploads/budi-webp-optimizer-backups/` protected with `.htaccess` and `index.php`.
* Updates attachment metadata and registered thumbnail sizes to point to `.webp`.
* Preserves the exact WordPress attachment ID.
* Enables 1-click restore back to original JPG/PNG at any time.

---

## 🚀 Quick Actions & Bulk Processing

- **Convert All Images**: Queues eligible images in asynchronous AJAX chunks (5–10 images per batch) to prevent PHP timeouts and memory exhaustion.
- **Select & Convert**: Multi-select images in the dashboard table and run targeted batch optimizations.
- **Automatic Uploads**: Automatically converts incoming uploads into WebP when added to the Media Library.
- **Side-by-Side Preview**: Inspect original vs. WebP visual quality and compare file size reductions before saving.

---

## 📦 Installation

### From GitHub Release / ZIP:
1. Download `budi-webp-optimizer.zip`.
2. In your WordPress admin, go to **Plugins > Add New > Upload Plugin**.
3. Choose the ZIP file and click **Install Now**.
4. Click **Activate Plugin**.
5. Navigate to **WebP Optimizer > Dashboard** from the sidebar.

### Via Git Clone:
```bash
cd wp-content/plugins/
git clone https://github.com/DivBreeker/budi-webp-optimizer.git budi-webp-optimizer
```

---

## 📁 Repository Structure

```
budi-webp-optimizer/
├── budi-webp-optimizer.php      # Main plugin bootstrap & constants
├── uninstall.php                # Clean uninstallation policy
├── readme.txt                   # WordPress.org standard metadata
├── LICENSE                      # GPL-2.0+
├── includes/
│   ├── Admin/                   # Dashboard controller, menus, settings
│   ├── Conversion/              # WebP encoder, validator, quality manager
│   ├── Media/                   # Attachment model, scanner, thumbnail replacer
│   ├── Backup/                  # Backup manager & 1-click restore logic
│   ├── Queue/                   # Background AJAX batch processor
│   ├── Compatibility/           # Server GD & Imagick WebP diagnostics
│   ├── Security/                # Nonce verification, permission & path safety
│   └── Helpers/                 # Formatting, logger, path utilities
├── templates/                   # Dashboard, settings, backups, and reports views
├── assets/                      # Admin CSS, vanilla/jQuery JS, brand SVG
└── languages/                   # i18n translation template
```

---

## 📄 License
This project is licensed under the GNU General Public License v2.0 or later - see the [LICENSE](LICENSE) file for details.

Developed with ❤️ by **BUDI Studio**.
