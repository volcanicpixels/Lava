<?php
/**
 * @package Lava
 * @subpackage Extensions_Page
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Extensions_Page extends Lava_Settings_Page
{
	public $_page_context = 'extensions';

	function _register_scenes() {
		parent::_register_scenes();
		$this
			->_add_scene( 'enable_extensions' )
				->_set_scene_title( $this->__( 'Enable extensions' ) )
		;
	}

	function _load_defaults() {
		$this->_set_page_title( $this->__( 'Plugin Extensions' ) );
	}
}
?>