<?php
/**
 * Scene
 *
 * @package Lava
 * @subpackage Scene
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

/*
	Scenes are parts of pages. Different pages use them in different ways.

	Scenes are either: local, special, external
		When a page is loaded any special scenes that have been referenced (with &scene=scene_name) are printed first
		All local scenes are always pushed as part of the html
		External scenes are effectively just links

	e.g.

	Settings:
		All settings are pushed as local scenes with the scene of settings set by their origin or if plugin settings by the settings.yaml file

		e.g.
			general:
				[some genreal settings]
			advanced:
				[some advanced settings]
			skin/default_skin:
				[some skin settings]
			extension/access_logs:
				[access logs settings]

	Skins:
		Change Skin
		Default Skin Settings
		Editor
		Upload a skin
		Commission custom skin



*/
class Lava_Scene extends Lava_Base
{

	function _construct( $scene_controller, $scene_id, $scene_scope ) {
		$this->_scene_controller = $scene_controller;
		$this->_scene_id = $scene_id;
		$this->_scene_scope = $scene_scope;

		$this->_set_return_object( $scene_controller );
	}

	function _serialize() {

	}


	/*
		Accessors
	*/

	function _get_scene_id() {
		return $this->_scene_id;
	}

	function _get_scene_title() {
		return $this->_recall( '_scene_title', $this->_get_scene_id() );
	}

	function _get_scene_url() {
		$root_url = $this->_scene_controller->_get_page_url();
		return add_query_arg( 'scene', $this->_get_scene_id(), $root_url );
	}

	function _set_scene_title( $title = '' ) {
		if( ! empty( $title ) ) {
			$this->_remember( '_scene_title', $title );
		}
		return $this->_r();
	}

	function _is_selected() {
		return $this->_get_scene_id() == $_REQUEST['scene'];
	}

}
?>