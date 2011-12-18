<?php
/**
 * The lava Skin class
 * 
 * @package Lava
 * @subpackage lavaSkin
 * 
 * @author Daniel Chatfield
 * @copyright 2011
 * @version 1.0.0
 */
 
/**
 * lavaSkin
 * 
 * @package Lava
 * @subpackage LavaSkin
 * @author Daniel Chatfield
 * 
 * @since 1.0.0
 */
class lavaSkin extends lavaBase
{
    protected $slug;
    protected $author;
    protected $templates = array();
    
    /**
     * lavaSkin::lavaConstruct()
     * 
     * This method is called by the __construct method of lavaBase and handles the construction
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    function lavaConstruct( $slug )
    {
        $this->slug = $slug;
    }

    function _setName(  )
}
?>