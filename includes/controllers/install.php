<?php

namespace HysterYale\Updater\Controllers;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Install
{
	protected static $instance;

	/**
	 * Initializes plugin variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */
	protected function __construct()
	{
		register_activation_hook( HYSTERYALE_UPDATER_PLUGIN_FILE, array( $this, 'install' ) );
		register_deactivation_hook( HYSTERYALE_UPDATER_PLUGIN_FILE, array( $this, 'uninstall' ) );

		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
		add_action( 'admin_init', array( $this, 'maybe_install' ), 5 );
	}

	/**
	 * Static Singleton Factory Method
	 * @return [class] instance of the classe
	 */
	public static function instance()
	{
		if ( ! isset( self::$instance ) ) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	/**
	 * Installs the database tables on activation
	 * @return null
	 */
	public function install()
	{
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $wpdb;

		// Reschedule Cron
		$this->unschedule_cron();
		$this->schedule_cron();

		update_option( 'hysteryale_updater__db_version', HYSTERYALE_UPDATER_VERSION );
		flush_rewrite_rules();

		do_action( 'wp_hysteryale_updater__installed' );
	}

	/**
	 * Uninstall the database tables on activation
	 * @return null
	 */
	public function uninstall()
	{
		$this->unschedule_cron();
	}

	/**
	 * Maybe run installation ( for mu plugins integration)
	 *
	 * @return void
	 */
	public function maybe_install()
	{
		//$mu_dir = ABSPATH.MUPLUGINDIR.'/'.basename(__DIR__);
		$mu_installed = get_option( 'wp_hysteryale_updater__mu' );

		if ( ! $mu_installed ) {
			$this->install();
			update_option( 'wp_hysteryale_updater__mu', true );
		}
	}

	/**
	 * Runs any database updates that need run
	 *
	 * @return void
	 */
	public function check_version()
	{
		$version = get_option( 'hysteryale_updater__db_version' );

		if ( $version != HYSTERYALE_UPDATER_VERSION ) {
			$this->install();
			do_action( 'wp_hysteryale_updater__updated' );
		}
	}

	private function schedule_cron()
	{
		$midnight = mktime( 0, 0, 0, date( 'n' ), date( 'j' ) + 1 );
		wp_schedule_event( $midnight, 'daily', 'hysteryale_updater__cron_import' );
	}

	private function unschedule_cron()
	{
		$next_schedule = wp_next_scheduled( 'hysteryale_updater__cron_import' );
		wp_unschedule_event( $next_schedule, 'hysteryale_updater__cron_import' );
	}

}

Install::instance();
