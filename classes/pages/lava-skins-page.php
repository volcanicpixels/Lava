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
	function _register_scenes() {
		$this->_add_scene( 'choose_skin', 'choose_skin' );
	}
}
?>