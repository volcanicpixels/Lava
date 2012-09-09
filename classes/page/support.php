<?php
/**
 * Support_Page
 *
 * @package Lava
 * @subpackage Support_Page
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Support_Page extends Lava_Page
{
	public $_page_context = 'support';

	function _parse_vars( $vars ) {
		parent::_parse_vars( $vars );
		foreach( $vars as $var => $value ) {
			switch( $var ) {
				case 'support_forum_url':
					$this->_add_support_forum( $value );
					break;
			}
		}
	}

	function _register_scenes() {
		parent::_register_scenes();
	}

	function _load_defaults() {
		$this->_set_page_title( $this->__( 'Plugin Support' ) );
	}

	function _add_support_forum( $url ) {
		$this
			->_add_scene( 'support_forum' )
				->_set_scene_title( $this->__( 'Support forums' ) )
				->_set_support_forum_url( $url )
		;
	}
}
?>