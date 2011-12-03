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
        $this->status = "default";
        
        $this->properties = array();
        $this->validation = array();
        $this->tags = array();
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
     *  Sets the type of the setting (text, password, timeperiod etc.)
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
     * lavaSetting::addTag( $tag )
     *  Adds a tag to the setting that is used for hooks and printed in the html to allow easy customizations
     * 
     * @param $tag - The name of the tag to add to the setting. Should be lowercase letters and hyphen only
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function addTag( $tag )
    {
        if( !empty( $tag ) )
        {
            $this->tags[ $tag ] = $tag;

            $this->_settings( false )->_addTag( $tag, $this->key, $this->who );
        }
        return $this->_settings( false );
    }

    /**
     * lavaSetting::removeTag( $tag )
     *  Removes a previously added tag
     * 
     * @param $tag - The name of the tag to remove to the setting. 
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function removeTag( $tag )
    {
        unset( $this->tags[ $tag ] );

        return $this->_settings( false )->_removeTag( $tag, $this->key, $this->who );
    }
    

    /**
     * lavaSetting::addValidation( $validation )
     *  Adds client side and server side validation to the data
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
            $this->validation[ $validation ] = $callback;
        }
        
        
        return $this->_settings( false );
    }
    
    /**
     * lavaSetting::multisite()
     *  Makes the setting only appear to network admins
     * 
     * @return chain
     * 
     * @since 1.0.0
     */
    function multiSite()
    {
        $this->addTag( "multisite" );
        
        return $this->_settings( false );
    }
    
    /**
     * lavaSetting::hidden( $hidden )
     *  Hides the HTML from view (still printed though)
     * 
     * @param $hidden - boolean value of whether to hide or unhide the setting
     * 
     * @return chain
     * 
     * @since 1.0.0
     */
    function hidden( $hidden = true )
    {
        //hides the setting from the admin panel (still deciding whether it should generate HTML and just hide it or not generate at all)
        if( $hidden == true )
        {
            $this->addTag( "hidden" );
        }
        else
        {
            $this->removeTag( "hidden" );
        }
        return $this->_settings( false );
    }

    /**
     * lavaSetting::help( $help )
     *  Sets the contextual help
     * 
     * @param $help - translated help
     * 
     * @return chain
     * 
     * @since 1.0.0
     */    
    function help( $help )
    {
        $this->help = $help;
        return $this->_settings( false );
    }
    

    /**
     * lavaSetting::help( $help )
     *  Sets the contextual help
     * 
     * @param $help - translated help
     * 
     * @return chain
     * 
     * @since 1.0.0
     */
    function name( $name )
    {
        $this->name = $name;
        return $this->_settings( false );
    }
    
    function settingValue()
    {
        
        return $this->settingDefaultValue();
    }

    /**
     * lavaSetting::setProperty( $property, $value )
     * 
     * @param $property
     * @param $value
     * 
     * @return chain
     * 
     * @since 1.0.0
     */
    function setProperty( $property, $value)
    {
        $this->properties[ $property ] = $value;
        return $this->_settings( false );
    }









    /**
     * lavaSetting::settingClasses( $format )
     *  Gets the classes for the setting and either returns them as a formatted string or as an array
     * 
     * @param $format
     * 
     * @return chain
     * 
     * @since 1.0.0
     */
    function settingClasses( $format = false )
    {
        $classes = array();

        $classes[] = "setting";
        $classes[] = "clearfix";

        foreach( $this->tags as $tag )
        {
            $classes[] = "tag-{$tag}";
        }

        $type = $this->settingType();
        $classes[] = "type-{$type}";

        $classes = $this->runFilters( "settingClasses", $classes );

        if( $format == false )
        {
            return $classes;
        }

        $classesFormatted = "";
        foreach( $classes as $class)
        {
            $classesFormatted .= " $class";
        }

        return $classesFormatted;
    }


    /**
     * lavaSetting::settingTags( $format )
     *  Gets the tags for the setting and either returns them as a formatted string or as an array
     * 
     * @param $format
     * 
     * @return chain
     * 
     * @since 1.0.0
     */
    function settingTags( $format = false )
    {

        if( $format == false )
        {
            return $this->tags;
        }

        $formatted = "";
        foreach( $this->tags as $tag)
        {
            $formatted .= " $tag";
        }

        return $formatted;
    }

    /**
     * lavaSetting::settingType()
     *
     * @return chain
     * 
     * @since 1.0.0
     */
    function settingType()
    {
        return $this->type;
    }

    /**
     * lavaSetting::settingName()
     *
     * @return chain
     * 
     * @since 1.0.0
     */
    function settingName()
    {
        return $this->name;
    }

    /**
     * lavaSetting::settingKey()
     *
     * @return chain
     * 
     * @since 1.0.0
     */
    function settingKey()
    {
        return $this->key;
    }

    /**
     * lavaSetting::settingStatus()
     *
     * @return chain
     * 
     * @since 1.0.0
     */
    function settingStatus()
    {
        return $this->status;
    }

    /**
     * lavaSetting::settingDefaultValue()
     *
     * @return chain
     * 
     * @since 1.0.0
     */
    function settingDefaultValue()
    {
        return $this->properties['default'];
    }




    /**
     * lavaSetting::doSetting()
     *  echos the setting HTML
     *
     * @return chain
     * 
     * @since 1.0.0
     */
    function doSetting()
    {
        if( array_key_exists( "no-display", $this->tags ) )
        {
            return;
        }
        
        $classes = $this->settingClasses( true );
        $tags = $this->settingTags( true );
        $type = $this->settingType();
        $name = $this->settingName();
        $key = $this->settingKey();
        $status = $this->settingStatus();
        $defaultValue = $this->settingDefaultValue();

        $settingStart = "<div class=\"{$classes}\" data-tags=\"{$tags}\" data-status=\"{$status}\" data-type=\"{$type}\" data-key=\"{$key}\" data-default-value=\"{$defaultValue}\">";
            $statusIndicator = '<span class="status-indicator"></span>';
            $preSettingStart = '<div class="pre-setting">';
                $settingName = "<span class=\"setting-name\">$name</span>";
            $preSettingEnd = '</div>';

            $settingInnerStart = '<div class="setting-inner clearfix">';
                $settingControlStart = '<div class="setting-control">';
                    $settingControl = $this->settingControl();
                $settingControlEnd = '</div>';
                $settingActionsStart = '<div class="setting-actions clearfix">';
                    $settingActions = $this->settingActions();
                $settingActionsEnd = '</div>';
            $settingInnerEnd ='</div>';

            $postSettingStart = '<div class="post-setting clearfix">';
            $postSettingEnd ='</div>';
        $settingEnd = '</div>';

        $settingFull = 
            "
            $settingStart
                $statusIndicator
            
                $preSettingStart
                    $settingName
                $preSettingEnd
            
                $settingInnerStart
                    $settingControlStart
                        $settingControl
                    $settingControlEnd
                    $settingActionsStart
                        $settingActions
                    $settingActionsEnd
                $settingInnerEnd

                $postSettingStart
                $postSettingEnd
            $settingEnd
            ";
        $settingFull = $this->runFilters( "settingFull", $settingFull );

        return $settingFull;
    }

    /**
     * lavaSetting::settingActions()
     *  Returns the actions for the setting
     *
     * @return HTML string of actions
     * 
     * @since 1.0.0
     */
    function settingActions()
    {
        $settingActions = $this->runFilters( "settingActions" );

        return $settingActions;
    }

    /**
     * lavaSetting::settingControl()
     *  Returns the setting control HTML
     *
     * @return HTML string of actions
     * 
     * @since 1.0.0
     */
    function settingControl( $type = "default" )
    {
        $settingKey = $this->settingKey();
        $settingWho = $this->who;
        $pluginSlug =  $this->_slug();
        $settingInputName = "{$pluginSlug}[{$settingWho}/{$settingKey}]";
        $settingInputID = "{$pluginSlug}-{$settingWho}-{$settingKey}";
        $settingValue = $this->settingDefaultValue();
        $settingPlaceholder = $this->properties['placeholder'];
        if( "default" == $type )
        {
            $type = $this->settingType();
        }

        switch( $type )
        {
            case "checkbox":
                if( "on" == $settingValue )
                {
                    $checked = 'checked="checked"';
                }
                $settingControl .= "<input id='{$settingInputID}-backup' type='hidden' name='{$settingInputName}' value='off' />";
                $settingControl .= "<input data-actual='true' {$checked} id='{$settingInputID}' type='checkbox' name='{$settingInputName}' value='on' />";
            break;

            case "password":
                $settingControl .= "<input placeholder='{$settingPlaceholder}' data-actual='true' id='{$settingInputID}' type='password' name='{$settingInputName}' value='{$settingValue}' />";
            break;

            case "timeperiod":
                $settingControl = "<input data-actual='true' id='{$settingInputID}' type='text' name='{$settingInputName}' value='{$settingValue}' />";
            case "text":
            default:
                $settingControl = "<input data-actual='true' id='{$settingInputID}' type='text' name='{$settingInputName}' value='{$settingValue}' />";
        }

        $settingControl = $this->runFilters( "settingControl", $settingControl );

        return $settingControl;
    }


    function hookTags()
    {
        $settingWho = $this->who;
        $settingKey = $this->key;
        $settingType = $this->type;

        $hooks = array( " ");
        $hooks[] = "who/{$settingWho}";
        $hooks[] = "type/{$settingType}";
        $hooks[] = "key/{$settingKey}";
        $hooks[] = "who/{$settingWho}-key/{$settingKey}";

        foreach( $this->tags as $tag)
        {
            $hooks[] = "tag/{$tag}";
        }

        return $hooks;
    }
}
?>