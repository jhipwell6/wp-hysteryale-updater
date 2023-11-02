<?php

/**
 * Plugin Name: HysterYale Feed Integration
 * Plugin URI: http://www.hysteryale.com/
 * Description: Updates HysterYale product data from the API feed
 * Version: 3.1.2
 * Author: WebFX
 * Author URI: https://webfx.com/
 * GitHub Plugin URI: jhipwell6/wp-hysteryale-updater
 * Primary Branch: main
 * Text Domain: wp-hysteryale
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Class WP_HYSTERYALE_UPDATER_Loader
 */
class WP_HYSTERYALE_UPDATER_Loader
{
	/**
	 * Holds plugin file.
	 *
	 * @var $plugin_file
	 */
	private static $plugin_file = 'wp-hysteryale-updater/wp-hysteryale-updater.php';

	/**
	 * Let's get going.
	 * Load the plugin and hooks.
	 *
	 * @return void
	 */
	public function run()
	{
		define( 'WP_HYSTERYALE_UPDATER_LOADER', true );
		require trailingslashit( WP_PLUGIN_DIR ) . self::$plugin_file;
		$this->load_hooks();
	}

	/**
	 * Load action and filter hooks.
	 *
	 * @return void
	 */
	public function load_hooks()
	{
		// Deactivate normal plugin as it's loaded as mu-plugin.
		add_action( 'activated_plugin', array( $this, 'deactivate' ), 10, 1 );

		/*
		 * Remove links and checkbox from Plugins page so user can't delete main plugin.
		 */
		add_filter( 'network_admin_plugin_action_links_' . static::$plugin_file, array( $this, 'mu_plugin_active' ) );
		add_filter( 'plugin_action_links_' . static::$plugin_file, array( $this, 'mu_plugin_active' ) );
		add_action(
			'after_plugin_row_' . static::$plugin_file,
			function () {
				print '<script>jQuery(".inactive[data-plugin=\'wp-hysteryale-updater/wp-hysteryale-updater.php\']").attr("class", "active");</script>';
				print '<script>jQuery(".active[data-plugin=\'wp-hysteryale-updater/wp-hysteryale-updater.php\'] .check-column input").remove();</script>';
			}
		);
	}

	/**
	 * Deactivate if plugin in loaded not as mu-plugin.
	 *
	 * @param string $plugin Plugin slug.
	 */
	public function deactivate( $plugin )
	{
		if ( static::$plugin_file === $plugin ) {
			deactivate_plugins( static::$plugin_file );
		}
	}

	/**
	 * Label as mu-plugin in plugin view.
	 *
	 * @param array $actions Link actions.
	 *
	 * @return array
	 */
	public function mu_plugin_active( $actions )
	{
		if ( isset( $actions['activate'] ) ) {
			unset( $actions['activate'] );
		}
		if ( isset( $actions['delete'] ) ) {
			unset( $actions['delete'] );
		}
		if ( isset( $actions['deactivate'] ) ) {
			unset( $actions['deactivate'] );
		}

		return array_merge( array( 'mu-plugin' => esc_html__( 'Activated as mu-plugin', 'wp-hysteryale-updater' ) ), $actions );
	}

}

( new WP_HYSTERYALE_UPDATER_Loader() )->run();
