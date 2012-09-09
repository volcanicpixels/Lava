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
			$file = dirname( $this->_get_plugin_file_path() ) . '/' . $file;
		}
		if( file_exists( $file ) ) {
			return Spyc::YAMLLoad( $file );
		}
		return array();
	}

	function _load_data( $file ) {
		$plugin_file = dirname( $this->_get_plugin_file_path() ) . '/data/' . $file . '.yaml';

		if( file_exists( $plugin_file ) ) {
			return $this->_load_yaml( 'data/' . $file . '.yaml' );
		} else {
			return $this->_load_yaml( 'lava/data/' . $file . '.yaml' );
		}
	}



	/*
		Manipulation
	*/

	

	


































	function lavaConstruct() {
		$this->addAutoMethods();
	}

	function adminInit() {
		$plugin_file = $this->pluginBasename();
		$screen = get_current_screen();
		$prefix = '';
		$this->addWpAction( "{$prefix}plugin_action_links_{$plugin_file}", "_addPluginLinks" );
		$prefix = 'network_admin_';
		$this->addWpAction( "{$prefix}plugin_action_links_{$plugin_file}", "_addPluginNetworkLinks" );
	}

	function current_context_url( $path )
	{
		if( is_multisite() and defined( 'WP_NETWORK_ADMIN' ) and WP_NETWORK_ADMIN == true )
		{
			return network_admin_url( $path );
		}
		return admin_url( $path );
	}

	function pluginBasename() {
		$file = apply_filters( "junction_link_fix", $this->_file() );

		return plugin_basename($file);
	}

	function addAutoMethods() {
		$objects = array(
			$this,
			$this->_this()->pluginCallbacks,
			$this->_ajax(),
			$this->_skins()
		);

		foreach( $objects as $object ) {
			$this->_addAutoMethods( $object );
		}
	}

	function _addAutoMethods( $object ) {
		$autoHooks = array(
			"init" => "init",
			"admin_init" => "adminInit"
		);
		foreach( $autoHooks as $hookTag => $actions ) {
				if( !is_array( $actions ) ) {
					$actions = array( $actions );
				}
				foreach( $actions as $action ) {
					if( method_exists( $object, $action ) ) {
						$callback = array( $object, $action );
						add_action( $hookTag, $callback );
					}
				}
			}
	}

	function _registerActions() {
		$hooks = array();

		foreach( $hooks as $hook ) {
			add_action( $hook, array( $this, $hook ) );
		}
	}

	function versionMatch( $ver1, $ver2 = null ) {
		if( is_null( $ver2 ) ) {
			$ver2 = $this->_version();
		}
		if( strpos( $ver2, "beta" ) ) {
			return false;//this is a beta plugin so we should assume run update hooks all the time
		}
		if( $ver1 == $ver2 ) {
			return false;
			return true;
		}
		return false;
	}

	function userAgentInfo() {
		$ua = $_SERVER['HTTP_USER_AGENT'];
		$info = array(
			'device' => 'pc',
			'os' => 'unknown',
			'browser' => 'unknown'
		);
		if( strpos( $ua, "iPad" ) ) {
			$info['device'] = "iPad";
			$info['os'] = 'ios';
			$info['browser'] = 'Mobile Safari';
			return $info;
		} else if( strpos( $ua, "iPod" ) ) {
			$info['device'] = "iPod";
			$info['os'] = 'ios';
			$info['browser'] = 'Mobile Safari';
			return $info;
		} else if( strpos( $ua, "iPhone" ) ) {
			$info['device'] = "iPhone";
			$info['os'] = 'ios';
			$info['browser'] = 'Mobile Safari';
			return $info;
		}

		//not an ios device
		 if( strpos( $ua, "Windows NT 6.2" ) ) {
			$info['os'] = 'Windows 8';
		 } else if( strpos( $ua, "Windows NT 6.1" ) ) {
			$info['os'] = 'Windows 7';
		 } else if( strpos( $ua, "Windows NT 6.0" ) ) {
			$info['os'] = 'Windows Vista';
		 } else if( strpos( $ua, "Windows NT 5.1" ) ) {
			$info['os'] = 'Windows XP';
		 } else if( strpos( $ua, "Macintosh" ) ) {
			$info['os'] = "OSX";
		 }
		 //do the browser
		 if( strpos( $ua, "Chrome" ) ) {
			$info['browser'] = 'Chrome';
		 } else if( strpos( $ua, "Safari" ) ) {
			$info['browser'] = 'Safari';
		 } else if( strpos( $ua, "MSIE" ) ) {
			$info['browser'] = 'Internet Explorer';
		 } else if( strpos( $ua, "Firefox" ) ) {
			$info['browser'] = 'Firefox';
		 } else if( strpos( $ua, "Opera" ) ) {
			$info['browser'] = 'Opera';
		 }
		 return $info;
	}

	/*
		Adds a link to the plugins page
	*/

	function addPluginLink( $linkText, $linkUrl, $network = false ) {
		$which_links = $network ? 'network_plugin_links' : 'plugin_links';
		$links = $this->lavaRecall( $which_links, array() );

		$links[] = array(
			"text" => $linkText,
			"url" => $linkUrl
		);

		$this->lavaRemember( $which_links, $links );

		return $this;
	}

	function addNetworkPluginLink( $linkText, $linkUrl ) {
		return $this->addPluginLink( $linkText, $linkUrl, true );
	}

	function _addPluginLinks( $links, $network = false ) {
		$which_links = $network ? 'network_plugin_links' : 'plugin_links';
		$the_links = $this->lavaRecall( $which_links, array() );

		foreach( $the_links as $link ) {
			$settings_link = '<a href="' . $link[ 'url' ] . '">' . $link[ 'text' ] . '</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}

	function _addNetworkPluginLinks( $links ) {
		return $this->_addPluginLinks( $links, true );
	}


}
?>