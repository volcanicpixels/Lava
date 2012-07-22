<?php
/**
 * Settings
 *
 * @package Lava
 * @subpackage Skins
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Skins extends Lava_Settings
{
	public $_skins = array();
	public $_controller_namespace = 'skin';

	function _construct() {
		parent::_construct();
	}

	function _get_active_skin_id() {
		return $this->_get_value_for( 'active_skin_id', 'default' );
	}

	function _admin_init() {
		parent::_admin_init();
		$args = array(
			'default' => 'default',
			'scene'   => 'choose_skin',
			'page'    => 'skins'
		);
		$this->_add_setting( 'active_skin_id', 'skin' )->_parse_vars( $args );
	}

	function _register_skins() {
		$plugin_skin_paths = glob( dirname( $this->_get_plugin_file_path() ) . '/skins/*', GLOB_ONLYDIR );
		$custom_skin_paths = glob( $this->_get_customisations_file_path() . '/skins/*', GLOB_ONLYDIR );

		foreach( $custom_skin_paths as $path ) {
			$this->_register_skin( $path, false );
		}

		foreach( $plugin_skin_paths as $path ) {
			$this->_register_skin( $path );
		}
	}

	function _register_skin( $path, $plugin = true ) {
		//get skin_id
		$path = str_replace('\\', '/', $path);
		$path = str_replace('//', '/', $path);
		$skin_id = explode( '/', $path );
		$skin_id = end( $skin_id );
		//add option
		$this->_get_setting( 'active_skin_id' )->_add_setting_option( $skin_id );
	}

}
?>