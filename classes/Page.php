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
	public $_should_register_action_methods = true;

	public $_is_network_page = false;

	public $_page_controller;
	public $_page_id;
	public $_section_id;
	public $_page_context = 'page';

	public $_page_scenes = array();
	public $_local_scenes = array();
	public $_external_scenes = array();
	public $_default_scene_id;

	public $_scene_types = array(

	);

	public $_page_hook;

	public $_page_styles = array();
	public $_page_scripts = array();

	public $_template_directories = array();
	public $_twig_template = 'Base.twig';

	// Template manipulation

	public $_show_actionbar = true;

	function _construct( $page_controller, $page_id, $section_id ) {
		$this->_page_controller = $page_controller;
		$this->_page_id = $page_id;
		$this->_section_id = $section_id;

		$this->_template_directories = array(
			$this->_get_plugin_dir() . '/templates/default/',
			$this->_get_plugin_dir() . '/templates/'
		);
		$plugin_dir = dirname ( $this->_get_filepath() ) . '/templates/';
		if ( is_dir( $plugin_dir ) ) {
			array_unshift( $this->_template_directories, $plugin_dir );
		}
		$this->_set_return_object( $this );

		$this->_set_parent( $this->_page_controller );

		$this->_add_action( 'admin_menu', '_register_page', 3 );

		$this->_add_lava_action( '_add_dependancies' );

	}

	/*
		
	*/



	

	function _serialize() {
		$old_vars = parent::_serialize();
		$new_vars = array(
			'menu_title'		=> $this->_get_menu_title(),
			'page_title'		=> $this->_get_page_title(),
			'page_id'			=> $this->_get_page_id(),
			'page_context'		=> $this->_get_page_context(),
			'page_nonce'		=> $this->_get_page_nonce(),
			'section_id'		=> $this->_get_section_id(),
			'url'				=> $this->_get_page_url(),
			'show_actionbar'	=> $this->_show_actionbar,
			'ajaxurl'			=> admin_url('admin-ajax.php')
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

	function _get_page_context() {
		return $this->_page_context;
	}

	function _get_page_nonce( $context = null ) {
		if( is_null( $context ) ) {
			$context = $this->_get_page_context();
		}
		return wp_create_nonce( $this->_namespace( $context ) );
	}

	function _get_request_nonce( $context = null ) {
		if( is_null( $context ) ) {
			$context = $this->_get_page_context();
		}

		if( array_key_exists('nonce', $_REQUEST) ) {
			if( is_array( $_REQUEST['nonce'] ) and array_key_exists( $context, $_REQUEST['nonce']) ) {
				return $_REQUEST["nonce"][$context];
			}
		}

		return '';
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

	function _get_lava_vars() {
		return array(
			'plugin_namespace' => $this->_namespace(),
			'plugin_id'        => $this->_get_plugin_id(),
			'plugin_name'      => $this->_get_plugin_name(),
			'plugin_version'   => $this->_get_plugin_version()
		);
	}

	/*
		Hook functions
	*/

	function _register_hooks() {
		parent::_register_hooks();
		$this->_register_actions( '_do_page', array(
				'default_scene'
		));
		$this->_register_filters( '_get_template_variables', array(
			'plugin_meta',
			'pages',
			'scenes'
		));

		$this->_add_action( 'admin_menu', 'register_scenes', 2 );
	}

	function _get_hook_identifier() {
		return '-page:' . $this->_get_page_slug();
	}


	/*
		Scene/act functions
	*/

	function _add_scene( $scene_id, $class = null , $scope = 'local') {
		if( is_null( $class ) ) {
			$class = $scene_id;
		}

		if( ! $this->_scene_exists( $scene_id ) ){
			$class_name = $this->_class( 'Scene_' . $class );
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

	function _get_scene_( $scene_id ) {
		return  $this->_page_scenes[ $scene_id ];
	}

	function _get_default_scene_id() {
		$scenes = $this->_get_scenes();
		$default = '';
		if( count($scenes) > 0 ) {
			$scene = reset($scenes);
			if( ! $scene->_should_hide_scene ) {
				$default = $scene->_get_scene_id();
			} else {
				$i = 1;
				$count = count( $scenes );
				while( $i < $count ) {
					$scene = next( $scenes );
					if( ! $scene->_should_hide_scene ) {
						$default = $scene->_get_scene_id();
						break;
					}
					$i ++;
				}
			}
		}
		if( !empty( $this->_default_scene_id ) ) {
			$default = $this->_default_scene_id;
		}
		return $this->_recall( '_default_scene_id', $default );
	}

	function _get_default_scene_form_id() {
		return 'lava_save_form-' . $this->_get_page_id();
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

		$this->_register_pagehook_actions();

	}

	function _register_pagehook_actions() {
		$plugin_page = $this->_page_hook;
		$this->_add_action( "admin_print_styles-{$plugin_page}", '_register_dependancies', 1 );
		$this->_add_action( "load-{$plugin_page}", '_do_page_load' );
		$this->_add_action( "admin_head-{$plugin_page}", '_do_admin_head' );
	}

	// if the page is accessed without the 'scene' query param then we should add it

	function _do_page_load() {
	}

	function _do_admin_head() {
		$this->_do_lava_vars();
	}

	function _do_lava_vars() {
		$lava_vars = $this->_get_lava_vars();
		?>
		<script type="text/javascript">
		var cinderVars = <?php echo json_encode( $lava_vars ) ?>;
		</script>
		<?php
	}

	function _do_page() {
		$hook = $this->_hook( '_do_page' );
		$this->_do_lava_action( $hook );
		$this->_initialize_twig();
		$template = $this->_load_template();
		$variables = $this->_get_template_variables( $this->_serialize() );

		$template->display( $variables );
	}

	function _do_page__default_scene() {
		$default_scene_id = $this->_get_default_scene_id();
		if( ! array_key_exists('scene', $_REQUEST) ) {
			$_REQUEST['scene'] = $default_scene_id;
		}

	}



	

	/*#######################################
		Template variable functions
	*/#######################################




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

	function _register_dependancies() {
		$this->_page_controller->_use_plugin_stylesheet( 'lava' );
		$this->_page_controller->_use_plugin_script( 'lava' );
	}

	function _add_dependancies() {

	}



























	
}
?>