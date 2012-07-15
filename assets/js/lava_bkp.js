/*
What: Lava.js provides all the lovely UI stuff for admin pages
Who: Daniel Chatfield
When: 2012
How: Voodoo
*/

(function( $, window, document ){ //prevents scope traversing
	var
		methods = {},
		hashMethods = {},

		keyMap = {
			'enter' : 13,
			'left'  : 37,
			'up'    : 38,
			'right' : 39,
			'down'  : 40
		},

		defaults = {

		},

		$html = $('html'),

		eventsBound = false

	;


	// The init method lava-ises the element
	methods.init = function( opts ) {
		opts = $.extend({}, defaults, opts)
		var $this = $(this)
		
		debug.info('Lava initialised')



		this.each(function(){
			methods.hide_nojs.apply( this )

			if( $html.hasClass( 'hashchange' ) ) {
				$links = $this.find( '.js-hash-link' )

				methods.hashify.apply( $links )
			}
		})
		if( !eventsBound ) {
			$(window).hashchange( methods.hashchange )
		}

		$(window).hashchange();

		eventsBound = true
		return this

	}

	methods.hide_nojs = function() {
		return $(this).find('.js-nojs').hide()
	}

	// takes an element and turns it from a link into a hash
	methods.hashify = function() {
		return this.each(function(){
			data = $(this).data( 'hashify' )

			if( !data ) {
				$(this).data('hashify', true)
				$(this).bind( 'click.hashify', function(e){
					hash_key   = $(this).attr('data-hash')
					hash_value = $(this).attr('data-' + hash_key)
					e.preventDefault();
					window.location.hash = '#' + hash_key + '=' + hash_value
				})
			}
		})
	}

	methods.hashchange = function() {
		//split hash into parts, check each part for change and if changed attempt to find a handler


		hash = window.location.hash.replace( '#', '' )

		hash = hash.split('&')

		$.each( hash, function( index, data){
			data = data.split('=')
			handler = data[0]
			value = true
			if( data.length > 1 ) {
				value = data[1]
			}


			$(window).trigger('lavaHashChange')
			$(window).trigger('lavaHashChange:' + handler, value )

		})
	}

	methods.changeScene = function( scene ) {
		$scene = $('#lava-scene_' + scene)

		if($scene.size() > 0) {
			$('#lava-programme .active-descendant').removeClass('active-descendant')
			$('#lava-programme li[data-scene="' + scene + '"]').addClass('active-descendant')
			$('#lava-stage .active-descendant').removeClass('active-descendant').addClass('inactive-descendant')
			$scene.removeClass('inactive-descendant').addClass('active-descendant').css({'opacity': 0,'position': 'relative', 'left': '-10px'}).animate({'opacity': 1, 'left': 0}, 200)
			//fire scene selected events

			$scene.trigger('lavaActive')
		}

	}

	hashMethods.scene = function(e, value) {
		methods.changeScene( value )
	}

	
	$(window).bind('lavaHashChange:scene', hashMethods.scene )







	$.fn.lava = function( method ) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1))
		} else if (typeof method === 'object' || ! method) {
			return methods.init.apply(this, arguments)
		} else {
			logging.error( 'Lava call failed with:', method )
		}
	};
})( jQuery, window, document );

jQuery('document').ready(function(){
	jQuery( '.lava-cntr' ).lava();
})