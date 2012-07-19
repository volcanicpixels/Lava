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

		$value = $this->_get_setting_value();

		switch ($value) {
			case 'off':
			case 'no':
			case 'false':
				//die($value);
				break;
			case 'on':
			case 'yes':
			case 'true':
			case true:
				$new['checked'] = 'checked';
				break;

		}

		$new['data-actual-value'] = $this->_get_setting_value();
		return array_merge( $old, $new );
	}

	function _get_setting_value() {
		$value = parent::_get_setting_value();

		if( $value === true ) {
			$value = 'on';
		} elseif( $value === false ) {
			$value = 'off';
		}

		return $value;
	}
}
?>