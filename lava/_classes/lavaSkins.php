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
        $callbacks = $this->_new( 'lavaSkinsCallback' );

        //add the setting that holds which skin is selected
        $this->_settings()
            ->addSetting( 'skin', 'skins' )
                ->setType( 'skin' )
                ->setName( __( 'Current skin', $this->_framework() ) );
    }


    function parseSkins()
    {
        $skinPaths = glob( dirname( $this->_file() ) . '/skins/*' , GLOB_ONLYDIR);
        lava::currentPlugin( $this->_this() );//make sure theme files can access the plugin easily

        foreach( $skinPaths as $skinPath )
        {
            $includePath = $skinPath . '/skin.php';
            if( file_exists( $includePath ) )
            {
                $dir = str_replace(  '\\' , '/' , $skinPath );
                $dir = explode( '/', $dir );
                $dir = end( $dir );
                $this->currentSkinSlug = $dir;

				$skinName = $dir;
				$skinAuthor = "Undefined";

				if( $this->_request( "admin" ) )://only parse file headers on admin requests

					$skinHeader = file_get_contents( $includePath );

					if( strpos( substr( $skinHeader, 0, 20 ) , '/*' ) === false ) { //the substr prevents the search incorrectly matching the string in code (like on this line) by only searching the top of the file (where the header should be)
						//File has no header so leave defaults
					} else {
						$skinHeader = explode( '/*', $skinHeader );
						$skinHeader = $skinHeader[1];
						$skinHeader = explode( '*/', $skinHeader );
						$skinHeader = $skinHeader[0];
						$skinHeader = explode( "\n", $skinHeader );

						foreach( $skinHeader as $head )
						{
							$head = trim( $head );
							if( !empty( $head ) )
							{
								$head = explode( ":", $head );
								if( count( $head == 2 ) )
								{
									$property = strtoupper( $head[0] );
									$value = trim( $head[1] );

									switch( $property )
									{
										case 'NAME':
										case 'TITLE':
											$skinName = $value;
										break;
										case 'AUTHOR':
											$skinAuthor = $value;
										break;
									}
								}
							}
						}
					}
				endif;

				$this->registerSkin()
					->setName( $skinName )
					->setAuthor( $skinAuthor )
				;

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

        $this->skins[ $skinSlug ] = $theSkin;

		$this->_settings( false )
				->fetchSetting( "skin", "skins" )
                ->addPropertyValue( "radio-values", $theSkin );

        return $theSkin;
    }

	function getSkin( $handle )
	{
		if( array_key_exists( $handle, $this->skins ) )
		{
			return $this->skins[$handle];
		}
		$dir = str_replace(  '\\' , '/' , $handle );
		$dir = explode( '/', $dir );
		$dir = end( $dir );
		if( array_key_exists( $dir, $this->skins ) )
		{
			return $this->skins[$dir];
		}
		$dir = str_replace(  '\\' , '/' , dirname($handle) );
		$dir = explode( '/', $dir );
		$dir = end( $dir );
		if( array_key_exists( $dir, $this->skins ) )
		{
			return $this->skins[$dir];
		}
	}

    function fetchSkins()
    {
        return $this->skins;
    }
}
?>