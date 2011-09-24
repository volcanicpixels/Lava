<?php
/**
 * The lava page class
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
class lavaPage extends lavaBase
{
    protected $menu_slug, $menu_title, $page_title, $capability, $callback, $displayCallback, $page;
    
    function lavaConstruct()
    {
        $this->menu_slug = "undefined";
        $this->menu_title = "undefined";
        $this->page_title = "undefined";
        $this->capability = "manage_options";
        
        $this->displayCallback = array( $this, "displayPage" );
        //register hooks
        if( $this->_settings()->config( "PAGE_REGISTRATION" ) != "CLOSED" )//this page is being defined either at or after the menu is being defined
        {
            $callback = array( $this, "registerSubpage" );
            remove_action( "admin_menu", $callback );
            add_action( "admin_menu", $callback );
        }
    }
    
    function promote()
    {
        $callback = array( $this, "registerSubpage" );
        remove_action( "admin_menu", $callback );
        
        $this->_settings()->config( "PARENT_ADDED", true );
        $callback = array( $this, "registerPage" );
        $this->_pages()->parentCallback = $callback;
    }
    
    function get( $what )
    {
        return $this->$what;
    }
    
    function capability( $capability )
    {
        $this->capability = $capability;
        return $this->_pages( false );
    }
    
    function slug( $slug, $slugify = true )
    {
        if( $slugify == true )
        {
            $this->menu_slug = $this->_slug( $slug );
        }
        return $this->_pages( false );
    }
    
    function title( $title )
    {
        $this->menu_title = $title;
        $this->page_title = $title;
        return $this->_pages( false );
    }
    
    function registerSubpage()
    {
        if( $this->config( "DONT_REGISTER_PAGE" ) == true )
        {//used for silent pages and the about page (it is registered as top level page
            return false;
        }
        $this->page = add_submenu_page( 
            $this->_pages()->parentSlug(), 
            $this->page_title, 
            $this->menu_title, 
            $this->capability, 
            $this->menu_slug, 
            $this->get( "displayCallback" ) 
        );
        $hook_suffix = $this->page;
        add_action( "admin_print_styles-$hook_suffix", array( $this, "enqueueScripts" ) );
    }
    
    function registerPage()
    {
        if( $this->config( "DONT_REGISTER_PAGE" ) == true )
        {//used for silent pages and the about page (it is registered as top level page
            return false;
        }
        $this->page = add_menu_page( 
            $this->page_title, 
            $this->menu_title, 
            $this->capability, 
            $this->menu_slug, 
            $this->get( "displayCallback" ) 
        );
        $hook_suffix = $this->page;
        add_action( "admin_print_styles-$hook_suffix", array( $this, "enqueueScripts" ) );
        return $this->menu_slug;
    }
    
    function enqueueScripts()
    {
        foreach( $this->_pages()->pluginStyles as $name => $notNeeded )
        {
            wp_enqueue_style( $name );
        }
        foreach( $this->_pages()->pluginScripts as $name => $notNeeded )
        {
            wp_enqueue_script( $name );
        }
        foreach( $this->_pages()->pluginExternalStyles as $name => $notNeeded )
        {
            wp_enqueue_style( $name );
        }
        foreach( $this->_pages()->pluginExternalScripts as $name => $notNeeded )
        {
            wp_enqueue_script( $name );
        }
    }
    
    function displayPage()
    {
        if( $this->config( "SUPPRESS_HEADER" ) != true )
        {
            $this->displayHeader();
        }
    }
    
    function displayHeader()
    {
        ?>
        
        <div id="cutout">
            <h2>[Plugin Name] <span class="versions-pre">ver.</span> <span class="versions-post">[version]</span></h2>
            <div class="window loading">
                <div class="loading"></div>
                <div class="licensed">
                    <div class="status">Premium features unlocked</div>
                    <div class="usage">Unique ID can be used on <span class="remaining">0</span> more sites. Please contact support or make another donation for more.</div>
                    <div class="unique">Unique ID: <span>ABCDE</span></div>
                </div>
                <div class="unlicensed">
                    <div class="status">Premium features locked</div>
                    <div class="message">Please make a contribution to unlock premium features.</div>
                    <div class="unique">Unique ID: <span>ABCDE</span></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="top-edge"></div>
            <div class="bottom-edge"></div>
        </div>
        
        <?php
    }
}
?>