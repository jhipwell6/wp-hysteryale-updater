<?php

namespace HysterYale\Updater\Core;

class Autoload
{
	/**
	 * Path to the includes directory
	 * @var string
	 */
	private $include_path = '';

	public function __construct()
	{
		// Autoload classes
		if ( function_exists( "__autoload" ) )
			spl_autoload_register( "__autoload" );

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = untrailingslashit( plugin_dir_path( HYSTERYALE_UPDATER_PLUGIN_FILE ) ) . '/includes/';
	}

	/**
	 * Take a class name and turn it into a file name
	 * @param  string $class
	 * @return string
	 */
	private function get_file_name_from_class( $class )
	{
		return array_pop( explode( '\\', str_replace( '_', '-', $class ) ) ) . '.php';
	}

	/**
	 * Take a class name and turn it into a file name
	 * @param  string $class
	 * @return string
	 */
	private function get_file_path_from_class( $class )
	{
		$peices = explode( '\\', str_replace( 'hysteryale\\updater\\', '', $class ) );
		return $this->include_path . str_replace( '_', '-', implode( DS, $peices ) . '.php' );
	}

	/**
	 * Include a class file
	 * @param  string $path
	 * @return bool successful or not
	 */
	private function load_file( $path )
	{
		if ( is_readable( $path ) ) {
			include_once $path;
			return true;
		}

		return false;
	}

	/**
	 * Auto loads Required files for plugin
	 *
	 * @return void
	 */
	public function autoload( $class )
	{
		$class = strtolower( $class );
		$path = $this->get_file_path_from_class( $class );

		$this->load_file( $path );
	}

}

new Autoload();
