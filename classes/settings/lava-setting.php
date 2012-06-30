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
	public $_setting_title = '';
	public $_setting_default_value;

	public $_template_directories = array();
	public $_twig_config = array();
	public $_twig_template = 'default.twig';

	public $_should_register_action_methods = true;

	function _construct( $setting_controller, $setting_id ) {
		$this->_setting_controller = $setting_controller;
		$this->_setting_id = $setting_id;
		$this->_set_return_object( $setting_controller );
		$this->_add_action( 'admin_menu', '_register_with_scene', 2 );

		$this->_template_directories = array(
			$this->_get_lava_path() . '/templates/default/settings/',
			$this->_get_lava_path() . '/templates/'
		);
	}

	function _serialize() {
		$old = parent::_serialize();
		$new = array(
			'full_setting_id' => $this->_get_full_setting_id(),
			'setting_id'      => $this->_get_setting_id(),
			'setting_title'   => $this->_get_setting_title(),
			'setting_type'    => $this->_get_setting_type(),
			'setting_value'   => $this->_get_setting_value()
		);
		return array_merge( $old, $new );
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
				case 'title':
				case 'name':
					$this->_set_setting_title( $value );
					break;
				case 'default':
					$this->_set_setting_default_value( $value );
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

	function _get_setting_id() { //this is the local id to the controller - e.g. login_duration
		return $this->_setting_id;
	}

	function _get_full_setting_id() {// this is the fully qualified id e.g. settings-login_duration
		return $this->_setting_controller->_get_setting_id_prefix() . '-' . $this->_get_setting_id();
	}

	function _get_setting_type() {
		return $this->_setting_type;
	}

	function _get_setting_title() {
		return $this->_setting_title;
	}

	function _set_setting_title( $setting_title ) {
		$this->_setting_title = $setting_title;
		return $this->_r();
	}

	function _get_setting_default_value() {
		return $this->_setting_default_value;
	}

	function _set_setting_default_value( $setting_default, $should_overwrite = true ) {
		if( $should_overwrite or is_null( $this->_setting_default_value ) )
			$this->_setting_default_value = $setting_default;
		return $this->_r();
	}

	function _get_setting_value() {
		return $this->_get_setting_default_value();
		return $this->_setting_controller->_get_value_for( $this->_get_setting_id(), $this->_get_setting_default_value() );
	}

	/*
		Flow functions
	*/

	function _register_with_scene() {
		//add settings page
		//add relevant scene
		$scene_suffix = $this->_recall( '_scene_suffix', 'general' );
		$scene_id = $this->_recall( '_scene_id', $this->_setting_controller->_get_scene_id_prefix() . '-' . $scene_suffix );

		$this->
			_pages()
				->_add_page( 'settings', 'settings' )
					->_add_scene( $scene_id, 'settings' )
						->_set_scene_title( $this->_recall( '_scene_title' ) )
						->_add_setting( $this )
		;
	}

	function _do_setting() {
		$this->_initialize_twig();
		$template = $this->_load_template();
		$variables = $this->_get_template_variables( $this->_serialize() );
		return $template->display( $variables );
	}






	
}
?>