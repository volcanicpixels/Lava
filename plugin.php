<?php
/*
Plugin Name: Ultimate Password Protect Privacy
Plugin URI: http://go.volcanicpixels.com/privacy-plugin/
Description: Ultimate Password Protect Privacy is a password protection wordpress plugin which allows you to password protect all of your wordpress blog including all posts and feeds.
Version: 4.0 beta
Author: Daniel Chatfield
Author URI: http://www.volcanicpixels.com
License: GPLv2
*/
?>
<?php
include( dirname( __FILE__ ) ."/lava/lava.php" );

$pluginName = "Private Blog";
$pluginVersion = "4.0 beta";

$thePlugin = lava::newPlugin( __FILE__, $pluginName, $pluginVersion );
$pluginSlug = $thePlugin->_slug();

$thePlugin->_settings()     
        ->addSetting( "Enabled" )
            ->type( "checkbox" )
            ->help( __( "Use this to enable or disable the plugin", $pluginSlug ) )
            ->default( "on" )
        ->addSetting( "password" )
            ->type( "password" )
            ->help( __( "Password required by visitors to display site", $pluginSlug ) )
;


$thePlugin->_pages()
    ->addSettingsPage()
    ->addSkinsPage()
        ->title( __( "Login page skins", $pluginSlug ) )
        ->heading( __( "Select Login Page Skin", $pluginSlug ) )
        ->heading( __( "Configure Login Skin", $pluginSlug ), "configure" )
    ->addTablePage( "accesslogs" )
        ->title( __( "Access Logs", $pluginSlug ) )
    ->addSupportPage()
    ->addLicensingPage()
;


?>