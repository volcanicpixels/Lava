<?php

require_once( dirname( __FILE__ ) . '/base.php' );

/**
 * Plugin Class
 *
 * @package Lava
 * @subpackage Plugin
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Plugin extends Lava_Base {
	public $_singletons = array();
	public $_plugin_name = 'Undefined plugin';
	public $_plugin_version = 1.0;
	public $_plugin_id = null;
	public $_plugin_vendor;
	public $_load_vendor = true;
	public $_request_id;
	public $_fingerprint_key;

	public $_plugin_actions = array();
	public $_plugin_filters = array();

	public $_should_register_action_methods = true;
	public $_should_register_plugin_hooks = true;

	static $_plugin_instance;

	/**
	 * Constructor function called at initialization
	 */
	function __construct( $filepath ) {
		self::$_plugin_instance = $this;

		$this->_the_plugin = $this;
		$this->_plugin_filepath = $filepath;

		if( is_null( $this->_plugin_id ) )
			$this->_plugin_id = strtolower( str_replace( ' ', '_', $this->_plugin_name ) );


		//Add the class autoloader
		spl_autoload_register( array( $this, '__autoload' ) );

		//initialise this class so that hooks are registered
		$this->_register_action_methods( $this );
		$this->_register_plugin_methods( true );
		$this->_add_action( 'init', '_do_admin_init', 30 );

		// This ensures that the hooks are added in the right order
		$this->_ajax();
		$this->_settings();
		$this->_skins();
		$this->_extensions();

	}

	function __call( $method_name, $args ) {
		return $this;
	}


	/**
	 * Defines what to do when a non-declared class is referenced
	 * @since 1.0.0
	 */
	function __autoload( $class_name )
	{
		/*
			Logic:
				Check whether the class starts with the plugin prefix
				if it does then make it lower case and substitute underscores for slashes and include it
		*/

		if( substr_count( $class_name, $this->_class() ) > 0 ) {
			$filepath = str_replace( $this->_class(), '', $class_name );
			$filepath = strtolower( $filepath );
			$filepath = str_replace( '_', '/', $filepath );
			$filepath = '/classes' . $filepath . '.php';
			$filepath = $this->_get_plugin_path( $filepath );

			if( file_exists( $filepath ) ) {
				include_once( $filepath );
			}
		}
	}

	/*
		Hook functions
	*/

	function _testing() {
		die('testing');
	}

	function _do_admin_init() {
		if( is_admin() ) {
			$this->_do_lava_action( 'admin_init' );
		}
	}

	function _register_settings() {
		$settings = $this->_funcs()->_load_yaml( 'settings.yaml' );
		$sections = array(
			'general' => array(
				'General Settings'
			)
		);


		foreach( $settings as $setting_id => $setting_vars ){

			if( array_key_exists( 'type', $setting_vars ) and $setting_vars['type'] == 'section' ) {
				$sections[$setting_id] = array(
					'title' => $this->_get_element( $setting_vars, 'title', $setting_id . ' settings' )
				);
				unset( $settings[ $setting_id ] );
			}
		}

		foreach( $settings as $setting_id => $setting_vars ){
			$setting_class = '';
			if( ! is_array( $setting_vars ) ) {
				$setting_vars = array();
			}
			if( array_key_exists( 'type', $setting_vars ) ) {
				$setting_class = $setting_vars[ 'type' ];
			}

			if( !array_key_exists( 'section', $setting_vars ) ) {
				$setting_vars['section'] = 'general';
			}

			if( array_key_exists( $setting_vars['section'], $sections) ) {
				$section = $sections[ $setting_vars['section'] ];
				$setting_vars['section_title'] = $section['title'];
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
			exit('this is a test');
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


	/**
	 * Accessor methods for plugin data
	 */

	function _get_plugin_dir() {
		return dirname( dirname( __FILE__ ) );
	}

	function _get_plugin_filepath() {
		return $this->_plugin_filepath;
	}

	function _get_plugin_url( $append ) {
		return plugins_url( $append, $this->_get_plugin_filepath() );
	}

	function _get_plugin_path( $append = '' ) {
		return dirname( $this->_get_plugin_filepath() ) . $append;
	}

	function _get_customisations_path( $append = '' ) {
		return WP_CONTENT_DIR . '/' . $this->_get_plugin_id() . $append;
	}

	function _get_customisations_url( $append ) {
		return content_url( '/' . $this->_get_plugin_id() . $append );
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

	function _ajax( $kill_child = true ) {
		$class_name = $this->_class('Ajax_Controller');
		return $this->_get_singleton( $class_name, $kill_child );
	}

	function _extensions( $kill_child = true ) {
		$class_name = $this->_class('Extension_Controller');
		return $this->_get_singleton( $class_name, $kill_child );
	}

	function _funcs( $kill_child = true ) {
		return $this->_functions( $kill_child );
	}

	function _functions( $kill_child = true ) {
		$class_name = $this->_class('Functions');
		return $this->_get_singleton( $class_name, $kill_child );
	}

	function _pages( $kill_child = true ) {
		$class_name = $this->_class('Page_Controller');
		return $this->_get_singleton( $class_name, $kill_child );
	}

	function _settings( $kill_child = true ) {
		$class_name = $this->_class('Setting_Controller');
		return $this->_get_singleton( $class_name, $kill_child );
	}

	function _skins( $kill_child = true ) {
		$this->_funcs()->_load_dependancy( 'Twig_Autoloader' );
		$class_name = $this->_class("Skin_Controller");
		return $this->_get_singleton( $class_name, $kill_child );
	}

	function _widgets( $kill_child = true ) {
		$this->_funcs()->_load_dependancy( 'Twig_Autoloader' );
		$class_name = $this->_class("Widget_Controller");
		return $this->_get_singleton( $class_name, $kill_child );
	}



	static function _get_plugin() {
		return self::$_plugin_instance;
	}

}
?>