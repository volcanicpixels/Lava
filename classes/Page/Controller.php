<?php
/**
 * Pages
 *
 * @package Lava
 * @subpackage Pages
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Page_Controller extends Lava_Base {
	public $_admin_sections = array();
	//special sections are the WordPress sections - this allows us to add a page to one of these sections

	protected $_special_section_friendly_names = array(
		'themes' => 'themes.php',
		'tools' => 'tools.php',
		'management' => 'tools.php',
		'options' => 'options-general.php',
		'plugins' => 'plugins.php',
		'users' => 'users.php',
		'dashboard' => 'index.php',
		'posts' => 'edit.php',
		'media' => 'upload.php',
		'links' => 'link-manager.php',
		'pages' => 'edit.php?post_type=page',
		'comments' => 'edit-comments.php'
	);
	
	public $_admin_pages = array();
	public $_admin_pages_by_section = array();

	public $_styles = array();
	public $_scripts = array();


	function _construct() {
		$this->_add_action( 'admin_enqueue_scripts', '_add_dependancies', 1 );
		$this->_add_action( 'admin_enqueue_scripts', '_register_styles', 2 );
		$this->_add_action( 'admin_enqueue_scripts', '_register_scripts', 2 );
		$this->_add_action( 'admin_print_styles', '_enqueue_styles' );
		$this->_add_action( 'admin_print_styles', '_enqueue_scripts' );
	}

	
	

	/*
		A section is a top-level page
		All 'pages' are actually sub pages of 'sections'
		The framework was designed to require minimal work so a page can be defined without first defining a section (it will automatically create appropriate section)
	*/






	function _add_section(  $section_title = 'Undefined Section', $section_id = 'default' ) {

		if( $this->_section_exists( $section_id ) ) {
			return $this->_r();
		}

		$class_name = $this->_class("Section");

		$args = array(
			$this,
			$section_id,
			$section_title
		);
		$this->_admin_sections[ $section_id ] = $this->_construct_class( $class_name, $args );


		$this->_remember( '_section', $section_id );

		return $this->_r();
	}

	function _add_page( $page_id, $page_class, $section_id = null ) {
		$page_class = str_replace( "_", " ", $page_class );
		$page_class = $this->_capitalize( $page_class );
		$page_class = str_replace( " ", "_", $page_class );

		$this->_kill_child();

		// Sinces pages are actually sub pages we need a section to bind it to
		if( is_null( $section_id ) ){
			if( ! $this->_is_in_memory( '_section' ) ) //if there isn't a section in memory then we should create one using plugin meta
				$this->_add_section( $this->_get_plugin_name(), $this->_get_plugin_id() );

			$section_id = $this->_recall( '_section' );
		}

		$section = $this->_get_section( $section_id );

		$section->_set_default_page( $page_id, false );


		if( ! $this->_page_exists( $page_id ) ) {
			$class_name = $this->_class( 'Page_' . $page_class ) ;

			$args = array(
				$this, // $page_controller
				$page_id,
				$section_id
			);

			$the_page = $this->_admin_pages[ $page_id ] = $this->_construct_class( $class_name, $args );

			if( ! array_key_exists( $section_id, $this->_admin_pages_by_section ) ) {
				$this->_admin_pages_by_section[ $section_id ] = array();
			}

			$this->_admin_pages_by_section[ $section_id ][] = $page_id;
		}


		$this->_set_child( $this->_admin_pages[ $page_id ] );
		return $this->_r();
	}


	/*
		The difference between _get_page and _get_page_ is that the first adds the page object to memory and returns itself (like a jQuery chain) where as the second actually returns the object
	*/

	function _get_page( $page_id ) {
		$this->_kill_child();
		if( $this->_page_exists( $page_id ) )
			$this->_set_child( $this->_admin_pages[ $page_id ] );
		return $this->_r();
	}

	function _get_page_( $page_id ) {
		return $this->_admin_pages[ $page_id ];
	}

	function _page_exists( $page_id ) {
		if( array_key_exists( $page_id , $this->_admin_pages) )
			return true;
		else
			return false;
	}


	function _get_pages() {
		return $this->_admin_pages;
	}

	function _get_pages_by_section( $section_id ) {
		$pages = array();
		if( array_key_exists( $section_id , $this->_admin_pages_by_section ) ) {
			foreach( $this->_admin_pages_by_section[ $section_id ] as $page_id ) {
				$pages[$page_id] = $this->_get_page_( $page_id );
			}
		}
		return $pages;
	}

	function _get_sections() {
		return $this->_admin_sections;
	}

	function _section_exists( $section_id ) {
		if( array_key_exists( $section_id , $this->_admin_sections ) )
			return true;
		else
			return false;
	}

	function _get_section( $section_id ) {
		if( $this->_section_exists( $section_id ) ) {
			return $this->_admin_sections[ $section_id ];
		} else {
			//raise exception
			print_r( $this->_admin_sections );
			die( 'Could not find section with ID:' . $section_id );
		}
	}


	function _add_settings_page( $page_id = 'settings', $section_id = null ) {
		$this->_add_page( 'settings', $page_id, $section_id )
				->_set_page_title( $this->__( 'Plugin Settings') )
		;

		return $this->_r();
	}

	function _add_skins_page( $page_id = 'skins', $section_id = null ) {
		$this->_add_page( 'skins', $page_id,  $section_id )
				->_set_page_title( $this->__( 'Plugin Skin' ) )
		;

		return $this->_r();
	}








	/*
		Dependancies
	*/

	function _add_dependancies() {
		$this->_add_plugin_stylesheet( 'lava', 'lava.css' );
		$this->_add_plugin_script( 'debug', 'ba-debug.min.js' );
		$this->_add_plugin_script( 'history', 'history.js' );
		$this->_add_plugin_script( 'modernizr', 'modernizr-2.6.1.js', array(), '2.6.1' );
		$this->_add_plugin_script( 'selectivizr', 'selectivizr-min.js', array() );
		$this->_add_plugin_script( 'lava', 'lava.js', array( 'jquery', $this->_namespace( 'debug' ), $this->_namespace( 'modernizr' ), $this->_namespace( 'selectivizr' ), $this->_namespace( 'history' ) ) );
		$this->_do_lava_action( '_add_dependancies' );
	}

	function _add_plugin_stylesheet( $handle, $src, $deps = array(), $ver = false, $media = false, $should_enqueue = false ) {
		$src = $this->_get_plugin_url( '/assets/css/' . $src );
		$handle = $this->_namespace( $handle );
		return $this->_add_stylesheet( $handle, $src, $deps, $ver, $media, $should_enqueue );
	}

	function _add_stylesheet( $handle, $src, $deps = array(), $ver = false, $media = false, $should_enqueue = false ) {
		$style = compact( 'handle', 'src', 'deps', 'ver', 'media', 'should_enqueue' );
		$this->_styles[ $handle ] = $style;
		return $this->_r();
	}

	function _stylesheet_exists( $handle ) {
		return array_key_exists( $handle, $this->_styles );
	}

	function _use_plugin_stylesheet( $handle ) {
		$handle = $this->_namespace( $handle );
		return $this->_use_stylesheet( $handle );
	}

	function _use_stylesheet( $handle ) {
		if( $this->_stylesheet_exists( $handle ) ) {
			$this->_styles[ $handle ]['should_enqueue'] = true;
		}
		return $this->_r();
	}

	function _register_styles() {
		foreach( $this->_styles as $style ) {
			extract( $style );
			wp_register_style( $handle, $src, $deps, $ver, $media );
		}
	}

	function _enqueue_styles() {
		foreach( $this->_styles as $style ) {
			extract( $style );
			if( $should_enqueue ) {
				wp_enqueue_style( $handle );
			}
		}
	}

	function _add_plugin_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false, $should_enqueue = false ) {
		$src = $this->_get_plugin_url( '/assets/js/' . $src );
		$handle = $this->_namespace( $handle );
		return $this->_add_script( $handle, $src, $deps, $ver, $in_footer, $should_enqueue );
	}

	function _add_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false, $should_enqueue = false ) {
		$script = compact( 'handle', 'src', 'deps', 'ver', 'in_footer', 'should_enqueue' );
		$this->_scripts[ $handle ] = $script;
		return $this->_r();
	}

	function _script_exists( $handle ) {
		return array_key_exists( $handle, $this->_scripts );
	}

	function _use_plugin_script( $handle ) {
		$handle = $this->_namespace( $handle );
		return $this->_use_script( $handle );
	}

	function _use_script( $handle ) {
		if( $this->_script_exists( $handle ) ) {
			$this->_scripts[ $handle ]['should_enqueue'] = true;
		}
		return $this->_r();
	}

	function _register_scripts() {
		foreach( $this->_scripts as $script ) {
			extract( $script );
			wp_register_script( $handle, $src, $deps, $ver, $in_footer );
		}
	}

	function _enqueue_scripts() {
		foreach( $this->_scripts as $script ) {
			extract( $script );
			if( $should_enqueue ) {
				wp_enqueue_script( $handle );
			}
		}
	}

}

?>