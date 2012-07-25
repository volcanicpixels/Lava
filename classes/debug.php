<?php
function test( $env ) {
	print_r( $env->getLoader()->getPaths() );
	print_r( $env->getLoader()->getCacheKey( 'login.html' ) );
}

class interesting {
	static function test2( $env, $context ) {
		echo 'jam';
	}
}

?>