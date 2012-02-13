<?php
/**
 * The lava Skins Callback class
 * 
 * 
 * @package Lava
 * @subpackage lavaSkinsCallback
 * 
 * @author Daniel Chatfield
 * @copyright 2011
 * @version 1.0.0
 */
 
/**
 * lavaSkinsCallback
 * 
 * @package Lava
 * @subpackage LavaSkinsCallback
 * @author Daniel Chatfield
 * 
 * @since 1.0.0
 */
class lavaSkinsCallback extends lavaSettingsCallback
{
    /**
     * lavaSkinsCallback::lavaConstruct()
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
        add_filter( $this->_slug( "{$hookTag}-type/skin" ), array( $this, "removeActions" ), 20, 2 );

        //settingControl
        $hookTag = "settingControl";
        add_filter( $this->_slug( "{$hookTag}-type/skins" ), array( $this, "addSkinsUx" ), 10, 2 );
    }

	function addSkinsUx( $settingControl, $theSetting )
    {
        $settingKey = $theSetting->getKey();
        $settingWho = $theSetting->who;
        $pluginSlug =  $theSetting->_slug();
        $settingInputName = "{$pluginSlug}[{$settingWho}/{$settingKey}]";
        $settingInputID = "{$pluginSlug}-{$settingWho}-{$settingKey}";
        $settingValue = $theSetting->getValue( true );
        $settingPlaceholder = $theSetting->getProperty( "placeholder" );
        $theOptions = $theSetting->getProperty( "radio-values" );
        if( !is_array( $theOptions ) )
        {
            $theOptions = array();
        }
        $settingControl = '<div class="">' . $settingControl . ' </div>';
        foreach( $theOptions as $option )
        {
            
        }
        //add ux cntr, put in the labels, js will handle the rest
        
        return $settingControl;
    }

    function addSkinsUx2( $settingControl, $theSetting )
    {
        $settingKey = $theSetting->getKey();
        $settingWho = $theSetting->who;
        $pluginSlug =  $theSetting->_slug();
        $settingInputName = "{$pluginSlug}[{$settingWho}/{$settingKey}]";
        $settingInputID = "{$pluginSlug}-{$settingWho}-{$settingKey}";
        $settingValue = $theSetting->getValue( true );
        $settingPlaceholder = $theSetting->getProperty( "placeholder" );
        $theOptions = $theSetting->getProperty( "radio-values" );
        if( !is_array( $theOptions ) )
        {
            $theOptions = array();
        }
        $settingControl = "";
        foreach( $theOptions as $option )
        {
            $slug = $option->slug;
            $name = $option->name;
            $settingControl .= 
                "<div class='lava-skin' data-skin='$slug' >". 
                    "<div class='skin-ux js-only'>".
                        "<img alt='Thumbnail of the skin' src='http://dummyimage.com/180x130/E8117F/fff.png&text=Skin+image+test' class='lava-thumb' />".
                        "<div class='lava-actions'>".
                            "<div class='select-button lava-btn lava-btn-metro lava-btn-metro-black'>" . __( "Select this skin", $this->_framework() ) . "</div>".
                        "</div>".
                    "</div>".
                    "<input id='$settingInputID-$slug' type='radio' name='{$settingInputName}' value='{$slug}' />".
                    "<div class='selection-icon'></div>".
                "</div>"
            ;
        }
        //add ux cntr, put in the labels, js will handle the rest
        
        return $settingControl;
    }
}
?>