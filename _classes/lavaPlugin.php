<?php
/**
 * The lava plugin class
 * 
 * This class is the main plugin class and the only class that doesn't extend lavaBase
 * 
 * @package Lava
 * @subpackage lavaPlugin
 * 
 * @author Daniel Chatfield
 * @copyright 2011
 * @version 1.0.0
 */
 
/**
 * lavaPlugin
 * 
 * @package Lava
 * @subpackage LavaPlugin
 * @author Daniel Chatfield
 * 
 * @since 1.0.0
 */
class lavaPlugin
{
    /**
     * __construct function.
     * 
     * @access public
     * @param mixed $pluginName
     * @param mixed $pluginVersion
     * @return void
     * 
     * @since 1.0.0
     */
    function __construct( $pluginFile, $pluginName, $pluginVersion )
    {
        $this->pluginFile = $pluginFile;
        $this->pluginName = $pluginName;
        $this->pluginVersion = $pluginVersion;
        $this->pluginSlug = strtolower( str_replace( " ", "_", $pluginName ) );
        
        spl_autoload_register( array( $this, "__autoload" ) );
    }
    
    /**
     * __autoload function.
     * 
     * The __autoload function defines what to do when a non-declared class is referenced
     * 
     * @access public
     * @param mixed $className
     * @return void
     * 
     * @since 1.0.0
     */
    function __autoload( $className )
    {    
        if( file_exists( dirname( __FILE__ ) . "/{$className}.php" ) AND !class_exists( $className ) )//don't want to include the file if it doesn't exist
        {
        	include_once( dirname( __FILE__ ) . "/{$className}.php" );
        }
    }
    
    
    
    
    
    
    /**
     * _name function.
     * 
     * @return ->pluginName
     * 
     * @since 1.0.0
     */
    function _name()
    {
        return $this->pluginName;
    }
    
    /**
     * _slug function.
     * 
     * @return ->pluginSlug
     * 
     * @since 1.0.0
     */
    function _slug( $append = null )
    {
        $append = isset( $append )? "_{$append}" : "";
        return $this->pluginSlug . $append;
    }
    
    /**
     * _version function.
     * 
     * @return ->pluginVersion
     * 
     * @since 1.0.0
     */
    function _version()
    {
        return $this->pluginVersion;
    }
    
    /**
     * _file function.
     * 
     * @return ->pluginFile
     * 
     * @since 1.0.0
     */
    function _file()
    {
        return $this->pluginFile;
    }
    
    
    
    
    
    
    /**
     * _new function.
     * 
     * The _new function is used for instantiating new classes - it is needed for chaining to work
     * 
     * @access private
     * @param mixed $className
     * @param array $arguments
     * 
     * @return new class
     * 
     * @since 1.0.0
     */
    function _new( $className, $arguments = array() )
    {
        return new $className( $this, $arguments );
    }
    
    /**
     * _framework function
     * 
     * Function used for translation purposes
     * 
     * @return framework version
     * 
     * @since 1.0.0
     */
    function _framework()
    {
        return "lavaPlugin";
    }
    
    /**
     * _handle function.
     * 
     * 
     * 
     * @access private
     * @param mixed $what
     * @param bool $reset
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function _handle( $what, $reset )
    {
        $pointer = "_" . strtolower( $what );
        if( !isset( $this->$pointer ) )
        {
            $this->$pointer = $this->_new( "lava$what" );
        }
        if( $reset == true )
        {
            return $this->$pointer->lavaReset();
        }
        else
        {
            return $this->$pointer;
        }
    }
    
    /**
     * _settings function.
     * 
     * @return lavaSettings
     * 
     * @since 1.0.0
     */
    function _settings( $reset = true )
    {
        return $this->_handle( "Settings", $reset );
    }
    
    /**
     * _pages function.
     * 
     * @return lavaPages
     * 
     * @since 1.0.0
     */
    function _pages()
    {
        return $this->_handle( "Pages", $reset );
    }
    
    /**
     * _tables function.
     * 
     * @return lavaTables
     * 
     * @since 1.0.0
     */
    function _tables()
    {
        return $this->_handle( "Tables", $reset );
    }
    
}
?>