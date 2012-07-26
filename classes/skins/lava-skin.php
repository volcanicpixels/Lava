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
	public $_skin_parent;
	public $_skin_has_parent = false;

	public $_extension_namespace = 'skin';



	function _construct( $skin_dir, $skin_id ) {
		parent::_construct( $skin_dir, $skin_id );
		$skin_ancestry = explode( '.', $skin_id);
		if( count( $skin_ancestry ) == 2 ) {
			$this->_skin_has_parent = true;
			$this->_skin_parent = $this->_skins()->_get_skin( $skin_ancestry[0] );
		}

		$this->_register_filters( '_get_template_variables', array(
			'settings'
		));
	}

	/*
		Accessors
	*/

	function _get_scripts() {
		$return = array();
		if( $this->_skin_has_parent ) {
			$return = $this->_skin_parent->_get_scripts();
		}
		if( $this->_file_exists( 'scripts.js' ) ) {
			$return[] = $this->_get_url( 'scripts.js' );
		}
		return $return;
	}

	function _get_styles() {
		$return = array();
		if( $this->_skin_has_parent ) {
			$return = $this->_skin_parent->_get_styles();
		}
		if( $this->_file_exists( 'styles.css' ) ) {
			$return[] = $this->_get_url( 'styles.css' );
		}
		return $return;
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
		if( $this->_skin_has_parent ) {
			$settings = array_merge( $this->_skin_parent->_get_template_variables__settings(), $settings );
		}
		return array_merge( $settings, $vars ); //a setting should not override another variable
	}
}
?>