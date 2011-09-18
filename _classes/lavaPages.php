<?php
/**
 * The lavaPages class
 * 
 * This class is the controller for the plugin admin pages
 * 
 * @package Lava
 * @subpackage lavaPages
 * 
 * @author Daniel Chatfield
 * @copyright 2011
 * @version 1.0.0
 */
 
/**
 * lavaPages
 * 
 * @package Lava
 * @subpackage LavaPages
 * @author Daniel Chatfield
 * 
 * @since 1.0.0
 */
class lavaPages extends lavaBase
{
    protected $pages = array();
    public $parentSlug, $parentCallback;
    public $pluginStyles = array(), $pluginScripts = array(), $pluginExternalScripts = array(), $pluginExternalStyles = array();
    
    function lavaConstruct()
    {
        $this->menu_slug = $this->_slug();
        $this->menu_title = $this->_name();
        $this->page_title = $this->_name() . " " . $this->_version();
        $this->capability = "administrator";
        
        $this->addStyle( $this->_slug( "lavaStyles" ), "_static/styles.css" );
        $this->addScript( $this->_slug( "lavaScripts" ), "_static/scripts.js" );
        $this->addExternalStyle( "chewy", "http://fonts.googleapis.com/css?family=Chewy" );
        
        add_action( "admin_enqueue_scripts", array( $this, "registerIncludes" ) );
        add_action( "admin_enqueue_scripts", array( $this, "registerExternalIncludes" ) );
    }
    
    
    /**
     * addPage function.
     * 
     * This function adds an admin page
     * 
     * @param mixed $slug
     * @param string $type (default: "")
     * @return void
     */
    function addPage( $slug, $type = "", $slugify = true )
    {
        if( $this->_settings()->config( "PARENT_ADDED" ) != true )
        {
            $this->_settings()->config( "PARENT_ADDED", true );
            $this->addInfoPage();
        }
        unset( $this->chain[ "current" ] );
        if( !isset( $this->pages[ $slug] ) )
        {
            $this->pages[ $slug ] = $this->_new( "lava{$type}Page" );
            $thisPage = $this->pages[ $slug ];
            $thisPage->slug( $slug, $slugify )->title( $slug );
        }
        $this->chain[ "current" ] = $this->pages[ $slug ];
        
        
        return $this;
    }
    
    /**
     * fetchPage function.
     * 
     * @access public
     * @param mixed $slug
     * @return void
     */
    function fetchPage( $slug )
    {
        unset( $this->chain[ "current" ] );
        if( isset( $this->pages[ $slug ] ) )
        {
            $this->chain[ "current" ] = $this->pages[ $slug ];
        }
        return $this;
    }
    
    
    
    
    
    /**
     * addAboutPage function.
     * 
     * @access public
     * @return void
     */
    function addAboutPage()
    {
        $this   ->addPage( "about", "About" )
                    ->title( __( "About", $this->_framework() ) )
                    ->config( "DONT_REGISTER_PAGE", true );//this is going to be used as the main page for the plugin and therefore doesn't need to register itself as subpage
        return $this->fetchPage( "about" );
    }
    
    function addInfoPage( $slug = "info" )
    {
        $this   ->addPage( $slug, "About" )
                    ->title( __( $this->_name(), $this->_framework() ) )
                    ->promote()
        ;
                    
        return $this->fetchPage( $slug );
    }
    /**
     * addSettingsPage function.
     * 
     * @access public
     * @return void
     */
    function addSettingsPage( $slug = "settings" )
    {
        $this   ->addPage( $slug, "Settings" )
                    /* translators: This is the title of the settings page */
                    ->title( __( "Plugin Settings", $this->_framework() ) )
                ->addPage( "multisite{$slug}", "Settings" )
                    /* translators: The multisite here refers to the wordpress "multisite" and the equivalent word used by wordpress in your language should be used for consistency */
                    ->title( __( "Multisite Settings", $this->_framework() ) )
                    ->network( true );
                    
        return $this->fetchPage( $slug );
    }
    
    /**
     * addSkinsPage function.
     * 
     * @access public
     * @param string $slug (default: "skins")
     * @return void
     */
    function addSkinsPage( $slug = "skins" )
    {
        $this   ->addPage( $slug, "Skins" )
                    /* translators: This is the title of the settings page */
                    ->title( __( "Skins", $this->_framework() ) )
        ;
                    
        return $this->fetchPage( $slug );
    }

    
    /**
     * addTablePage function.
     * 
     * @access public
     * @param mixed $slug
     * @return void
     */
    function addTablePage( $slug = "table" )
    {
        $this   ->addPage( $slug, "Table" )
                    ->title( __( "Table", $this->_framework() ) )
        ;
        return $this->fetchPage( $slug );
    }
    
    /**
     * addLicensingPage function.
     * 
     * @access public
     * @param string $slug (default: "licensing")
     * @return void
     */
    function addLicensingPage( $slug = "premium" )
    {
        $this   ->addPage( $slug, $type = "Licensing", $slugify = false )
                    ->title( __( "Unlock Premium", $this->_framework() ) )
        ;
        return $this->fetchPage( $slug );
    }
    
    /**
     * addSupportPage function.
     * 
     * @access public
     * @param string $slug (default: "support")
     * @return void
     */
    function addSupportPage( $slug = "support" )
    {
        $this   ->addPage( $slug, "Support" )
                    ->title( __( "Plugin Support", $this->_framework() ) )
        ;
        return $this->fetchPage( $slug );
    }
    
    function addStyle( $name, $relativePath = "" )
    {
        $this->pluginStyles[$name] = $relativePath;
    }
    function addScript( $name, $relativePath = "" )
    {
        $this->pluginScripts[$name] = $relativePath;
    }
    function addExternalStyle( $name, $fullPath = "" )
    {
        $this->pluginExternalStyles[$name] = $fullPath;
    }
    function addExternalScript( $name, $fullPath = "" )
    {
        $this->pluginExternalScripts[$name] = $fullPath;
    }
    
    /**
     * lavaPages::registerScripts()
     * 
     * @return void
     */
    function registerScripts()
    {
        if( $this->_settings()->config( "CUSTOM_STYLES" == true ) )
        {
            $this->addStyle( $this->_slug( "custom-styles" ), "includes/style.css" );
        }
        if( $this->_settings()->config( "CUSTOM_SCRIPTS" == true ) )
        {
            $this->addScript( $this->_slug( "custom-scripts" ), "includes/scripts.js" );
        }
        foreach( $this->pluginScripts as $name => $path )
        {
            if( !empty( $path ) )
            {
                wp_register_script( $name, plugins_url( $path, $this->_file() ) );
            }
        }
        foreach( $this->pluginStyles as $name => $path )
        {
            if( !empty( $path ) )
            {
                wp_register_style( $name, plugins_url( $path, $this->_file() ) );
            }
        }
    }
    
    function registerExternalScripts()
    {
        foreach( $this->pluginExternalScripts as $name => $path )
        {
            if( !empty( $path ) )
            {
                wp_register_script( $name, $path );
            }
        }
        foreach( $this->pluginExternalStyles as $name => $path )
        {
            if( !empty( $path ) )
            {
                wp_register_style( $name, $path );
            }
        }
    }
    

    
    function parentSlug()
    {
        if( empty( $this->parentSlug ) )
        {
            $this->parentSlug = call_user_func( $this->parentCallback );
        }
        return $this->parentSlug;
    }

}
?>