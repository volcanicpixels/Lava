<?php
/**
 * @package Lava
 * @subpackage Extension_Setting
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Setting_Extension extends Lava_Setting_Checkbox {
	function _serialize() {
		$old = parent::_serialize();
		$meta = $this->_get_extension_meta();
		$new = array(
			'setting_description' => $meta['description']
		);
		return array_merge( $old, $new );
	}
	function _get_setting_input_attrs() {
		$old = parent::_get_setting_input_attrs();
		if( !array_key_exists( 'class', $old ) ) {
			$old['class'] = '';
		}
		$old['class'] .= ' cinder-setting-extension-checkbox';
		$new = array(
		);
		return array_merge( $old, $new );
	}

	function _get_extension_meta() {
		return $this->_extensions()->_get_extension_meta( $this->_get_setting_id() );
	}

	function _get_setting_classes() {
		$classes = parent::_get_setting_classes();
		$classes[] = 'cinder-setting-no-border';
		return $classes;
	}
}
?>