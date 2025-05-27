<?php

/**
 * Plugin Name: HysterYale Feed Integration
 * Plugin URI: http://www.hysteryale.com/
 * Description: Updates HysterYale product data from the API feed
 * Version: 3.2.0
 * Author: WebFX
 * Author URI: https://webfx.com/
 * GitHub Plugin URI: jhipwell6/wp-hysteryale-updater
 * Primary Branch: main
 * Text Domain: wp-hysteryale
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

final class HYSTERYALE_UPDATER
{
	/**
	 * @var string
	 */
	public $version = '3.2.0';

	/**
	 * @var string
	 */
	public $domain = 'wp-hysteryale';

	/**
	 * Plugin instance.
	 *
	 * @see instance()
	 * @type object
	 */
	protected static $instance = NULL;

	/**
	 * Weather or not to output scripts
	 *
	 * @type bool
	 */
	public $enqueue_scripts = false;

	/**
	 * Available Product Types
	 *
	 * @type array
	 */
	public $available_product_types = array(
		'Hyster' => 'Hyster',
		'Yale' => 'Yale'
	);

	/**
	 * Factory for returning equipment
	 * @var null
	 */
	private $equipment_factory = null;

	/**
	 * URL to this plugin's directory.
	 *
	 * @type string
	 */
	public $plugin_url = '';

	/**
	 * Path to this plugin's directory.
	 *
	 * @type string
	 */
	public $plugin_path = '';

	/**
	 * URL to ajax directory.
	 *
	 * @type string
	 */
	public $ajax_url = '';

	/**
	 * Static Singleton Factory Method
	 * @return self returns a single instance of our class
	 */
	public static function instance()
	{
		if ( ! isset( self::$instance ) ) {
			$class_name = __CLASS__;
			self::$instance = new $class_name;
		}
		return self::$instance;
	}

	/**
	 * Initiate the plugin
	 *
	 * @return void
	 */
	protected function __construct()
	{
		$this->define_constants();

		// Hooks
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
	}

	/**
	 * Setup needed includes and actions for plugin
	 * @hooked plugins_loaded -20
	 */
	public function init()
	{
		do_action( 'before_hysteryale_updater_init' );

		$this->includes();
		$this->init_hooks();
		$this->init_factories();

		do_action( 'after_hysteryale_updater_init' );
	}

	/**
	 * Define constant if not already set
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value )
	{
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 *
	 * @var string $type ajax, frontend or admin
	 * @return bool
	 */
	private function is_request( $type )
	{
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Define Constants
	 */
	private function define_constants()
	{
		$this->define( 'DS', DIRECTORY_SEPARATOR );
		$this->define( 'HYSTERYALE_UPDATER_PLUGIN_FILE', __FILE__ );
		$this->define( 'HYSTERYALE_UPDATER_VERSION', $this->version );
		$this->define( 'HYSTERYALE_UPDATER_DOMAIN', $this->domain );
		$this->define( 'HYSTERYALE_UPDATER_DEBUG', FALSE );
	}

	/**
	 * Include required files
	 * @return void
	 */
	private function includes()
	{
		// Core
		include_once $this->plugin_path() . '/includes/core/autoload.php';
		include_once $this->plugin_path() . '/includes/helpers/general-functions.php';
		include_once $this->plugin_path() . '/includes/core/custom-fields.php';
		include_once $this->plugin_path() . '/includes/core/equipment-factory.php';

		if ( $this->is_request( 'admin' ) ) {
			$this->admin_includes();
		}
		if ( $this->is_request( 'ajax' ) ) {
			$this->ajax_includes();
		}
		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}
		if ( $this->is_request( 'cron' ) ) {
			$this->cron_includes();
		}
	}

	private function frontend_includes()
	{
		include_once $this->plugin_path() . '/includes/controllers/equipment.php';
	}

	private function cron_includes()
	{
		include_once $this->plugin_path() . '/includes/controllers/importers/hyg-api.php';
	}

	private function admin_includes()
	{
		include_once $this->plugin_path() . '/includes/controllers/install.php';
		include_once $this->plugin_path() . '/includes/controllers/admin/settings.php';
		include_once $this->plugin_path() . '/includes/controllers/importers/hyg-api.php';
		include_once $this->plugin_path() . '/includes/controllers/equipment.php';
	}

	private function ajax_includes()
	{
		include_once $this->plugin_path() . '/includes/controllers/importers/hyg-api.php';
		include_once $this->plugin_path() . '/includes/controllers/progress.php';
	}

	public function admin_assets( $hook )
	{
		$screen = get_current_screen();
		$is_hysteryale_updater = ($hook == 'settings_page_hysteryale-updater-settings');
		$is_hysteryale = (strpos( $screen->post_type, 'hysteryale_' ) === 0);

		if ( ! $is_hysteryale_updater and ! $is_hysteryale )
			return;

		wp_enqueue_media();

		wp_enqueue_style(
			'hysteryale-updater-admin'
			, $this->plugin_url() . '/assets/css/admin.css'
		);

		wp_enqueue_script(
			'hysteryale-updater-admin'
			, $this->plugin_url() . '/assets/js/admin.js'
			, array( 'jquery' )
			, false
			, true
		);
	}
	
	public function init_hooks()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
	}

	/**
	 * Create factories to create new class instances
	 */
	public function init_factories()
	{
		$this->equipment_factory = new \HysterYale\Updater\Core\Equipment_Factory;
	}

	/**
	 * Return the Model of a equipment
	 * @param  mixed $equipment item
	 * @return [type]          [description]
	 */
	public function equipment( $equipment = false )
	{
		return $this->equipment_factory->get( $equipment );
	}

	/**
	 * Load the view
	 */
	public function view( $template, $data = array() )
	{
		if ( ! empty( $data ) ) {
			extract( $data );
		} else {
			$Equipment = $this->equipment();
		}

		ob_start();
		include $this->get_template_path( $template );
		return ob_get_clean();
	}

	private function get_template_path( $template )
	{
		$file = get_stylesheet_directory() . '/hygapi/' . $template . '.php';
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			return $this->plugin_path() . '/templates/' . $template . '.php';
		}
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url()
	{
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path()
	{
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get Ajax URL.
	 * @return string
	 */
	public function ajax_url()
	{
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * log information to the debug log
	 * @param  string|array $log [description]
	 * @return void
	 */
	public function log( $log )
	{
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( 'HYSTERYALE UPDATER: ' );
				error_log( print_r( $log, true ) );
			} else {
				error_log( 'HYSTERYALE UPDATER: ' . $log );
			}
		}
	}

	public function debug_log()
	{
		$log_location = $this->plugin_path() . '/logs/hyg-debug.log';
		$datetime = new DateTime( 'NOW' );
		$timestamp = $datetime->format( 'Y-m-d H:i:s' );
		$args = func_get_args();
		$formatted = array_map( function ( $item ) {
			return print_r( $item, true );
		}, $args );
		array_unshift( $formatted, $timestamp );
		$joined = implode( ' ', $formatted ) . "\n";
		error_log( $joined, 3, $log_location );
	}

}

/**
 * Returns the main instance of HYSTERYALE_UPDATER to prevent the need to use globals.
 *
 * @since  0.1
 * @return HYSTERYALE_UPDATER
 */
function HYSTERYALE_UPDATER()
{
	return HYSTERYALE_UPDATER::instance();
}

HYSTERYALE_UPDATER();
