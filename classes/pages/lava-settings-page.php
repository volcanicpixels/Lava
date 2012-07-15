<?php
/**
 * Settings_Page
 *
 * @package Lava
 * @subpackage Settings_Page
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Settings_Page extends Lava_Page
{

	public $_show_actionbar = true;


	function _register_dependancies(){
		parent::_register_dependancies();
	}


	function _load_defaults() {
		$this->_set_page_title( $this->__( 'Plugin Settings' ) );
	}

	function _register_scenes() {
		$this->_add_scene( 'save_form', 'save_form' );
	}

	function _do_page_load() {
		parent::_do_page_load();
		if( array_key_exists('action', $_REQUEST) ) {
			//form submitted
			$action = $_REQUEST['action'];

			if( 'save' == $action ) {
				$this->_do_save();
			}

			if( 'preview' == $action ) {
				$this->_do_preview();
			}
		}
	}

	function _do_save() {
		if( $this->_nonce( 'settings', $this->_get_page_nonce() ) ) {
			//nonce verified
			$this->_do_lava_action( '_do_save' );
			$redirect = add_query_arg( 'save', 'done' );
			$redirect = add_query_arg( 'request', $this->_request(), $redirect );
			wp_redirect( $redirect );
			die;
		} else {
			//queue nonce error
		}
	}

}
?>