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
class Lava_Skins_Page extends Lava_Settings_Page
{
	public $_page_context = 'skins';

	function _register_scenes() {
		parent::_register_scenes();
		$this
			->_add_scene( 'choose_skin' )
				->_set_scene_title( $this->__( 'Choose Skin' ) )
		;
	}
}
?>