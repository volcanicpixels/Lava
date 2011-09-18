<?php
class lavaSettings extends lavaBase
{
    protected $settings = array();
    protected $groups = array();
    protected $config = array();
    
    function lavaConstruct()
    {
        add_option( $this->_slug( "settings" ), array() );
    }
   
    function addGroup( $group )
    {
        $this->chain[ "group" ] = $group;
        return $this;
    }
    
    function addSetting( $key )
    {
        if( !isset( $this->settings[ $key] ) )
        {
            $this->groups[ $key ][ $key ] = $this->settings[ $key ] = $this->_new( "lavaSetting" );
            
            $group = isset( $this->chain[ "group" ] ) ? $this->chain[ "group" ] : $key;
            
            $this->settings[ $key ]->key( $key );
            $this->settings[ $key ]->group( $group );
            $this->settings[ $key ]->setDefault();
        }
        $this->chain[ "current" ] = $this->settings[ $key ];
        return $this;
    }
    
    function fetchSetting( $key )
    {
        unset( $this->chain[ "current" ] );//unset it so if the fetch fails then any subsequent chained actions aren't accidentally applied to another setting
        if( isset( $this->settings[ $key] ) )
        {
            $this->chain[ "current" ] = $this->settings[ $key];
        }
        return $this;
    }
    
    function settingExists( $key )
    {
        if( isset( $this->settings[ $key] ) )
        {
            return true;
        }
        return false;
    }
    
    
    
    function moveToGroup( $key, $oldGroup, $newGroup )
    {
        $this->groups[ $newGroup ][ $key ] = $this->groups[ $oldGroup ][ $key ]; // copy accross
        
        unset( $this->groups[ $oldGroup ][ $key ] );
        
        if( count( $this->groups[ $oldGroup ] ) == 0 )
        {
            unset( $this->groups[ $oldGroup ] );
        }
        
        return $this;
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