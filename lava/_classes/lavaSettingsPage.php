<?php
class lavaSettingsPage extends lavaPage
{
    public $multisiteSupport = true;
    public $who = "settings";

    function registerActions()
    {

    }

    function loadPage()
    {
        //do settings save
        //do redirect
        //queue notifications
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

        foreach( $settings as $setting )
        {
            $array = array(
                "key" => $setting->key,
                "default" => $setting->properties["default"],
                "type" => $setting->type,
                "tags" => $setting->tags
            );
            print_r($array);
            echo("<br/>");
        }
    }

    function siteChecks()
    {
        
    }

    function networkChecks()
    {
        
    }
}
?>