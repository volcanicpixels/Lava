<?php
/*
Plugin Name: My Awesome Plugin
Plugin URI: 
Description: Short description of your plugin
Version: 1.0.0
Author: Daniel Chatfield
Author URI: http://www.volcanicpixels.com
License: GPLv2
*/
?>
<?php
include( dirname( __FILE__ ) ."/lava/lava.php" );

$pluginName = "My Awesome Plugin";
$pluginVersion = "1.0.0";

$thePlugin = lava::newPlugin( __FILE__, $pluginName, $pluginVersion );
$pluginSlug = $thePlugin->_slug();


/**
 * Define the plugin settings:
 *      Use this space to list all the settings that you are defining
 */
 
 /**
 * Define the plugin admin pages:
 *      Use this space to list all of the admin pages that are being defined
 */


?>