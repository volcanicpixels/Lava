<?php
class lavaExtension extends lavaBase {
	function lavaConstruct() {
		$this->_misc()->_addAutoMethods( $this );
	}
}
?>