<?php

require_once( dirname( __FILE__ ) . '/lava-base.php' );

/**
 * Plugin Class
 *
 * @package Lava
 * @subpackage Plugin
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Plugin extends Lava_Base
{
	public $_singletons = array();
	public $_plugin_name = 'Undefined plugin';
	public $_plugin_version = 1.0;
	public $_plugin_id = null;
	public $_plugin_class_prefix = null;
	public $_plugin_vendor;
	public $_load_vendor = true;
	public $_request_id;

	public $_should_register_action_methods = true;

	/**
	 * Constructor function called at initialization
	 *
	 * @access public
	 * @param __FILE__ $plugin_file_path
	 * @param string $plugin_name
	 * @param float $plugin_version
	 * @param boolean $load_vendor
	 * @return void
	 *
	 * @since 1.0.0
	 */
	function __construct( $plugin_file_path ) {
		$plugin_file_path = apply_filters( 'junction_fixer', $plugin_file_path );
		$this->_the_plugin = $this;
		$this->_plugin_file_path = $plugin_file_path;

		if( is_null( $this->_plugin_id ) )
			$this->_plugin_id = strtolower( str_replace( ' ', '_', $this->_plugin_name ) );

		if( is_null( $this->_plugin_class_prefix ) )
			$this->_plugin_class_prefix = get_class( $this );


		//Add the class autoloader
		spl_autoload_register( array( $this, '__autoload' ) );

		//initialise this class so that hooks are registered
		$this->_register_action_methods( $this );

		$this->_skins();

		if( $this->_load_vendor ) {
			require_once( dirname( $plugin_file_path ) .  '/vendor.php' );
			$class_name = $this->_plugin_class( 'Vendor' );
			$this->_plugin_vendor = $this->_construct_class( $class_name );
		}
	}

	function __call( $method_name, $args ) {
		return $this;
	}


	/**
	 * Defines what to do when a non-declared class is referenced
	 *
	 * @access public
	 * @param string $className
	 * @return void
	 *
	 * @since 1.0.0
	 */
	function __autoload( $class_name )
	{
		$file_name = strtolower( str_replace( '_' , '-', $class_name ) );
		$main_dirs = array(
			dirname( $this->_get_plugin_file_path() ) . '/classes',		//check plugin _classes folder and sub dirs
			dirname( __FILE__ )   //check lava _classes folder and sub dirs

		);

		$sub_dirs = array(
			'',
			'ajax',
			'extensions',
			'external',
			'pages',
			'scenes',
			'settings',
			'skins',
			'tables'
		);



		foreach( $main_dirs as $main_dir ) {
			foreach( $sub_dirs as $sub_dir ) {
				$file_path = "{$main_dir}/{$sub_dir}/{$file_name}.php";
				if( file_exists( $file_path ) and ! class_exists( $class_name ) ) {
					include_once( $file_path );
				}
			}
		}
	}

	/*
		Hook functions
	*/

	function _init() {
		if( is_admin() ) {
			$this->_do_lava_action( 'admin_init' );
		}
	}

	function _register_settings() {
		$settings = $this->_funcs()->_load_yaml( 'settings.yaml' );

		foreach( $settings as $setting_id => $setting_vars ){
			$setting_class = '';
			if( ! is_array( $setting_vars ) ) {
				$setting_vars = array();
			}
			if( array_key_exists( 'type', $setting_vars ) ) {
				$setting_class = $setting_vars[ 'type' ];
			}
			$this->_settings()
					->_add_setting( $setting_id, $setting_class )
						->_parse_vars( $setting_vars );
			;
		}
	}

	function _register_pages() {
		
		$pages = $this->_funcs()->_load_yaml( 'admin_pages.yaml' );

		foreach( $pages as $page_id => $page_vars ){
			$page_class = '';
			if( ! is_array( $page_vars ) ) {
				$page_vars = array();
			}
			$section_title = $this->_get_plugin_name();
			if( array_key_exists( 'type', $page_vars ) ) {
				$page_class = $page_vars[ 'type' ];
			}
			if( array_key_exists( 'section_title', $page_vars ) ) {
				$section_title = $page_vars[ 'section_title' ];
			}
			$section_id = strtolower( str_replace( ' ', '_', $section_title ) );
			if( array_key_exists( 'section', $page_vars ) ) {
				$section_id = $page_vars[ 'section' ];
			}
			$this->_pages()
					->_add_section( $section_title, $section_id )
					->_add_page( $page_class, $page_id, $section_id )
						->_parse_vars( $page_vars );
			;
		}
	}

	

	function _get_singleton( $class_name, $remove_child ) {
		if( array_key_exists( $class_name , $this->_singletons ) ) {
			return $this->_singletons[ $class_name ];
		} else {
			return $this->_singletons[ $class_name ] = $this->_construct_class( $class_name );
		}
	}

	function _namespace( $append = null ) {
		$namespace = $this->_get_plugin_id();
		if( ! is_null( $append ) ) {
			$namespace .= "_{$append}";
		}

		return $namespace;
	}

	function _get_plugin_class_prefix( $append = '' ) {
		if( empty( $append ) )
			return $this->_plugin_class_prefix;
		else
			return $this->_plugin_class_prefix . '_' . $append;
	}



	/**
	 * Accessor methods for plugin data
	 */

	function _get_plugin_file_path() {
		return $this->_plugin_file_path;
	}

	function _get_lava_path() {
		return dirname( dirname( __file__ ) );
	}

	function _get_customisations_file_path() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/' . $this->_get_plugin_id();
	}

	function _get_plugin_name() {
		return $this->_plugin_name;
	}

	function _get_plugin_id() {
		return strtolower( str_replace( ' ', '_', $this->_get_plugin_name() ) );
	}

	function _get_plugin_version() {
		return $this->_plugin_version;
	}

	function _get_plugin_vendor() {
		return $this->_plugin_vendor;
	}

	function _get_plugin_callbacks() {
		return $this->_plugin_callbacks;
	}


	/**
	 * Methods to access controller classes
	 */

	function _funcs( $kill_child = true ) {
		return $this->_functions( $kill_child );
	}

	function _functions( $kill_child = true ) {
		$class_name = $this->_lava_class('Functions');
		return $this->_get_singleton( $class_name, $kill_child );
	}

	function _pages( $kill_child = true ) {
		$class_name = $this->_lava_class('Pages');
		return $this->_get_singleton( $class_name, $kill_child );
	}

	function _settings( $kill_child = true ) {
		$class_name = $this->_lava_class('Settings');
		return $this->_get_singleton( $class_name, $kill_child );
	}

	function _skins( $kill_child = true ) {
		$class_name = $this->_lava_class("Skins");
		return $this->_get_singleton( $class_name, $kill_child );
	}

}
?>