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
    public $displayToolbar = false;
    
    
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

        add_action( "admin_init", array($this, "_registerActions") );
        if( is_callable( array( $this, "registerActions" ) ) )
        {
            add_action( "admin_init", array($this, "registerActions") );
        }
    }

    function _registerActions()
    {
        $pageHook = $this->pageHook;
        if( is_callable( array( $this, "loadPage" ) ) )
        {
            add_action( "load-{$pageHook}", array( $this, "loadPage" ) );
        }
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
        <div class="wrap">
            <div class="lava-header" style="margin-bottom:10px;">
                <div id="icon-options-general" class="icon32"></div>
                <h2><?php echo $pluginName; ?> <span class="version"><?php echo $pluginVersion; ?></span></h2>
                <div class="ajax-checks">
                    <!-- When no-update is implemented wrap this in an "if" or better implement a hook -->
                    <div class="js-only loader" data-name="update-available"></div>
                 <!--.ajax-checks END-->
                </div>
            <!--.lava-header END-->
            </div>
            <div id="lava-nav" class="lava-nav texture texture-drk-red bleed-l-19 bleed-r-15 lava-sticky" style="height:40px;">
                <ul class="nav nav-horizontal clearfix stitch-left-x stitch-right-x">
                    <?php foreach( $this->_pages( false )->adminPages() as $page ): ?>
                   <li class="stitch-left stitch-right clearfix <?php echo $page->get( "slug" ); ?> <?php if( $page_hook == $page->get( "slug" ) ){ echo "active"; } ?>"><a href="<?php echo $page->url(); ?>"><?php echo $page->get( "title" ); ?></a></li>
                   <?php endforeach; ?>
                </ul>
                <?php
                if($this->displayToolbar) $this->doToolbar();
                ?>
            </div>
            <?php if($this->displayToolbar): ?>
            <div class="toolbar-padder"></div>
            <?php endif; ?>
            <noscript>
                <div class="lava-message warning">
                    <span class="message"><?php _e( "You don't have JavaScript enabled. Some features will not work without JavaScript.", $this->_framework()) ?></span>
                </div>
            </noscript>
        <?php
    }

    function displayFooter()
    {
        ?>
        <!--.wrap END-->
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

    function doToolbar()
    {
        $leftActions = $this->_leftActions();
        $rightActions = $this->_rightActions();

        echo '<div class="lava-toolbar">';
            echo '<div class="actions-left">';
                foreach($leftActions as $action)
                {
                    echo '<div class="action">';
                        echo $action;
                    echo '</div>';
                }
            echo '</div>';
            echo '<div class="actions-right">';
                foreach($rightActions as $action)
                {
                    echo '<div class="action">';
                        echo $action;
                    echo '</div>';
                }
            echo '</div>';
        echo '</div>';
    }

    function _leftActions()
    {
        $actions = array();
        if( method_exists($this, "leftActions") )
        {
            $actions = $this->leftActions();
        }

        $actions = apply_filters( $this->_slug( "leftActions" ), $actions );
        $actions = apply_filters( $this->_slug( "leftActions-page/{$this->slug}" ), $actions );

        
        

        return $actions;
    }

    function _rightActions()
    {
        $actions = array();
        if( method_exists($this, "rightActions") )
        {
            $actions = $this->rightActions();
        }

        $actions = apply_filters( $this->_slug( "rightActions" ), $actions );
        $actions = apply_filters( $this->_slug( "rightActions-page/{$this->slug}" ), $actions );


        return $actions;
    }

    function hookTags()
    {
        $hooks = array(
            "",
            "slug/{$this->slug}",
            "multisiteSupport/{$this->multisiteSupport}",
            "toolbar/{$this->displayToolbar}",
            "class/{$class}",
        );
        return $hooks;
    }
}
?>