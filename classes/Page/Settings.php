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
class Lava_Page_Settings extends Lava_Page
{

	public $_page_context = 'settings';

	function _register_dependancies(){
		parent::_register_dependancies();
	}

	function _register_admin_ajax_handlers() {
		$class = 'UploadImage';
		$verb = "upload_image";
		$this->_ajax()->_add_ajax_handler( $class, $verb );
	}


	function _load_defaults() {
		$this->_set_page_title( $this->__( 'Plugin Settings' ) );
	}

	function _register_scenes() {
		$this->_add_scene( 'SaveForm' );
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
		if( $this->_nonce(
				$this->_get_page_context(),
				$this->_get_page_nonce()
			)
		  ) {
			//nonce verified
			$this->_do_lava_action( '_do_save' );
			$redirect = add_query_arg( 'save', 'done' );
			$redirect = add_query_arg( 'request', $this->_request(), $redirect );
			wp_redirect( $redirect );
			die;
		} else {
			//queue nonce error
			die('none_error');
		}
	}

}
?>