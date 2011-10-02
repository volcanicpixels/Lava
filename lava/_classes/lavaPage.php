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

    function sidebar( $enable = true )
    {
        remove_filter( "admin_page_class-{$pluginSlug}-{$page_hook}", array( $this, "sidebarCallback") );
        if( true == $enable )
        {
            $pluginSlug = $this->_slug();
            $page_hook = $this->get( "slug" );
            add_filter( "admin_page_class-{$page_hook}", array( $this, "sidebarCallback") );
        }

        return $this->_pages( false );
    }

    function sidebarCallback( $classes )
    {
        return "{$classes} sidebar";
    }
    
    function get( $what )
    {
        return $this->$what;
    }

    
    function url()
    {
        $slug = $this->get( "slug" );
        if( defined( 'WP_NETWORK_ADMIN' ) and WP_NETWORK_ADMIN == true )
        {
            //if we are in the network admin then make sure it is a network link
            return network_admin_url( "admin.php?page={$slug}");
        }
        return admin_url( "admin.php?page={$slug}");
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
        $this->displayHeader();
        $this->displayNotifications();
        $this->displayPage();
        $this->displayFooter();
    }
    
    function displayHeader()
    {
        $pluginSlug = $this->_slug();
        $pluginName = $this->_name();
        $pluginVersion = $this->_version();

        $page_hook = $_GET['page'];
        $lavaPageClass = apply_filters( "admin_page_class-{$pluginSlug}", "" );
        $lavaPageClass = apply_filters( "admin_page_class-{$page_hook}", $lavaPageClass );
        ?>
        <div id="lava-page" class="<?php echo $lavaPageClass;?>">
            <div id="lava-header">
                <div class="texture texture-lt-red stitch-btm">
                    <div class="lava-cntr lava-cntr-fw">
                        <h1 class="lobster-heading"><?php echo $pluginName; ?><span style="margin-left:20px;" class="version"><?php echo $pluginVersion; ?></span></h1>
                        <?php do_action( "post_heading-{$pluginSlug}" ) ?>
                    </div>
                </div>
                <div class="texture texture-drk-red stitch-top">
                    <div class="lava-cntr lava-cntr-fw clearfix">
                        <ul class="nav nav-awning clearfix stitch-left-x stitch-right-x">
                            <?php foreach( $this->_pages( false )->adminPages() as $page ): ?>
                                <li class="stitch-left stitch-right <?php echo $page->get( "slug" ); ?> <?php if( $page_hook == $page->get( "slug" ) ){ echo "active"; } ?>"><a href="<?php echo $page->url(); ?>"><?php echo $page->get( "title" ); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="bunting"></div>
            </div>
            
            <div class="lava-content lava-cntr">
                <div class="top"></div>
                    <div class="content">
                
                    
        
        <?php
    }

    function displayFooter()
    {
        ?>
                    </div>
                <div class="bottom"></div>
            </div>
        </div>
        <?php
    }

    function displayNotifications()
    {
        $notifications = array();
        if( isset( $_GET[ 'messagesnonce' ] ) )
        {
            $storedNotifications = get_option( "lavaNotifications" );

            if( is_array( $storedNotifications ) and isset( $storedNotifications[ $_GET[ 'messagesnonce' ] ] ) )
            {
                $storedNotifications = $storedNotifications[ $_GET[ 'messagesnonce' ] ];
    
                if( is_array( $storedNotifications ) )
                {
                    foreach( $storedNotifications as $notification )
                    {
                        $notifications[] = $notification;
                    }
                }
            }
        }

        $notifications = apply_filters( "lava_notifications-{$page_hook}", $notifications );
        
        foreach( $notifications as $notification )
        {
            ?>
            <div class="lava-notification lava-notification-"><?php echo $notification['message'];?></div>
            <?php
        }
    }

    function displayPage()
    {
        ?>
        <div class="lava-notification lava-notification-error"><?php _e( "It looks like this page has gone walk-abouts.", $this->_framework() ) ?></div>
        <?php
    }
}
?>