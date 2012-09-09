<?php
/**
 * @package Lava
 * @subpackage Extensions
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Extensions extends Lava_Settings {

	public $_extensions = array();
	public $_controller_namespace = 'extension';




	function _parent__get_extension_dir( $extension_id ) {
		$extension = explode( '.', $extension_id );
		unset( $extension[0] );
		return implode('.', $extension);
	}

	function _parent__get_extension_path( $extension_id ) {
		$extension_debris = explode( '.', $extension_id );
		if( $extension_debris[0] == 'plugin' ) {
			$path = dirname( $this->_get_plugin_file_path() );
		} else {
			$path = $this->_get_customisations_file_path();
		}

		return $path . '/' . $this->_get_controller_namespace( true ) . '/' . $this->_get_extension_dir( $extension_id );
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

	function _load_extension( $extension_id ) {
		$path = $this->_get_extension_path( $extension_id );
		//check whether an extension.php file exists otherwise
		
		$class_name = $this->_class( $this->_get_controller_namespace() );
		$args = array(
			$this,
			$path,
			$extension_id
		);
		$this->_extensions[ $extension_id ] = $this->_construct_class( $class_name, $args );
	}

	function _register_extensions() {
		$plugin_extension_paths = glob( dirname( $this->_get_plugin_file_path() ) . '/' . $this->_get_controller_namespace( true ) . '/*', GLOB_ONLYDIR );
		$custom_extension_paths = glob( $this->_get_customisations_file_path() . '/' . $this->_get_controller_namespace( true ) . '/*', GLOB_ONLYDIR );

		foreach( $plugin_extension_paths as $path ) {
			$path = explode( '/', $this->_path( $path ) );
			$extension_id = 'plugin.' . end( $path );
			//@todo - remove vendor from list
			$this->_register_extension( $extension_id );
		}

		foreach( $custom_extension_paths as $path ) {
			$path = explode( '/', $this->_path( $path ) );
			$extension_id = 'custom.' . end( $path );
			$this->_register_extension( $extension_id );
		}
	}

	function _register_extension( $extension_id ) {
		$args = array(
			'title'   => $extension_id,
			'default' => 'off',
			'scene'   => 'enable_extensions',
			'page'    => 'extensions'
		);
		$this->_add_setting( $extension_id, 'extension' )->_parse_vars( $args );
	}




	

}
?>