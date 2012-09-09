<?php
/**
 * @package Lava
 * @subpackage Get_Skin_Ajax_Handler
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Get_Skin_Settings_Ajax_Handler extends Lava_Ajax_Handler {
	function _do_ajax_data() {
		$skin_id = $_REQUEST['skin'];
		$this->_pages()->_get_page( 'skins' )->_add_scene( 'configure_skin' )->_set_skin_id( $skin_id );

		$scene = $this->_pages()->_get_page( 'skins' )->_get_scene_( 'configure_skin' );

		$scene_html = $scene->_do_scene();
		$scene_actions = implode( '', $scene->_do_scene_actions() );

		return array(
			'scene' => $scene_html,
			'actions' => $scene_actions,
			'hidden' => count( $scene->_settings ) == 0
		);
	}
}
?>