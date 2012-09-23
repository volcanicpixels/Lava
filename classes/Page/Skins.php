<?php
/**
 * Skins_Page
 *
 * @package Lava
 * @subpackage Skins_Page
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Page_Skins extends Lava_Page_Settings
{
	public $_page_context = 'skins';

	function _register_admin_ajax_handlers() {
		$class = 'GetSkinSettings';
		$verb = "get_skin_settings";
		$this->_ajax()->_add_ajax_handler( $class, $verb );
	}

	function _register_scenes() {
		parent::_register_scenes();
		$this
			->_add_scene( 'Settings_Skins' )
				->_set_scene_title( $this->__( 'Choose Skin' ) )
			->_add_scene( 'Settings_Skin' )
		;
	}

}
?>