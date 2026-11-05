<?php
/**
 * Frontend Image Delivery & Content Rewriter.
 * Dynamically serves next-gen WebP images on the frontend.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUDI_WebP_Frontend {

	/**
	 * Memory cache for file_exists checks to maximize performance.
	 *
	 * @var array
	 */
	private static $file_cache = array();

	/**
	 * Initialize frontend hooks.
	 */
	public static function init() {
		add_filter( 'the_content', array( __CLASS__, 'filter_content' ), 99 );
		add_filter( 'the_excerpt', array( __CLASS__, 'filter_content' ), 99 );
		add_filter( 'widget_text', array( __CLASS__, 'filter_content' ), 99 );
		add_filter( 'post_thumbnail_html', array( __CLASS__, 'filter_content' ), 99 );
	}

	/**
	 * Filter HTML content and replace JPG/PNG image URLs with WebP if WebP file exists.
	 *
	 * @param string $content
	 * @return string
	 */
	public static function filter_content( $content ) {
		if ( empty( $content ) || ! is_string( $content ) || false === strpos( $content, 'wp-content/uploads' ) ) {
			return $content;
		}

		$upload_dir        = wp_upload_dir();
		$base_url          = $upload_dir['baseurl'];
		$base_dir          = $upload_dir['basedir'];
		$base_url_no_proto = preg_replace( '#^https?:#i', '', $base_url );

		// Fast Regex: Match URLs inside src, srcset, data-src, or href pointing to uploads
		$pattern = '#https?://[^\s"\'<>]+/wp-content/uploads/[^\s"\'<>]+\.(?:jpe?g|png)(?:\?[^\s"\'<>]*)?#i';

		return preg_replace_callback(
			$pattern,
			function ( $matches ) use ( $base_url, $base_dir, $base_url_no_proto ) {
				$url = $matches[0];

				// Separate query parameters if any (e.g. ?v=123)
				$url_parts = explode( '?', $url, 2 );
				$clean_url = $url_parts[0];
				$query     = isset( $url_parts[1] ) ? '?' . $url_parts[1] : '';

				if ( isset( self::$file_cache[ $clean_url ] ) ) {
					return self::$file_cache[ $clean_url ] ? self::$file_cache[ $clean_url ] . $query : $url;
				}

				$clean_url_no_proto = preg_replace( '#^https?:#i', '', $clean_url );

				// Check if URL is within uploads
				if ( false === strpos( $clean_url_no_proto, $base_url_no_proto ) ) {
					self::$file_cache[ $clean_url ] = false;
					return $url;
				}

				$rel_path        = str_replace( $base_url_no_proto, '', $clean_url_no_proto );
				$local_file      = $base_dir . $rel_path;
				$webp_local_file = BUDI_WebP_Utils::get_webp_path( $local_file );

				if ( file_exists( $webp_local_file ) ) {
					$webp_url                        = BUDI_WebP_Utils::get_webp_url( $clean_url );
					self::$file_cache[ $clean_url ] = $webp_url;
					return $webp_url . $query;
				}

				self::$file_cache[ $clean_url ] = false;
				return $url;
			},
			$content
		);
	}
}
