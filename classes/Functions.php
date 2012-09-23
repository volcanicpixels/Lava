<?php
class Lava_Functions extends Lava_Base
{
	function _register_action_methods( $object ) {
		$hooks_with_same_method = array(
			'admin_menu',
			'admin_bar_menu',
			'get_header',
			'init',
		);

		$other_hooks = array(
			'init' => array(
				array(
					'method' => 'register_public_ajax_handlers',
					'priority' => 50
				),
				'register_widgets'
			)
		);

		$lava_hooks = array(
			'admin_init' => array(
				'admin_init',
				array(
					'method' => 'register_admin_ajax_handlers',
					'priority' => 50
				),
				'register_extensions',
				'register_settings',
				'register_skins',
				'register_pages'
			)
		);

		foreach( $hooks_with_same_method as $hook ) {
			if( method_exists( $object, "_{$hook}" ) ) {
				$callback = array( array( $object, "_{$hook}" ) );
				$this->_add_action( $hook, $callback );
			}
		}

		


		foreach( $other_hooks as $hook => $methods ) {
			if( ! is_array( $methods ) )
				$methods = array( $methods );
			foreach( $methods as $method ) {
				if( method_exists( $object, "_{$method}" ) ) {
					$callback = array( array( $object, "_{$method}" ) );
					$this->_add_action( $hook, $callback );
				}
			}
		}

		foreach( $lava_hooks as $hook => $methods ) {
			if( ! is_array( $methods ) )
				$methods = array( $methods );
			foreach( $methods as $method ) {
				if( is_array( $method ) ) {
					$method_array = $method;
					$priority = 10;
					$method = $method_array['method'];
					if( array_key_exists( 'priority', $method_array ) ) {
						$priority = $method_array['priority'];
					}
					if( method_exists( $object, "_{$method}" ) ) {
						$callback = array( array( $object, "_{$method}" ) );
						$this->_add_lava_action( $hook, $callback, $priority );
					}
				}else if( method_exists( $object, "_{$method}" ) ) {
					$callback = array( array( $object, "_{$method}" ) );
					$this->_add_lava_action( $hook, $callback );
				}
			}
		}
		if( method_exists( $object, "_register_widgets" ) ) {
			$object->_add_action( 'init', '_register_widgets', 5 );//very high priority as the widget may be used in init
		}
		if( method_exists( $object, "_load_extensions" ) ) {
			$object->_add_action( 'init', '_load_extensions', 5 );//very high priority as the extensions need to load before majority of init hooks
		}
	}

	

	function _load_dependancy( $dependancy ) {
		// allows for a more flexible dependancy loader where filenames do not correspond to class names
		$dependancies = array(
			'Twig_Autoloader' => dirname( __file__ ) . '/twig/Autoloader.php',
			'Spyc' => dirname( __file__ ) . '/spyc/spyc.php'
		);

		if( ! class_exists( $dependancy ) ) {
			if( array_key_exists( $dependancy, $dependancies) ) {
				require_once( $dependancies[ $dependancy ] );
			} else {
				die( 'Dependancy: "' . $dependancy . '" could not be loaded' );
			}
		}

		switch( $dependancy ) {
			case 'Twig_Autoloader':
				Twig_Autoloader::register();
		}
		// Raise Dependancy not found error
	}


	/*
		Language extensions
	*/

	function _make_associative( $array, $default_key ) {
		if( count( $array ) == 0 ) {
			return array();
		}
		if( ! $this->_is_associative( $array ) ) {
			return array( $default_key => $array );
		}
		return $array;
	}

	// http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-sequential
	function _is_associative( $arr ) {
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	function _load_yaml( $file, $absolute_path = false ) {
		$this->_load_dependancy( 'Spyc' );
		if( ! $absolute_path ) {
			$file = $this->_get_filepath( $file );
		}
		if( file_exists( $file ) ) {
			return Spyc::YAMLLoad( $file );
		}
		return array();
	}

	function _load_data( $file ) {
		return $this->_load_yaml( 'data/' . $file . '.yaml' );
	}



	/*
		Manipulation
	*/

	

	



}
?>