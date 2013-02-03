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
class Lava_Base {
	protected $_the_plugin;
	protected $_memory = array();
	public $_suffixes = array( '/pre', '', '/post' );//@deprecated
	/*
		If a method is called that doesn't exist an error will be chucked out
	*/
	public $_should_throw_error_if_method_is_missing = true;
	/* If this is true then some methods will get auto called at the appropriate time */
	public $_should_register_action_methods = false;
	public $_should_register_plugin_hooks   = false;

	public $_twig_template;
	public $_template_directories = array();

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
	function __construct( $the_plugin = null, $args = array() )
	{

		if( is_null( $the_plugin ) ) {
			die( get_class( $this ) );
		}
		$this->_the_plugin = $the_plugin;

		if( method_exists( $this, '_construct' ) )//call the sub classes construct method
		{
			$callback = array( $this, '_construct' );
			call_user_func_array( $callback, $args );
		}

		$this->_load_defaults();
		$this->_register_hooks();

		$this->_register_action_methods( $this );
		$this->_register_plugin_methods();
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

		if( $this->_should_throw_error_if_method_is_missing or true ) {
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

	function _load_defaults() {
		
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

	function _register_plugin_methods( $override = false ) {
		if( $this->_should_register_plugin_hooks ) {
			if( $override ) { //this is being called from the plugin class
				$the_plugin = $this;
			} else {
				$the_plugin = $this->_the_plugin;
			}
			foreach ( $the_plugin->_plugin_actions as $hook ) {
				if( method_exists( $this, $hook ) ) {
					$this->_add_plugin_action( $hook );
				}
			}

			foreach ( $the_plugin->_plugin_filters as $hook ) {
				if( method_exists( $this, $hook ) ) {
					$this->_add_plugin_filter( $hook );
				}
			}
		}

		return $this->_r();
	}


	/**
	 * Filter and action methods
	 */


	/**
	 * If the hook name is the same as the method then the method parameter can be ommitted
	 */
	function _add_action( $hooks, $methods = '', $priority = 10, $how_many_args = 0, $should_namespace = false, $is_filter = false ) {
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
						add_action( $hook, $callback, $priority );
				}
			}
		}
		return $this;
	}

	function _remove_action( $hooks, $methods = '', $priority = 10, $how_many_args = 0, $should_namespace = false, $is_filter = false ) {
		if( !is_array( $hooks ) )
			$hooks = array( $hooks );

		if( !is_array( $methods ) )
			$methods = array( $methods );

		foreach( $hooks as $hook ) {

			foreach( $methods as $method ) {
				if( empty( $method ) )
					$method = $hook;

				if( $should_namespace ) {
					$hook = $this->_namespace( $hook );
				}

				$callback = $method;

				if( ! is_array( $callback ) ) {
					$callback = array( $this, $callback );
				}
				if( is_callable( $callback ) ) {
					if( $is_filter )
						remove_filter( $hook, $callback, $priority, $how_many_args );
					else
						remove_action( $hook, $callback, $priority );
				}
			}
		}
		return $this;
	}



	function _add_filter( $hooks, $methods = '', $priority = 10, $how_many_args = 1, $should_namespace = false ) {
		return $this->_add_action( $hooks, $methods, $priority, $how_many_args, $should_namespace, true );
	}

	function _add_plugin_action() {
		$args = func_get_args();
		return call_user_func_array( array( $this, '_add_lava_action' ), $args );
	}

	function _remove_plugin_action() {
		$args = func_get_args();
		return call_user_func_array( array( $this, '_remove_lava_action' ), $args );
	}

	function _add_lava_action( $hooks, $methods = '', $priority = 10, $how_many_args = 0 ) {
		return $this->_add_action( $hooks, $methods, $priority, $how_many_args, true );
	}

	function _remove_lava_action( $hooks, $methods = '', $priority = 10, $how_many_args = 0 ) {
		return $this->_remove_action( $hooks, $methods, $priority, $how_many_args, true );
	}

