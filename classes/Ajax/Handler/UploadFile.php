<?php
class Lava_Ajax_Handler_UploadFile extends Lava_Ajax_Handler {
	
	public $allowed_file_types = array(); // should be overloaded


	function _do_ajax_data() {
		$upload_name = $this->_namespace('upload');
		if( array_key_exists('upload_name', $_REQUEST) ) {
			$upload_name = $_REQUEST['upload_name'];
		}
		$overrides = array('test_form' => false);
		$the_upload = wp_handle_upload( $_FILES[ $upload_name ], $overrides);
		return array(
			"name" => $_FILES[$upload_name]['name'],
			"size" => $_FILES[$upload_name]['size'],
			"url" => $the_upload['url']
		);
	}
}
?>