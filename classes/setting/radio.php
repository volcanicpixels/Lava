<?php
/**
 * Radio Setting
 *
 * @package Lava
 * @subpackage Radio_Setting
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Radio_Setting extends Lava_Setting {

	public $_setting_options = array();

	function _serialize() {
		$old = parent::_serialize();
		$new = array(
			'setting_options' => $this->_get_setting_options()
		);
		return array_merge( $old, $new );
	}

	function _get_radio_id( $value ) {
		return $this->_get_full_setting_id() . '-' . strtolower( str_replace( ' ', '_', $value ) );
	}

	function _get_setting_options() {
		$setting_options = $this->_setting_options;
		foreach( $setting_options as $value => $setting_option ) {
			$setting_options[$value]['setting_input_attrs']['id'] = 'lava_setting-' . $this->_get_radio_id( $value);
			$setting_options[$value]['radio_id'] = 'lava_setting-' . $this->_get_radio_id( $value );
		}
		$value = $this->_get_setting_value();
		$default = $this->_get_setting_default_value();
		if( ! $this->_setting_option_exists( $value ) ) {
			$value = $default;
			if( ! $this->_setting_option_exists( $value ) ) {
				$this->_add_setting_option( $value );
			}
		}
		$setting_options[$value]['setting_input_attrs']['checked'] = 'checked';
		return $setting_options;
	}

	function _add_setting_option( $value, $args = array() ) {
		$args['value'] = $value;
		if( ! array_key_exists( 'setting_input_attrs', $args ) ) {
			$args['setting_input_attrs'] = array();
		}
		$this->_setting_options[ $value ] = $args;
		return $this->_r();
	}

	function _setting_option_exists( $value ) {
		return array_key_exists( $value, $this->_setting_options );
	}

	function _set_setting_default_value( $value ) {
		$this->_add_setting_option( $value );
		return parent::_set_setting_default_value( $value );
	}
}
?>