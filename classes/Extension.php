<?php
/**
 * Extension
 *
 * @package Lava
 * @subpackage Extension
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Extension extends Lava_Setting_Controller {
	public $_extension_namespace = 'extension';
	public $_extension_path;
	public $_extension_id;
	public $_extension_slug;
	public $_extension_controller;


	public $_should_register_action_methods = true;
	public $_should_register_plugin_hooks = true;

	static function _init_extension( $class ) {
		{{ lava.class_namespace }}::_get_plugin()->_extensions()->_init_extension( $class );


	}

	function _construct( $extension_controller, $extension_path, $extension_id ) {
		parent::_construct();
		$this->_extension_id = $extension_id;
		$this->_extension_path = $extension_path;
		$this->_extension_controller = $extension_controller;
		$this->_extension_slug = strtolower( str_replace( '.', '_', $extension_id ) );
		$this->_controller_namespace_plural = $this->_controller_namespace = $this->_extension_namespace . '-' . $this->_get_extension_slug();
		$this->_register_dropins();
		$this->_init();
	}

	function _init() {
		
	}

	function _register_dropins() {

	}

	function _add_dropin( $dropin ) {
		$this->_extension_controller->_add_dropin( $dropin );
		return $this->_r();
	}

	function _get_extension_settings() {
		return $this->_get_settings();
	}
 
	function _get_extension_id() {
		return $this->_extension_id;
	}


	function _get_extension_dirname() {
		return $this->_extension_controller->_get_extension_dir( $this->_get_extension_id() );
	}

	function _get_extension_slug() {
		return $this->_extension_slug;
	}

	function _get_extension_name() {
		return $this->_get_extension_id();
	}

	function _get_extension_path( $append = '' ) {
		return $this->_extension_controller->_get_extension_path( $this->_get_extension_id(), $append );
	}

	function _get_extension_scene_id() {
		return $this->_extension_namespace . '-' . $this->_get_extension_slug();
	}

	function _get_extension_url( $append = '' ) {
		return $this->_extension_controller->_get_extension_url( $this->_get_extension_id(), $append );
	}

	/*
		Hook functions
	*/

	function _get_hook_identifier() {
		return '-' . $this->_extension_namespace . ':' . $this->_get_extension_slug();
	}


	function _extension_file_exists( $file ) {
		return file_exists( $this->_get_extension_path( $file ) );
	}

	/*
		Flow functions
	*/

	function _register_settings() {
		//should do inherited ones first
		//check whether a 'settings.yaml' file exists
		$settings = $this->_funcs()->_load_yaml( $this->_get_extension_path() . '/settings.yaml', true );
		$this->_parse_settings( $settings );
		return $this->_r();
	}

	function _parse_settings( $settings ) {
		$sections = array(
			'general' => array(
				'title' => $this->_get_extension_name()
			)
		);
		foreach( $settings as $setting_id => $setting_vars ){
			if( ! is_array( $setting_vars ) ) {
				$setting_vars = array();
			}
			if( array_key_exists( 'type', $setting_vars ) and $setting_vars['type'] == 'section' ) {
				$sections[$setting_id] = array(
					'title' => $this->_get_element( $setting_vars, 'title', $setting_id . ' settings' )
				);
				unset( $settings[ $setting_id ] );
			}
		}
		foreach( $settings as $setting_id => $setting_vars ){
			$setting_class = 'text';
			if( ! is_array( $setting_vars ) ) {
				$setting_vars = array();
			}
			if( array_key_exists( 'type', $setting_vars ) ) {
				$setting_class = $setting_vars[ 'type' ];
			}
			if( !array_key_exists( 'section', $setting_vars ) ) {
				$setting_vars['section'] = 'general';
			}

			if( array_key_exists( $setting_vars['section'], $sections) ) {
				$section = $sections[ $setting_vars['section'] ];
				$setting_vars['section_title'] = $section['title'];
			}
			$this
				->_add_setting( $setting_id, $setting_class )
					->_parse_vars( $setting_vars );
			;
		}
	}

	function _parse_setting_vars( $vars ) {
		$vars = parent::_parse_setting_vars( $vars );
		if( $this->_key_is_true( $vars, 'relative_file' ) ) {
			$vars['default'] = $this->_get_extension_dir() . $vars['default'];
		}

		if( $this->_key_is_true( $vars, 'get_url' ) ) {
			$vars['default'] = $this->_get_extension_url( $vars['default'] );
		}
		$extension_namespace = $this->_capitalize( $this->_extension_namespace );
		$defaults = array(
			'scene_title' => $this->_get_extension_name() . ' ' . $extension_namespace
		);
		return array_merge( $defaults, $vars );
	}

}
?>