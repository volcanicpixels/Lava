<?php
class lavaSettingsPage extends lavaPage
{
    public $multisiteSupport = true;

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
        
    }

    function siteChecks()
    {
        
    }

    function networkChecks()
    {
        
    }
}
?>