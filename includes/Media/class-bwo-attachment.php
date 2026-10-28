<?php
/**
 * Attachment Model / Wrapper.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Attachment {

	public $id;
	public $post;
	public $file_path;
	public $relative_path;
	public $filename;
	public $mime_type;
	public $width = 0;
	public $height = 0;
	public $original_size = 0;
	public $webp_size = 0;
	public $status = 'pending'; // 'converted', 'pending', 'failed'
	public $savings_percent = 0;
	public $has_backup = false;

	public function __construct( $attachment_id ) {
		$this->id   = absint( $attachment_id );
		$this->post = get_post( $this->id );

		if ( ! $this->post || 'attachment' !== $this->post->post_type ) {
			return;
		}

		$this->file_path     = get_attached_file( $this->id );
		$this->relative_path = get_post_meta( $this->id, '_wp_attached_file', true );
		$this->filename      = basename( $this->file_path );
		$this->mime_type     = $this->post->post_mime_type;

		$meta = wp_get_attachment_metadata( $this->id );
		if ( ! empty( $meta['width'] ) ) {
			$this->width = $meta['width'];
		}
		if ( ! empty( $meta['height'] ) ) {
			$this->height = $meta['height'];
		}

		$this->has_backup = BWO_Backup_Manager::has_backup( $this->id );
		$this->load_status();
	}

	protected function load_status() {
		$is_failed    = get_post_meta( $this->id, '_bwo_failed', true );
		$webp_data    = get_post_meta( $this->id, '_bwo_webp_data', true );
		$is_converted = get_post_meta( $this->id, '_bwo_converted', true );

		if ( $this->file_path && file_exists( $this->file_path ) ) {
			$this->original_size = filesize( $this->file_path );
		}

		if ( $is_failed ) {
			$this->status = 'failed';
		} elseif ( $is_converted || ! empty( $webp_data['webp_file'] ) ) {
			$this->status = 'converted';
			if ( ! empty( $webp_data['original_size'] ) ) {
				$this->original_size = $webp_data['original_size'];
			}
			if ( ! empty( $webp_data['webp_size'] ) ) {
				$this->webp_size = $webp_data['webp_size'];
			} elseif ( ! empty( $webp_data['webp_file'] ) && file_exists( $webp_data['webp_file'] ) ) {
				$this->webp_size = filesize( $webp_data['webp_file'] );
			}
			$this->savings_percent = BWO_Formatting::calculate_savings_percent( $this->original_size, $this->webp_size );
		} else {
			$this->status = 'pending';
		}
	}

	public function get_thumbnail_url( $size = 'thumbnail' ) {
		$src = wp_get_attachment_image_src( $this->id, $size );
		return $src ? $src[0] : wp_mime_type_icon( $this->mime_type );
	}

	public function to_table_array() {
		return array(
			'id'              => $this->id,
			'filename'        => $this->filename,
			'width'           => $this->width,
			'height'          => $this->height,
			'dimensions'      => ( $this->width && $this->height ) ? "{$this->width} × {$this->height}" : '—',
			'thumbnail_url'   => $this->get_thumbnail_url(),
			'original_size'   => $this->original_size,
			'original_size_f' => BWO_Formatting::format_bytes( $this->original_size ),
			'webp_size'       => $this->webp_size,
			'webp_size_f'     => $this->webp_size > 0 ? BWO_Formatting::format_bytes( $this->webp_size ) : '—',
			'savings_percent' => $this->savings_percent > 0 ? round( $this->savings_percent ) . '%' : '—',
			'status'          => $this->status,
			'has_backup'      => $this->has_backup,
			'date'            => get_the_date( 'Y-m-d', $this->id ),
		);
	}
}
