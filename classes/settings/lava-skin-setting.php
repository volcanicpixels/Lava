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

class Lava_Skin_Setting extends Lava_Radio_Setting {
	function _get_setting_classes() {
		$classes = parent::_get_setting_classes();
		$classes[] = 'lava-setting-skin';
		return $classes;
	}

	function _get_setting_options() {
		$setting_options = parent::_get_setting_options();
		foreach( $setting_options as $value => $setting_option ) {
			if( !array_key_exists('class', $setting_options[$value]['setting_attrs'])) {
				$setting_options[$value]['setting_attrs']['class'] = '';
			}
			$setting_options[$value]['setting_attrs']['class'] .= ' lava-setting-skin-radio';
		}
		return $setting_options;
	}

	
}
?>