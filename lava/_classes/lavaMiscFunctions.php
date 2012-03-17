<?php
class lavaMiscFunctions extends lavaBase
{

	function lavaConstruct() {
		$this->addAutoMethods();
	}

    function current_context_url( $path )
    {
        if( is_multisite() and defined( 'WP_NETWORK_ADMIN' ) and WP_NETWORK_ADMIN == true )
        {
            return network_admin_url( $path );
        }
        return admin_url( $path );
    }

    function addAutoMethods() {
    	$objects = array(
    		$this,
    		$this->_this()->pluginCallbacks,
    		$this->_ajax(),
    		$this->_skins()
    	);

		foreach( $objects as $object ) {
			$this->_addAutoMethods( $object );
		}
    }

    function _addAutoMethods( $object ) {
        $autoHooks = array(
            "init" => "init",
            "admin_init" => "adminInit"
        );
        foreach( $autoHooks as $hookTag => $actions ) {
                if( !is_array( $actions ) ) {
                    $actions = array( $actions );
                }
                foreach( $actions as $action ) {
                    if( method_exists( $object, $action ) ) {
                        $callback = array( $object, $action ); 
                        add_action( $hookTag, $callback );
                    }
                }
            }
    }

    function _registerActions() {
    	$hooks = array();

    	foreach( $hooks as $hook ) {
    		add_action( $hook, array( $this, $hook ) );
    	}
    }

    function versionMatch( $ver1, $ver2 = null ) {
        if( is_null( $ver2 ) ) {
            $ver2 = $this->_version();
        }
        if( strpos( $ver2, "beta" ) ) {
            return false;//this is a beta plugin so we should assume run update hooks all the time
        }
        if( $ver1 == $ver2 ) {
            return true;
        }
        return fasle;
    }

    
}
?>