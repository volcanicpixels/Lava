<?php
/**
 * The lava Skins class
 * 
 * @package Lava
 * @subpackage lavaSkins
 * 
 * @author Daniel Chatfield
 * @copyright 2011
 * @version 1.0.0
 */
 
/**
 * lavaSkins
 * 
 * @package Lava
 * @subpackage LavaSkins
 * @author Daniel Chatfield
 * 
 * @since 1.0.0
 */
class lavaSkins extends lavaBase
{
    protected $skins = array();
    public $currentSkinSlug;
    
    /**
     * lavaSkins::lavaConstruct()
     * 
     * This method is called by the __construct method of lavaBase and handles the construction
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function lavaConstruct()
    {
        $callbacks = $this->_new( "lavaSkinsCallback" );

        $this->parseSkins();
    }


    function parseSkins()
    {
        $skinPaths = glob( dirname( $this->_file() ) . '/skins/*' , GLOB_ONLYDIR);
        lava::currentPlugin( $this->_this() );//make sure theme files can access the plugin easily

        foreach( $skinPaths as $skinPath )
        {
            $includePath = $skinPath . "/index.php";
            if( file_exists( $includePath ) )
            {
                $dir = str_replace(  "\\" , "/" , $skinPath );
                $dir = explode( "/", $dir );
                $dir = end( $dir );
                $this->currentSkinSlug = $dir;

                include_once( $includePath );
            }
        }
    }

    function registerSkin()
    {
        $skinSlug = $this->currentSkinSlug;

        $arguments = array(
            $skinSlug
        );
        $theSkin = $this->_new( "lavaSkin", $arguments );

        $skins[ $skinSlug ] = $theSkin;

        return $theSkin;
    }
}
?>