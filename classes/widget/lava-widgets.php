<?php
/**
 * Widgets
 *
 * @package Lava
 * @subpackage Widgets
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Widgets extends Lava_Base
{
	public $_widgets = array();


	function _add_widget( $widget_id, $widget_class = '' ) {
		if( !$this->_widget_exists( $widget_id ) ) {
			$class_name = $this->_class( $widget_class ) . '_Widget';
			$args = array(
				$widget_id
			);
			$this->_widgets[ $widget_id ] = $this->_construct_class( $class_name, $args );
		}
		return $this->_get_widget( $widget_id );
	}

	function _get_widget( $widget_id ) {
		$this->_set_child( $this->_widgets[ $widget_id ] );
		return $this->_r();
	}

	function _get_widget_( $widget_id ) {
		return $this->_widgets[ $widget_id ];
	}

	function _widget_exists( $widget_id ) {
		return array_key_exists( $widget_id, $this->_widgets );
	}

	function _do_widget( $widget_id ) {
		return $this->_get_widget_( $widget_id )->_do_widget();
	}

}

?>