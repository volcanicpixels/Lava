<?php
/*
 * @package Lava
 * @subpackage Enable_Extensions_Scene
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Scene_Settings_Extensions extends Lava_Scene_Settings
{

	function _get_classes() {
		$old = parent::_get_classes();
		$new = array(
			'lava-extensions-scene'
		);
		return array_merge( $old, $new );
	}
}
?>