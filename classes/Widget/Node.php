<?php
/**
 * Node_Widget
 *
 * @package Lava
 * @subpackage Node_Widget
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Node_Widget extends Lava_Widget
{
	public $_nodes = array();

	function _register_hooks() {
		$this->_register_filters( '_get_template_variables', 'nodes' );
	}

	function _get_template_variables__nodes($vars) {
		$vars['nodes'] = $this->_nodes;
		return $vars;
	}

	function _add_node( $node_id, $node_type, $node_args = array() ) {
		$default = array();
		$node_args['node_type'] = $node_type;
		switch( $node_type ) {
			case 'input':
				$default = array(
					'input_type' => 'text'
				);
		}
		$node_args = array_merge($default, $node_args);

		$this->_nodes[ $node_id ] = $node_args;
		return $this->_r();
	}

	function _add_input( $input_id, $args = array() ) {
		if( !array_key_exists( 'id', $args ) ) {
			$args['id'] = $input_id;
		}

		$arg_map = array(
			'id' => 'input_id',
			'name' => 'input_name',
			'type' => 'input_type',
			'label' => 'input_label',
			'attrs' => 'input_attrs'
		);


		foreach( $arg_map as $old => $new ) {
			if( array_key_exists($old, $args)) {
				$args[$new] = $args[$old];
				unset($args[$old]);
			}
		}

		return $this->_add_node( $input_id, 'input', $args );
	}

	function _add_password_input( $input_id = 'password', $args = array() ) {
		$default = array(
 			'name' => $this->_namespace( 'password' ),
 			'type' => 'password',
 			'label' => $this->__( 'Password' )
		);

		$args = array_merge($default, $args);

		return $this->_add_input( $input_id, $args );
	}
}

?>