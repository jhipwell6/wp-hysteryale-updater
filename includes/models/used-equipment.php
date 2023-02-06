<?php

namespace HysterYale\Updater\Models;

use \HysterYale\Updater\Models\Abstracts\Post_Model;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Used_Equipment extends Post_Model
{
	const POST_TYPE = 'used_equipment';
	const UNIQUE_KEY = '';
	const WP_PROPS = array(
		'post_title' => 'title',
	);
	const ALIASES = array(
	);
	const HIDDEN = array(
	);

	// meta
	protected $title;
	protected $price;
	protected $stock_number;
	protected $year;
	protected $model;
	protected $serial;
	protected $hours;
	protected $side_shifter;
	protected $warranty;
	protected $location;
	protected $fuel_type;
	protected $capacity;
	protected $mast;
	protected $attachment;
	protected $battery_charger;
	protected $lift_height;
	protected $lower_height;
	protected $images;
	// computed
	private $url;
	private $image;
	private $manufacturer;

	/*
	 * Getters
	 */

	public function get_title()
	{
		return $this->get_post_title();
	}

	public function get_description()
	{
		return $this->get_post_content();
	}

	public function get_price()
	{
		return $this->get_prop( 'price' );
	}

	public function has_price()
	{
		return (bool) $this->get_price();
	}

	public function get_stock_number()
	{
		return $this->get_prop( 'stock_number' );
	}

	public function has_stock_number()
	{
		return (bool) $this->get_stock_number();
	}

	public function get_year()
	{
		return $this->get_prop( 'year' );
	}

	public function has_year()
	{
		return (bool) $this->get_year();
	}

	public function get_model()
	{
		return $this->get_prop( 'model' );
	}

	public function get_serial()
	{
		return $this->get_prop( 'serial' );
	}

	public function get_hours()
	{
		return $this->get_prop( 'hours' );
	}

	public function get_side_shifter()
	{
		return $this->get_prop( 'side_shifter' );
	}

	public function get_warranty()
	{
		return $this->get_prop( 'warranty' );
	}

	public function get_location()
	{
		return $this->get_prop( 'location' );
	}

	public function get_fuel_type()
	{
		return $this->get_prop( 'fuel_type' );
	}

	public function get_capacity()
	{
		return $this->get_prop( 'capacity' );
	}

	public function get_mast()
	{
		return $this->get_prop( 'mast' );
	}

	public function get_attachment()
	{
		return $this->get_prop( 'attachment' );
	}

	public function get_battery_charger()
	{
		return $this->get_prop( 'battery_charger' );
	}

	public function get_lift_height()
	{
		return $this->get_prop( 'lift_height' );
	}

	public function get_lower_height()
	{
		return $this->get_prop( 'lower_height' );
	}

	public function get_images()
	{
		$this->get_prop( 'images' );
		if ( empty( $this->images ) ) {
			$this->images = array( array(
					'url' => $this->get_image(),
					'alt' => $this->get_title(),
					'sizes' => array(
						'thumbnail' => get_the_post_thumbnail_url( $this->get_id(), 'thumbnail' )
					)
				) );
		}
		return $this->images;
	}

	/*
	 * Computed
	 */

	public function get_url()
	{
		if ( null === $this->url ) {
			$this->url = get_permalink( $this->get_id() );
		}
		return $this->url;
	}

	public function get_image()
	{
		if ( null === $this->image ) {
			if ( has_post_thumbnail( $this->get_id() ) ) {
				$this->image = get_the_post_thumbnail_url( $this->get_id(), 'full' );
			} else {
				$image = array_first( get_field( 'images', $this->get_id() ) );
				$this->image = isset( $image['url'] ) ? $image['url'] : '';
			}
		}
		return $this->image;
	}

	public function get_manufacturer()
	{
		if ( null === $this->manufacturer ) {
			$manufacturers = wp_get_post_terms( $this->get_id(), 'used_manufacturer' );
			$manufacturer = ! empty( $manufacturers ) ? array_first( $manufacturers ) : false;
			$this->manufacturer = $manufacturer && is_object( $manufacturer ) ? $manufacturer->name : '';
		}
		return $this->manufacturer;
	}

	public function is_hyg_equipment()
	{
		return false;
	}

	public function get_content()
	{
		return '';
	}

	/*
	 * Setters
	 */

	public function set_title( $value )
	{
		return $this->set_prop( 'title', $value );
	}

	public function set_description( $value )
	{
		return $this->set_prop( 'description', $value );
	}

	public function set_price( $value )
	{
		return $this->set_prop( 'price', $value );
	}

	public function set_stock_number( $value )
	{
		return $this->set_prop( 'stock_number', $value );
	}

	public function set_year( $value )
	{
		return $this->set_prop( 'year', $value );
	}

	public function set_model( $value )
	{
		return $this->set_prop( 'model', $value );
	}

	public function set_serial( $value )
	{
		return $this->set_prop( 'serial', $value );
	}

	public function set_hours( $value )
	{
		return $this->set_prop( 'hours', $value );
	}

	public function set_side_shifter( $value )
	{
		return $this->set_prop( 'side_shifter', $value );
	}

	public function set_warranty( $value )
	{
		return $this->set_prop( 'warranty', $value );
	}

	public function set_location( $value )
	{
		return $this->set_prop( 'location', $value );
	}

	public function set_fuel_type( $value )
	{
		return $this->set_prop( 'fuel_type', $value );
	}

	public function set_capacity( $value )
	{
		return $this->set_prop( 'capacity', $value );
	}

	public function set_mast( $value )
	{
		return $this->set_prop( 'mast', $value );
	}

	public function set_attachment( $value )
	{
		return $this->set_prop( 'attachment', $value );
	}

	public function set_battery_charger( $value )
	{
		return $this->set_prop( 'battery_charger', $value );
	}

	public function set_lift_height( $value )
	{
		return $this->set_prop( 'lift_height', $value );
	}

	public function set_lower_height( $value )
	{
		return $this->set_prop( 'lower_height', $value );
	}

	public function set_images( $value )
	{
		return $this->set_prop( 'images', $value );
	}

	/*
	 * Savers
	 */

	public function after_save()
	{
		
	}

	public function save_title_meta( $value )
	{
		return $this->save_post_title( $value );
	}

	public function save_description_meta( $value )
	{
		return $this->save_post_content( $value );
	}

	/*
	 * Helpers
	 */

	private function flatten_array_values( $array, $key )
	{
		return array_filter( array_column( (array) $array, $key ) );
	}

	static public function get_properties()
	{
		$props = array_keys( get_object_vars( self ) );
		return array_combine( $props, $props );
	}

}
