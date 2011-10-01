<?php
/**
 * The lava base class
 * 
 * This class is the base class for all lava classes - it adds chaining and config.
 * 
 * @package Lava
 * @subpackage lavaBase
 * 
 * @author Daniel Chatfield
 * @copyright 2011
 * @version 1.0.0
 */
 
/**
 * lavaBase
 * 
 * @package Lava
 * @subpackage LavaBase
 * @author Daniel Chatfield
 * 
 * @since 1.0.0
 */
class lavaBase
{
    protected $pluginInstance;
    protected $chain = array();
    protected $memory = array();
    
    
    /**
     * __construct function.
     * 
     * This method stores the plugin instance into a property so that chaining can be implemented.
     * 
     * @magic
     * @param lavaPlugin $pluginInstance
     * @param array $arguments
     * @return void
     * 
     * @since 1.0.0
     */
    function __construct( $pluginInstance, $arguments = array() )
    {
        $this->pluginInstance = $pluginInstance;
        
        $callback = array( $this, "lavaConstruct" );//call the sub classes construct argument
        if( is_callable( $callback ) )
        {
            call_user_func_array( $callback, $arguments );
        }
    }
    
    /**
     * __call function.
     * 
     * This method implements chaining (allows lavaPlugin method calls to be called from any class)
     * 
     * @magic
     * @param lavaPlugin $pluginInstance
     * @param array $arguments
     * @return void
     * 
     * @since 1.0.0
     */
    function __call( $methodName, $arguments )
    {
        // lavaPlugin chainable methods start with "_" - so this is checking to see whether we should try a lavaPlugin method
        if( substr( $methodName, 0, 1 ) == "_" )
        {
            $callback = array( $this->pluginInstance, $methodName );
            if( is_callable( $callback ) )
            {
                return call_user_func_array( $callback, $arguments );
            }
        }
        elseif( isset( $this->chain[ "current" ] ) )
        {
            //lets see if the class that is the current context has this method
            $callback = array( $this->chain[ "current" ], $methodName );
            if( is_callable( $callback ) )
            {
                return call_user_func_array( $callback, $arguments );
            }
        }
        if( isset( $this->lavaCallReturn ) )
        {
            return $this->lavaCallReturn;
        }
        return $this;//couldn't find anything to call so return this object so chaining doesn't break
    }
    
    /**
     * lavaReset function.
     * 
     * Resets the chain (prevents unexpected chaining to occur)
     * 
     * @return $this
     * 
     * @since 1.0.0
     */
    function lavaReset()
    {
        $this->chain = array();
        return $this;
    }
    
    /**
     * lavaContext function.
     * 
     * Resets the chain (prevents unexpected chaining to occur)
     * 
     * @return $this
     * 
     * @since 1.0.0
     */
    final function lavaContext( $context = "", $handle = "current" )
    {
        $this->chain[ $handle ] = $context;
    }
    
    /**
     * lavaRemember function.
     * 
     * The lavaRemember function stores data as a key>value pair as a protected property to a class
     * 
     * @magic
     * @param string $key
     * @param $value (default: null)
     * @return $this || $value || false
     * 
     * @since 1.0.0
     */
    function lavaRemember( $key, $value = null )
    {
        if( isset( $value ) )
        {//value has been given - so lets set it
            $this->memory[ $key ] = $value;
            return $this;
        }
        if( isset( $this->memory[ $key ] ) )
        {
            return $this->memory[ $key ];
        }
        return false;
    }
}
?>