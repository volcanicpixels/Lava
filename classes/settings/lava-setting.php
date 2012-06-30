<?php
/**
 * Setting
 *
 * @package Lava
 * @subpackage Setting
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Setting extends Lava_Base
{
	public $_setting_controller;
	public $_setting_type = '';
	public $_setting_id;
	public $_setting_name = '';
	public $_setting_default = '';

	public $_should_register_action_methods = true;

	function _construct( $setting_controller, $setting_id ) {
		$this->_setting_controller = $setting_controller;
		$this->_setting_id = $setting_id;
		$this->_set_return_object( $setting_controller );
		$this->_add_action( 'admin_menu', '_register_with_scene', 2 );
	}

	/*
		Hooks
	*/




	/*
		Accessors
	*/

	function _load_defaults() {

	}

	function _parse_vars( $vars ) {
		foreach( $vars as $key => $value ) {
			switch( $key ) {
				case 'name':
					$this->_set_name( $value );
					break;
				case 'section':
					$this->_remember( '_scene_suffix', $value );
					break;
				case 'scene':
					$this->_remember( '_scene_id', $value );
					break;
				case 'scene_title':
				case 'section_title':
					$this->_remember( '_scene_title', $value );
					break;
			}
		}
	}

	function _get_setting_id() {
		return $this->_setting_id;
	}

	function _get_name() {
		return $this->_setting_name;
	}

	function _set_name( $setting_name ) {
		$this->_setting_name = $setting_name;
		return $this->_r();
	}

	function _get_default() {
		return $this->_setting_default;
	}

	function _set_default( $setting_default, $should_overwrite = true ) {
		if( $should_overwrite or is_null( $this->_setting_default ) )
			$this->_setting_default = $setting_default;
		return $this->_r();
	}

	function _get_value() {
		return $this->_setting_controller->_get_value_for( $setting_id, $this->_get_default() );
	}

	/*
		Flow functions
	*/

	function _register_with_scene() {
		//add settings page
		//add relevant scene
		$scene_suffix = $this->_recall( '_scene_suffix', 'General' );
		$scene_id = $this->_recall( '_scene_id', $this->_setting_controller->_get_scene_id( $scene_suffix ) );

		$this->
			_pages()
				->_add_page( 'settings', 'settings' )
					->_add_scene( $scene_id, 'settings' )
						->_set_scene_title( $this->_recall( '_scene_title' ) )
		;
	}






	
}
?>