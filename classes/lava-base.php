<?php
/**
 * Base class that all classes extend
 *
 * @package Lava
 * @subpackage Base
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
if( ! class_exists('Lava_Base') ):
class Lava_Base
{
	protected $_the_plugin;
	protected $_memory = array();
	public $_suffixes = array( '/pre', '', '/post' );//@deprecated
	/*
		If a method is called that doesn't exist an error will be chucked out
	*/
	public $_should_throw_error_if_method_is_missing = true;
	/* If this is true then some methods will get auto called at the appropriate time */
	public $_should_register_action_methods = false;



	public $_twig_config = array(
		'debug' => true
	);

	/**
	 * Stores the plugin instance into a property so that chaining can be implemented.
	 *
	 * @magic
	 * @param lavaPlugin $the_plugin
	 * @param array $args
	 * @return void
	 *
	 * @since 1.0.0
	 */
	function __construct( $the_plugin, $args = array() )
	{
		$this->_the_plugin = $the_plugin;

		if( method_exists( $this, '_construct' ) )//call the sub classes construct method
		{
			$callback = array( $this, '_construct' );
			call_user_func_array( $callback, $args );
		}

		$this->_register_hooks();

		$this->_register_action_methods( $this );
	}

	/**
	 * This method implements chaining (allows lavaPlugin method calls to be called from any class)
	 *
	 * @magic
	 * @param string $method_name
	 * @param array $args
	 * @return void
	 *
	 * @since 1.0.0
	 */
	function __call( $method_name, $args = array() )
	{
		/* Lets see whether we have a child */
		if( $this->_has_child() ){
			//right, we have a child but does it have this method
				$child = $this->_get_child();
				if( method_exists( $child, $method_name ) ) {
					$callback = array( $child, $method_name );
					return call_user_func_array( $callback , $args);
				}
		}
		/* Lets see if we have any parents */
		if( $this->_has_parent() ){
			//right, we have a parent but does it have this method
				$parent = $this->_get_parent();
				if( method_exists( $parent, $method_name ) ) {
					$callback = array( $parent, $method_name );
					return call_user_func_array( $callback , $args);
				}
		}
		/* Lets check whether we have _parent_ methods */
		if( method_exists( $this, "_parent_{$method_name}") ) {
			$callback = array( $this, "_parent_{$method_name}" );
			return call_user_func_array( $callback, $args );
		}
		/* lets check whether we have a method with an extra underscore (used in templates) */

		if( method_exists( $this, "_{$method_name}") ) {
			$callback = array( $this, "_{$method_name}" );
			return call_user_func_array( $callback, $args );
		}

		/* Check plugin instance */
		if( method_exists( $this->_the_plugin, $method_name ) ) {
			$callback = array( $this->_the_plugin, $method_name );
			return call_user_func_array( $callback, $args );
		}

			/* We couldn't find anywhere to send this request */

		if( $this->_should_throw_error_if_method_is_missing ) {
			echo get_class( $this->_the_plugin );
			$callee = next(debug_backtrace());
			//Trigger appropriate error
			trigger_error('Could not find method ' . $method_name . ' in '.$callee['file'].' on line '.$callee['line'].'. The class is:' . get_class( $this ), E_USER_ERROR);


			echo "<h2>LavaError thrown in lavaBase.php</h2> <br/>";
			echo "Could not find method '{$method_name}' on object of class '" . get_class( $this ) . "'. We also tried the current child which has class '" . get_class( $this->_get_child() ) . "'and the parent which has class '" . get_class( $this->_get_parent() ) . "'.";
			exit;
		} else {
			return $this->_get_return_object();
		}
	}

	function _blank() {
		
	}

	function _register_hooks() {

	}

	/**
	 *  Translation
	 */

	function __( $string ) {
		$domain = 'lava_framework';
		return __( $string, $domain );
	}


	/**
	 * Functions for adding, removing and retrieving data from the class
	 *
	 */

	function _is_in_memory( $key ) {
		if( array_key_exists( $key, $this->_memory ) ) {
			return true;
		} else {
			return false;
		}
	}

	function _remember( $key, $value = null )
	{
		$this->_memory[ $key ] = $value;
		return $this;
	}

	function _recall( $key, $default = null ) {
		if( array_key_exists( $key, $this->_memory ) ) {
			return $this->_memory[ $key ];
		} else {
			return $default;
		}
	}

	function _forget( $key ) {
		if( array_key_exists( $key, $this->_memory ) ) {
			unset( $this->_memory[ $key ] );
		}
	}

	/**
	 * Methods for getting and setting the object that is returned if a method that doesn't exist is called.
	 */

	function _r( $kill_child = false ) {
		return $this->_get_return_object()->_this( $kill_child );
	}

	function _this( $kill_child = true ) {
		if( $kill_child )
			$this->_kill_child();
		return $this;
	}

	function _get_return_object() {
		return $this->_recall( '_return_object', $this );
	}

	function _set_return_object( $object = null ) {
		if( is_null( $object ) ) {
			$object = $this;
		}
		return $this->_remember( '_return_object', $object );
	}


	/**
	 * Accessor methods for family
	 */

	function _has_child() {
		return $this->_is_in_memory( '_child' );
	}

	function _get_child() {
		return $this->_recall( '_child' );
	}

	function _set_child( $child ) {
		return $this->_remember( '_child', $child );
	}

	function _kill_child() {
		return $this->_forget( '_child' );
	}


	function _has_parent() {
		return $this->_is_in_memory( '_parent' );
	}

	function _get_parent() {
		return $this->_recall( '_parent' );
	}

	function _set_parent( $parent ) {
		return $this->_remember( '_parent', $parent );
	}

	function _kill_parent() {
		return $this->_forget( '_parent' );
	}

	function _set_twig_context( $context ) {
		if( ! is_null( $context ) ) {
			$this->_remember( '_twig_context', $context );
		}
	}

	function _get_twig_context( $item = null, $default = array() ) {
		if( is_null( $item ) ) {
			return $this->_recall( '_twig_context', $default );
		} else {
			$context = $this->_recall( '_twig_context', $default );
			if( array_key_exists( $item, $context ) ) {
				return $context[ $item ];
			} else {
				return $default;
			}
		}

	}



	/**
	 * Registers methods with hook names (e.g. _adminInit() ) to be called when that hook is called
	 */

	function _register_action_methods( $ignore ) {
		if( $this->_should_register_action_methods ) {
			$this->_funcs()->_register_action_methods( $this );
		}
	}


	/**
	 * Filter and action methods
	 */


	/**
	 * If the hook name is the same as the method then the method parameter can be ommitted
	 */
	function _add_action( $hooks, $methods = '', $priority = 10, $how_many_args = 0, $should_namespace = false, $is_filter = false ) {
		$debug = false;
		if( !is_array( $hooks ) )
			$hooks = array( $hooks );

		if( !is_array( $methods ) )
			$methods = array( $methods );

		foreach( $hooks as $hook ) {

			foreach( $methods as $method ) {
				if( empty( $method ) )
					$method = $hook;

				if( $should_namespace ) {
					$hook = $this	->_namespace( $hook );
				}

				$callback = $method;

				if( ! is_array( $callback ) ) {
					$callback = array( $this, $callback );
				}
				if( is_callable( $callback ) ) {
					if( $is_filter )
						add_filter( $hook, $callback, $priority, $how_many_args );
					else
						add_action( $hook, $callback );
				}
			}
		}
		return $this;
	}



	function _add_filter( $hooks, $methods = '', $priority = 10, $how_many_args = 1, $should_namespace = false ) {
		return $this->_add_action( $hooks, $methods, $priority, $how_many_args, $should_namespace, true );
	}

	function _add_lava_action( $hooks, $methods = '', $priority = 10, $how_many_args = 0 ) {
		return $this->_add_action( $hooks, $methods, $priority, $how_many_args, true );
	}

	function _add_lava_filter( $hooks, $methods = '', $priority = 10, $how_many_args = 1 ) {
		return $this->_add_filter( $hooks, $methods, $priority, $how_many_args, true );
	}

	function _do_action( $hooks, $args = array(), $should_namespace = false ) {

		if( ! is_array( $hooks ) )
			$hooks = array( $hooks );

		foreach ( $hooks as $hook ) {

			if( $should_namespace ) {
				$hook = $this->_namespace( $hook );
			}
			$callback = "do_action";

			$_args = $args;
			array_unshift( $_args, $hook );

			call_user_func_array( $callback , $_args );
		}

		return $this;
	}

	function _do_lava_action( $hooks, $args = array() ) {
		return $this->_do_action( $hooks, $args, true );
	}

	function _apply_filters_( $hooks, $value, $args = array(), $should_namespace = false ) {
		if( ! is_array( $hooks ) )
			$hooks = array( $hooks );

		foreach ( $hooks as $hook ) {

			if( $should_namespace ) {
				$hook = $this->_namespace( $hook );
			}
			$callback = "apply_filters";

			$_args = $args;
			array_unshift( $_args, $value );
			array_unshift( $_args, $hook );

			$value = call_user_func_array( $callback , $_args );
		}

		return $value;
	}

	function _apply_filters( $hooks, $value = '' ) {
		$args = func_get_args();

		if( func_num_args() >= 2 ) {
			unset( $args[0] );
			unset( $args[1] );
		}

		return $this->_apply_filters_( $hooks, $value, $args, false );
	}

	function _apply_lava_filters( $hooks, $value = '' ) {
		$args = func_get_args();

		if( func_num_args() >= 2 ) {
			unset( $args[0] );
			unset( $args[1] );
		}

		return $this->_apply_filters_( $hooks, $value, $args, true );
	}

	function _hook() {
		$identifier = $this->_get_hook_identifier();
		$hooks = func_get_args();

		$hook = "{$identifier}";

		foreach( $hooks as $subhook ) {
			$hook = $hook . '/' . $subhook;
		}
		return $hook;
	}

	function _get_hook_identifier() {
		return '';
	}

	function _register_filters( $hook, $filters ) {
		$full_hook = $this->_hook( $hook );
		foreach( $filters as $filter ) {
			$this->_add_lava_filter( $full_hook, "{$hook}__{$filter}" );
		}
	}

	function _register_actions( $hook, $actions ) {
		$full_hook = $this->_hook( $hook );
		foreach( $actions as $action ) {
			$this->_add_lava_action( $full_hook, "{$hook}__{$action}" );
		}
	}

	function _serialize() {
		$vars = array(
			'_class_name' => get_class( $this )
		);

		return $vars;
	}

	/*
		Manipulation
	*/

	function _capitalize( $string ) {
		$string = explode( ' ', $string);
		foreach( $string as $i => $word ) {
			$string[$i] = ucfirst( $word );
		}
		return implode( ' ', $string );
	}

	function _class( $string ) {
		if( substr_count( $string, 'plugin/') == 1 ) {
			return $this->_plugin_class( str_replace( 'plugin/', '', $string) );
		} else {
			return $this->_lava_class( $string );
		}
	}

	function _lava_class( $string ) {
		return $this->_namespaced_class( $string, 'Lava' );
	}

	function _plugin_class( $string ) {
		return $this->_namespaced_class( $string, $this->_get_plugin_class_prefix() );
	}

	function _namespaced_class( $string, $namespace ) {
		$string = str_replace( '-', ' ', $string);
		$string = str_replace( '_', ' ', $string);
		$string = $this->_capitalize( $string );
		$string = str_replace( ' ', '_', $string);

		return $namespace . '_' . $string;
	}

	function _nonce( $action, $nonce = null ) {
		if( !is_null($nonce) ) {
			return wp_verify_nonce($nonce, $this->_namespace( $action ));
		} else {
			return wp_create_nonce( $this->_namespace( $action ) );
		}
	}

	function _request() {
		if( is_null( $this->_the_plugin->_request_id ) ) {
			$this->_the_plugin->_request_id = rand( 10000, 99999 );
		}

		return $this->_the_plugin->_request_id;
	}

	/*
		Constructors
	*/
	
	function _construct_class( $class_name, $args = array() ) {
		return new $class_name( $this->_the_plugin, $args );
	}

	/* 
		Template functions
	*/

	function _get_template_directories() {
		return $this->_template_directories;
	}

	function _get_template_variables( $vars = array() ) {
		$hook = $this->_hook( '_get_template_variables' );
		$vars = $this->_apply_lava_filters( $hook, $vars );
		$vars['context'] = $vars;
		return $vars;
	}

	function _initialize_twig() {
		if( !property_exists($this, '_twig_loader') ) {
			$this->_funcs()->_load_dependancy( 'Twig_Autoloader' );
			$template_directories = $this->_get_template_directories();

			$this->_twig_loader = new Twig_Loader_Filesystem( $template_directories );
			$this->_twig_environment = new Twig_Environment( $this->_twig_loader, $this->_twig_config );
			$this->_twig_environment->addExtension(new Twig_Extension_Debug());
		}
	}

	function _load_template( $template = null ) {
		if( is_null( $template ) ) {
			$template = $this->_twig_template;
		}
		return $this->_twig_environment->loadTemplate( $template );
	}


}

endif;
?>