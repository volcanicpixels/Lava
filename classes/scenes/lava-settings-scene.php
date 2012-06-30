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

	/*
		Flow functions
	*/


}
?>