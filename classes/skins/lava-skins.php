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
class Lava_Skins extends Lava_Settings
{
	public $_skin_dirs = array();
	public $_skins = array();
	public $_controller_namespace = 'skin';


	function _construct() {
		parent::_construct();
	}

	function _get_active_skin_id() {
		return $this->_get_value_for( 'active_skin_id', 'default' );
	}

	function _init() {
		parent::_init();
		$args = array(
			'default' => 'default',
			'scene'   => 'choose_skin',
			'page'    => 'skins'
		);
		$this->_add_setting( 'active_skin_id', 'skin' )->_parse_vars( $args );
		$this->_register_skins();
	}

	function _admin_init() {
		parent::_admin_init();
	}

	function _register_skins() {
		if( !is_dir( dirname( $this->_get_plugin_file_path() ) . '/skins/default/' ) ) {
			return;
		}
		$plugin_skin_paths = glob( dirname( $this->_get_plugin_file_path() ) . '/skins/*', GLOB_ONLYDIR );
		$custom_skin_paths = glob( $this->_get_customisations_file_path() . '/skins/*', GLOB_ONLYDIR );

		foreach( $custom_skin_paths as $path ) {
			$this->_register_skin( $path, false );
		}

		foreach( $plugin_skin_paths as $path ) {
			$this->_register_skin( $path );
		}

		$this->_get_skin();
	}

	function _register_skin( $path, $plugin = true ) {
		//get skin_id
		$path = str_replace('\\', '/', $path);
		$path = str_replace('//', '/', $path);
		$skin_id = explode( '/', $path );
		$skin_id = end( $skin_id );
		//add option
		$this->_skin_dirs[ $skin_id ] = $path;
		$this->_get_setting( 'active_skin_id' )->_add_setting_option( $skin_id );
	}

	function _get_skin( $skin_id = null ) {
		if( is_null( $skin_id ) ) {
			$skin_id = $this->_get_active_skin_id();
		}
		if( array_key_exists( $skin_id, $this->_skins ) ) {
			return $this->_skins[ $skin_id ];
		} else {
			$args = array(
				$this->_skin_dirs[ $skin_id ],
				$skin_id
			);
			return $this->_skins[ $skin_id ] = $this->_construct_class( 'Lava_Skin', $args );
		}
	}

	function _get_template( $template ) {
		$active_skin_id = $this->_get_active_skin_id();
		$this->_template_directories = array();

		$try = array(
			$this->_get_customisations_file_path() . '/skins/' . $active_skin_id . '/templates/',
			dirname( $this->_get_plugin_file_path() ) . '/skins/' . $active_skin_id . '/templates/'
		);

		if( $active_skin_id != 'default' ) {
			$try[] = dirname( $this->_get_plugin_file_path() ) . '/skins/default/templates/'; //if the skin has vanished then fallback to the default skin 
		}

		foreach( $try as $path ) {
			if( is_dir( $path ) ) {
				$this->_template_directories[] = $path;
			}
		}
		$this->_initialize_twig();
		return $this->_load_template( $template . '.html' );
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
		$this->_add_twig_function( 'get_bloginfo', 'get_bloginfo', array('should_escape' => false));

	}

	

}
?>