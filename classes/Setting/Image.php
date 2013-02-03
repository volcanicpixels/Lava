<?php
/**
 * Image Setting
 *
 * @package Lava
 * @subpackage Image_setting
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Setting_Image extends Lava_Setting {
	function _serialize() {
		$old = parent::_serialize();
		$action = $this->_namespace('upload_image');
		$nonce = $this->_nonce( 'ajax-upload_image' );
		$upload_name = $this->_get_upload_name();
		$new = array(
			'ajaxurl' => admin_url('admin-ajax.php') . "?action={$action}&nonce={$nonce}&upload_name=" . $upload_name,
			'upload_name' => $upload_name
		);
		return array_merge($old, $new);
	}

	function _get_upload_name() {
		return $this->_namespace( 'uploads-') . $this->_get_full_setting_id();
	}
}
?>