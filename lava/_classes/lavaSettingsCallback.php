<?php
/**
 * The lava Settings Callback class
 * 
 * This class has all the callback methods involved with settings
 * 
 * @package Lava
 * @subpackage lavaSettingsCallback
 * 
 * @author Daniel Chatfield
 * @copyright 2011
 * @version 1.0.0
 */
 
/**
 * lavaSettingsCallback
 * 
 * @package Lava
 * @subpackage LavaSettingsCallback
 * @author Daniel Chatfield
 * 
 * @since 1.0.0
 */
class lavaSettingsCallback extends lavaBase
{
    /**
     * lavaSettingsCallback::lavaConstruct()
     * 
     * This method is called by the __construct method of lavaBase and handles the construction
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function lavaConstruct()
    {
        //settingActions
        $hookTag = "settingActions";
        add_filter( $this->_slug( "{$hookTag}-type/password" ), array( $this, "addShowPassword" ) );
        add_filter( $this->_slug( "{$hookTag}" ), array( $this, "addResetToDefault" ) );

        //settingControl
        $hookTag = "settingControl";
        add_filter( $this->_slug( "{$hookTag}-type/timeperiod" ), array( $this, "addTimePeriodSelector" ), 10, 2 );
        add_filter( $this->_slug( "{$hookTag}-type/password" ), array( $this, "addPasswordWrapper" ), 10, 2 );
        add_filter( $this->_slug( "{$hookTag}-type/checkbox" ), array( $this, "addCheckboxUx" ), 10, 2 );
    }


    /**
     * lavaSettingsCallback::addResetToDefault()
     * 
     * Adds the "reset to default" html to the setting actions
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function addResetToDefault( $settingActions )
    {
        $settingActions .=      '<span class="action js-only reset-setting flex-3">' . __( "Reset to default", $this->_framework() ) . '</span>'.
                                '<span style="display:none" class="action js-only undo-reset-setting flex-3">' . __( "Undo Reset", $this->_framework() ) . '</span>';
        return $settingActions;
    }

    /**
     * lavaSettingsCallback::addShowPassword()
     * 
     * Adds the "show password" html to the setting actions
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function addShowPassword( $settingActions )
    {
        $settingActions =      '<span class="js-only action show-password-handle flex-1">' . __( "Show Password", $this->_framework() ) . '</span>'.
                                '<span style="display:none" class="js-only action hide-password-handle flex-1">' . __( "Hide Password", $this->_framework() ) . '</span>'.$settingActions;
        return $settingActions;
    }

    /**
     * lavaSettingsCallback::addTimePeriodSelector()
     * 
     * Adds the "show password" html to the setting actions
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function addTimePeriodSelector( $settingControl, $theSetting )
    {
        $seconds = $theSetting->settingValue();

        $selectedAttr = 'selected="selected"';

        $weeksSelected = $daysSelected = $hoursSelected = $minutesSelected = "";
        if( $seconds % ( 60 * 60 * 24 * 7 ) == 0 )
        {
            $weeksSelected = $selectedAttr;
            $theValue = round( $seconds / ( 60 * 60 * 24 * 7 ) );
        }
        elseif( $seconds % ( 60 * 60 * 24 ) == 0 )
        {
            $daysSelected = $selectedAttr;
            $theValue = round( $seconds / ( 60 * 60 * 24 ) );
        }
        elseif( $seconds % ( 60 * 60 ) == 0 )
        {
            $hoursSelected = $selectedAttr;
            $theValue = round( $seconds / ( 60 * 60  ) );
        }
        else
        {
            $minutesSelected = $selectedAttr;
            $theValue = round( $seconds / 60 );
        }
        $settingControl .=  '<div class="input-cntr clearfix js-only">'.
                                '<div class="validation" data-state="not-invoked"></div>'.
                                '<input class="time-period-ux" type="text" value="' . $theValue . '"/>'.
                            '</div>'.
                            
                            '<select class="scale-selector js-only">'.
                                '<option ' . $minutesSelected . ' value="' . 60 . '" >' . __( "Minutes"/* used as part of an input "[input] Minutes" */, $this->_framework() ) . '</option>'.
                                '<option ' . $hoursSelected . ' value="' . 60 * 60 . '" >' . __( "Hours"/* used as part of an input "[input] Minutes" */, $this->_framework() ) . '</option>'.
                                '<option ' . $daysSelected . ' value="' . 60 * 60 * 24 . '" >' . __( "Days"/* used as part of an input "[input] Minutes" */, $this->_framework() ) . '</option>'.
                                '<option ' . $weeksSelected . ' value="' . 60 * 60 * 24 * 7 . '" >' . __( "Weeks"/* used as part of an input "[input] Minutes" */, $this->_framework() ) . '</option>'.
                            '</select>';
        return $settingControl;
    }

    /**
     * lavaSettingsCallback::addPasswordWrapper()
     * 
     * Adds the wrapping html to the password input
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function addPasswordWrapper( $settingControl, $theSetting )
    {
        $placeholder = 'placeholder="'. $theSetting->properties['placeholder'] .'"';
        $settingControl =  '<div class="input-cntr clearfix" data-show="password">'.
                                '<div class="validation" data-state="not-invoked"></div>'.
                                '<input '.$placeholder.' type="text" class="password-show" value="' . $theSetting->settingValue() . '"/>'.
                                $settingControl.
                            '</div>';
        return $settingControl;
    }

    /**
     * lavaSettingsCallback::addCheckboxUx()
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function addCheckboxUx( $settingControl, $theSetting )
    {
        $checked = "unchecked";
        if( $theSetting->settingValue() == "on")
        {
            $checked = 'checked';
        }
        $settingControl .=  '<div title ="' . __( /* In context of a checkbox slider */"Click to enable/disable ", $this->_framework() ) . '" class="js-only tiptip checkbox-ux '.$checked.'"></div>';
        return $settingControl;
    }
}
?>