<?php
/**
 * @package Lava
 * @subpackage Configure_Skin_Scene
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Scene_Settings_Skin extends Lava_Scene_Settings
{
	public $_skin_id;

	function _load_defaults() {
		parent::_load_defaults();
		$this->_set_scene_title( $this->__( 'Configure Skin' ) );
	}

	function _set_skin_id( $skin_id ) {
		$this->_skin_id = $skin_id;
	}

	function _get_skin_id() {
		if( is_null( $this->_skin_id ) ) {
			return $this->_skins()->_get_active_skin_id();
		} else {
			return $this->_skin_id;
		}
	}

	function _get_settings() {
		if( $this->_recall( '_do_get_settings', true ) ) {
			$skin = $this->_skins()->_get_skin( $this->_get_skin_id() );
			$skin->_register_settings();
			$this->_settings = $skin->_get_settings();
			$this->_remember( '_do_get_settings', false );
		}
		return $this->_settings;
	}


	function _get_attrs() {
		$old = parent::_get_attrs();
		$new = array(
			'data-skin-id' => $this->_get_skin_id()
		);
		return array_merge( $old, $new);
	}


}
?>