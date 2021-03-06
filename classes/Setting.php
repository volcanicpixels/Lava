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
	public $_setting_scene = null;

	public $_template_directories = array();
	public $_twig_config = array();
	public $_twig_template;

	public $_should_register_action_methods = true;

	function _construct( $setting_controller, $setting_id ) {
		$this->_setting_controller = $setting_controller;
		$this->_setting_id = $setting_id;
		$this->_set_parent( $setting_controller );

		$this->_template_directories = array(
			$this->_get_plugin_dir() . '/templates/default/Setting/',
			$this->_get_plugin_dir() . '/templates/default/',
			$this->_get_plugin_dir() . '/templates/'
		);
	}

	function _serialize() {
		$old = parent::_serialize();
		$new = array(
			'full_setting_id' => $this->_get_full_setting_id(),
			'setting_id'      => $this->_get_setting_id(),
			'setting_title'   => $this->_get_setting_title(),
			'setting_type'    => $this->_get_setting_type(),
			'setting_value'   => $this->_get_setting_value(),
			'setting_name'    => $this->_get_setting_name(),
			'setting_name_prefix'    => $this->_get_setting_name_prefix(),
			'input_attrs'     => $this->_get_input_attrs(),
			'setting_input_attrs'   => $this->_get_setting_input_attrs(),
			'setting_classes' => $this->_get_setting_classes(),
			'setting_attrs' => $this->_get_setting_attrs()
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
		$vars = $this->_setting_controller->_parse_setting_vars( $vars );
		foreach( $vars as $key => $value ) {
			switch( $key ) {
				case 'ignore_rest':
					break 2;
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
				case 'page':
				case 'page_id':
					$this->_set_page_id( $value );
					break;
				case 'toggle':
					$this->_remember( '_setting_toggle', $value );
					break;
				case 'hidden':
					$this->_remember( '_setting_hidden', $value );
					break;
			}
		}
		return $vars;
	}

	function _get_setting_id() { //this is the local id to the controller - e.g. login_duration
		return $this->_setting_id;
	}

	function _get_full_setting_id() {// this is the fully qualified id e.g. settings-login_duration
		return $this->_setting_controller->_get_setting_id_prefix() . '-' . $this->_get_setting_id();
	}

	function _get_setting_class( $class = null ) {
		if( is_null( $class ) ) {
			$class = get_class( $this );
		}
		if( $class == 'Lava_Setting' or $class == 'Lava__Setting'  ) {
			$class =  'default';
		} else {
			$class = substr( $class, 5, -8 );
			$class = strtolower( $class );
			$class = str_replace( '_', '-', $class);
		}
		return $class;
	}

	function _get_setting_classes() {
		return $this->_get_classes();
	}

	function _get_setting_attrs() {
		return array(
			'data-setting-toggle' => $this->_recall( '_setting_toggle', $this->_get_full_setting_id() . '_enabled' )
		);
	}

	function _get_setting_name() {
		return $this->_namespace( $this->_setting_controller->_get_setting_name_prefix( 'plural' ) . "[{$this->_get_setting_id()}]" );
	}

	function _get_setting_name_prefix() {
		return $this->_namespace( $this->_setting_controller->_get_setting_name_prefix( 'plural' ) . "[{$this->_get_setting_id()}" );
	}

	function _get_setting_type() {
		return $this->_setting_type;
	}

	function _get_input_attrs() {
		$old = $this->_get_twig_context( 'input_attrs', array() );
		$new = array(
		);
		return array_merge( $old, $new );
	}

	function _get_setting_input_attrs() {
		$input_attrs = $this->_get_input_attrs();
		$old = $this->_get_twig_context( 'setting_input_attrs', array() );
		$new = array(
		);
		return array_merge( $input_attrs, $old, $new );
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
		return $this->_setting_controller->_get_value_for( $this->_get_setting_id(), $this->_get_setting_default_value() );
	}

	function _get_formatted_value($settings) {
		return $settings[$this->_get_setting_id()];
	}

	function _get_setting_meta($settings, $meta) {
		return $settings[$this->_get_setting_id() . '-' . $meta];
	}


	// this allows settings like time-periods to process input first
	function _set_setting_value_from_request( $settings ) {
		$value = $this->_get_formatted_value( $settings );
		$value = stripslashes( $value ); // http://snippi.com/s/9dl143f
		$this->_set_setting_value( $value );
	}

	function _set_setting_value( $value ) {
		$this->_setting_controller->_set_value_for( $this->_get_setting_id(), $value );
		return $this;
	}

	function _get_scene_id() {
		$scene_suffix = $this->_recall( '_scene_suffix', 'general' );
		return $this->_recall( '_scene_id', $this->_setting_controller->_get_scene_id_prefix() . '-' . $scene_suffix );
	}

	function _get_page_id( $default = 'settings' ) {
		return $this->_recall( '_page_id', $default );
	}

	function _set_page_id( $page_id ) {
		$this->_remember( '_page_id', $page_id );
		return $this;
	}

	/*
		Flow functions
	*/

	function _should_register_with_page() {

		if( $this->_recall( '_setting_hidden', false ) ) {
			return false;
		}

		return true;
	}

	function _register_setting_with_page( $page_ids = null, $scene_ids = null ) {

		if( !$this->_should_register_with_page() ) {
			return;
		}
		//add settings page
		//add relevant scene
		if( is_null( $scene_ids ) ) {
			$scene_ids = $this->_get_scene_id();
		}
		if( is_null( $page_ids ) ) {
			$page_ids  = $this->_get_page_id();
		}

		if( !is_array( $page_ids ) ) {
			$page_ids = array( $page_ids );
		}

		if( !is_array( $scene_ids ) ) {
			$scene_ids = array( $scene_ids );
		}


		foreach( $page_ids as $page_id ) {
			if( ! $this->_pages()->_page_exists( $page_id ) ) {
				$this->_pages()->_add_page( $page_id, $page_id ); //@todo Hide page if not registered at admin_menu
			}

			foreach( $scene_ids as $scene_id ) {
				$this->
					_pages()
						->_get_page( $page_id )
							->_add_scene( $scene_id, 'settings' )
								->_set_scene_title( $this->_recall( '_scene_title' ) )
								->_add_setting( $this )
				;

				$this->_pages()->_get_page( $page_id )->_get_scene( $scene_id );
			}
		}
	}

	function _do_setting( $context = null ) {
		$this->_set_twig_context( $context );

		$this->_initialize_twig();
		$template = $this->_load_template();
		$variables = $this->_get_template_variables( $this->_serialize() );
		return $template->display( $variables );
	}






	
}
?>