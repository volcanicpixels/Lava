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
class Lava_Extension extends Lava_Settings {
	public $_extension_namespace = 'extension';
	public $_extension_dir;
	public $_extension_id;
	public $_extension_slug;


	public $_should_register_action_methods = true;

	function _construct( $extension_dir, $extension_id ) {
		parent::_construct();
		$this->_extension_id = $extension_id;
		$this->_extension_dir = $extension_dir;
		$this->_extension_slug = strtolower( str_replace( '.', '_', $extension_id ) );
		$this->_controller_namespace_plural = $this->_controller_namespace = $this->_extension_namespace . '-' . $this->_get_extension_slug();
	}

	function _get_extension_id() {
		return $this->_extension_id;
	}

	function _get_extension_slug() {
		return $this->_extension_slug;
	}

	function _get_extension_name() {
		return $this->_funcs()->_capitalize( $this->_get_extension_name() );
	}

	function _get_extension_dir() {
		return $this->_extension_dir;
	}

	function _get_extension_scene_id() {
		return $this->_extension_namespace . '-' . $this->_get_extension_slug();
	}

	function _get_url( $path ) {
		$path = '/' . $this->_extension_namespace . 's/' . $this->_get_extension_id() . '/' . $path;
		//@todo - must be a better way of determining whether this is a plugin skin or custom one
		if( substr_count( $this->_get_extension_dir(), 'plugins') ) {
			return $this->_get_plugin_url( $path );
		} else {
			return $this->_get_customisations_url( $path );
		}
	}

	/*
		Hook functions
	*/

	function _get_hook_identifier() {
		return '-' . $this->_extension_namespace . ':' . $this->_get_extension_slug();
	}


	function _file_exists( $file ) {
		return file_exists( $this->_get_extension_dir() . '/' . $file );
	}

	/*
		Flow functions
	*/

	function _register_settings() {
		//should do inherited ones first
		//check whether a 'settings.yaml' file exists
		$settings = $this->_funcs()->_load_yaml( $this->_get_extension_dir() . '/settings.yaml', true );
		$this->_parse_settings( $settings );
	}

	function _parse_settings( $settings ) {
		foreach( $settings as $setting_id => $setting_vars ){
			$setting_class = '';
			if( ! is_array( $setting_vars ) ) {
				$setting_vars = array();
			}
			if( array_key_exists( 'type', $setting_vars ) ) {
				$setting_class = $setting_vars[ 'type' ];
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
			$vars['default'] = $this->_get_url( $vars['default'] );
		}
		return $vars;
	}

}
?>