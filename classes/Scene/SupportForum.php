<?php
/**
 * @package Lava
 * @subpackage Support_Forum_Scene
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Scene_SupportForum extends Lava_Scene_Iframe
{

	function _set_support_forum_url( $url ) {
		return $this->_set_iframe_url( $url );
	}

	function _get_support_forum_url() {
		return $this->_get_iframe_url();
	}
}
?>