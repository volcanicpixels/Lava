###
No actionbar callbacks
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'lavaNoActionbarScene'
	selector = '.lava-scene.lava-scene-no-actionbar'

	methods.init = (e, lava) ->
		$(lava).each () ->
			$elems = $(this).find( selector )
			$.merge $elems, $(this).filter( selector )

			$elems.on "active.lava.#{namespace}", methods.active
			$elems.on "inactive.lava.#{namespace}", methods.inactive

	methods.active = (e) ->
		# remove action bar
		$('#lava_theatre').addClass( 'no-actionbar' )

	methods.inactive = (e) ->
		# replace actionbar
		$('#lava_theatre').removeClass( 'no-actionbar' )


	lavaBindMethods methods, namespace