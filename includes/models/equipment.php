<?php

namespace HysterYale\Updater\Models;

use \HysterYale\Updater\Models\Abstracts\Post_Model;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Equipment extends Post_Model
{
	const POST_TYPE = 'equipment';
	const UNIQUE_KEY = 'hysteryale_api_id';
	const WP_PROPS = array(
		'post_title' => 'hygapi_title',
		'post_content' => 'placeholder_content',
		'post_date' => 'placeholder_date',
	);
	const ALIASES = array(
		'api_id' => 'hysteryale_api_id',
		'title' => 'hygapi_title',
		'description' => 'hygapi_description',
		'url' => 'hygapi_url',
		'brand' => 'hygapi_brand',
		'culture' => 'hygapi_culture',
		'region' => 'hygapi_region',
		'image' => 'hygapi_image',
		'category' => 'hygapi_category',
		'type' => 'hygapi_type',
		'tagline' => 'hygapi_tagline',
		'features' => 'hygapi_features',
		'metadata' => 'hygapi_metadata',
		'content' => 'hygapi_content',
		'assets' => 'hygapi_assets',
		'images' => 'hygapi_images',
		'videos' => 'hygapi_videos',
		'properties' => 'hygapi_properties',
	);
	const HIDDEN = array(
	);

	// meta
	protected $hysteryale_api_id;
	protected $hygapi_title;
	protected $placeholder_content;
	protected $placeholder_date;
	protected $hygapi_description;
	protected $hygapi_url;
	protected $hygapi_brand;
	protected $hygapi_culture;
	protected $hygapi_region;
	protected $hygapi_image;
	protected $hygapi_category;
	protected $hygapi_type;
	protected $hygapi_tagline;
	protected $hygapi_features;
	protected $hygapi_metadata;
	protected $hygapi_content;
	protected $hygapi_assets;
	protected $hygapi_images;
//    protected $hygapi_videos; // todo
	protected $hygapi_properties;
	protected $price;
	protected $stock_number;
	protected $year;
	// computed
	private $url;
	private $image;
	protected $images;
	private $is_hyg_equipment;
	private $manufacturer;
	// private
	private $forklifts_term_id;

	/*
	 * Getters
	 */

	public function get_hysteryale_api_id()
	{
		return $this->get_prop( 'hysteryale_api_id' );
	}

	public function get_hygapi_title()
	{
		return $this->get_post_title();
	}
	
	public function get_placeholder_content( $apply_filters = false )
	{
		return $this->get_post_content( $apply_filters );
	}
	
	public function get_placeholder_date( $format = 'Y-m-d h:i:s' )
	{
		return $this->get_post_date( $format );
	}

	public function get_hygapi_description()
	{
		return $this->get_prop( 'hygapi_description' );
	}

	public function get_hygapi_url()
	{
		return $this->get_prop( 'hygapi_url' );
	}

	public function get_hygapi_brand()
	{
		return $this->get_prop( 'hygapi_brand' );
	}

	public function get_hygapi_culture()
	{
		return $this->get_prop( 'hygapi_culture' );
	}

	public function get_hygapi_region()
	{
		return $this->get_prop( 'hygapi_region' );
	}

	public function get_hygapi_image()
	{
		return $this->get_prop( 'hygapi_image' );
	}

	public function get_hygapi_category()
	{
		return $this->get_prop( 'hygapi_category' );
	}

	public function get_hygapi_type()
	{
		return $this->get_prop( 'hygapi_type' );
	}

	public function get_hygapi_tagline()
	{
		return $this->get_prop( 'hygapi_tagline' );
	}

	public function get_hygapi_features()
	{
		if ( null === $this->hygapi_features ) {
			$this->get_prop( 'hygapi_features' );
			$this->hygapi_features = $this->flatten_array_values( $this->hygapi_features, 'feature' );
		}
		return $this->hygapi_features;
	}

	public function get_hygapi_metadata()
	{
		if ( null === $this->hygapi_metadata ) {
			$this->get_prop( 'hygapi_metadata' );
			if ( isset( $this->metadata['keywords'] ) ) {
				$this->hygapi_metadata['keywords'] = $this->flatten_array_values( $this->hygapi_metadata['keywords'], 'keyword' );
			}
		}
		return $this->hygapi_metadata;
	}

	public function get_hygapi_content()
	{
		return $this->get_prop( 'hygapi_content' );
	}

	public function get_content()
	{
		return $this->get_hygapi_content();
	}

	public function get_hygapi_assets()
	{
		return $this->get_prop( 'hygapi_assets' );
	}

	public function get_hygapi_images()
	{
		return $this->get_prop( 'hygapi_images' );
	}

	public function get_images()
	{
		if ( $this->is_hyg_equipment() ) {
			return $this->get_hygapi_images();
		} else {
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
	}

//	public function get_hygapi_videos()
//	{
//		return $this->get_prop('hygapi_videos');
//	}

	public function get_hygapi_properties( $sub_prop = null )
	{
		if ( null === $this->hygapi_properties ) {
			$this->get_prop( 'hygapi_properties' );
			if ( isset( $this->hygapi_properties['environment'] ) ) {
				$this->hygapi_properties['environment'] = $this->flatten_array_values( $this->hygapi_properties['environment'], 'environment' );
			}
			if ( isset( $this->hygapi_properties['power'] ) ) {
				$this->hygapi_properties['power'] = $this->flatten_array_values( $this->hygapi_properties['power'], 'power' );
			}
			if ( isset( $this->hygapi_properties['primary_task'] ) ) {
				$this->hygapi_properties['primary_task'] = $this->flatten_array_values( $this->hygapi_properties['primary_task'], 'primary_task' );
			}
			if ( isset( $this->hygapi_properties['industries'] ) ) {
				$this->hygapi_properties['industries'] = $this->flatten_array_values( $this->hygapi_properties['industries'], 'industry' );
			}
		}
		return $sub_prop && isset( $this->hygapi_properties[$sub_prop] ) ? $this->hygapi_properties[$sub_prop] : $this->hygapi_properties;
	}

	public function get_price()
	{
		return $this->get_prop('price');
	}
	
	public function has_price()
	{
		return (bool) $this->get_price();
	}

	public function get_serial()
	{
		return $this->get_prop('serial');
	}
	
	public function has_serial()
	{
		return (bool) $this->get_serial();
	}
	
	public function get_hours()
	{
		return $this->get_prop('hours');
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

	/*
	 * Computed
	 */

	public function get_title()
	{
		return $this->get_hygapi_title();
	}

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
				$this->image = get_the_post_thumbnail_url( $this->get_id() );
			} else {
				if ( $this->is_hyg_equipment() ) {
					$this->image = $this->get_hygapi_image();
				} else {
					$image = array_first( get_field( 'images', $this->get_id() ) );
					$this->image = isset( $image['url'] ) ? $image['url'] : '';
				}
			}
		}
		return $this->image;
	}

	public function get_manufacturer()
	{
		if ( null === $this->manufacturer ) {
			$manufacturers = wp_get_post_terms( $this->get_id(), 'manufacturer' );
			$manufacturer = ! empty( $manufacturers ) ? array_first( $manufacturers ) : false;
			$this->manufacturer = $manufacturer && is_object( $manufacturer ) ? $manufacturer->name : '';
		}
		return $this->manufacturer;
	}

	private function get_forklifts_term_id()
	{
		if ( null === $this->forklifts_term_id ) {
			$forklifts = get_term_by( 'name', 'Forklifts', 'equipment_category' );
			$this->forklifts_term_id = $forklifts && is_object( $forklifts ) ? $forklifts->term_id : false;
		}
		return $this->forklifts_term_id;
	}

	private function get_taxonomy_mapping()
	{
		return array(
			'hygapi_category' => array(
				'taxonomy' => 'equipment_category',
				'parent' => $this->get_forklifts_term_id(),
			),
			'hygapi_brand' => array(
				'taxonomy' => 'manufacturer',
				'parent' => null,
			),
		);
	}

	public function is_hyg_equipment()
	{
		if ( null === $this->is_hyg_equipment ) {
			$this->is_hyg_equipment = (bool) get_field( 'hysteryale_api_id', $this->get_id() );
		}
		return $this->is_hyg_equipment;
	}

	/*
	 * Setters
	 */

	public function set_hysteryale_api_id( $value )
	{
		return $this->set_prop( 'hysteryale_api_id', $value );
	}

	public function set_hygapi_title( $value )
	{
		return $this->set_prop( 'hygapi_title', $value );
	}
	
	public function set_placeholder_content( $value )
	{
		return $this->set_prop( 'placeholder_content', ' ' );
	}
	
	public function set_placeholder_date( $value )
	{
		return $this->set_prop( 'placeholder_date', '' );
	}

	public function set_hygapi_description( $value )
	{
		return $this->set_prop( 'hygapi_description', $value );
	}

	public function set_hygapi_url( $value )
	{
		return $this->set_prop( 'hygapi_url', $value );
	}

	public function set_hygapi_brand( $value )
	{
		return $this->set_prop( 'hygapi_brand', $value );
	}

	public function set_hygapi_culture( $value )
	{
		return $this->set_prop( 'hygapi_culture', $value );
	}

	public function set_hygapi_region( $value )
	{
		return $this->set_prop( 'hygapi_region', $value );
	}

	public function set_hygapi_image( $value )
	{
		return $this->set_prop( 'hygapi_image', $value );
	}

	public function set_hygapi_category( $value )
	{
		return $this->set_prop( 'hygapi_category', $value );
	}

	public function set_hygapi_type( $value )
	{
		return $this->set_prop( 'hygapi_type', $value );
	}

	public function set_hygapi_tagline( $value )
	{
		return $this->set_prop( 'hygapi_tagline', $value );
	}

	public function set_hygapi_features( $value )
	{
		return $this->set_prop( 'hygapi_features', array_values( $value ) );
	}

	public function set_hygapi_metadata( $value )
	{
		if ( is_array( $value ) ) {
			return $this->set_prop( 'hygapi_metadata', $value, array(
					'title',
					'description',
					'keywords',
				) );
		}
	}

	public function set_hygapi_content( $value )
	{
		if ( is_array( $value ) ) {
			return $this->set_prop( 'hygapi_content', $value, array(
					'eyebrow',
					'title',
					'tabs',
				) );
		}
	}

	public function set_hygapi_assets( $value )
	{
		if ( is_array( $value ) ) {
			return $this->set_prop( 'hygapi_assets', $value, array(
					'name',
					'path',
				) );
		}
	}

	public function set_hygapi_images( $value )
	{
		if ( is_array( $value ) ) {
			return $this->set_prop( 'hygapi_images', $value, array(
					'alt_text',
					'name',
					'path',
				) );
		}
	}

