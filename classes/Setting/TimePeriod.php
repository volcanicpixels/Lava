<?php
class Lava_Setting_TimePeriod extends Lava_Setting_Text {
	function _serialize() {
		$old = parent::_serialize();
		$new = array(
			'formatted_value' => $this->_formatted_value(),
			'formats' => $this->_formats()
		);
		return array_merge($old, $new);
	}

	// takes the value and turns it into a more manageable number

	function _formatted_value() {
		$value = $this->_get_setting_value();
		$formats = $this->_formats();
		foreach( $formats as $format ) {
			if( $value % $format['seconds'] == 0) {
				return $value / $format['seconds'];
			}
		}
		return $value;
	}

	function _formats() {
		$value = $this->_get_setting_value();
		$formats = array(
			'week' => array(
				'seconds' => 60*60*24*7,
				'name'=> 'Weeks'
			),
			'day' => array(
				'seconds' => 60*60*24,
				'name'=> 'Days'
			),
			'hour' => array(
				'seconds' => 60*60,
				'name'=> 'Hours'
			),
			'minute' => array(
				'seconds' => 60,
				'name'=> 'Seconds'
			),
		);

		foreach($formats as $format_id => $format_info) {
			if($value % $format_info['seconds'] == 0) {
				$formats[$format_id]['selected'] = true;
				break;
			}
		}
		return $formats;
	}

	function _get_formatted_value($settings) {
		$formats = $this->_formats();
		$value = parent::_get_formatted_value($settings);
		$format = $this->_get_setting_meta($settings, 'format');
		return $value * $formats[$format]['seconds'];
	}
}
?>