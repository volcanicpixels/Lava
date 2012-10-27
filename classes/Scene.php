<?php
/**
 * Scene
 *
 * @package Lava
 * @subpackage Scene
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

/*
	Scenes are parts of pages. Different pages use them in different ways.

	Scenes are either: local, special, external
		When a page is loaded any special scenes that have been referenced (with &scene=scene_name) are printed first
		All local scenes are always pushed as part of the html
		External scenes are effectively just links

	e.g.

	Settings:
		All settings are pushed as local scenes with the scene of settings set by their origin or if plugin settings by the settings.yaml file

		e.g.
			setting-general:
				[some genreal settings]
			setting-advanced:
				[some advanced settings]
			skin-default_skin:
				[some skin settings]
			extension-access_logs:
				[access logs settings]

	Skins:
		Change Skin
		Default Skin Settings
		Editor
		Upload a skin
		Commission custom skin



*/
class Lava_Scene extends Lava_Base {
	public $_template_directories = array();
	public $_twig_template;
	public $_should_hide_scene = false;
	public $_scene_controller;
	public $_scene_context; //This allows multiple scenes to collaborate - ie multiple scenes sharing the same submission form
	public $_scene_context_action;
	public $_scene_form_id;

	function _construct( $scene_controller, $scene_id, $scene_scope ) {
		$this->_scene_controller = $scene_controller;
		$this->_scene_id = $scene_id;
		$this->_scene_scope = $scene_scope;
		if( is_null( $this->_scene_context ) ) {
			$action = '';
			if( ! is_null( $this->_scene_context_action ) ) {
				$action = '-' . $this->_scene_context_action;
			}
			$this->_scene_context = $scene_controller->_get_page_context() . $action;
		}
		
		$this->_set_return_object( $scene_controller );

		$this->_template_directories = array(
			$this->_get_plugin_dir() . '/templates/default/Scene/',
			$this->_get_plugin_dir() . '/templates/default/',
			$this->_get_plugin_dir() . '/templates/'
		);
	}

	function _serialize() {
		$old = parent::_serialize();
		$new = array(
			'scene_id'		 => $this->_get_scene_id(),
			'scene_title'	 => $this->_get_scene_title(),
			'scene_url'		 => $this->_get_scene_url(),
			'scene_context'	 => $this->_get_scene_context(),
			'scene_nonce'	 => $this->_get_scene_nonce(),
			'is_selected'    => $this->_is_selected(),
			'input_attrs'    => $this->_get_input_attrs(),
			'setting_input_attrs'	 => $this->_get_setting_input_attrs(),
			'classes'        => $this->_get_classes(),
			'attrs'        => $this->_get_attrs(),
			'scene_form_id'  => $this->_get_scene_form_id()
		);



		return array_merge( $old, $new );
	}


	/*
		Accessors
	*/

	function _get_scene_id() {
		return $this->_scene_id;
	}

	function _get_scene_class( $class = null ) {
		if( is_null( $class ) ) {
			$class = $this->_get_class();
		}
		if( $class == 'Lava_Scene' ) {
			$class =  'default';
		} else {
			$class = substr( $class, 5, -6 );
			$class = strtolower( $class );
			$class = str_replace( '_', '-', $class);
		}
		return $class;
	}

	function _get_scene_title() {
		return $this->_recall( '_scene_title', $this->_get_scene_id() );
	}

	function _get_scene_url() {
		$root_url = $this->_scene_controller->_get_page_url();
		return add_query_arg( 'scene', $this->_get_scene_id(), $root_url );
	}

	function _get_scene_context() {
		return $this->_scene_context;
	}

	function _set_scene_context( $scene_context ) {
		$this->_scene_context = $scene_context;
		return $this;
	}

	function _get_scene_nonce( $context = null ) {
		if( is_null( $context ) ) {
			$context = $this->_get_scene_context();
		}
		return wp_create_nonce( $this->_namespace( $context ) );
	}

	function _get_scene_template() {
		return 'scenes/' . $this->_twig_template . '.twig';
	}

	function _set_scene_title( $title = '' ) {
		if( ! empty( $title ) ) {
			$this->_remember( '_scene_title', $title );
		}
		return $this->_r();
	}

