<?php
class lavaSettingsPage extends lavaPage
{
    public $multisiteSupport = true;
    public $displayToolbar = true;
    public $who = "settings";

    function loadPage()
    {
        $this->saveSettings();
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

        $this->runActions( "settingsHiddenInputs" );


        foreach( $settings as $setting )
        {
            //action hook
            echo $setting->doSetting();
            //action hook
        }
        ?>
        <div style="margin-left:30px; margin-top:20px;">
            <input type="submit" class="lava-btn lava-btn-chunk lava-btn-chunk-yellow" name="action" value="<?php _e( "Save Settings", $this->_framework() ) ?>" />
        </div>
        <?php
        echo '</form>';
    }

    function saveSettings()
    {
        if( !isset( $_REQUEST['setting-nonce'] ) )
        {//nothing being submitted
            return;
        }
        $referrer = wp_referer_field( false );
        $messageNonce = rand( 1000, 9999);
        $redirect = add_query_arg( "message_nonce", $messageNonce );

        if( is_network_admin() and !current_user_can( "manage_network_options") )
        {
            //Queue access denied message
            
            
            
        }
        if( is_admin() and !current_user_can( "manage_options") )
        {
            //Queue access denied message
            
        }
        wp_redirect( $redirect );
        exit;
    }

    function leftActions()
    {
        $actions[] = '<div class="js-only subtle-button">'. __( "Export Settings", $this->_framework() ) .'</div>';
        $actions[] = '<div class="js-only subtle-button">'. __( "Import Settings", $this->_framework() ) .'</div>';
        $actions[] = '<div class="js-only subtle-button">'. __( "Reset All Settings", $this->_framework() ) .'</div>';
        $actions[] = '<div class="js-only lava-btn-mini lava-btn-2d lava-btn lava-btn-chunk lava-btn-chunk-yellow">'. __( "Save Settings", $this->_framework() ) .'</div>';

        return $actions;
    }
}
?>