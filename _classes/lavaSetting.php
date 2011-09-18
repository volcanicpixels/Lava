<?php
class lavaSetting extends lavaBase
{
    protected $type, $group, $key, $help, $validation = array(), $name;
    
    function lavaConstruct()
    {
        $this->type = "text";
        $this->clearance = "r_user";
        $this->key = "undefined";
        $this->group = "undefined";
    }
    
    function setDefault()
    {
        $settings = get_option( $this->_slug( "settings" ) );
        if( !isset( $settings[ $this->key ] ) )
        {//if the option doesn't exist set it as the default
            $this->nakedValue( "%default%" );
        }
    }
    
    function get( $property )
    {
        return $this->$property;
    }
    
    
    
    
    
    
    
    function group( $group )
    {
        if( $this->get( "group" ) != $group )
        {
            $this->group = $group;
            return $this->_settings( false )->moveToGroup( $this->key, $this->get( "group" ), $group );
        }
    }
    
    function type( $type )
    {
        $this->type = $type;
        
        if( !isset( $this->default ) )
        {
            switch( $type )
            {
                case "checkbox":
                    $this->defaultValue( "on" );
                    break;
                case "text":
                default:
                    $this->defaultValue( "undefined" );
            }
        }
        return $this->_settings( false );
    }
    
    function defaultValue( $default )
    {
        $this->default = $default;
        return $this->_settings( false );
    }
    
    function validate( $validate )
    {
        if( substr( $validate, 0, 1 ) == "-" )
        {
            unset( $this->validation[ substr( $validate, 1 ) ] );
        }
        else
        {
            $this->validation[ $validate ] = $validate;
        }
        return $this->_settings( false );
    }
    
    function clearance( $clearance )
    {
        $this->clearance = $clearance;
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
    
    function nakedValue( $value = null )
    {
        $settings = get_option( $this->_slug( "settings" ) );
        if( isset( $value ) )
        {
            $settings[ $this->key ] = $value;
            update_option( $this->_slug( "settings" ), $settings );

            return $this->_settings( false );
        }
        return $settings[ $this->key ];
    }
}
?>