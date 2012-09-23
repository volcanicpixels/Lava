<?php
/**
 * Scene
 *
 * @package Lava
 * @subpackage Choose_Skin_Scene
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

class Lava_Scene_Settings_Skins extends Lava_Scene_Settings
{

	function _get_classes() {
		$old = parent::_get_classes();
		$new = array(
			'lava-choose-skins-scene'
		);
		return array_merge( $old, $new );
	}
}
?>