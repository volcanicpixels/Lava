<?php
/**
 * Checkbox Setting
 *
 * @package Lava
 * @subpackage Checkbox_Setting
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Checkbox_Setting extends Lava_Setting {
	public $_setting_type = 'Checkbox';

	public $_twig_template = 'checkbox.twig';

	function _get_setting_attrs() {
		$old = parent::_get_setting_attrs();
		$new = array();
		if( $this->_get_setting_value() === ( true or 'true' or 'on' or 'yes') ) {
			$new['checked'] = 'checked';
		}
		return array_merge( $old, $new );
	}
}
?>