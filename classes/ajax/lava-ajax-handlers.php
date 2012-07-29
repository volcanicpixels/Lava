<?php
/**
 * @package Lava
 * @subpackage Ajax
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Ajax_Handlers extends Lava_Base {
	public $_ajax_handlers = array();

	function _register_public_ajax_handlers() {

	}

	function _register_admin_ajax_handlers() {

	}

	function _add_ajax_handler( $class, $verb = '' ) {
		if( !empty( $verb ) and array_key_exists( $verb, $this->_ajax_handlers ) ) {
			$this->_set_child( $this->_ajax_handlers[ $verb ] );
			return $this->_r();
		}
		$class = $this->_class( $class ) . '_Ajax_Handler';
		$args = array(
			$verb
		);
		$handler = $this->_construct_class( $class, $args );
		$verb = $handler->_get_ajax_handler_verb();
		$this->_ajax_handlers[$verb] = $handler;
		$this->_set_child( $this->_ajax_handlers[ $verb ] );
		return $this->_r();
	}
}
?>