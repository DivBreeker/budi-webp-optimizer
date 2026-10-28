<?php
/**
 * Bulk Job Queue Processor.
 *
 * @package BUDI_WebP_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BWO_Job_Queue {

	const OPTION_KEY = 'bwo_queue_state';

	/**
	 * Initialize or reset bulk conversion queue.
	 *
	 * @param array $ids List of attachment IDs
	 * @return array Initial state
	 */
	public static function initialize_queue( $ids = array() ) {
		$ids = array_map( 'absint', (array) $ids );
		$ids = array_unique( array_filter( $ids ) );

		$state = array(
			'active'     => true,
			'total'      => count( $ids ),
			'queue'      => $ids,
			'processed'  => 0,
			'successful' => 0,
			'failed'     => 0,
			'errors'     => array(),
			'start_time' => time(),
			'updated_at' => time(),
		);

		update_option( self::OPTION_KEY, $state, false );
		return $state;
	}

	/**
	 * Get current queue status.
	 *
	 * @return array
	 */
	public static function get_state() {
		$state = get_option(
			self::OPTION_KEY,
			array(
				'active'     => false,
				'total'      => 0,
				'queue'      => array(),
				'processed'  => 0,
				'successful' => 0,
				'failed'     => 0,
				'errors'     => array(),
			)
		);
		return $state;
	}

	/**
	 * Process next chunk of items from queue.
	 *
	 * @param int $batch_size
	 * @return array Progress state
	 */
	public static function process_batch( $batch_size = 5 ) {
		$state = self::get_state();

		if ( empty( $state['active'] ) || empty( $state['queue'] ) ) {
			$state['active'] = false;
			update_option( self::OPTION_KEY, $state, false );
			return array(
				'completed'  => true,
				'total'      => $state['total'],
				'processed'  => $state['processed'],
				'successful' => $state['successful'],
				'failed'     => $state['failed'],
				'percent'    => 100,
			);
		}

		$batch_size = max( 1, min( 20, absint( $batch_size ) ) );
		$chunk      = array_splice( $state['queue'], 0, $batch_size );

		$last_processed_name = '';

		foreach ( $chunk as $attachment_id ) {
			$state['processed']++;
			$attachment = new BWO_Attachment( $attachment_id );
			$last_processed_name = $attachment->filename;

			$res = BWO_Converter::convert( $attachment_id );

			if ( $res->success ) {
				$state['successful']++;
			} else {
				$state['failed']++;
				$state['errors'][] = array(
					'id'    => $attachment_id,
					'name'  => $attachment->filename,
					'error' => $res->error_message,
				);
			}
		}

		$is_done = empty( $state['queue'] );
		$state['active']     = ! $is_done;
		$state['updated_at'] = time();

		update_option( self::OPTION_KEY, $state, false );

		$percent = $state['total'] > 0 ? round( ( $state['processed'] / $state['total'] ) * 100 ) : 100;

		return array(
			'completed'     => $is_done,
			'total'         => $state['total'],
			'processed'     => $state['processed'],
			'successful'    => $state['successful'],
			'failed'        => $state['failed'],
			'percent'       => $percent,
			'current_item'  => $last_processed_name,
			'remaining'     => count( $state['queue'] ),
			'errors'        => array_slice( $state['errors'], -5 ),
		);
	}

	/**
	 * Cancel or clear the queue.
	 */
	public static function cancel_queue() {
		delete_option( self::OPTION_KEY );
	}
}
