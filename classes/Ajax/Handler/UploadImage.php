<?php
class Lava_Ajax_Handler_UploadImage extends Lava_Ajax_Handler_UploadFile {
	
	public $allowed_file_types = array('jpg', 'svg', 'png', 'gif'); // should be overloaded

}
?>