	function _is_selected() {
		return array_key_exists( 'scene', $_REQUEST ) and $this->_get_scene_id() == $_REQUEST['scene'];
	}

	function _get_input_attrs() {
		$old = $this->_get_twig_context( 'input_attrs', array() );
		$new = array();
		return array_merge( $old, $new );
	}

	function _get_setting_input_attrs() {
		$old = $this->_get_twig_context( 'setting_input_attrs', array() );
		$new = array();
		return array_merge( $old, $new );
	}

	function _get_attrs() {
		return array();
	}

	function _get_classes() {
		$classes = parent::_get_classes();

		if( $this->_should_hide_scene ) {
			$classes[] = 'hidden-descendant';
		}

		return $classes;
	}

	function _get_scene_form_id() {
		if( is_null( $this->_scene_form_id ) ) {
			return $this->_scene_controller->_get_default_scene_form_id();
		}
		return $this->_scene_form_id;
	}

	function _set_scene_form_id( $form_id = null ) {
		if( is_null( $form_id ) ) {
			$form_id = 'lava_save_form-' . $this->_get_scene_id();
		}
		$this->_scene_form_id = $form_id;
		return $this->_r();
	}



	/*
		Hook functions
	*/

	function _get_hook_identifier() {
		return '-scene:' . $this->_scene_controller->_get_page_slug() . '-' . $this->_get_scene_id();
	}

	/*
		Flow functions
	*/

	function _do_scene( $context = null ) {
		$this->_set_twig_context( $context );

		$this->_initialize_twig();

		$template = $this->_load_template();
		$variables = $this->_get_template_variables( $this->_serialize() );
		return $template->render( $variables );
	}

	function _do_scene_actions() {
		$buttons = array();
		$classes = $this->_get_lava_classes();
		$classes = array_reverse($classes);
		foreach( $classes as $class ) {
			if( substr_count( $class, 'Scene' ) ) {
				$class = str_replace( '-', '/', $class);
				$buttons = $this->_load_buttons( $class, $buttons );
			}
		}
		$buttons = $this->_load_buttons( $this->_get_scene_class(), $buttons );
		return $buttons;
	}

	function _load_buttons( $file, $buttons = array() ) {
		$base_buttons = $this->_funcs()->_load_data( 'buttons/base' );
		$new_buttons = $this->_funcs()->_load_data( "buttons/{$file}" );

		if( is_array( $new_buttons ) ) {
			foreach( $new_buttons as $button_id => $button_args  ) {
				$button_args = $this->_load_button( $button_args, $base_buttons );

				if( array_key_exists( $button_id, $base_buttons) ) {
					$button_args = array_merge( $this->_load_button( $base_buttons[$button_id] , $base_buttons), $button_args );
				}
				if( ! array_key_exists( 'class', $button_args ) ) {
					$button_args['class'] = 'default';
				}
				$buttons[$button_id] = $this->_do_button( $button_args['class'], $button_args );
			}
		}


		return $buttons;
	}

	function _load_button( $button_args, $base ) {
		if( ! is_array( $button_args ) ) {
			$button_args = array();
		}
		if( array_key_exists( 'extends', $button_args ) ) {
			$extends = explode( ',', $button_args['extends'] );
			foreach( $extends as $extension ) {
				$extension = trim( $extension );
				if( array_key_exists( $extension, $base) ) {
					$button_args = array_merge( $this->_load_button( $base[$extension], $base ), $button_args );
				}
			}
		}

		if( array_key_exists('extends', $button_args)) {
			unset( $button_args['extends'] );
		}

		

		if( array_key_exists('add_form', $button_args) and $button_args['add_form'] ) {
				if( ! array_key_exists('attrs', $button_args)) {
				$button_args['attrs'] = array();
			}
			$button_args['attrs']['form'] = $this->_get_scene_form_id();
		}

		return $button_args;
	}

	function _do_button( $type = 'default', $args = array() ) {
		$defaults = array();
		$args = array_merge_recursive($defaults, $args);
		$this->_initialize_twig();
		$template = $this->_load_template( 'Button/' . $type . '.twig' );
		return $template->render( $args );
	}

	/*
		_load_template functions
	*/

}
?>