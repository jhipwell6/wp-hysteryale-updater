<?php

namespace HysterYale\Updater\Controllers\Importers;

use \HysterYale\Updater\Controllers\Progress;

if ( ! defined( 'ABSPATH' ) )
	exit;

class HYG_API
{
	protected static $instance;
	private $api_base_url = 'https://hygapi.wpengine.com/wp-json/hyg/v1/products/';
	private $authkey = '5a36a885-77ad-4b5a-8562-b52f0cdc94e5';
	private $types = null;
	private $total_num_products = 0;
	public $tree = array();
	public $products = array();

	/**
	 * Initializes variables and sets up WordPress hooks/actions.
	 *
	 * @return void
	 */
	protected function __construct()
	{
		add_action( 'wp_ajax_hysteryale_updater__import', array( $this, 'import' ) );
		add_action( 'hysteryale_updater__cron_import', array( $this, 'import' ) );
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
	 * Grabs config options from settings page
	 *
	 * @return void
	 */
	private function get_types()
	{
		if ( is_null( $this->types ) )
			$this->types = get_option( 'hysteryale_updater__type_limitation' );

		if ( ! is_array( $this->types ) )
			$this->types = array();

		return $this->types;
	}

	/**
	 * Get tree of all updatable products
	 *  - from configured types
	 *  - mapped to posts if applicable
	 *  - cache as transient
	 */
	public function get_product_tree( $force_update = false, $cache = false )
	{
		$tree = array();

		// Get list of equipment with hysteryale id set for reference
		global $wpdb;
		$preconfigured = $wpdb->get_results(
			"SELECT meta_value as hysteryale_id
                    ,post_id
			 FROM {$wpdb->postmeta} m
			 WHERE meta_key = 'hysteryale_api_id'
			 	   AND meta_value IS NOT NULL
			 	   AND meta_value != ''
            "
			, OBJECT_K
		);

		$types = $this->get_types();
		foreach ( $types as $type ) {
			$data = $this->api_call( $type, $force_update, $cache );

			$data_with_post_ids = array();

			if ( is_array( $data ) && ! empty( $data ) ) {
				foreach ( $data as $index => $product ) {
					$post_id = '';
					$update = false;
					$hysteryale_id = empty( $product['api_id'] ) ? false : $product['api_id'];

					if (
						isset( $preconfigured[$hysteryale_id] )
						and ! empty( $preconfigured[$hysteryale_id]->post_id )
					) {
						$post_id = $preconfigured[$hysteryale_id]->post_id;
						$update = true;
					}

					$product['__update'] = $update;
					$product['__post_id'] = $post_id;
					$data_with_post_ids[$index] = $product;
				}
			}

			$tree[$type] = $data_with_post_ids;
		}

		return $tree;
	}

	/**
	 * Get response for specified endpoint
	 *  - Cache as transient
	 *  - Force fresh call if set
	 */
	protected function api_call( $type, $force_update = false, $cache = false )
	{

		$bust_cache = 'bc_' . uniqid();
		$url = add_query_arg( array(
			'brand' => $type,
			'limit' => 999,
			$bust_cache => 1,
			), $this->api_base_url );

		$transient_key = 'hysteryale_' . $type;
		$transient_key = strtolower( $transient_key );

		$data_store = false;

		if ( $cache ) {
			if ( $force_update ) {
				delete_transient( $transient_key );
			} else {
				$data_store = get_transient( $transient_key );
			}
		}

		if ( empty( $data_store ) ) {
			$product_data = array();
			$json = $this->fetch_product_list( $url );
			$raw_data = json_decode( $json, true );

			if ( ! empty( $raw_data ) ) {
				$data_store = json_encode( $raw_data );

				if ( $cache ) {
					/*
					  Cache time:
					  60*60*8 = 28800
					 */
					set_transient( $transient_key, $data_store, 28800 );
				}
			} else {
				die( "<p>Hyster-Yale's feed appears to be down. Please refresh and try again.</p>
				<script>
					var timeinmilliseconds = 3000;
					var reloadCnt = window.sessionStorage.getItem( 'reloadCounter' ) ? parseInt(window.sessionStorage.getItem( 'reloadCounter' )) + 1 : 1;

					window.sessionStorage.setItem( 'reloadCounter', reloadCnt )
					console.log(reloadCnt);
					if ( reloadCnt <= 3 )
					  setTimeout(function(){ window.location.reload(true) }, timeinmilliseconds);
				  </script>
				" );
			}
		}

		try {
			$data = json_decode( $data_store, true );
		} catch ( Exception $e ) {
			return false;
		}

		if ( ! is_array( $data ) )
			$data = array();

		return $data;
	}

	/**
	 * Get response for specified endpoint
	 *  - Cache as transient
	 *  - Force fresh call if set
	 */
	protected function fetch_product_list( $url )
	{
		$response = wp_remote_get( $url, array(
			'timeout' => 120,
			'headers' => array(
				'authkey' => $this->authkey,
			)
			) );

		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			return wp_remote_retrieve_body( $response );
		}

		return array();
	}

	/**
	 * Starts the import process
	 *
	 * @hook   wp_ajax_cnf_import_class
	 * @param  string $url the url to get the data from
	 */
	public function import()
	{
		ini_set( 'memory_limit', '512M' );
		ini_set( 'max_execution_time', 2400 );
		set_time_limit( 0 );

		$config = $this->get_import_config();

		// No post, running from shell or cron
		if ( empty( $config ) ) {
			$tree = $this->get_product_tree( true );
			$config = array();
			foreach ( $tree as $type => $products ) {
				foreach ( $products as $product ) {
					$hysteryale_id = empty( $product['api_id'] ) ? false : $product['api_id'];
					$wp_id = empty( $product['__post_id'] ) ? false : $product['__post_id'];
					$import_config = empty( $product['__update'] ) ? false : 'update';

					if ( ! $import_config or ! $wp_id or ! $hysteryale_id )
						continue;

					$config[$hysteryale_id] = array(
						'hysteryale_id' => $hysteryale_id
						, 'type' => $type
						, 'import_config' => $import_config
						, 'wp_id' => $wp_id
					);
				}
			}
		}

		if ( empty( $config ) )
			exit;

		$this->products( $config );

		exit;
	}

	/**
	 * Get import config from $_POST
	 *  - preprocess:
	 *    - remove "ignores"
	 */
	protected function get_import_config()
	{
		global $wpdb;

		$raw_config = empty( $_POST['config'] ) ? array() : $_POST['config'];

		// No post, running from shell or cron
		if ( empty( $_POST ) ) {
			return array();
		}

		$config = array();
		$delete = array(
			'hysteryale_ids' => array()
			, 'placeholders' => array()
		);
		$delete_except = array(
			'hysteryale_ids' => array()
			, 'wp_ids' => array()
			, 'placeholders' => array()
		);

		foreach ( $raw_config as $i => $data ) {
			$action = isset( $data['import_config'] ) ? $data['import_config'] : '';

			if (
			// Must have a hysteryale ID to fetch
				! empty( $data['hysteryale_id'] )
				// Valid import option
				and ! empty( $action )
			) {
				if (
					$action == 'create'
					// if update, wp_id must be specified
					or (
					$action == 'update'
					and ! empty( $data['wp_id'] )
					)
				) {
					$config[$i] = $data;

					if ( $action == 'update' ) {
						$delete_except['hysteryale_ids'][] = $data['hysteryale_id'];
						$delete_except['wp_ids'][] = $data['wp_id'];
						$delete_except['placeholders'][] = '%s';
					}
				} elseif ( $action == 'ignore' ) {
					$delete['hysteryale_ids'][] = $data['hysteryale_id'];
					$delete['placeholders'][] = '%s';
				}
			}
		}

		if ( ! empty( $delete['hysteryale_ids'] ) ) {
			// Remove values for freshly ignored items
			$sql = "DELETE FROM {$wpdb->postmeta}
                    WHERE meta_key = 'hysteryale_api_id'
                        AND meta_value IN (" . join( ",", $delete['placeholders'] ) . ")
            ";
			$data = $delete['hysteryale_ids'];

			$result = $wpdb->query( $wpdb->prepare(
					$sql
					, $data
			) );
		}

		if ( ! empty( $delete_except['hysteryale_ids'] ) ) {
			// Remove all but selected value for updating items
			$sql = "DELETE FROM {$wpdb->postmeta}
                    WHERE meta_key = 'hysteryale_api_id'
                        AND meta_value IN (" . join( ",", $delete_except['placeholders'] ) . ")
                        AND post_id NOT IN (" . join( ",", $delete_except['placeholders'] ) . ") 
            ";
			$data = array_merge( $delete_except['hysteryale_ids'], $delete_except['wp_ids'] );

			$result = $wpdb->query( $wpdb->prepare(
					$sql
					, $data
			) );
		}

		return $config;
	}

	public function products( $config )
	{
		$counter = 1;
		Progress::set( 0, count( $config ), 'Processing Products' );

		$cache = defined( 'HYSTERYALE_UPDATER_CACHE_PRODUCT_FEED_CALLS' ) ? HYSTERYALE_UPDATER_CACHE_PRODUCT_FEED_CALLS : false;
		$force = ! $cache;

		$product_data = array();
		$types = $this->get_types();
		foreach ( $types as $type ) {
			$product_data[$type] = $this->api_call( $type, $force, $cache );
		}

		foreach ( $config as $product ) {
			$data = $product_data[$product['type']];
			if ( ! is_array( $data ) )
				continue;

			$key = array_search( $product['hysteryale_id'], array_column( $data, 'api_id' ) );

			// Validate ID
			if ( empty( $data[$key]['api_id'] ) or $data[$key]['api_id'] != $product['hysteryale_id'] ) {
				Progress::update( 'index', $counter );
				$counter ++;
				continue;
			}

			$Equipment = HYSTERYALE_UPDATER()->equipment();
			$Equipment = $Equipment->get_by_unique_key( $product['hysteryale_id'] );
			$Equipment->make( $data[$key] );
			$Equipment = $Equipment->save(); // todo: set title and slug

			Progress::update( 'index', $counter );
			$counter ++;
		}

		do_action( 'hysteryale_updater__products_processed', $this );
	}
}

HYG_API::instance();
