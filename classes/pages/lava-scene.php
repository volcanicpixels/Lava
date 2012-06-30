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
			setting-general:
				[some genreal settings]
			setting-advanced:
				[some advanced settings]
			skin-default_skin:
				[some skin settings]
			extension-access_logs:
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

	public $_template_directories = array();
	public $_twig_config = array();
	public $_twig_template = 'default.twig';

	function _construct( $scene_controller, $scene_id, $scene_scope ) {
		$this->_scene_controller = $scene_controller;
		$this->_scene_id = $scene_id;
		$this->_scene_scope = $scene_scope;

		$this->_set_return_object( $scene_controller );

		$this->_template_directories = array(
			$this->_get_lava_path() . '/templates/default/scenes/',
			$this->_get_lava_path() . '/templates/'
		);
	}

	function _serialize() {
		$old = parent::_serialize();
		$new = array(
			'scene_id'          => $this->_get_scene_id(),
			'scene_title'       => $this->_get_scene_title(),
			'scene_url'         => $this->_get_scene_url(),
			'is_selected' => $this->_is_selected()
		);

		return array_merge( $old, $new );
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

	function _get_scene_template() {
		return 'scenes/' . $this->_scene_template . '.twig';
	}

	function _set_scene_title( $title = '' ) {
		if( ! empty( $title ) ) {
			$this->_remember( '_scene_title', $title );
		}
		return $this->_r();
	}

	function _is_selected() {
		return array_key_exists( 'scene', $_REQUEST ) and $this->_get_scene_id() == $_REQUEST['scene'];
	}

	/*
		Hook functions
	*/

	function _get_hook_identifier() {
		return '-scene:' . $this->_scene_controller->_get_page_slug() . '-' . $this->_get_scene_id();
	}

	/*
		Flow functions
	*/

	function _do_scene() {
		$this->_initialize_twig();

		$template = $this->_load_template();
		$variables = $this->_get_template_variables( $this->_serialize() );
		return $template->display( $variables );
	}

	/*
		Template functions
	*/

}
?>