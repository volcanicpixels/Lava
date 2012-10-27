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
		$default_thumbnail = $this->_get_plugin_url( '/assets/img/lava_skin_thumb_default.jpg' );
		foreach( $setting_options as $value => $setting_option ) {
			if( !array_key_exists('class', $setting_options[$value]['setting_input_attrs'])) {
				$setting_options[$value]['setting_input_attrs']['class'] = '';
			}
			$setting_options[$value]['setting_input_attrs']['class'] .= ' lava-setting-skin-radio';

			$thumbnail_url = $this->_skins()->_get_skin_url( $value, '/thumb.jpg' );
			$thumbnail_path = $this->_skins()->_get_skin_path( $value, '/thumb.jpg' );

			if( file_exists( $thumbnail_path ) ) {
				$setting_options[$value]['thumbnail_url'] = $thumbnail_url;
			} else {
				$setting_options[$value]['thumbnail_url'] = $default_thumbnail;
			}
			$meta = $this->_skins()->_get_extension_meta( $value );
			$setting_options[$value]['skin_name'] = $meta['title'];
		}
		return $setting_options;
	}

	
}
?>