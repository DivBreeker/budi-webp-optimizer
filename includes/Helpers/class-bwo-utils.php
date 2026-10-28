<?php
/**
 * Utility functions for paths, extensions, and environment.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Utils {

	/**
	 * Get WebP filepath for an image path.
	 *
	 * @param string $path
	 * @return string
	 */
	public static function get_webp_path( $path ) {
		$info = pathinfo( $path );
		$dir  = isset( $info['dirname'] ) ? $info['dirname'] : '';
		$name = isset( $info['filename'] ) ? $info['filename'] : '';

		return ( $dir ? $dir . DIRECTORY_SEPARATOR : '' ) . $name . '.webp';
	}

	/**
	 * Get WebP URL for an image URL.
	 *
	 * @param string $url
	 * @return string
	 */
	public static function get_webp_url( $url ) {
		$path_parts = pathinfo( $url );
		$dir  = isset( $path_parts['dirname'] ) ? $path_parts['dirname'] : '';
		$name = isset( $path_parts['filename'] ) ? $path_parts['filename'] : '';

		return ( $dir ? $dir . '/' : '' ) . $name . '.webp';
	}

	/**
	 * Check if file path is an eligible image (jpg, jpeg, png).
	 *
	 * @param string $path
	 * @return bool
	 */
	public static function is_supported_image( $path ) {
		$ext = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
		return in_array( $ext, array( 'jpg', 'jpeg', 'png' ), true );
	}

	/**
	 * Ensure backup directory exists and has protection.
	 *
	 * @return string Directory path
	 */
	public static function get_backup_dir() {
		$upload_dir = wp_upload_dir();
		$backup_dir = trailingslashit( $upload_dir['basedir'] ) . 'budi-webp-optimizer-backups';

		if ( ! file_exists( $backup_dir ) ) {
			wp_mkdir_p( $backup_dir );
			// Write index.php for security
			@file_put_contents( trailingslashit( $backup_dir ) . 'index.php', '<?php // Silence is golden' );
			// Write .htaccess to prevent direct directory listing
			@file_put_contents( trailingslashit( $backup_dir ) . '.htaccess', "Options -Indexes\n<Files *.php>\ndeny from all\n</Files>" );
		}

		return $backup_dir;
	}
}
