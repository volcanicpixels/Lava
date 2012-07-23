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
class Lava_Pages extends Lava_Base
{
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

	function _add_page( $page_class, $page_id = null, $section_id = null ) {
		$this->_kill_child();
		if( is_null( $page_id ) ) {
			$page_id = $page_class;
		}

		// Sinces pages are actually sub pages we need a section to bind it to
		if( is_null( $section_id ) ){
			if( ! $this->_is_in_memory( '_section' ) ) //if there isn't a section in memory then we should create one using plugin meta
				$this->_add_section( $this->_get_plugin_name(), $this->_get_plugin_id() );

			$section_id = $this->_recall( '_section' );
		}

		$section = $this->_get_section( $section_id );

		$section->_set_default_page( $page_id, false );


		if( ! $this->_page_exists( $page_id ) ) {
			$class_name = $this->_class( $page_class ) . '_Page';

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
		$this->_add_lava_stylesheet( 'lava', 'lava.css' );
		$this->_add_lava_script( 'debug', 'ba-debug.min.js' );
		$this->_add_lava_script( 'history', 'history.js' );
		$this->_add_lava_script( 'modernizr', 'modernizr-2.5.3.js', array(), '2.5.3' );
		$this->_add_lava_script( 'modernizr', 'modernizr-2.6.1.js', array(), '2.6.1' );
		$this->_add_lava_script( 'selectivizr', 'selectivizr-min.js', array() );
		$this->_add_lava_script( 'lava', 'lava.js', array( 'jquery', $this->_namespace( 'debug' ), $this->_namespace( 'modernizr' ), $this->_namespace( 'selectivizr' ), $this->_namespace( 'history' ) ) );
		$this->_do_lava_action( '_add_dependancies' );
	}

	function _add_stylesheet( $handle, $src, $deps = array(), $ver = false, $media = false, $should_enqueue = true ) {
		$this->_add_stylesheet_( $handle, $src, $deps, $ver, $media, $should_enqueue );
		return $this->_r();
	}

	function _add_lava_stylesheet( $handle, $src, $deps = array(), $ver = false, $media = false, $should_enqueue = false ) {
		$this->_add_stylesheet_( $handle, $src, $deps, $ver, $media, $should_enqueue, true, 'lava/assets/' );
		return $this->_r();
	}

	function _add_plugin_stylesheet( $handle, $src, $deps = array(), $ver = false, $media = false, $should_enqueue = true ) {
		$this->_add_stylesheet_( $handle, $src, $deps, $ver, $media, $should_enqueue, true, 'assets/' );
		return $this->_r();
	}

	function _add_stylesheet_( $handle, $src, $deps, $ver, $media, $should_enqueue, $should_namespace = false, $asset_folder = false ) {
		if( $should_namespace ) {
			$handle = $this->_namespace( $handle );
		}

		if( $asset_folder ) {
			$src = plugins_url( $asset_folder . $src, $this->_get_plugin_file_path() );
		}
		$style = compact( 'handle', 'src', 'deps', 'ver', 'media', 'should_enqueue' );
		$this->_styles[ $handle ] = $style;
	}

	function _stylesheet_exists( $handle ) {
		return array_key_exists( $handle, $this->_styles );
	}

	function _use_stylesheet( $handle, $should_namespace = false ) {
		if( $should_namespace ) {
			$handle = $this->_namespace( $handle );
		}

		if( $this->_stylesheet_exists( $handle ) ) {
			$this->_styles[ $handle ]['should_enqueue'] = true;
		}
		return $this->_r();
	}

	function _use_lava_stylesheet( $handle ) {
		$this->_use_stylesheet( $handle, true );
		return $this->_r();
	}

	function _use_plugin_stylesheet( $handle ) {
		$this->_use_stylesheet( $handle, true );
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

	function _add_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false, $should_enqueue = true ) {
		$this->_add_script_( $handle, $src, $deps, $ver, $in_footer, $should_enqueue );
		return $this->_r();
	}

	function _add_lava_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false, $should_enqueue = false ) {
		$this->_add_script_( $handle, $src, $deps, $ver, $in_footer, $should_enqueue, true, 'lava/assets/js/' );
		return $this->_r();
	}

