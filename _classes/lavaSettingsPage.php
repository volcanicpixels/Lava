<?php
class lavaSettingsPage extends lavaPage
{
    protected $network = false;
    
    function network( $value )
    {
        $this->network = $value;
        if( $value == true )
        {
            $this->capability( "manage_network_options" );
        }
        return $this->_pages( false );
    }
}
?>