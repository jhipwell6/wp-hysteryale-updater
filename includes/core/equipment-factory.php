<?php

namespace HysterYale\Updater\Core;

use \HysterYale\Updater\Core\Abstracts\Factory;
use \HysterYale\Updater\Models\Equipment;
use \HysterYale\Updater\Models\Used_Equipment;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Equipment_Factory extends Factory
{
	private $found = array();

	/**
	 * Get an item from the collection by key.
	 *
	 * @param  mixed  $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function get( $Equipment = false, $default = null )
	{
		$equipment_id = $this->get_equipment_id( $Equipment );
		if ( $equipment_id && $this->contains( 'id', $equipment_id ) && $equipment_id != 0 && ! in_array( $equipment_id, $this->found ) ) {
			$this->found[] = $equipment_id;
			return $this->where( 'id', $equipment_id );
		}

		if ( 'used_equipment' === get_post_type( $equipment_id ) ) {
			$Equipment = new Used_Equipment( $equipment_id );
		} else {
			$Equipment = new Equipment( $equipment_id );
		}

		$this->add( $Equipment );

		return $this->last();
	}

	/**
	 * Get the equipment ID depending on what was passed.
	 *
	 * @return int|bool false on failure
	 */
	private function get_equipment_id( $Equipment )
	{
		global $post;

		if ( false === $Equipment && isset( $post, $post->ID ) && 'equipment' === get_post_type( $post->ID ) ) {
			return absint( $post->ID );
		} elseif ( false === $Equipment && isset( $post, $post->ID ) && 'used_equipment' === get_post_type( $post->ID ) ) {
			return absint( $post->ID );
		} elseif ( is_numeric( $Equipment ) ) {
			return $Equipment;
		} elseif ( $Equipment instanceof \HysterYale\Updater\Models\Equipment ) {
			return $Equipment->get_id();
		} elseif ( $Equipment instanceof \HysterYale\Updater\Models\Used_Equipment ) {
			return $Equipment->get_id();
		} elseif ( ! empty( $Equipment->ID ) ) {
			return $Equipment->ID;
		} else {
			return false;
		}
	}

}