	function _add_plugin_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false, $should_enqueue = true ) {
		$this->_add_script_( $handle, $src, $deps, $ver, $in_footer, $should_enqueue, true, 'assets/js/' );
		return $this->_r();
	}

	function _add_script_( $handle, $src, $deps, $ver, $in_footer, $should_enqueue, $should_namespace = false, $asset_folder = false ) {
		if( $should_namespace ) {
			$handle = $this->_namespace( $handle );
		}

		if( $asset_folder ) {
			$src = plugins_url( $asset_folder . $src, $this->_get_plugin_file_path() );
		}
		$script = compact( 'handle', 'src', 'deps', 'ver', 'in_footer', 'should_enqueue' );
		$this->_scripts[ $handle ] = $script;
	}

	function _script_exists( $handle ) {
		return array_key_exists( $handle, $this->_scripts );
	}

	function _use_script( $handle, $should_namespace = false ) {
		if( $should_namespace ) {
			$handle = $this->_namespace( $handle );
		}

		if( $this->_script_exists( $handle ) ) {
			$this->_scripts[ $handle ]['should_enqueue'] = true;
		}
		return $this->_r();
	}

	function _use_lava_script( $handle ) {
		$this->_use_script( $handle, true );
		return $this->_r();
	}

	function _use_plugin_script( $handle ) {
		$this->_use_script( $handle, true );
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
		












	function addPageFromTemplate( $slug, $template )
	{
		return $this->addPage( $slug );
	}


	/**
	 * addAboutPage function.
	 *
	 * @access public
	 * @return void
	 */
	function addAboutPage( $slug = "about" )
	{
		$this   ->addPage( $slug, "About" )
					->setTitle( sprintf( __( "About %s", $this->_framework() ), $this->_name() ) );
		return $this;
	}

	/**
	 * addSettingsPage function.
	 *
	 * @access public
	 * @return void
	 */
	function addSettingsPage( $slug = "settings" )
	{
		$this   ->addPage( $slug, "Settings" )
					/* translators: This is the title of the settings page */
					->setTitle( __( "Plugin Settings", $this->_framework() ) )
		;

		$page = $this->fetchPage( $slug );

		$this	->_misc()
					->addPluginLink( __( 'Settings', $this->_framework() ), $page->getUrl() )
		;

		return $this;

		//add Link to plugin page


	}

	/**
	 * addSkinsPage function.
	 *
	 * @param string $slug (default: "skins") - to be appended to the plugin slug to make the url
	 * @return void
	 */
	function addSkinsPage( $slug = "skins" )
	{
		$this->_skins( false );

		$this   ->addPage( $slug, "Skins" )
					/* translators: This is the title of the settings page */
					->setTitle( __( "Skins", $this->_framework() ) )
		;

		return $this;
	}


	/**
	 * addTablePage function.
	 *
	 * @access public
	 * @param mixed $slug (default: "table") - to be appended to the plugin slug to make the url
	 * @return void
	 * @since 1.0.0
	 */
	function addTablePage( $slug = "table" )
	{
		$this   ->addPage( $slug, "Table" )
					->setTitle( __( "Table", $this->_framework() ) )
		;
		return $this;
	}





	/**
	 * defaultPage function.
	 *  Sets the currently chained page as the one to be displayed when the top-level page is clicked.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function defaultPage()
	{
		if( isset( $this->chain[ "current" ] ) )
		{
			$this->defaultPage = $this->chain[ "current" ];
		}

		return $this;
	}

	/**
	 * registerPages function.
	 *  Registers each of the admin pages
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function registerPages()
	{
		$defaultPage = $this->defaultPage;
		//register the main page
		add_menu_page( $defaultPage->get( "title" ),  $this->_name(), $defaultPage->get( "capability" ), $defaultPage->get( "slug" ), array( $defaultPage, "doPage" ) );

		$parentSlug = $defaultPage->get( "slug" );

		//register each foreacheh
		

	}

	/**
	 * registerNetworkPages function.
	 *  Registers each of the admin pages
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function registerNetworkPages()
	{
		$defaultPage = $this->defaultPage;
		//register the main page
		add_menu_page( $defaultPage->get( "title" ),  $this->_name(), $defaultPage->get( "capability" ), $defaultPage->get( "slug" ), array( $this, "blank" ) );

		$parentSlug = $defaultPage->get( "slug" );

		//register each subpage
		foreach( $this->adminPages as $page )
		{
			if( true === $page->multisiteSupport )//if they support multisite
			{
				$page->registerPage( $parentSlug );
			}
		}
	}





	function addStyle( $name, $path = "" )
	{
		$include = array(
			'path' => $path
		);

		$this->styles[ $name ] = $include;
		return $this;
	}

	function addScript( $name, $path = "", $dependencies = array() )
	{
		$include = array(
			'path' => $path,
			'dependencies' => $dependencies
		);

		$this->scripts[ $name ] = $include;
		return $this;
	}

	/**
	 * lavaPages::registerIncludes()
	 *
	 * @return void
	 */
	function registerIncludes()
	{
		foreach( $this->scripts as $name => $include )
		{
			$path		 = $include['path'];
			$dependencies = $include['dependencies'];

			if( !empty( $path ) )
			{
				if( strpos( $path, 'http' ) === false ) {
					$path = plugins_url( $path, $this->_file() );
				}
				wp_register_script( $name, $path, $dependencies );
			}
		}
		foreach( $this->styles as $name => $include )
		{
			$path = $include['path'];

			if( !empty( $path ) )
			{
				if( strpos( $path, "http" ) === false ) {
					$path = plugins_url( $path, $this->_file() );
				}
				wp_register_style( $name, $path );
			}
		}
	}




	function addCustomStyles()
	{
		$this->addStyle( $this->_slug( "Pluginstyles" ), "_static/styles.css" );
		return $this;
	}

	function addCustomScripts()
	{
		$this->addScript($this->_slug( "pluginScripts" ), "_static/scripts.js");
		return $this;
	}

}

?>