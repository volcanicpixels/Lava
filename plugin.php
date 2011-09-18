<?php
/*
Plugin Name: Ultimate Private Blog
Plugin URI: http://www.spiders-design.co.uk/plugins/password-protect-wordpress/
Description: Ultimate Private Blog is a password protect wordpress plugin which allows you to password protect all of your wordpress blog including all posts and feeds.
Version: 4.0 beta
Author: Daniel Chatfield
Author URI: http://www.spiders-design.co.uk
License: GPLv2
*/
?>
<?php
include( dirname( __FILE__ ) ."/lava.php" );

$pluginName = "Private Blog";
$pluginVersion = "4.0 beta";

$thePlugin = lava::newPlugin( __FILE__, $pluginName, $pluginVersion );
$pluginSlug = $thePlugin->_slug();

$thePlugin->_settings()     
        ->addSetting( "enabled" )
            ->type( "checkbox" )
            ->help( __( "Use this to enable or disable the plugin", $pluginSlug ) )
            ->default( "on" )
    ->addGroup( "passwords" )->groupName( __( "Configure password" ) )
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