<?php
/**
 * Settings
 *
 * @package Lava
 * @subpackage Skins
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Skin_Controller extends Lava_Extension_Controller
{
	public $_controller_namespace = 'skin';

	function _construct() {
		call_user_func_array( array( 'parent', '_construct' ), func_get_args() );
		$args = array(
			'default' => 'plugin.default',
			'scene'   => 'choose_skin',
			'page'    => 'skins'
		);
		$this->_add_setting( 'active_skin_id', 'skin' )->_parse_vars( $args );
	}


	function _get_active_skin_id() {
		return $this->_get_value_for( 'active_skin_id', 'plugin.default' );
	}

	function _get_active_skin_path() {
		return $this->_get_extension_path( $this->_get_active_skin_id() );
	}

	

	function _register_extension( $extension_id ) {
		$this->_get_setting( 'active_skin_id' )->_add_setting_option( $extension_id );
	}

	function _load_extensions() {
		$active_skin_id = $this->_get_active_skin_id();
		$this->_load_extension( $active_skin_id );
	}


	function _get_skin( $skin_id = null ) {
		if( is_null( $skin_id ) ) {
			$skin_id = $this->_get_active_skin_id();
		}
		return $this->_get_extension( $skin_id );
	}

	function _get_template( $template ) {
		$active_skin_id = $this->_get_active_skin_id();
		$this->_template_directories = $this->_get_skin()->_get_template_directories();
		$this->_initialize_twig();
		return $this->_load_template( $template . '.twig' );
	}

	function _display_template( $template, $vars = array() ) {
		$vars = $this->_get_skin()->_get_template_variables();
		$template = $this->_get_template( $template );
		return $template->display( $vars );
	}

	function _initialize_twig() {
		parent::_initialize_twig();
		$this->_add_twig_function( 'head', '->_skins()->_get_skin()->_template_head', array( 'is_safe' => array('html') ) );
		$this->_add_twig_function( 'styles', '->_skins()->_get_skin()->_template_styles', array( 'is_safe' => array('html') )  );
		$this->_add_twig_function( 'scripts', '->_skins()->_get_skin()->_template_scripts', array( 'is_safe' => array('html') ) );
		$this->_add_twig_function( 'widget', '->_skins()->_get_skin()->_template_widget', array( 'is_safe' => array('html') ) );
		$this->_add_twig_function( 'widget_exists', '->_skins()->_get_skin()->_template_widget_exists' );
		$this->_add_twig_function( 'translate', '->__' );
		$this->_add_twig_function( 'namespace', '->_namespace' );
		$this->_add_twig_function( 'get_bloginfo', 'get_bloginfo', array('should_escape' => false));

	}


	function _do_save() {
		parent::_do_save();
		$skin = $this->_get_active_skin_id();
		$this->_get_skin( $skin )->_register_settings()->_do_save();//@todo make bug report
	}


}
?>