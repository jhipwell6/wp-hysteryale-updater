<?php

namespace HysterYale\Updater\Controllers;

use \FLPageData;
use \HysterYale\Updater\Models\Equipment as Equipment_Model;
use \HysterYale\Updater\Models\Equipment as Used_Equipment_Model;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Equipment
{
	protected static $instance;

	public function __construct()
	{
		add_action( 'fl_page_data_add_properties', array( $this, 'add_post_properties' ) );
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

	public function add_post_properties()
	{
		if ( class_exists( 'FLPageData' ) ) {
			FLPageData::add_post_property( 'hygapi_equipment_card', array(
				'label' => __( 'Equipment Card (API)', HYSTERYALE_UPDATER_DOMAIN ),
				'group' => 'posts',
				'type' => array( 'string' ),
				'getter' => array( $this, 'fc_equipment_card' )
			) );

			FLPageData::add_post_property( 'hygapi_equipment_accordion', array(
				'label' => __( 'Equipment Accordion (API)', HYSTERYALE_UPDATER_DOMAIN ),
				'group' => 'posts',
				'type' => array( 'string' ),
				'getter' => array( $this, 'fc_equipment_accordion' )
			) );

			FLPageData::add_post_property( 'hygapi_equipment_images', array(
				'label' => __( 'Equipment Images (API)', HYSTERYALE_UPDATER_DOMAIN ),
				'group' => 'posts',
				'type' => array( 'string' ),
				'getter' => array( $this, 'fc_equipment_images' )
			) );

			FLPageData::add_post_property( 'hygapi_equipment_tabs', array(
				'label' => __( 'Equipment Tabs (API)', HYSTERYALE_UPDATER_DOMAIN ),
				'group' => 'posts',
				'type' => array( 'string' ),
				'getter' => array( $this, 'fc_equipment_tabs' )
			) );
		}
	}

	public function fc_equipment_card()
	{
		return HYSTERYALE_UPDATER()->view( 'equipment-card' );
	}

	public function fc_equipment_accordion()
	{
		return HYSTERYALE_UPDATER()->view( 'equipment-accordion' );
	}

	public function fc_equipment_images()
	{
		return HYSTERYALE_UPDATER()->view( 'equipment-images' );
	}

	public function fc_equipment_tabs()
	{
		if ( HYSTERYALE_UPDATER()->equipment()->is_hyg_equipment() ) {
			return HYSTERYALE_UPDATER()->view( 'equipment-tabs-hysteryale' );
		} else {
			return HYSTERYALE_UPDATER()->view( 'equipment-tabs' );
		}
	}

}

Equipment::instance();
