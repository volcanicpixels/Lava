<?php
/**
 * The lava Setting class
 * 
 * This class is the class base for all settings
 * 
 * @package Lava
 * @subpackage lavaSetting
 * 
 * @author Daniel Chatfield
 * @copyright 2011
 * @version 1.0.0
 */
 
/**
 * lavaSetting
 * 
 * @package Lava
 * @subpackage LavaSetting
 * @author Daniel Chatfield
 * 
 * @since 1.0.0
 */
class lavaSetting extends lavaBase
{
    /**
     * @property $type - text, colour, password, image, file, checkbox, selectbox
     * @property $who - Who set the setting? Is it a "plugin" setting or a "theme" setting.
     * @property $key - A lowercase, string with no spaces that is used to identify the setting
     * @property $properties - an array of properties that may be type specific (like: maxsize for uploads)
     * @property $validation - an array of key=>value pairs where the key is the name of the validation (e.g. email) and the value is a callback that will process the validation upon submission
     * 
     */ 
    
    /**
     * lavaSetting::lavaConstruct( $name, $who )
     * 
     * @param $name - The kname of the setting
     * @param $who - The ID of which section the setting is for
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function lavaConstruct( $key, $who )
    {
        $this->type = "text";
        $this->who = $who;
        $this->key = $key;
        
        $this->properties = array();
        $this->validation = array();
    }
    
    /**
     * lavaSetting::defaultValue( $value, $overwrite )
     * 
     * @param $value - The value to be used as the default
     * @param $overwrite (default:true) - should we overwrite an existing value (false when used internally)
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function defaultValue( $value, $overwrite = true )
    {
        $this->properties[ "default" ] = $value;
        
        //To allow chaining return the lavaSettings instance and don't reset any current chain
        return $this->_settings( false );
    }
    
    /**
     * lavaSetting::type( $type )
     * 
     * @param $type 
     * 
     * @return chainable object
     * 
     * @since 1.0.0
     */
    function type( $type )
    {
        $this->type = $type;
        switch( $type )
        {
            case "checkbox":
                $this->defaultValue( "on" , false );
                break;
            case "color"://bloody American spelling - polluting the world
            case "colour"://that's more like it
                //no support for alpha channels at this time
                $this->defaultValue( "#FFFFFF", false );
                break;
            case "password":
                $this->defaultValue( "password" );
                break;
        }
        return $this->_settings( false );
    }
    

    /**
     * lavaSetting::addValidation( $validation )
     * 
     * @param $validation - the slug of the validation function to apply
     * 
     * @return chain
     * 
     * @since 1.0.0
     */
    function addValidation( $validation = "notnull" )
    {
        $validationCallbacks = $this->_settings( false )->validationCallback;
        
        $callback = $this->_settings( false )->validationCallback( $validation );
        
        if( null != $callback )
        {
            $this->validationp[ $validation ] = $callback;
        }
        
        
        return $this->_settings( false );
    }
    
    /**
     * lavaSetting::multisite()
     * 
     * @return chain
     * 
     * @since 1.0.0
     */
    function multiSite()
    {
        $this->hidden();
        
        return $this->_settings( false );
    }
    
    /**
     * lavaSetting::hidden( $hidden )
     * 
     * @param $hidden - boolean value of whether to hide or unhide the setting
     * 
     * @return chain
     * 
     * @since 1.0.0
     */
    function hidden( $hidden = true )
    {
        //hides the setting from the admin panel
        
        return $this->_settings( false );
    }
    
    function help( $help )
    {
        $this->help = $help;
        return $this->_settings( false );
    }
    
    function name( $name )
    {
        $this->name = $name;
        return $this->_settings( false );
    }
    
    function value( $value = null )
    {//verification of change should have already been carried out
        if( isset( $value ) )
        {
            $settings = get_option( $this->_slug( "settings" ) );
            $this->value = $settings[ $this->key ] = $value;

            return $this->_settings( false );
        }
        if( isset( $this->value ) )
        {
            return $this->value;
        }
        $nakedValue = $this->nakedValue();
        
        if( $nakedValue == "%default%" )
        {
            $nakedValue = apply_filters( $this->_slug( "settingDefault" ), $this->get( "default" ), $this->key, $this->type );
            $nakedValue = apply_filters( $this->_slug( "settingDefault_{$this->key}" ), $nakedValue );
        }
        
    }

}
?>