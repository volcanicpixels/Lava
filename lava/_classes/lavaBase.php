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
    public $suffixes = array( "/pre", "", "/post" );
    
    
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
        elseif( !is_null( $this->lavaContext() ) )
        {
            //lets see if the class that is the current context has this method
            $callback = array( $this->lavaContext(), $methodName );
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
     * adds/removes context
     * 
     * @return $this
     * 
     * @since 1.0.0
     */
    final function lavaContext( $context = null, $handle = "current" )
    {
        if( null != $context)
        {
            $this->chain[ $handle ] = $context;
        }
        return $this->chain[ $handle ];
    }

    /**
     * clearLavaContext function.
     * 
     * adds/removes context
     * 
     * @return $this
     * 
     * @since 1.0.0
     */
    final function clearLavaContext( $handle = "current" )
    {
        $this->chain[ $handle ] = null;
    }
    
    /**
     * lavaRemember function.
     * 
     * The lavaRemember function stores data as a key>value pair as a protected property to a class
     * 
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

    /**
     * runActions function.
     * 
     * Runs the actions with all the parameters
     *
     * @param string $key
     * @param $value (default: null)
     * 
     * @since 1.0.0
     */
    function runActions( $hookTag )
    {
        $hooks = array_unique( $this->hookTags() );
        $suffixes = array_unique( $this->suffixes );

        foreach( $suffixes as $suffix)
        {
            foreach( $hooks as $hook )
            {
                if( !empty($hook) )
                {
                    $hook = "-".$hook;
                }
                do_action( $this->_slug( "{$hookTag}{$hook}{$suffix}" ), $this );
            }
        }
    }

     /**
     * runActions function.
     * 
     * Runs the filters with all the parameters
     * 
     * @param string $hookTag
     * @param $args (default: null)
     * 
     * @since 1.0.0
     */
    function runFilters( $hookTag, $argument = "" )
    {
        
        $hooks = array_unique( $this->hookTags() );
        $suffixes = array_unique( $this->suffixes );

        foreach( $suffixes as $suffix)
        {
            foreach( $hooks as $hook )
            {
                if( $hook == " " )
                {
                    $hook = "";
                    
                }
                else
                {
                    $hook = "-".$hook;
                }
                //echo( $this->_slug( "{$hookTag}{$hook}{$suffix}" ). "<br/>" );
                $theHook = $this->_slug( "{$hookTag}{$hook}{$suffix}" );

                $argument = apply_filters( $theHook, $argument, $this );
            }
        }

        return $argument;
    }

    function hookTags()
    {
        return array("");
    }
}
?>