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
class Lava_Setting_Checkbox extends Lava_Setting {
	function _get_setting_input_attrs() {
		$old = parent::_get_setting_input_attrs();
		$new = array(
			'id' => $this->_get_setting_id() . '-checkbox'
		);
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