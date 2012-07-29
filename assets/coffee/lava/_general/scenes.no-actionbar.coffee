###
No actionbar callbacks
###

do ($ = jQuery, window, document) ->

	methods = {}
	namespace = 'lavaNoActionbarScene'

	methods.init = (e, lava) ->
		$(lava).each () ->
			$blocks = $(this).find( '.lava-scene.lava-scene-no-actionbar' )

			$blocks.on "active.lava.#{namespace}", methods.active
			$blocks.on "inactive.lava.#{namespace}", methods.inactive

			$blocks = $(this).filter( '.lava-scene.lava-scene-no-actionbar' )

			$blocks.on "active.lava.#{namespace}", methods.active
			$blocks.on "inactive.lava.#{namespace}", methods.inactive

	methods.active = (e) ->
		# remove action bar
		$('#lava-theatre').addClass( 'no-actionbar' )

	methods.inactive = (e) ->
		# replace actionbar
		$('#lava-theatre').removeClass( 'no-actionbar' )


	lavaBindMethods methods, namespace