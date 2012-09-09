<?php
/**
 * @package Lava
 * @subpackage Ajax
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Ajax_Handler extends Lava_Base {

	public $_ajax_handler_verb;

	function _construct( $verb = '' ) {
		if( !empty( $verb ) ) {
			$this->_ajax_handler_verb = $verb;
		}
	}

	function _get_ajax_handler_verb() {
		return $this->_ajax_handler_verb;
	}

	function _register_hooks() {
		$hook = 'wp_ajax_' . $this->_namespace( $this->_get_ajax_handler_verb() );
		$this->_add_action( $hook, '_do_ajax_request' );
	}

	function _do_ajax_request() {
		$data = $this->_do_ajax_data();
		echo json_encode( $data );
		exit;
	}

	function _do_ajax_data() {
		return $this->_get_ajax_handler_verb();
	}

}
?>