	function _add_plugin_filter() {
		$args = func_get_args();
		return call_user_func_array( array( $this, '_add_lava_filter' ), $args );
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

	function _do_plugin_action() {
		$args = func_get_args();
		return call_user_func_array( array( $this, '_do_lava_action' ), $args );
	}

	function _do_action_if( $action, $condition = null, $default = false, $should_terminate = false ) {
		if( is_null( $condition ) ) {
			$condition = $action;
		}

		if( $this->_apply_plugin_filters( 'is_' . $condition, $default ) ) {
			$this->_do_plugin_action( 'do_' . $action );
			if( $should_terminate ) {
				exit();
			}
			return true;
		}
		return false;
	}

	function _do_action_unless( $action, $condition = null, $default = true, $should_terminate = false ) {
		if( is_null( $condition ) ) {
			$condition = $action;
		}

		if( ! $this->_apply_plugin_filters( 'is_' . $condition, $default ) ) {
			$this->_do_plugin_action( 'do_' . $action );
			if( $should_terminate ) {
				exit();
			}
			return true;
		}
		return false;
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

	function _apply_plugin_filters() {
		$args = func_get_args();
		return call_user_func_array( array( $this, '_apply_lava_filters' ), $args );
	}

	function _hook() {
		$identifier = $this->_get_hook_identifier();
		$hooks = func_get_args();

		$hook = $this->_get_plugin_id() . "{$identifier}";

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
		if( !is_array( $filters )) {
			$filters = array( $filters );
		}
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

	function _path( $path ) {
		$path = str_replace( '\\', '/', $path );
		$path = str_replace( '//', '/', $path );
		return $path;
	}

	function _class( $append = null ) {
		$class_namespace = get_class( $this->_the_plugin );
		if( is_null( $append ) ) {
			return $class_namespace;
		} else {
			$append = str_replace( '_', ' ', $append );
			$append = $this->_capitalize( $append );
			$append = str_replace( ' ', '_', $append );
			return $class_namespace . "_" . $append;
		}
	}


	function _get_class() {
		return get_class( $this );
	}

	function _get_classes() {
		return $this->_get_lava_classes( 'cinder', true );
	}

	function _get_lava_classes( $prefix = '', $lowercase = false ) {
		if( !empty( $prefix ) ) {
			$prefix .= '-';
		}
		$classes = array();
		$current_class = get_class( $this );
		$base_class = $this->_class( 'Base' );
		$namespace = $this->_class();

		while( $current_class != $base_class ) {
			$lava_class = explode( '_', str_replace( $namespace . '_', '', $current_class) );
			if( $lowercase ) {
				$classes[] = strtolower( $prefix . implode( '-', $lava_class ) );
			} else {
				$classes[] = $prefix . implode( '-', $lava_class );
			}
			$current_class = get_parent_class( $current_class );
		}
		return $classes;
	}

	function _key_is_true( $arr, $prop ) {
		return (array_key_exists( $prop, $arr) and $arr[$prop]);
	}

	function _get_element( $array, $key, $default = '' ) {
		if( array_key_exists( $key, $array ) ) {
			return $array[$key];
		} else {
			return $default;
		}
	}

	function _request_var( $var, $default = false, $namespace = true ) {
		if( $namespace ) {
			$var = $this->_namespace( $var );
		}
		if( array_key_exists( $var, $_REQUEST ) ) {
			return $_REQUEST[$var];
		} else {
			return $default;
		}
	}

	function _nonce( $action, $nonce = null ) {
		if( !is_null($nonce) ) {
			return wp_verify_nonce($nonce, $this->_namespace( $action ));
		} else {
			return wp_create_nonce( $this->_namespace( $action ) );
		}
	}

	function _get_filepath( $append = '' ) {
		return dirname( dirname( __FILE__ ) ) . '/' . $append;
	}

	/*
	
	 fingerprint key stored as plugin property

	*/

	function _get_fingerprint_key() {
		if( is_null( $this->_the_plugin->_fingerprint_key ) ) {
			if( array_key_exists( $this->_namespace( 'fingerprint' ), $_COOKIE ) ) {
				$this->_the_plugin->_fingerprint_key = $_COOKIE[ $this->_namespace( 'fingerprint' ) ];
			} else {
				$key = '';
				$alpha = str_split( 'abcdefghijklmnopqrstuvwxyz0123456789', 1 );
				for( $i = 0; $i < 8; $i++ ) {
					$key .= $alpha[ rand(0,count($alpha) - 1) ];
				}
				$this->_the_plugin->_fingerprint_key = md5( $key );
			}
		}
		return $this->_the_plugin->_fingerprint_key;
	}

	function _set_fingerprint_cookie() {
		$this->_fingerprint_expiration();
		if( !headers_sent() ) {
			setcookie( $this->_namespace( 'fingerprint' ), $this->_get_fingerprint_key(), time() + 60*60*24*30*2, COOKIEPATH, COOKIE_DOMAIN );
		}
	}

	function _merge_fingerprint( $new ) {
		$old = $this->_get_fingerprint();
		return $this->_set_fingerprint( array_merge( $old, $new ) );
	}



	function _get_fingerprint( $expiration = '_expiration' ) {
		$fingerprint_db = get_option( $this->_namespace( 'fingerprint_db' ), array() );
		$fingerprint_key = $this->_get_fingerprint_key();

		//check whether fingerprint exists, and hasn't expired
		if(
			!array_key_exists( $fingerprint_key, $fingerprint_db ) or
			!array_key_exists( $expiration, $fingerprint_db[ $fingerprint_key ] ) or
			$fingerprint_db[ $fingerprint_key ][ $expiration ] <  current_time( 'timestamp' ) //expired
		  ) {
			return array();
		}
		return $fingerprint_db[ $fingerprint_key ];
	}

	function _set_fingerprint( $fingerprint ) {
		$default = array(
			'_expiration' =>  current_time( 'timestamp' ) + 60*60*24
		);
		$fingerprint = array_merge( $default, $fingerprint );
		$fingerprint_key = $this->_get_fingerprint_key();

		$expiration = $fingerprint['_expiration'];

		$fingerprint_expiration = get_option( $this->_namespace( 'fingerprint_expiration' ), array() );
		$fingerprint_db = get_option( $this->_namespace( 'fingerprint_db' ), array() );

		$fingerprint_expiration[$fingerprint_key] = $expiration;
		$fingerprint_db[ $fingerprint_key ] = $fingerprint;

		update_option( $this->_namespace( 'fingerprint_db' ), $fingerprint_db );
		update_option( $this->_namespace( 'fingerprint_expiration' ), $fingerprint_expiration );

		return $this->_r();
	}


	function _fingerprint_expiration() {
		if( rand(0,9) != 1 ) {
			return;
		}
		$fingerprint_key = $this->_get_fingerprint_key();
		$fingerprint_db = get_option( $this->_namespace( 'fingerprint_db' ), array() );
		$fingerprint_expiration = get_option( $this->_namespace( 'fingerprint_expiration' ), array() );
		sort( $fingerprint_expiration );
		$current_time = current_time( 'timestamp' );
		foreach( $fingerprint_expiration as $key => $expiration ) {
			if( $expiration < $current_time ) {
				unset( $fingerprint_expiration[$key] );
				if( array_key_exists( $key, $fingerprint_db) ) {
					unset( $fingerprint_db[$key] );
				}
			} else {
				break;
			}
		}

		
		update_option( $this->_namespace( 'fingerprint_db' ), $fingerprint_db );
		update_option( $this->_namespace( 'fingerprint_expiration' ), $fingerprint_expiration );
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
			if( !is_null( $this->_twig_template ) ) {
				$template = $this->_twig_template;
			} else {
				$classes = $this->_get_lava_classes();

				foreach( $classes as $class ) {
					$class = str_replace( '-', '/', $class);
					foreach( $this->_template_directories as $directory ) {
						if( file_exists( $directory . $class . '.twig' ) ) {
							$template = $class . '.twig';
							break 2;
						}
					}
				}

				if( is_null( $template ) ) {
					echo 'Could not find any template:';
					echo '<br/>Looked for:';
					foreach( $classes as $class ) {
					$class = str_replace( '-', '/', $class);
						echo '<br/>' . $class;
					}
					echo '<br/>in:';
					foreach( $this->_template_directories as $directory ) {
						echo '<br/>' . $directory;
					}
					die;
				}
			}
		}
		return $this->_twig_environment->loadTemplate( $template );
	}

	function _add_twig_function( $function, $plugin_call, $options = array() ) {
		$options = array_merge(array(
			'should_escape' => true
		), $options);
		$class = get_class( $this->_the_plugin );
		if( $options['should_escape']) {
			$plugin_call = $class . '::_get_plugin()' . $plugin_call;
		}
		$this->_twig_environment->addFunction( 
			$function,
			new Twig_Function_Function( $plugin_call, $options ) );
	}


}
?>