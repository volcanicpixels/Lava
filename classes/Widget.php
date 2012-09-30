<?php
/**
 * Widget
 *
 * @package Lava
 * @subpackage Widget
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Widget extends Lava_Base
{
	public $_widget_id;
	public $_twig_template;
	public $_template_directories = array();

	function _construct( $widget_id ) {
		$this->_widget_id = $widget_id;

		if( is_null( $this->_twig_template ) ) {
			$class = $this->_get_widget_class();
			$this->_twig_template = $class . '.twig';
		}

		$this->_template_directories = array(
			$this->_get_plugin_dir() . '/templates/default/widget/',
			$this->_get_plugin_dir() . '/templates/default/',
			$this->_get_plugin_dir() . '/templates/'
		);
	}

	function _get_widget_id() {
		return $this->_widget_id;
	}

	function _get_hook_identifier() {
		return '-widget:' . $this->_get_widget_id();
	}

	function _get_widget_class( $class = null ) {
		if( is_null( $class ) ) {
			$class = get_class( $this );
		}
		if( $class == 'Lava_Widget' ) {
			$class =  'default';
		} else {
			$class = substr( $class, 5, -7 );
			$class = strtolower( $class );
			$class = str_replace( '_', '-', $class);
		}
		return $class;
	}

	function _do_widget() {
		$this->_initialize_twig();
		$template = $this->_load_template();
		$variables = $this->_get_template_variables( $this->_serialize() );
		return $template->render( $variables );
	}
}

?>