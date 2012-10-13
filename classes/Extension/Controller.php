<?php
/**
 * @package Lava
 * @subpackage Extensions
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Extension_Controller extends Lava_Setting_Controller {
	public $_extensions = array();
	public $_extension_meta = array();
	public $_extension_paths = array();
	public $_controller_namespace = 'extension';




	function _parent__get_extension_dirname( $extension_id ) {
		$extension = explode( '.', $extension_id );
		unset( $extension[0] );
		return implode('.', $extension);
	}

	function _parent__get_extension_path( $extension_id, $append = '' ) {
		if( !array_key_exists( $extension_id, $this->_extension_paths ) ) {
			
			$extension_debris = explode( '.', $extension_id );
			$append_ = '/' . $this->_get_controller_namespace( true ) . '/' . $this->_get_extension_dirname( $extension_id );

			if( $extension_debris[0] == 'plugin' ) {
				$this->_extension_paths[ $extension_id ] = $this->_get_plugin_path( $append_ );
			} else {
				$this->_extension_paths[ $extension_id ] = $this->_get_customisations_path( $append_ );
			}
		}

		return $this->_extension_paths[ $extension_id ] . $append;

	}

	function _get_extension_url( $extension_id, $append = '' ) {
		$extension_id_debris = explode( '.', $extension_id );
		$append = '/' . $this->_get_controller_namespace( true ) . '/' . $this->_get_extension_dirname( $extension_id ) . $append;
		if( $extension_id_debris[0] == 'plugin' ) {
			return $this->_get_plugin_url( $append );
		} else {
			return $this->_get_customisations_url( $append );
		}
	}


	/*
		
	*/

	function _load_extensions() {
		//actually loads the extensions
		if( $this->_get_controller_namespace() == 'extension' and $this->_the_plugin->_load_vendor ) {
			$this->_load_extension( 'plugin.vendor' );
		}

		$extensions = $this->_get_option();


		foreach( $extensions as $extension_id => $active ) {
			if( $active == 'on' ) {
				$this->_load_extension( $extension_id );
			}
		}
	}

	function _get_extension( $extension_id ) {
		if( !array_key_exists( $extension_id, $this->_extensions ) ) {
			$this->_load_extension( $extension_id );
		}
		return $this->_extensions[ $extension_id ];
	}

	function _get_extension_meta( $extension_id, $filter = true ) {
		if( !array_key_exists( $extension_id, $this->_extension_meta ) ) {
			$meta_path = $this->_parent__get_extension_path( $extension_id, '/' . $this->_get_controller_namespace() . '.yaml' );
			$this->_extension_meta[ $extension_id ] = $this->_functions()->_load_yaml( $meta_path, true );
		}
		$meta = $this->_extension_meta[ $extension_id ];
		$default_meta = array(
			'name' 			=> $extension_id,
			'auto_enable'	=> 'off',
			'version'		=> '1.0.0',
			'description'	=> ''
		);
		if( $filter ) {
			$meta = array_merge( $default_meta, $meta );
			if( !array_key_exists( 'title', $meta ) ) {
				$meta['title'] = $meta['name'];
			}
		}
		return $meta;
	}

	function _load_extension( $extension_id ) {
		$path = $this->_get_extension_path( $extension_id );
		//check whether an extension.php file exists
		
		$class_name = $this->_class( $this->_get_controller_namespace() );
		$args = array(
			$this,
			$path,
			$extension_id
		);
		$this->_extensions[ $extension_id ] = $this->_construct_class( $class_name, $args );
	}

	function _register_extensions() {
		$append = '/' . $this->_get_controller_namespace( true ) . '/*';
		$plugin_extension_paths = glob( $this->_get_plugin_path( $append ), GLOB_ONLYDIR );
		$custom_extension_paths = glob( $this->_get_customisations_path( $append ), GLOB_ONLYDIR );

		foreach( $plugin_extension_paths as $path ) {
			if( file_exists( $path . '/' . $this->_get_controller_namespace() . '.yaml' ) ) {
				
				$path_debris = explode( '/', $this->_path( $path ) );
				$extension_id = 'plugin.' . end( $path_debris );
				//@todo - remove vendor from list
				$this->_extension_paths[ $extension_id ] = $path;
				if( $extension_id != 'plugin.vendor' ) {
					$this->_register_extension( $extension_id );
				}
				
			}
		}

		foreach( $custom_extension_paths as $path ) {
			$path = explode( '/', $this->_path( $path ) );
			$extension_id = 'custom.' . end( $path );
			$this->_register_extension( $extension_id );
		}
	}

	function _register_extension( $extension_id ) {
		$meta = $this->_get_extension_meta( $extension_id );
		$args = array(
			'title'   => $meta['title'],
			'default' => $meta['auto_enable'],
			'scene'   => 'Settings_Extensions',
			'page'    => 'extensions'
		);
		$this->_add_setting( $extension_id, 'extension' )->_parse_vars( $args );
	}




	

}
?>