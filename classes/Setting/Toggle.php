<?php
/**
 * @package Lava
 * @subpackage Toggle_Setting
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Setting_Toggle extends Lava_Setting_Checkbox {
	function _get_setting_attrs() {
		$attrs = parent::_get_setting_attrs();
		if( array_key_exists( 'data-setting-toggle', $attrs ) ) {
			unset( $attrs['data-setting-toggle'] );
		}
		return $attrs;
	}

	function _get_setting_input_attrs() {
		$attrs = parent::_get_setting_input_attrs();
		$class = $this->_get_element( $attrs, 'class' );
		$class .= ' cinder-setting-toggle-input';
		$attrs['class'] = $class;
		return $attrs;
	}
}
?>