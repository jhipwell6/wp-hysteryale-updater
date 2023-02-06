<?php

namespace HysterYale\Updater\Controllers\Admin;

use HysterYale\Updater\Controllers\Importers\HYG_API;
use \WP_Query;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Settings extends \HysterYale\Updater\Core\Abstracts\Settings
{
	protected static $instance;
	private $options_key = 'hysteryale-updater-settings';
	private $setting_tabs = array(
		'general' => 'General'
		, 'importer' => 'Importer'
	);

	/**
	 * Initializes variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */
	protected function __construct()
	{
		parent::__construct();
	}

	/* Static Singleton Factory Method */

	public static function instance()
	{
		if ( ! isset( self::$instance ) ) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	/**
	 * Adds the menu item to
	 *
	 * @return void
	 */
	public function add_menu()
	{
		$page = add_submenu_page(
			'options-general.php'
			, 'Hyster-Yale'
			, 'Hyster-Yale'
			, 'manage_options'
			, $this->options_key
			, array( $this, 'load_page_template' )
		);
	}

	public function load_page_template()
	{
		$types = get_option( 'hysteryale_updater__type_limitation' );
		$types = is_array( $types ) ? $types : array();

		// Default to general only if no types are selected
		// otherwise, default to import
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] :
			( empty( $types ) ? 'general' : 'importer' );

		$available_types = HYSTERYALE_UPDATER()->available_product_types;

		if ( $tab == "importer" ) {
//            $importer = HysterYale_API::instance();
			$importer = HYG_API::instance();
			$force = ! empty( $_GET['force'] );
			$cache = ! $force;
			$tree = $importer->get_product_tree( $force, $cache );

			$equipments = array();
			$wp_query = new WP_Query( array(
				'post_type' => 'equipment'
				, 'orderby' => 'title'
				, 'order' => 'asc'
				, 'posts_per_page' => '-1'
				, 'post_status' => array( 'publish', 'draft', 'pending' )
				) );
			$equipments = $wp_query->posts;
		}
		?>

		<div class="wrap">
			<h2>Hyster-Yale Settings</h2>
			<?php $this->tabs( $tab ); ?>

			<?php if ( $tab !== 'importer' ) : ?>
				<form method="post" action="">
					<?php settings_fields( 'hysteryale_updater__settings' ); ?>
				<?php endif; ?>

				<?php include_once HYSTERYALE_UPDATER()->plugin_path() . '/templates/admin/settings/' . $tab . '.php'; ?>

				<?php if ( $tab !== 'importer' ): ?>
					<?php submit_button(); ?>
				</form>
			<?php endif; ?>

		</div>

		<?php
	}

	/**
	 * Renders our settings tabs
	 * @return [type] [description]
	 */
	private function tabs( $current_tab )
	{
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->setting_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
		}
		echo '</h2>';
	}

	public function add_settings_page()
	{
		if ( ! empty( $_POST ) ) {
			if ( isset( $_POST['hysteryale_updater__type_limitation'] ) ) {
				update_option( 'hysteryale_updater__type_limitation', $_POST['hysteryale_updater__type_limitation'] );
			}
		}
	}

	/*
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 */

	function section_general_desc()
	{
		echo '';
	}

	function section_new_desc()
	{
		echo 'Manage Hyster-Yale Options';
	}

}

Settings::instance();
