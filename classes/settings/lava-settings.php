<?php
/**
 * Settings
 *
 * @package Lava
 * @subpackage Settings
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Settings extends Lava_Base
{
	protected $_settings = array();
	protected $_controller_namespace = 'setting'; //overloaded by sub classes
	protected $_setting_types = array(
		'' 			=> ''
	);



	function _construct(){
	}

	/*
		Generic functions
	*/

	function _get_value_for( $setting_id, $default = '' ) {
		$settings = $this->_get_option();
		if( array_key_exists( $setting_id, $settings) ) {
			return $settings[ $setting_id ];
		} else {
			return $default;
		}
	}


	// adds a value if the key does not exist only
	function _add_value_for( $setting_id, $setting_value ) {
		$settings = $this->_get_option();
		if( ! array_key_exists( $setting_id, $settings) ) {
			$this->_set_value_for( $setting_id, $setting_value );
		}
	}

	function _set_value_for( $setting_id, $setting_value ) {
		$settings = $this->_get_option();
		$settings[ $setting_id ] = $setting_value;
		$this->_update_option( $settings );
	}



	/*
		Admin load functions - these are only used when the admin interface is being used
	*/


	function _add_setting( $setting_id, $setting_type = '' ) {
		if( ! $this->_setting_exists( $setting_id ) ) {

			$class_name = $this->_class( $setting_type ) . '_Setting';

			$args = array(
				$this,
				$setting_id
			);

			$this->_settings[ $setting_id ] = $this->_construct_class( $class_name, $args );
		}

		$this->_set_child( $this->_settings[ $setting_id ] );

		return $this->_r();
	}


	function _get_setting( $setting_key )
	{
		$this->_kill_child();

		if( $this->_setting_exists( $setting_key ) ) {
			$this->_set_child( $this->_settings[ $setting_key ] );
		}

		return $this->_r();
	}

	function _setting_exists( $setting_key ) {
		if( array_key_exists( $setting_key , $this->_settings ) )
			return true;
		else
			return false;
	}


	function _get_settings() {
		return $this->_settings;
	}

	/*
		Sugar functions
	*/

	function _get_scene_id( $append = '' ) {
		$append = strtolower( $append );
		return $this->_controller_namespace . '-' . $append;
	}

	/*
		Database Functions
	*/

	function _get_option_id() {
		return $this->_namespace( $this->_setting_prefix );
	}

	function _get_option() {
		return get_option( $this->_get_option_id() );
	}

	function _update_option( $settings ) {
		return update_option( $this->_get_option_id(), $settings );
	}



	function _get_settings_from_db() {
		return get_option( $this->_namespace( $this->_setting_prefix ) );
	}

	function _get_setting_from_db( $setting_key, $default = null ) {
		$settings = $this->_get_settings_from_db();

		if( array_key_exists( $setting_key, $settings ) )
			return $settings[ $setting_key ];
		else
			return $default;
	}

	function _update_settings_to_db( $settings ) {
		return update_option( $this->_namespace( $this->_setting_prefix ) );
	}

	function _update_setting_to_db( $setting_key, $setting_value ) {
		$settings = $this->_get_settings_from_db();
		$settings[ $setting_key ] = $setting_value;
		return $this->_update_settings_to_db( $settings );
	}

	/*
		Flow functions
	*/

	function _admin_init() {
		$this->_add_default_array();
	}

	function _add_default_array() {
		// adds plugin_name_settings to options database
		add_option( $this->_namespace( $this->_setting_namespace ), array() );
	}
}
?>