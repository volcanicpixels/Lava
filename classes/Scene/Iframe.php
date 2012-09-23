<?php
/**
 * @package Lava
 * @subpackage Iframe_Scene
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Scene_Iframe extends Lava_Scene
{
	public $_iframe_url;
	public $_iframe_classes = array(
		'lava-scene-inner-abs'
	);

	function _serialize() {
		$old = parent::_serialize();
		$iframe = array(
			'url' => $this->_get_iframe_url(),
			'width' => $this->_recall( '_iframe_width', '100%' ),
			'class' => implode(' ', $this->_iframe_classes) ,
			'height' => $this->_recall( '_iframe_height', '100%' ),
			'scrolling' => $this->_recall( '_iframe_scrolling', 'no' )
		);
		return array_merge( $old, array(
			'iframe' => $iframe
		) );
	}

	function _get_classes() {
		$classes = parent::_get_classes();
		$classes[] = 'js-height-adjust';
		return $classes;
	}

	function _set_iframe_url( $url ) {
		$this->_iframe_url = $url;
		return $this->_r();
	}

	function _get_iframe_url() {
		return $this->_iframe_url;
	}
}
?>