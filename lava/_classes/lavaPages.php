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
    protected $adminPages = array();
    protected $defaultPage;
    public $styles = array(), $scripts = array(), $externalStyles = array(), $externalScripts = array();
    
    /**
    * lavaPages::lavaConstruct()
    * 
    * @return void
    *
    * @since 1.0.0
    */
    function lavaConstruct()
    {
        $this->addStyle( $this->_slug( "lavaStyles" ), "lava/_static/styles.css" );
        $this->addScript( $this->_slug( "lavaScripts" ), "lava/_static/scripts.js" );
        
        add_action( "admin_enqueue_scripts", array( $this, "registerIncludes" ) );

        add_action( "admin_menu", array( $this, "registerPages") );

        if( is_multisite() )
            add_action( "network_admin_menu", array( $this, "registerNetworkPages" ) );
    }
    
    
    /**
     * addPage function.
     * 
     * This function adds an admin page
     * 
     * @param mixed $slug
     * @param string $type (default: "")
     * @return void
     *
     * @since 1.0.0
     */
    function addPage( $slug, $type = "", $slugify = true )
    {
        if( true == $slugify )
        {
            $slug = $this->_slug( $slug );
        }


        if( !isset( $this->adminPages[ $slug] ) )
        {
            $arguments = array( $slug );
            $this->adminPages[ $slug ] = $this->_new( "lava{$type}Page", $arguments );
        }
        $this->chain[ "current" ] = $this->adminPages[ $slug ];
        
        if( empty( $this->defaultPage ) )// If a default page (the page that displays when the main page is clicked) hasn't been set then set it (otherwise a blank page will be displayed).
        {
            $this->defaultPage = $this->adminPages[ $slug ];
        }

        return $this;
    }
    
    /**
     * fetchPage function.
     * 
     * @access public
     * @param mixed $slug
     * @return void
     *
     * @since 1.0.0
     */
    function fetchPage( $slug )
    {
        unset( $this->chain[ "current" ] );
        if( isset( $this->adminPages[ $slug ] ) )
        {
            $this->chain[ "current" ] = $this->adminPages[ $slug ];
        }
        return $this;
    }

    function adminPages()
    {
        return apply_filters( "admin_pages_order-".$this->_slug(), $this->adminPages );
    }
    
    
    function addPageFromTemplate( $slug, $template )
    {
        return $this->addPage( $slug );
    }
    
    
    /**
     * addAboutPage function.
     * 
     * @access public
     * @return void
     */
    function addAboutPage( $slug = "about" )
    {
        $this   ->addPage( $slug, "About" )
                    ->title( sprintf( __( "About %s", $this->_framework() ), $this->_name() ) );
        return $this;
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
                    ->title( __( "Plugin Settings", $this->_framework() ) );
                    
        return $this;
    }
    
    /**
     * addSkinsPage function.
     * 
     * @param string $slug (default: "skins") - to be appended to the plugin slug to make the url
     * @return void
     */
    function addSkinsPage( $slug = "skins" )
    {
        $this   ->addPage( $slug, "Skins" )
                    /* translators: This is the title of the settings page */
                    ->title( __( "Skins", $this->_framework() ) )
        ;
                    
        return $this;
    }

    
    /**
     * addTablePage function.
     * 
     * @access public
     * @param mixed $slug (default: "table") - to be appended to the plugin slug to make the url
     * @return void
     * @since 1.0.0
     */
    function addTablePage( $slug = "table" )
    {
        $this   ->addPage( $slug, "Table" )
                    ->title( __( "Table", $this->_framework() ) )
        ;
        return $this;
    }
    




    /**
     * defaultPage function.
     *  Sets the currently chained page as the one to be displayed when the top-level page is clicked.
     * 
     * @return void
     * @since 1.0.0
     */
    function defaultPage()
    {
        if( isset( $this->chain[ "current" ] ) )
        {
            $this->defaultPage = $this->chain[ "current" ];
        }

        return $this;
    }

    /**
     * registerPages function.
     *  Registers each of the admin pages
     * 
     * @return void
     * @since 1.0.0
     */
    function registerPages()
    {
        $defaultPage = $this->defaultPage;
        //register the main page
        add_menu_page( $defaultPage->get( "title" ),  $this->_name(), $defaultPage->get( "capability" ), $defaultPage->get( "slug" ), array( $this, "blank" ) );

        $parentSlug = $defaultPage->get( "slug" );

        //register each subpage
        foreach( $this->adminPages as $page )
        {
            $page->registerPage( $parentSlug );
        }
    }

    /**
     * registerNetworkPages function.
     *  Registers each of the admin pages
     * 
     * @return void
     * @since 1.0.0
     */
    function registerNetworkPages()
    {
        $defaultPage = $this->defaultPage;
        //register the main page
        add_menu_page( $defaultPage->get( "title" ),  $this->_name(), $defaultPage->get( "capability" ), $defaultPage->get( "slug" ), array( $this, "blank" ) );

        $parentSlug = $defaultPage->get( "slug" );

        //register each subpage
        foreach( $this->adminPages as $page )
        {
            if( true === $page->multisiteSupport )//if they support multisite
            {
                $page->registerPage( $parentSlug );
            }
        }
    }





    function addStyle( $name, $path = "", $external = false )
    {
        if( true == $external)
        {
            $this->externalStyles[ $name ] = $path;
        }
        else
        {
            $this->styles[ $name ] = $path;
        }
    }

    function addScript( $name, $path = "", $external = false )
    {
        if( true == $external)
        {
            $this->externalScripts[ $name ] = $path;
        }
        else
        {
            $this->scripts[ $name ] = $path;
        }
    }

    //@deprecated
    /**
     * lavaPages::registerScripts()
     * 
     * @return void
     */
    function registerIncludes()
    {
        if( $this->_settings()->config( "CUSTOM_STYLES" == true ) )
        {
            $this->addStyle( $this->_slug( "custom-styles" ), "includes/style.css" );
        }
        if( $this->_settings()->config( "CUSTOM_SCRIPTS" == true ) )
        {
            $this->addScript( $this->_slug( "custom-scripts" ), "includes/scripts.js" );
        }
        foreach( $this->scripts as $name => $path )
        {
            if( !empty( $path ) )
            {
                wp_register_script( $name, plugins_url( $path, $this->_file() ) );
            }
        }
        foreach( $this->styles as $name => $path )
        {
            if( !empty( $path ) )
            {
                wp_register_style( $name, plugins_url( $path, $this->_file() ) );
            }
        }
        
        //do external includes
        foreach( $this->externalScripts as $name => $path )
        {
            if( !empty( $path ) )
            {
                wp_register_script( $name, $path );
            }
        }
        foreach( $this->externalStyles as $name => $path )
        {
            if( !empty( $path ) )
            {
                wp_register_style( $name, $path );
            }
        }
    }

    function blank()
    {
    
    }

}
?>