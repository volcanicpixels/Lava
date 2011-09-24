<?php
/**
 * The lava Settings class
 * 
 * This class is the class that controls the settings
 * 
 * @package Lava
 * @subpackage lavaSettings
 * 
 * @author Daniel Chatfield
 * @copyright 2011
 * @version 1.0.0
 */
 
/**
 * lavaSettings
 * 
 * @package Lava
 * @subpackage LavaSettings
 * @author Daniel Chatfield
 * 
 * @since 1.0.0
 */
class lavaSettings extends lavaBase
{
    protected $settings = array();
    protected $settingsIndexes = array();
    
    /**
     * lavaSettings::lavaConstruct()
     * 
     * This method is called by the __construct method of lavaBase and handles the construction
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function lavaConstruct()
    {
        add_option( $this->_slug( "settings" ), array() );//add the option if it doesn't exist
    }
   
    /**
     * lavaSettings::addSetting( $name, $who )
     * 
     * This method adds a plugin setting.
     * 
     * @param $name The name of the setting - should be unique within the section
     * @param $who - Who is adding the setting:
     *      "plugin" - This is a plugin setting like "enabled"
     *      "theme" - This is a theme setting
     *      "keyholder" - This is a licensing setting (usually used to prevent file tamper)
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function addSetting( $name, $who = "plugin" )
    {
        $key = str_replace( " ", "_", strtolower( $name ) );
        
        if( !isset( $this->settings[ $who ][ $key ] ) )
        {
            $arguments = array(
                $name,
                $who
            );
            $this->settings[ $who ][ $key ] = $this->_new( "lavaSetting", $arguments );
        }
        $this->lavaContext( $this->settings[ $key ] );
        return $this;
    }
    
    /**
     * lavaSettings::fetchSetting( $key)
     * 
     * This method fetches a plugin setting.
     * 
     * @param $key 
     * 
     * @return $this
     * 
     * @since 1.0.0
     */
    function fetchSetting( $key )
    {
        unset( $this->chain[ "current" ] );//unset it so if the fetch fails then any subsequent chained actions aren't accidentally applied to another setting
        if( isset( $this->settings[ $key] ) )
        {
            $this->lavaContext( $this->settings[ $key] );
        }
        return $this;
    }
    
    /**
     * lavaSettings::settingEists( $key )
     * 
     * This method determines whether a setting exists
     * 
     * @param $key 
     * 
     * @return boolean
     * 
     * @since 1.0.0
     */
    function settingExists( $key )
    {
        if( isset( $this->settings[ $key] ) )
        {
            return true;
        }
        return false;
    }
    
    function config( $key, $value = null )
    {
        if( $value != null )
        {
            $this->config[ $key ] = $value;
            return $this;
        }
        if( isset( $this->config[ $key ] ) )
        {
            return $this->config[ $key ];
        }
        return false;
    }
}
?>