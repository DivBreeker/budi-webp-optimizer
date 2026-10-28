<?php
/**
 * Conversion Result Value Object.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Conversion_Result {

	public $success       = false;
	public $attachment_id = 0;
	public $original_path = '';
	public $webp_path     = '';
	public $original_size = 0;
	public $webp_size     = 0;
	public $saved_bytes   = 0;
	public $saved_percent = 0.0;
	public $thumbnails    = array();
	public $error_message = '';
	public $backup_path   = '';

	public function to_array() {
		return array(
			'success'       => $this->success,
			'attachment_id' => $this->attachment_id,
			'original_size' => $this->original_size,
			'webp_size'     => $this->webp_size,
			'saved_bytes'   => $this->saved_bytes,
			'saved_percent' => $this->saved_percent,
			'formatted'     => array(
				'original_size' => BWO_Formatting::format_bytes( $this->original_size ),
				'webp_size'     => BWO_Formatting::format_bytes( $this->webp_size ),
				'saved_bytes'   => BWO_Formatting::format_bytes( $this->saved_bytes ),
				'saved_percent' => $this->saved_percent . '%',
			),
			'thumbnails'    => $this->thumbnails,
			'error'         => $this->error_message,
		);
	}
}
