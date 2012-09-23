<?php
/**
 * Skin Setting
 *
 * @package Lava
 * @subpackage Skin_Setting
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Setting_Skin extends Lava_Setting_Radio {
	function _get_setting_options() {
		$setting_options = parent::_get_setting_options();
		foreach( $setting_options as $value => $setting_option ) {
			if( !array_key_exists('class', $setting_options[$value]['setting_input_attrs'])) {
				$setting_options[$value]['setting_input_attrs']['class'] = '';
			}
			$setting_options[$value]['setting_input_attrs']['class'] .= ' lava-skin-setting-radio';
		}
		return $setting_options;
	}

	
}
?>