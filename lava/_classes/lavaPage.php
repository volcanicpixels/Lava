<?php
/**
 * The lavaPage class
 * 
 * This class is the base class for all admin pages
 * 
 * @package Lava
 * @subpackage lavaPage
 * 
 * @author Daniel Chatfield
 * @copyright 2011
 * @version 1.0.0
 */
 
/**
 * lavaPage
 * 
 * @package Lava
 * @subpackage LavaPlugin
 * @author Daniel Chatfield
 * 
 * @since 1.0.0
 */
class lavaPage extends lavaBase
{
    public $multisiteSupport = false;//Whether the page should appear in the network page
    
    
    /**
    * lavaPage::lavaConstruct()
    * 
    * @return void
    *
    * @since 1.0.0
    */
    function lavaConstruct( $slug )
    {
        $this->slug( $slug, false );
        $this->title( $slug );
        $this->capability( "manage_options" );
        $this->lavaCallReturn = $this->_pages( false );
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
        $this->slug = $slug;

        if( $slugify == true )
        {
            $this->slug = $this->_slug( $slug );
        }
        return $this->_pages( false );
    }
    
    function title( $title )
    {
        $this->title = $title;
        return $this->_pages( false );
    }
    
    function registerPage( $parentSlug )
    {
        $this->pageHook = add_submenu_page( 
            $parentSlug,
            $this->get( "title" ), 
            $this->get( "title" ), 
            $this->get( "capability" ),  
            $this->get( "slug" ), 
            array( $this, "doPage") 
        );
        $hook_suffix = $this->pageHook;
        add_action( "admin_print_styles-$hook_suffix", array( $this, "enqueueIncludes" ) );
    }


    
    function enqueueIncludes()
    {
        foreach( $this->_pages()->styles as $name => $notNeeded )
        {
            wp_enqueue_style( $name );
        }
        foreach( $this->_pages()->scripts as $name => $notNeeded )
        {
            wp_enqueue_script( $name );
        }
        foreach( $this->_pages()->externalStyles as $name => $notNeeded )
        {
            wp_enqueue_style( $name );
        }
        foreach( $this->_pages()->externalScripts as $name => $notNeeded )
        {
            wp_enqueue_script( $name );
        }
    }
    
    function doPage()
    {
        if( true !== $this->config( "SUPPRESS_HEADER" ) )
        {
            $this->displayHeader();
        }
    }
    
    function displayHeader()
    {
        $pluginSlug = $this->_slug();
        $pluginName = $this->_name();
        $pluginVersion = $this->_version();

        $page_hook = $_GET['page'];
        $lavaPageClass = apply_filters( "_admin_page_class-{$pluginSlug}", "" );
        $lavaPageClass = apply_filters( "admin_page_class-{$pluginSlug}-{$page_hook}", $lavaPageClass );
        ?>
        <div id="lava-page" class="<?php echo $lavaPageClass;?>">
            <div id="lava-header">
                <div class="texture texture-lt-red">
                    <div class="lava-cntr lava-cntr-fw">
                        <h1 class="lobster-heading"><?php echo $pluginName; ?><span class="version"><?php echo $pluginVersion; ?></span></h1>
                        <?php do_action( "post_heading-{$pluginSlug}" ) ?>
                    </div>
                </div>
                <div class="texture texture-drk-red">
                    <div class="lava-cntr lava-cntr-fw">
                        <ul class="nav nav-awning">
                            <?php foreach( $this->_pages( false )->adminPages() as $page ): ?>
                                <li class="<?php echo $page->get( "slug" ); ?> <?php if( $page_hook == $page->get( "slug" ) ){ echo "active"; } ?>"><?php echo $page->get( "title" ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
    }
}
?>