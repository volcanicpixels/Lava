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
        add_filter( $this->_slug( "{$hookTag}-type/skin" ), array( $this, "addSkinsUx" ), 10, 2 );

		//skinRibbons
        $hookTag = "skinRibbons";
        add_action( $this->_slug( "{$hookTag}" ), array( $this, "addActiveRibbon" ), 10, 2 );
    }

	function addSkinsUx( $settingControl, $theSetting )
    {
        $settingControl = '<div class="js-fallback">' . $settingControl . ' </div>';
		$settingControl .= '<div class="skin-thumb"><img alt="Skin Thumbnail" /><div class="actions"><div class="lava-btn lava-btn-action-large lava-btn-action lava-btn-action-blue lava-btn-show-underground">' . __( "Change Skin", $this->_framework() ) . '</div></div></div>';
        
        //add ux cntr, put in the labels, js will handle the rest
        
        return $settingControl;
    }

	function addActiveRibbon()
	{
		?>
		<div class="ribbon ribbon-active ribbon-green">
			<span class="ribbon-fold ribbon-fold-left"></span>
			<span class="ribbon-fold ribbon-fold-right"></span>
			<?php _e( "Selected", $this->_framework() ) ?>
		</div>
		<?php
	}

}
?>