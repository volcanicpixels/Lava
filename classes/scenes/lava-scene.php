<?php
/**
 * Scene
 *
 * @package Lava
 * @subpackage Scene
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */

/*
	Scenes are parts of pages. Different pages use them in different ways.

	Scenes are either: local, special, external
		When a page is loaded any special scenes that have been referenced (with &scene=scene_name) are printed first
		All local scenes are always pushed as part of the html
		External scenes are effectively just links

	e.g.

	Settings:
		All settings are pushed as local scenes with the scene of settings set by their origin or if plugin settings by the settings.yaml file

		e.g.
			general:
				[some genreal settings]
			advanced:
				[some advanced settings]
			skin/default_skin:
				[some skin settings]
			extension/access_logs:
				[access logs settings]

	Skins:
		Change Skin
		Default Skin Settings
		Editor
		Upload a skin
		Commission custom skin



*/
class Lava_Scene extends Lava_Base
{
	$_type = 'local';

	function _construct() {
		
	}
}
?>