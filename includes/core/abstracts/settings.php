<?php

namespace HysterYale\Updater\Core\Abstracts;

if ( ! defined( 'ABSPATH' ) )
	exit;

abstract class Settings
{

	protected function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_menu' ), 1 );
		add_action( 'admin_init', array( $this, 'add_settings_page' ) );
	}

	// Force Extending class to define these method
	abstract public function add_menu();

	abstract public function add_settings_page();

	abstract public function load_page_template();
}
