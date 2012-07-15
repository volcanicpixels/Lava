<?php
/**
 * Scene
 *
 * @package Lava
 * @subpackage Settings_Scene
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Settings_Scene extends Lava_Scene
{
	public $_settings = array();
	public $_twig_template = 'settings.twig';

	function _register_hooks() {
		parent::_register_hooks();
		$this->_register_filters( '_get_template_variables', array('settings') );
	}


	function _add_setting( $setting ) {
		$this->_settings[ $setting->_get_full_setting_id() ] = $setting;
	}

	function _get_template_variables__settings( $vars ) {
		$vars['settings'] = $this->_settings;
		return $vars;
	}

	function _get_input_attrs() {
		$old = parent::_get_input_attrs();
		$new = array(
			'form' => 'lava_save_form'
		);
		return array_merge( $old, $new );
	}

	function _get_classes() {
		$old = parent::_get_classes();
		$new = array(
			'lava-settings-scene'
		);
		return array_merge( $old, $new );
	}


	/*
		Flow functions
	*/

	function _do_actions() {
		$buttons = parent::_do_actions();
		$args = array(
			'type' => 'submit',
			'text' => $this->__( 'Update Settings' ),
			'value' => 'save',
			'attrs' => array(
				'form' => 'lava_save_form',
				'name' => 'action'
			)
		);
		$buttons['save'] = $this->_do_button('primary', $args);
		$args = array(
			'type' => 'submit',
			'text' => $this->__( 'Preview' ),
			'value' => 'preview',
			'attrs' => array(
				'form' => 'lava_save_form',
				'name' => 'action'
			)
		);
		$buttons['save'] = $this->_do_button('default', $args);

		return $buttons;
	}


}
?>