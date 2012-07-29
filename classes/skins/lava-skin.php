<?php
/**
 * Skin
 *
 * @package Lava
 * @subpackage Skin
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Skin extends Lava_Extension
{
	public $_extension_namespace = 'skin';



	/*
		Accessors
	*/

	function _get_scripts() {
		$return = array();
		if( $this->_file_exists( 'scripts.js' ) ) {
			$return[] = $this->_get_url( 'scripts.js' );
		}
		return $return;
	}

	function _get_template_directories() {
		return array(
			$this->_get_extension_path() . '/templates'
		);
	}

	function _get_styles() {
		$return = array();
		if( $this->_file_exists( 'styles.css' ) ) {
			$return[] = $this->_get_url( 'styles.css' );
		}
		return $return;
	}

	function _get_skin_settings() {
		return $this->_get_extension_settings();
	}


	/*
		Hook Functions
	*/


	function _register_hooks() {
		parent::_register_hooks();
		$this->_register_filters( '_get_template_variables', array(
			'settings'
		));
	}

	function _do_save() {
		$key = $this->_namespace( $this->_get_setting_name_prefix( 'plural' ) );
		//print_r( $_REQUEST[$key] );echo $key;exit;
		parent::_do_save();
	}


	/*
		Template functions
	*/

	function _template_head() {
		
	}

	function _template_scripts() {
		$scripts = $this->_get_scripts();

		$return = '';

		foreach( $scripts as $script ) {
			$return .= "<script type='text/javascript' src='{$script}'></script>";
		}

		return $return;
	}

	function _template_styles() {
		$styles = $this->_get_styles();

		$return = '';

		foreach( $styles as $style ) {
			$return .= "<link rel='stylesheet' href='{$style}' />";
		}

		return $return;
	}

	function _template_widget( $widget_id ) {
		//renders part of the page
		if( $this->_widgets()->_widget_exists( $widget_id ) ) {
			return $this->_widgets()->_do_widget( $widget_id );
		}
	}

	function _template_widget_exists( $widget_id ) {
		return $this->_widgets()->_widget_exists( $widget_id );
	}

	function _get_template_variables__settings( $vars ) {
		$settings = $this->_get_option();
		return array_merge( $settings, $vars ); //a setting should not override another variable
	}
}
?>