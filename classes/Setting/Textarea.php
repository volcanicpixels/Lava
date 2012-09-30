<?php
/**
 * @package Lava
 * @subpackage Textarea_Setting
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Setting_Textarea extends Lava_Setting {
	function _get_setting_input_attrs() {
		$setting_attrs = parent::_get_setting_input_attrs();
		$class = $this->_get_element( $setting_attrs, 'class');
		$class .= ' actual-input';
		$setting_attrs['class'] = $class;
		return $setting_attrs;
	}
}
?>