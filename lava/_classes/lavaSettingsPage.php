<?php
class lavaSettingsPage extends lavaPage
{
    public $multisiteSupport = true;
    public $displayToolbar = true;
    public $who = "settings";

    function registerActions()
    {

    }

    function loadPage()
    {
        //do settings save
        //queue notifications
        //do redirect
    }

    function displayPage()
    {
        if( is_multisite() and defined( "WP_NETWORK_ADMIN" ) and WP_NETWORK_ADMIN == true)
            $this->networkChecks();
        else
            $this->siteChecks();
        
        $settings = $this->_settings()->fetchSettings();

        //display heading
        //start settings wrap
        $this->doSettings( $settings );
        //do save dialog
        //close wrap
    }
    
    function doSettings( $settings )
    {
        $settings = apply_filters( $this->_slug( $this->who . "settingsOrder" ), $settings );

        echo '<form class="settings-wrap" method="post">';


        foreach( $settings as $setting )
        {
            //action hook
            echo $setting->doSetting();
            //action hook
        }
        echo '</form>';
    }

    function siteChecks()
    {
        
    }

    function networkChecks()
    {
        
    }

    function leftActions()
    {
        $actions[] = '<a class="js-only subtle-button">'. __( "Export Settings", $this->_framework() ) .'</a>';
        $actions[] = '<a class="js-only subtle-button">'. __( "Import Settings", $this->_framework() ) .'</a>';
        $actions[] = '<a class="js-only subtle-button">'. __( "Reset All Settings", $this->_framework() ) .'</a>';
        $actions[] = '<a class="js-only lava-btn-mini lava-btn-2d lava-btn lava-btn-chunk lava-btn-chunk-yellow">'. __( "Save Settings", $this->_framework() ) .'</a>';

        return $actions;
    }
}
?>