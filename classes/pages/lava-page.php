<?php
/**
 * Page
 *
 * @package Lava
 * @subpackage Page
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Page extends Lava_Base
{
	protected $_is_network_page = false;

	protected $_page_controller;
	protected $_page_id;
	protected $_section_id;

	protected $_page_scenes = array();
	protected $_local_scenes = array();
	protected $_external_scenes = array();

	public $_scene_types = array(

	);

	protected $_page_hook;

	public $_page_styles = array();
	public $_page_scripts = array();

	public $_template_directories = array();
	public $_twig_config = array();
	public $_twig_template = 'base.twig';

	function _construct( $page_controller, $page_id, $section_id ) {
		$this->_page_controller = $page_controller;
		$this->_page_id = $page_id;
		$this->_section_id = $section_id;

		$this->_template_directories = array(
			$this->_get_lava_path() . '/templates/default/',
			$this->_get_lava_path() . '/templates/'
		);
		$plugin_dir = dirname ( $this->_get_plugin_file_path() ) . '/templates/';
		if ( is_dir( $plugin_dir ) ) {
			array_unshift( $this->_template_directories, $plugin_dir );
		}
		$this->_set_return_object( $this );

		$this->_set_parent( $this->_page_controller );

		$this->_add_action( 'admin_menu', '_register_page', 3 );
		$this->_add_action( 'admin_menu', '_register_get_template_variables', 4 );

		$this->_add_lava_action( '_add_dependancies' );

		$this->_load_defaults();
	}

	function _get_hook_identifier() {
		return '-page:' . $this->_get_page_slug();
	}

	function _serialize() {
		$old_vars = parent::_serialize();
		$new_vars = array(
			'menu_title' => $this->_get_menu_title(),
			'page_title' => $this->_get_page_title(),
			'page_id'    => $this->_get_page_id(),
			'section_id' => $this->_get_section_id(),
			'url'        => $this->_get_page_url()
		);
		return array_merge( $old_vars, $new_vars );
	}



	/*
		Accessors
	*/

	function _load_defaults() {
		
	}

	function _parse_vars( $page_vars ) {
		foreach( $page_vars as $key => $value ) {
			switch( $key ) {
				case 'title':
					$this->_set_page_title( $value );
				break;
			}
		}
	}


	function _get_section_id() {
		return $this->_section_id;
	}

	function _get_page_id() {
		return $this->_page_id;
	}

	function _get_page_url() {
		$page_slug = $this->_get_page_slug();
		if( $this->_is_network_page and function_exists( 'network_admin_url' ) )
			return network_admin_url( "admin.php?page={$page_slug}" );
		else
			return admin_url( "admin.php?page={$page_slug}" );
	}

	function _get_page_title() {
		return $this->_recall( '_page_title', 'Undefined Page' );
	}

	function _set_page_title( $page_title ) {
		$this->_remember( '_page_title', $page_title );
		return $this->_r();
	}

	function _get_menu_title() {
		return $this->_recall( '_menu_title', $this->_get_page_title() );
	}

	function _set_menu_title( $menu_title ) {
		$this->_remember( '_menu_title', $menu_title );
		return $this->_r();
	}

	function _get_page_slug() {
		$section = $this->_page_controller->_get_section( $this->_get_section_id() );
		return  $section->_get_section_slug_fragment() . '_' . $this->_get_page_id();
	}

	/*
		Scene/act functions
	*/

	function _add_scene( $scene_id, $class = '' , $scope = 'local') {
		if( ! $this->_scene_exists( $scene_id ) ){
			$class_name = $this->_class( $class ) . '_Scene';
			$args = array(
				$this, // parent_page
				$scene_id,
				$scope
			);
			$this->_page_scenes[ $scene_id ] = $scene = $this->_construct_class( $class_name, $args );
			if( $scope == 'external' )
				$this->_external_scenes[] = $scene_id;
			else
				$this->_local_scenes[] = $scene_id;
		}
		return $this->_get_scene( $scene_id );
	}

	function _scene_exists( $scene_id ) {
		return array_key_exists( $scene_id, $this->_page_scenes);
	}

	function _get_scene( $scene_id ) {
		$this->_set_child( $this->_page_scenes[ $scene_id ] );
		return $this->_r();
	}

	function _get_scenes() {
		return $this->_page_scenes;
	}








	/*
		Flow functions
	*/

	function _register_scenes() {
		//should be overloaded to allow the registering of scenes
	}

	function _register_page() {

		$section = $this->_page_controller->_get_section( $this->_get_section_id() );
		$parent_slug = $section->_get_section_slug();
		$page_title = $this->_get_page_title();
		$menu_title = $this->_get_menu_title();
		$capability = 'manage_options'; # @todo add capability handling
		$menu_slug = $this->_get_page_slug();
		$function = array( $this, '_do_page' );


		$this->_page_hook = add_submenu_page(
			$parent_slug,
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function
		);

	}

	function _do_page() {
		$this->_funcs()->_load_dependancy( 'Twig_Autoloader' );
		$this->_initialize_twig();
		$template = $this->_load_template();
		$variables = $this->_get_template_variables();
		$template->display( $variables );
	}



	/* 
		Template functions
	*/

	function _get_template_directories() {
		return $this->_template_directories;
	}

	function _get_template_variables() {
		$hook = $this->_hook( '_get_template_variables' );
		return $this->_apply_lava_filters( $hook, array() );
	}

	function _initialize_twig() {
		$template_directories = $this->_get_template_directories();

		$this->_twig_loader = new Twig_Loader_Filesystem( $template_directories );
		$this->_twig_environment = new Twig_Environment( $this->_twig_loader, $this->_twig_config );
	}

	function _load_template( $template = null ) {
		if( is_null( $template ) ) {
			$template = $this->_twig_template;
		}
		return $this->_twig_environment->loadTemplate( $template );
	}

	/*#######################################
		Template variable functions
	*/#######################################

	function _register_get_template_variables() {
		$hook = $this->_hook( '_get_template_variables' );
		$filters = array(
			'plugin_meta',
			'pages',
			'scenes'
		);
		foreach( $filters as $filter ) {
			$this->_add_lava_filter( $hook, "_get_template_variables__{$filter}" );
		}
	}


	/*
		Exposes Plugin info to template
	*/
	function _get_template_variables__plugin_meta( $vars ) {
		$plugin = array(
			'id'      => $this->_get_plugin_id(),
			'name'    => $this->_get_plugin_name(),
			'version' => $this->_get_plugin_version()
		);
		$hook = $this->_hook( '_get_template_variables', '_plugin_meta' );
		$plugin = $this->_apply_lava_filters( $hook, $plugin );

		$vars[ 'plugin' ] = $plugin;

		return $vars;
	}


	/*
		Exposes array of pages to template
	*/
	function _get_template_variables__pages( $vars ) {
		$page_objects = $this->_page_controller->_get_pages_by_section( $this->_get_section_id() );
		$pages = array();
		$page_id = $this->_get_page_id();
		foreach( $page_objects as $page_object ) {
			$page = $page_object->_serialize();
			$page['link'] = $page['url'];
			if( $page['page_id'] == $page_id ) {
				$page['selected'] = true;
			} else {
				$page['selected'] = false;
			}
			$pages[] = $page;
		}
		$hook = $this->_hook( '_get_template_variables', '_pages' );
		$pages = $this->_apply_lava_filters( $hook, $pages );
		$vars['pages'] = $pages;
		return $vars;
	}

	function _get_template_variables__scenes( $vars ) {
		$scenes = $this->_get_scenes();
		// currently the whole array of objects is passed 
		$vars['scenes'] = $scenes;

		return $vars;
	}










	/* Page dependancy functions */

	function _add_dependancies() {
		$this->_use_lava_stylesheet( 'lava' );
		$this->_use_lava_script( 'html5shiv' );
		$this->_use_lava_script( 'lava' );
		$this->_use_lava_script( 'modernizr' );
	}



























	function enqueueIncludes()
	{
		foreach( $this->_pages()->styles as $name => $notNeeded )
		{
			wp_enqueue_style( $name );
		}
		foreach( $this->_pages()->scripts as $name => $notNeeded )
		{
			wp_enqueue_script( $name );
		}
	}

	function doPage()
	{
		$this->displayHeader();
		$this->displayNotifications();
		$this->displayPage();
		$this->displayFooter();
	}

	function displayHeader()
	{
		$pluginSlug = $this->_slug();
		$pluginName = $this->_name();
		$pluginVersion = $this->_version();

		$page_hook = $_GET['page'];
		$lavaPageClass = apply_filters( "admin_page_class-{$pluginSlug}", "" );
		$lavaPageClass = apply_filters( "admin_page_class-{$page_hook}", $lavaPageClass );

		?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-29306585-1']);
		  _gaq.push(['_setDomainName', 'example.com']);
		  _gaq.push(['_setAllowLinker', true]);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
		<div class="lava-full-screen-loader">
			<div class="lava-loader loading">
				<span class="child1"></span>
				<span class="child2"></span>
				<span class="child3"></span>
				<span class="child4"></span>
				<span class="child5"></span>
			</div>
		</div>
		<div class="wrap">
			<div class="lava-header" style="margin-bottom:10px;">
				<div id="icon-options-general" class="icon32"></div>
				<h2>
					<?php echo $pluginName; ?> <span class="version"><?php echo $pluginVersion; ?></span>
					<span class="lava-ajax-checks">
						<?php $this->runActions( "ajaxChecks" ); ?>
					</span>
				</h2>

			<!--.lava-header END-->
			</div>
			<div id="lava-nav" class="lava-nav bleed-left bleed-right with-padding lava-sticky-top clearfix">
				<div class="sticky-toggle tiptip" title="Toggle whether this bar should stick to the top of the screen."></div>
				<div class="left-grad"></div>
				<ul class="nav nav-horizontal clearfix">
					<?php foreach( $this->_pages( false )->adminPages() as $page ): ?>
				   <li class="clearfix <?php echo $page->get( "slug" ); ?> <?php if( $page_hook == $page->get( "slug" ) ){ echo "active"; } ?>"><a href="<?php echo $page->getUrl(); ?>"><?php echo $page->get( "title" ); ?></a></li>
				   <?php endforeach; ?>
				</ul>
				<?php $this->runActions( "lavaNav" ); ?>
			</div>
			<noscript>
				<div class="lava-message warning">
					<span class="message"><?php _e( "You don't have JavaScript enabled. Many features will not work without JavaScript.", $this->_framework()) ?></span>
				</div>
			</noscript>
			<?php $this->runActions( "pageHiddenStuff" ); ?>

			<div class="lava-content-cntr bleed-left bleed-right with-padding">
				<div class="lava-underground texture texture-woven bleed-left bleed-right with-padding underground-hidden" style="">
				<?php
					$this->runActions( "displayUnderground" );
					$this->displayUnderground();
				?>
				</div>
				<div class="lava-overground">
					<div class="torn-paper bleed-left bleed-right bleed-abs"></div>
					<div class="lava-btn-hide-underground underground-cancel-bar lava-btn lava-btn-block" style="display:none"><?php $this->cancelText() ?></div>
					<div class="content">
		<?php
	}

	function displayUnderground()
	{
		//sub classes should overload this method or rely on js to move things around (if they have to)
	}

	function displayFooter()
	{
		?>
					<!--.content END-->
					</div>
				<!--.lava-overground END-->
				</div>
				<?php $this->displayToolbar() ?>
			<!--.lava-content-cntr END-->
			</div>
		<!--.wrap END-->
		</div>
		<?php
	}

	function displayNotifications()
	{
		$notifications = array();
		if( isset( $_GET[ 'messagesnonce' ] ) )
		{
			$storedNotifications = get_option( "lavaNotifications" );

			if( is_array( $storedNotifications ) and isset( $storedNotifications[ $_GET[ 'messagesnonce' ] ] ) )
			{
				$storedNotifications = $storedNotifications[ $_GET[ 'messagesnonce' ] ];

				if( is_array( $storedNotifications ) )
				{
					foreach( $storedNotifications as $notification )
					{
						$notifications[] = $notification;
					}
				}
			}
		}
		$page_hook = $this->pageHook;
		$notifications = apply_filters( "lava_notifications-{$page_hook}", $notifications );

		foreach( $notifications as $notification )
		{
			?>
			<div class="lava-notification lava-notification-"><?php echo $notification['message'];?></div>
			<?php
		}
	}

	function displayPage()
	{
		?>
		<div class="lava-notification lava-notification-error"><?php _e( "It looks like this page has gone walk-abouts.", $this->_framework() ) ?></div>
		<?php
	}

	function displayToolbar()
	{
		?>
		<div class="lava-toolbar lava-sticky-bottom <?php echo $this->runFilters( "toolbarClass" ) ?>">
			<div class="inner">
				<?php $this->runActions( "toolbarButtons" ) ?>
			</div>
		</div>
		<?php
	}

	function dieWith( $message = "" ) {
		echo "$message";
		die;
	}

	function cancelText()
	{
		_e( "Cancel", $this->_framework() );
	}

	function hookTags()
	{
		$hooks = array(
			" ",
			"slug/{$this->slug}",
			"multisiteSupport/{$this->multisiteSupport}"
		);
		return $hooks;
	}
}
?>