//	public function set_hygapi_videos( $value )
//	{
//		if ( is_array( $value ) ) {
//			return $this->set_prop( 'hygapi_videos', $value, array(
//				'',
//			) );
//		}
//	}

	public function set_hygapi_properties( $value )
	{
		if ( is_array( $value ) ) {
			return $this->set_prop( 'hygapi_properties', $value, array(
					'models',
					'capacity',
					'load_weight',
					'max_lift_height',
					'environment',
					'power',
					'primary_task',
					'industries',
				) );
		}
	}

	/*
	 * Savers
	 */

	public function after_save()
	{
		$this->update_terms();
	}

	private function update_terms()
	{
		$terms = array();
		foreach ( $this->get_taxonomy_mapping() as $prop => $map ) {
			$getter = "get_{$prop}";
			$term_name = $this->{$getter}();
			if ( $term_name ) {
				$term = $this->maybe_create_term( $term_name, $map['taxonomy'], $map['parent'] );
				wp_set_post_terms( $this->get_id(), array( $term ), $map['taxonomy'] );
			}
		}
	}

	private function maybe_create_term( $term_name, $taxonomy, $parent )
	{
		if ( $term = term_exists( $term_name, $taxonomy, $parent ) ) {
			return isset( $term['term_id'] ) ? $term['term_id'] : $term;
		}

		$parent = $parent ? $parent : 0;
		$term = wp_insert_term( $term_name, $taxonomy, array( 'parent' => $parent ) );
		return isset( $term['term_id'] ) ? $term['term_id'] : $term;
	}

	public function save_hygapi_title_meta( $value )
	{
		return $this->save_post_title( $value );
	}

	public function save_placeholder_content_meta( $value )
	{
		if ( is_array( $value ) || ! $value ) {
			$value = ' ';
		}
		return $this->save_post_content( $value );
	}

	public function save_placeholder_date_meta( $value, $return_format = '' )
	{
		return $this->save_post_date( $this->to_datetime( $value ), $return_format );
	}

	public function save_hygapi_features_meta( $value )
	{
		$arr = array();
		foreach ( $value as $url ) {
			$arr[]['feature'] = $url;
		}
		return update_field( 'hygapi_features', $arr, $this->get_id() );
	}

	public function save_hygapi_metadata_meta( $value )
	{
		$arr = array();
		foreach ( $value as $prop => $val ) {
			if ( is_array( $val ) ) {
				$i = 0;
				switch ( $prop ) {
					case 'keywords':
						$sub_prop = 'keyword';
						break;
					default:
						$sub_prop = $prop;
				}
				foreach ( $val as $v ) {
					$arr[$prop][$i][$sub_prop] = $v;
					$i ++;
				}
			} else {
				$arr[$prop] = $val;
			}
		}
		return update_field( 'hygapi_metadata', $arr, $this->get_id() );
	}

	public function save_hygapi_properties_meta( $value )
	{
		$arr = array();
		foreach ( $value as $prop => $val ) {
			if ( is_array( $val ) ) {
				$i = 0;
				switch ( $prop ) {
					case 'industries':
						$sub_prop = 'industry';
						break;
					default:
						$sub_prop = $prop;
				}
				foreach ( $val as $v ) {
					$arr[$prop][$i][$sub_prop] = $v;
					$i ++;
				}
			} else {
				$arr[$prop] = $val;
			}
		}
		return update_field( 'hygapi_properties', $arr, $this->get_id() );
	}

	/*
	 * Helpers
	 */

	public function to_label( $string )
	{
		return ucwords( str_replace( '_', ' ', $string ) );
	}

	public function to_list( $array, $separator = ', ' )
	{
		return is_array( $array ) && ! empty( $array ) ? implode( $separator, $array ) : '';
	}

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
