<?php
class lavaSettingsPage extends lavaPage
{
    public $multisiteSupport = true;
    public $displayToolbar = true;
    public $who = "settings";

    function loadPage()
    {
        $this->saveSettings();
        $this->resetSettings();
        //queue notifications
        //do redirect
    }

    function displayPage()
    {
        $settings = $this->_settings()->fetchSettings( $this->who );

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

        $this->runActions( "settingsPre" );

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
        if( $_REQUEST['purpose'] != "save" )
        {
            //not saving
            return;
        }
        $referrer = wp_referer_field( false );
        $messageNonce = rand( 1000, 9999);
        $redirect = add_query_arg( "message_nonce", $messageNonce );

        if( is_network_admin() and !current_user_can( "manage_network_options") )
        {
            //Queue access denied message
            
        }
        else if( is_admin() and !current_user_can( "manage_options") )
        {
            //Queue access denied message
            
        }
        else
        {//user is authorized to do something
            $redirect = add_query_arg( "action_done", "saved", $redirect );
            if( is_network_admin() )
            {
                //do network save
            }
            elseif( is_admin() )
            {
                $theSettings = $_POST[ $this->_slug() ];
                foreach( $theSettings as $setting => $value )
                {
                    $value = stripslashes( $value );
                    $settingArray = explode( "/", $setting );
                    $this->_settings()
                            ->fetchSetting( $settingArray[1], $settingArray[0] )
                                ->updateValue( $value, true, true )
                    ;
                }
                $this->_settings()->updateCache();
            }
        
        }
        wp_redirect( $redirect );
        exit;
    }

    function resetSettings()
    {
        if( !isset( $_REQUEST['setting-nonce'] ) )
        {//nothing being submitted
            return;
        }
        if( $_REQUEST['purpose'] != "reset" )
        {
            //not resetting
            return;
        }
        $referrer = wp_referer_field( false );
        $messageNonce = rand( 1000, 9999);
        $redirect = add_query_arg( "message_nonce", $messageNonce );
        $redirect = add_query_arg( "action_done", "reset", $redirect );

        if( is_network_admin() and !current_user_can( "manage_network_options") )
        {
            //Queue access denied message
            
        }
        else if( is_admin() and !current_user_can( "manage_options") )
        {
            //Queue access denied message
            
        }
        else
        {//user is authorized to do something

            if( is_network_admin() )
            {
                //do network reset
            }
            elseif( is_admin() )
            {
                $resetScope = $_REQUEST[ 'reset-scope' ];

                switch( $resetScope )
                {
                    case "total":
                        //delete everything and run the plugin activated hook
                        delete_option( $this->_slug( "settings" ) );
                        delete_option( $this->_slug( "config" ) );
                        delete_option( $this->_slug( "messages" ) );
                }
            }
        
        }
        wp_redirect( $redirect );
        exit;
    }

    function leftActions()
    {
        //$actions[] = '<div class="js-only subtle-button">'. __( "Export Settings", $this->_framework() ) .'</div>';
        //$actions[] = '<div class="js-only subtle-button">'. __( "Import Settings", $this->_framework() ) .'</div>';
        //$actions[] = '<div class="js-only subtle-button">'. __( "Reset All Settings", $this->_framework() ) .'</div>';
        $actions[] = '<div class="js-only lava-btn-mini lava-btn-2d lava-btn lava-btn-chunk lava-btn-chunk-yellow" onclick="jQuery(\'.settings-wrap\').submit()">'. __( "Save Settings", $this->_framework() ) .'</div>';
        $actions[] = '<div class="js-only lava-btn-mini lava-btn-2d lava-btn lava-btn-chunk lava-btn-chunk-white" onclick="jQuery(\'.lava-form-purpose\').val(\'reset\');jQuery(\'.settings-wrap\').submit()">'. __( "Reset Settings", $this->_framework() ) .'</div>';

        return $actions;
    }
}
?>