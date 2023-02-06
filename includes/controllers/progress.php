<?php

namespace HysterYale\Updater\Controllers;

class Progress
{
	protected static $instance;

	/**
	 * Initializes plugin variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */
	protected function __construct()
	{
		add_action( 'wp_ajax_hysteryale_updater__progress_poll', array( $this, 'result' ) );
		add_action( 'wp_ajax_nopriv_hysteryale_updater__progress_poll', array( $this, 'result' ) );
	}

	/**
	 * Singleton factory Method
	 * Forces that only on instance of the class exists
	 *
	 * @return $instance Object, Returns the current instance or a new instance of the class
	 */
	public static function instance()
	{
		if ( ! isset( self::$instance ) ) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	public function result()
	{
		echo json_encode( self::get() );
		exit;
	}

	public static function get()
	{
		$indexed = get_transient( 'hysteryale_updater__progress_indexed' );
		$total = get_transient( 'hysteryale_updater__progress_total' );
		$text = get_transient( 'hysteryale_updater__progress_text' );

		return array(
			'indexed' => $indexed
			, 'total' => $total
			, 'text' => $text
		);
	}

	/**
	 * Setup temporary cache data
	 * used by heartbeat api to update progress.
	 */
	public static function set( $interval = 0, $total = null, $text = '' )
	{
		set_transient( 'hysteryale_updater__progress_indexed', $interval, 10 * MINUTE_IN_SECONDS );

		if ( $total !== null )
			set_transient( 'hysteryale_updater__progress_total', $total, 10 * MINUTE_IN_SECONDS );

		if ( ! empty( $text ) )
			set_transient( 'hysteryale_updater__progress_text', $text, 10 * MINUTE_IN_SECONDS );
	}

	/**
	 * set a specific field
	 * @param string $field transient short name
	 * @param sting|int $value the value for the transient
	 */
	public static function update( $field, $value )
	{
		switch ( $field ) {
			case 'index':
				set_transient( 'hysteryale_updater__progress_indexed', $value, 10 * MINUTE_IN_SECONDS );
				break;
			case 'total':
				set_transient( 'hysteryale_updater__progress_total', $value, 10 * MINUTE_IN_SECONDS );
				break;
			case 'text':
				set_transient( 'hysteryale_updater__progress_text', $value, 10 * MINUTE_IN_SECONDS );
				break;
			default:
				set_transient( 'hysteryale_updater__progress_' . $field, $value, 10 * MINUTE_IN_SECONDS );
				break;
		}
	}

}

Progress::instance();
