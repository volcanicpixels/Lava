<?php
class lavaSettingsPage extends lavaPage
{
    public $multisiteSupport = true;

    function registerActions()
    {

        add_filter(  )
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
        
        $settings = apply_filters(  )
    }

    function siteChecks()
    {
        
    }

    function networkChecks()
    {
        
    }
}
?>