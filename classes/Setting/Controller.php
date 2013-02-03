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
class Lava_Setting_Controller extends Lava_Base
{
	public $_settings = array();
	public $_controller_namespace = 'setting'; //overloaded by sub classes
	public $_controller_namespace_plural;
	public $_should_register_action_methods = true;



	function _construct(){
		$this->_add_lava_action( '_do_save' );
	}

	function _register_hooks() {
		$this->_add_lava_action( 'admin_init', '_do_default', 50 );
		$this->_add_action( 'admin_menu', '_register_settings_with_page_hook', 3 );
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

			$class_name = $this->_class( 'Setting_' . $this->_capitalize( $setting_type ) );

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

	function _get_controller_namespace( $plural = false ) {
		if( ! $plural ) {
			return $this->_controller_namespace;
		} else if( is_null( $this->_controller_namespace_plural ) ) {
			return $this->_controller_namespace . 's';
		} else {
			return $this->_controller_namespace_plural;
		}
	}

	/*
		Sugar functions
	*/

	function _get_setting_id_prefix() {
		return $this->_controller_namespace;
	}

	function _get_setting_name_prefix() {
		return $this->_get_controller_namespace('plural'); //should be plural
	}

	function _get_scene_id_prefix( $append = '' ) {
		return $this->_controller_namespace;
	}

	function _parse_setting_vars( $vars ) {
		return $vars;
	}

	/*
		Database Functions
	*/

	function _get_option_id() {
		return  $this->_namespace( $this->_get_controller_namespace('plural') );
	}

	function _get_option() {
		return get_option( $this->_get_option_id(), array() );
	}

	function _update_option( $settings ) {
		return update_option( $this->_get_option_id(), $settings );
	}



	function _get_settings_from_db() {
		return get_option( $this->_get_option_id() );
	}

	function _get_setting_from_db( $setting_key, $default = null ) {
		$settings = $this->_get_settings_from_db();

		if( array_key_exists( $setting_key, $settings ) )
			return $settings[ $setting_key ];
		else
			return $default;
	}

	function _update_settings_to_db( $settings ) {
		return update_option( $this->_get_option_id() );
	}

	function _update_setting_to_db( $setting_key, $setting_value ) {
		$settings = $this->_get_settings_from_db();
		$settings[ $setting_key ] = $setting_value;
		return $this->_update_settings_to_db( $settings );
	}

	/*
		Flow functions
	*/

	function _register_settings_with_page_hook() {
		$this->_register_settings_with_page();
	}

	function _register_settings_with_page( $page_ids = null, $scene_ids = null ) {
		$settings = $this->_get_settings();
		foreach( $settings as $setting_id => $setting ) {
			$setting->_register_setting_with_page( $page_ids, $scene_ids );
		}
	}


	function _admin_init() {
		$this->_add_default_array();
	}

	function _add_default_array() {
		// adds plugin_name_settings to options database
		add_option( $this->_get_option_id(), array() );
	}

	function _do_save() {
		$key = $this->_namespace( $this->_get_setting_name_prefix( 'plural' ) );
		if( array_key_exists( $key , $_REQUEST ) ) {
			$settings = $_REQUEST[$key];
			foreach( $settings as $setting => $value ) {
				if( $this->_setting_exists( $setting ) ) {
					$this->_get_setting( $setting )->_set_setting_value_from_request( $settings );
				} else {
					// @todo queue error message
					echo 'there be no setting with that name';
					die( $setting );
				}
			}
		}
	}

	function _do_default() {
		$settings = $this->_get_option();
		foreach( $this->_get_settings() as $setting_id => $setting ) {
			if( !array_key_exists( $setting_id, $settings ) ) {
				$settings[$setting_id] = $setting->_get_setting_value();
			}
		}
		$this->_update_option( $settings );
	}
}
?>