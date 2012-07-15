<?php
/**
 * Scene
 *
 * @package Lava
 * @subpackage Settings_Scene
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Save_Form_Scene extends Lava_Scene
{
	public $_twig_template = 'save_form.twig';
	public $_should_hide_scene = true;

	function _serialize() {
		$old = parent::_serialize();
		$new = array(
			'nonce' => $this->_get_nonce()
		);

		return array_merge($old, $new);
	}

	function _get_nonce() {
		return $this->_nonce( 'settings' );
	}
}
?>