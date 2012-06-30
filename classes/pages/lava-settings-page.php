<?php
/**
 * Settings_Page
 *
 * @package Lava
 * @subpackage Settings_Page
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Settings_Page extends Lava_Page
{

	function _register_dependancies(){
		parent::_register_dependancies();
	}


	function _load_defaults() {
		$this->_set_page_title( $this->__( 'Plugin Settings' ) );
	}

	function _register_scenes() {
	}

}
?>